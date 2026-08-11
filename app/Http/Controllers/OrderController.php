<?php

namespace App\Http\Controllers;


use PDF;
use Carbon\Carbon;
use App\Http\Controllers\Concerns\BuildsTourLocationItinerary;
use App\Http\Controllers\Concerns\InteractsWithFormSubmissions;
use App\Http\Requests\Tours\StoreTourPackageOrderRequest;
use App\Http\Requests\Hotels\StoreAccommodationOrderRequest;
use App\Exceptions\PricingException;
use App\Models\Tax;
use App\Models\User;
use App\Models\Guide;
use App\Models\Tours;
use App\Models\Brides;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Villas;
use App\Models\Drivers;
use App\Models\LogData;
use App\Models\UserLog;

use App\Models\ExtraBed;
use App\Models\OrderLog;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\HotelRoom;
use App\Models\Promotion;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\TourPrices;
use App\Models\Transports;

use App\Models\VillaPrice;
use App\Models\BankAccount;
use App\Models\BookingCode;
use App\Models\Reservation;
use App\Models\HotelPackage;
use App\Models\InvoiceAdmin;
use App\Models\OptionalRate;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Mail\ReservationMail;
use App\Models\VendorPackage;
use App\Models\AirportShuttle;
use App\Models\TransportPrice;
use App\Models\BusinessProfile;
use App\Models\AdditionalService;
use App\Models\OptionalRateOrder;
use App\Models\Activities;
use App\Services\ActivityOrderLifecycleService;
use App\Services\ActivityReservationService;
use App\Services\AccommodationBookingGuardService;
use App\Services\AccommodationFinancialFileService;
use App\Services\AccommodationOrderLifecycleService;
use App\Services\AccommodationReservationService;
use App\Services\Hotels\HotelPricingService;
use App\Services\Orders\OrderPaymentDeadlineService;
use App\Services\TransportOrderNumberService;
use App\Services\TransportAvailabilityService;
use App\Services\TransportOrderLifecycleService;
use App\Services\TransportReservationService;
use App\Services\TourOrderLifecycleService;
use App\Services\TourReservationService;
use App\Services\Tours\TourPackagePricingService;
use App\Services\Pricing\OrderPricingSnapshotReader;
use App\Services\Pricing\OrderPricingSnapshotWriter;
use App\Support\MoneyFormatter;
use App\Support\Pricing\FixedScale;
use App\Support\Pdf\InvoiceLocale;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentConfirmation;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\NotifikasiWhatsApp;
use Google\Service\ShoppingContent\Resource\Promotions;

class OrderController extends Controller
{
    use BuildsTourLocationItinerary;
    use InteractsWithFormSubmissions;

    private const TOUR_ORDER_SUBMISSION_SCOPE = 'tour-order-create';
    private const ACCOMMODATION_ORDER_SUBMISSION_SCOPE = 'accommodation-order-create';
    private const TRANSPORT_ORDER_SUBMISSION_SCOPE = 'transport-order-create';
    private const ACTIVITY_ORDER_SUBMISSION_SCOPE = 'activity-order-create';

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    private function getBookingAgents()
    {
        return User::where('status', 'Active')
            ->whereNotNull('email_verified_at')
            ->where('is_approved', true)
            ->get();
    }

    private function submitCreatedHotelOrder(Request $request, Orders $order, User $agent)
    {
        $requestQuotation = $this->resolveHotelQuotationValue($request, $order);

        DB::transaction(function () use ($request, $order, $agent, $requestQuotation) {
            $order->update([
                'request_quotation' => $requestQuotation,
                'status' => 'Pending',
            ]);

            app(AccommodationReservationService::class)->ensurePendingReservationForOrder($order);

            OrderLog::create([
                'order_id' => $order->id,
                'action' => 'Submit Order '.$order->service,
                'url' => $request->ip(),
                'method' => 'Submit',
                'agent' => $agent->name,
                'admin' => Auth::id(),
            ]);

            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id, $requestQuotation));
        });
    }

    private function generateUniqueHotelOrderNumber(string $prefix, Carbon $now): string
    {
        $baseNumber = $prefix . $now->format('ymd') . '-';
        $lastSuffix = Orders::where('orderno', 'like', $baseNumber . '%')
            ->pluck('orderno')
            ->map(function ($orderNumber) use ($baseNumber) {
                $suffix = str_replace($baseNumber, '', $orderNumber);

                return ctype_digit($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        do {
            $lastSuffix++;
            $orderNumber = $baseNumber . $lastSuffix;
        } while (Orders::where('orderno', $orderNumber)->exists());

        return $orderNumber;
    }

    private function hasDuplicateHotelOrderNumber(Request $request): bool
    {
        if (!$request->filled('orderno')) {
            return false;
        }

        return Orders::where('orderno', $request->orderno)
            ->exists();
    }

    private function flashDuplicateOrderNumberRefresh(): void
    {
        session()->flash('warning', __('messages.Order number already exists. The form has been refreshed with a new order number.'));
    }

    private function findProcessedAccommodationOrderBySubmissionToken(?string $token): ?Orders
    {
        $orderId = $this->findProcessedFormSubmission(self::ACCOMMODATION_ORDER_SUBMISSION_SCOPE, $token);

        return $orderId ? Orders::find($orderId) : null;
    }

    private function rememberProcessedAccommodationOrderSubmission(?string $token, Orders $order): void
    {
        if (!$token) {
            return;
        }

        $this->rememberProcessedFormSubmission(self::ACCOMMODATION_ORDER_SUBMISSION_SCOPE, $token, $order->id);
    }

    private function redirectToExistingAccommodationOrder(Orders $order)
    {
        $route = in_array(Auth::user()?->position, ['developer', 'reservation', 'author'], true)
            ? 'view.detail-order-admin'
            : 'view.detail-order-hotel';

        return redirect()->route($route, ['id' => $order->id])->with('info', __('messages.The order has already been submitted'));
    }

    private function revalidateAccommodationAvailability(int $hotelId, int $roomId, string $checkin, string $checkout, int $rooms, bool $lock = false): void
    {
        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            $hotelId,
            $roomId,
            $checkin,
            $checkout,
            $rooms,
            null,
            $lock
        );
    }

    private function findProcessedActivityOrderBySubmissionToken(?string $token): ?Orders
    {
        $orderId = $this->findProcessedFormSubmission(self::ACTIVITY_ORDER_SUBMISSION_SCOPE, $token);

        return $orderId ? Orders::find($orderId) : null;
    }

    private function rememberProcessedActivityOrderSubmission(string $token, Orders $order): void
    {
        $this->rememberProcessedFormSubmission(self::ACTIVITY_ORDER_SUBMISSION_SCOPE, $token, $order->id);
    }

    private function transportOrderNumberToLetters(int $number): string
    {
        $letters = '';

        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $number = intdiv($number - 1, 26);
        }

        return $letters ?: 'A';
    }

    private function transportOrderLettersToNumber(?string $letters): int
    {
        $letters = strtoupper(preg_replace('/[^A-Z]/', '', (string) $letters));

        if ($letters === '') {
            return 0;
        }

        $number = 0;

        foreach (str_split($letters) as $letter) {
            $number = ($number * 26) + (ord($letter) - 64);
        }

        return $number;
    }

    private function generateTransportOrderNumber(User $agent, Carbon $orderDate): string
    {
        return app(TransportOrderNumberService::class)->generate($agent, $orderDate);
    }

    private function resolveHotelQuotationValue(Request $request, ?Orders $order = null): ?string
    {
        $roomCount = is_array($request->number_of_guests ?? null)
            ? count($request->number_of_guests)
            : (int) optional($order)->number_of_room;

        return $request->boolean('request_quotation') || $request->request_quotation === 'Yes' || $roomCount > 8
            ? 'Yes'
            : null;
    }

    private function prepareAccommodationBookingData(Request $request, HotelRoom $room): void
    {
        if ((int) $request->input('hotel_booking_version') !== 2) {
            return;
        }

        $adults = array_values((array) $request->input('room_adults', []));
        $children = array_values((array) $request->input('room_children', []));
        $adultCapacity = $this->getRoomAdultCapacity($room);
        $childCapacity = max((int) ($room->capacity_child ?? max((int) ($room->capacity ?? 0) - $adultCapacity, 0)), 0);
        $roomCapacity = $adultCapacity + $childCapacity;

        foreach ($adults as $index => $adultCount) {
            $childCount = (int) ($children[$index] ?? 0);
            if ((int) $adultCount > $adultCapacity
                || $childCount > $childCapacity
                || ((int) $adultCount + $childCount) > $roomCapacity) {
                throw ValidationException::withMessages([
                    "room_adults.$index" => __('messages.Selected occupancy exceeds the capacity of this room.'),
                ]);
            }
        }

        $guestNames = array_values((array) $request->input('guest_name', []));
        $guestRooms = array_values((array) $request->input('guest_room', []));
        $guestCategories = array_values((array) $request->input('guest_category', []));
        $guestDetails = [];

        foreach ($adults as $index => $adultCount) {
            $roomNumber = $index + 1;
            $childAges = array_values((array) $request->input("room_child_ages.$index", []));
            $childAgeIndex = 0;
            $roomGuests = collect($guestNames)
                ->filter(fn ($name, $guestIndex) => (int) ($guestRooms[$guestIndex] ?? 0) === $roomNumber)
                ->map(function ($name, $guestIndex) use ($guestCategories, $childAges, &$childAgeIndex) {
                    $category = $guestCategories[$guestIndex] ?? 'Adult';
                    $ageSuffix = $category === 'Child' && array_key_exists($childAgeIndex, $childAges)
                        ? ', ' . $childAges[$childAgeIndex++] . ' ' . __('messages.years old')
                        : '';

                    return trim((string) $name) . ' (' . __(
                        'messages.' . $category
                    ) . $ageSuffix . ')';
                })
                ->values()
                ->all();
            $guestDetails[] = implode(', ', $roomGuests);
        }

        $request->merge([
            'number_of_guests' => collect($adults)
                ->map(fn ($adultCount, $index) => (int) $adultCount + (int) ($children[$index] ?? 0))
                ->values()
                ->all(),
            'guest_detail' => $guestDetails,
            'special_day' => array_pad(array_values((array) $request->input('special_day', [])), count($adults), null),
            'special_date' => array_pad(array_values((array) $request->input('special_date', [])), count($adults), null),
        ]);
    }

    private function storeAccommodationGuests(Request $request, Orders $order, ?Reservation $reservation): void
    {
        if ((int) $request->input('hotel_booking_version') !== 2 || !Schema::hasTable('guests')) {
            return;
        }

        $names = array_values((array) $request->input('guest_name', []));
        $phones = array_values((array) $request->input('guest_phone', []));
        $categories = array_values((array) $request->input('guest_category', []));
        $sexes = array_values((array) $request->input('guest_sex', []));
        $guestColumns = collect(Schema::getColumnListing('guests'))->flip();

        foreach ($names as $index => $name) {
            Guests::create(collect([
                'order_id' => $order->id,
                'rsv_id' => optional($reservation)->id,
                'name' => trim((string) $name),
                'phone' => filled($phones[$index] ?? null) ? trim((string) $phones[$index]) : null,
                'age' => $categories[$index] ?? null,
                'sex' => $sexes[$index] ?? null,
            ])->filter(fn ($value, $column) => $guestColumns->has($column))->all());
        }
    }

    private function generateActivityOrderNumber(Carbon $travelDate): string
    {
        $baseNumber = 'ACT' . $travelDate->format('ymd') . '-';
        $lastSuffix = Orders::where('orderno', 'like', $baseNumber . '%')
            ->pluck('orderno')
            ->map(function ($orderNumber) use ($baseNumber) {
                $suffix = str_replace($baseNumber, '', (string) $orderNumber);

                return ctype_digit($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        do {
            $lastSuffix++;
            $orderNumber = $baseNumber . str_pad((string) $lastSuffix, 3, '0', STR_PAD_LEFT);
        } while (Orders::where('orderno', $orderNumber)->exists());

        return $orderNumber;
    }

    private function extractActivityDurationHours(?string $duration): int
    {
        if (!filled($duration)) {
            return 0;
        }

        preg_match('/(\d+)/', (string) $duration, $matches);

        return isset($matches[1]) ? max((int) $matches[1], 0) : 0;
    }

    private function resolveFrontendOrderDetailUrl(Orders $order): string
    {
        if (in_array($order->service, ['Hotel', 'Hotel Promo', 'Hotel Package'], true)) {
            return route('view.detail-order-hotel', ['id' => $order->id]);
        }

        if ($order->service === 'Private Villa') {
            return route('view.detail-order-villa', ['id' => $order->id]);
        }

        if ($order->service === 'Tour Package') {
            return route('view.detail-order-tour', ['id' => $order->id]);
        }

        if ($order->service === 'Transport') {
            return route('view.detail-order-transport', ['id' => $order->id]);
        }
        if ($order->service === 'Activity') {
            return route('view.detail-order-activity', ['id' => $order->id]);
        }

        return route('view.detail-order', ['id' => $order->id]);
    }

    private function buildOrderHistoryInvoiceLinks(Orders $order): array
    {
        $invoice = optional($order->reservations)->invoice;
        $invoiceNumber = optional($invoice)->inv_no;

        if (!$invoiceNumber) {
            return [];
        }

        return collect([
            InvoiceLocale::ENGLISH => 'EN',
            InvoiceLocale::SIMPLIFIED_CHINESE => '简体中文',
            InvoiceLocale::TRADITIONAL_CHINESE => '繁體中文',
        ])
            ->map(function ($label, $locale) use ($order, $invoice, $invoiceNumber) {
                if (app(AccommodationFinancialFileService::class)->isProtectedPublicOrder($order)) {
                    $file = app(AccommodationFinancialFileService::class)->resolveInvoiceFile($order, $invoice, $locale);

                    if (!$file) {
                        return null;
                    }

                    return [
                        'label' => $label,
                        'url' => route('orders.accommodation.invoice.preview', [
                            'order' => $order->id,
                            'locale' => $locale,
                        ]),
                    ];
                }

                $relativePath = "storage/document/invoice-{$invoiceNumber}-{$order->id}_{$locale}.pdf";

                if (!File::exists(public_path($relativePath))) {
                    return null;
                }

                return [
                    'label' => $label,
                    'url' => asset($relativePath),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveInvoiceDocumentLocale(): string
    {
        return InvoiceLocale::fromApplicationLocale(config('app.locale'));
    }

    private function resolveOrderInvoiceFileData(Orders $order, ?string $locale = null): ?array
    {
        $invoice = optional($order->reservations)->invoice
            ?: InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);
        $invoiceNumber = optional($invoice)->inv_no;

        if (!$invoiceNumber) {
            return null;
        }

        $preferredLocale = $locale ?: $this->resolveInvoiceDocumentLocale();
        $candidateLocales = array_values(array_unique(array_merge([$preferredLocale], InvoiceLocale::all())));

        if (app(AccommodationFinancialFileService::class)->isProtectedPublicOrder($order)) {
            foreach ($candidateLocales as $candidateLocale) {
                $file = app(AccommodationFinancialFileService::class)->resolveInvoiceFile($order, $invoice, $candidateLocale);

                if (!$file) {
                    continue;
                }

                return [
                    'invoice_number' => $invoiceNumber,
                    'locale' => $candidateLocale,
                    'relative_path' => null,
                    'absolute_path' => $file['absolute_path'],
                    'download_name' => $file['download_name'],
                    'mime' => $file['mime'],
                ];
            }

            return null;
        }

        foreach ($candidateLocales as $candidateLocale) {
            $relativePath = "storage/document/invoice-{$invoiceNumber}-{$order->id}_{$candidateLocale}.pdf";
            $absolutePath = public_path($relativePath);

            if (!File::exists($absolutePath)) {
                continue;
            }

            return [
                'invoice_number' => $invoiceNumber,
                'locale' => $candidateLocale,
                'relative_path' => $relativePath,
                'absolute_path' => $absolutePath,
                'download_name' => "invoice-{$invoiceNumber}.pdf",
            ];
        }

        return null;
    }

    private function buildFrontendTourInvoicePdfPayload(Orders $order, InvoiceAdmin $invoice): array
    {
        $agent = User::where('id', $order->sales_agent)->first();
        $reservation = Reservation::where('id', $order->rsv_id)->first();
        $business = BusinessProfile::where('id', 1)->first();
        $guest_name = Guests::where('rsv_id', $order->rsv_id)->get();
        $pickup_people = Guests::where('id', $order->pickup_name)->first();
        $bankAccount = BankAccount::where('id', $invoice->bank_id ?: 1)->first();

        return [
            'order' => $order,
            'invoice' => $invoice,
            'agent' => $agent,
            'business' => $business,
            'reservation' => $reservation,
            'guest_name' => $guest_name,
            'pickup_people' => $pickup_people,
            'bankAccount' => $bankAccount,
            'tourPricing' => app(OrderPricingSnapshotReader::class)->historicalValues($order, $invoice),
        ];
    }

    private function refreshFrontendTourInvoicePdfDocuments(Orders $order, InvoiceAdmin $invoice): void
    {
        if ($order->service !== 'Tour Package') {
            return;
        }

        $data = $this->buildFrontendTourInvoicePdfPayload($order, $invoice);
        $basePath = public_path("storage/document/invoice-{$invoice->inv_no}-{$order->id}");

        PDF::loadView('emails.invoiceTourEn', $data)->save($basePath . '_en.pdf');
        PDF::loadView('emails.invoiceTourZh', array_merge($data, ['invoiceLocale' => 'zh-CN']))
            ->save($basePath . '_zh-CN.pdf');
        PDF::loadView('emails.invoiceTourZh', array_merge($data, ['invoiceLocale' => 'zh']))
            ->save($basePath . '_zh.pdf');
    }

    private function findFrontendOrderForInvoice(int $id): ?Orders
    {
        return Orders::with('reservations.invoice.payment')
            ->where('sales_agent', Auth::id())
            ->where('id', $id)
            ->first();
    }

    private function getInvoicePaymentDeadline(?InvoiceAdmin $invoice): ?Carbon
    {
        return app(OrderPaymentDeadlineService::class)->deadlineForInvoice($invoice);
    }

    private function orderHasPaymentSubmission(?InvoiceAdmin $invoice): bool
    {
        if (!$invoice) {
            return false;
        }

        $payments = $invoice->relationLoaded('payment')
            ? $invoice->payment
            : $invoice->payment()->get();

        return $payments->contains(function ($payment) {
            return in_array($payment->status, ['Pending', 'Valid', 'Paid'], true);
        });
    }

    private function autoCancelExpiredApprovedOrder(Orders $order, ?InvoiceAdmin $invoice): Orders
    {
        app(OrderPaymentDeadlineService::class)->cancelIfExpired($order, null, [
            'url' => request()->ip() ?? '-',
            'admin' => Auth::id() ?? 0,
        ]);

        return $order->fresh() ?? $order;
    }

    public function preview_order_invoice($id)
    {
        $order = $this->findFrontendOrderForInvoice((int) $id);

        if ($order) {
            $order = $this->autoCancelExpiredApprovedOrder($order, optional($order->reservations)->invoice);
        }

        if (!$order || $order->status !== 'Approved') {
            abort(404);
        }

        $invoice = optional($order->reservations)->invoice
            ?: InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);

        if ($invoice) {
            $this->refreshFrontendTourInvoicePdfDocuments($order, $invoice);
        }

        $invoiceFile = $this->resolveOrderInvoiceFileData($order);

        if (!$invoiceFile) {
            abort(404);
        }

        return response()->file($invoiceFile['absolute_path'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoiceFile['download_name'] . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function download_order_invoice($id)
    {
        $order = $this->findFrontendOrderForInvoice((int) $id);

        if ($order) {
            $order = $this->autoCancelExpiredApprovedOrder($order, optional($order->reservations)->invoice);
        }

        if (!$order || $order->status !== 'Approved') {
            abort(404);
        }

        $invoice = optional($order->reservations)->invoice
            ?: InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);

        if ($invoice) {
            $this->refreshFrontendTourInvoicePdfDocuments($order, $invoice);
        }

        $invoiceFile = $this->resolveOrderInvoiceFileData($order);

        if (!$invoiceFile) {
            abort(404);
        }

        return response()->download($invoiceFile['absolute_path'], $invoiceFile['download_name'], [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function buildStandardOrderHistoryItem(Orders $order): array
    {
        $serviceLabel = __('messages.' . $order->service) !== 'messages.' . $order->service ? __('messages.' . $order->service) : $order->service;
        $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
        $stayStart = $order->travel_date ?: $order->checkin;
        $stayEnd = $order->travel_date ? null : $order->checkout;

        return [
            'key' => 'standard-' . $order->id,
            'type' => 'standard',
            'is_quote' => in_array($order->request_quotation, ['Yes', 1, '1', true], true),
            'orderno' => $order->orderno,
            'service' => $order->service,
            'service_label' => $serviceLabel,
            'status' => $order->status,
            'status_label' => $statusLabel,
            'display_group' => app(AccommodationOrderLifecycleService::class)->displayGroup($order),
            'title' => $order->servicename ?: $serviceLabel,
            'subtitle' => $order->subservice ?: $order->location,
            'location' => $order->location,
            'date_start' => $stayStart,
            'date_end' => $stayEnd,
            'date_label' => $stayEnd ? dateFormat($stayStart) . ' - ' . dateFormat($stayEnd) : dateFormat($stayStart),
            'created_label' => dateTimeFormat($order->created_at),
            'updated_at' => optional($order->updated_at)->timestamp ?? optional($order->created_at)->timestamp ?? 0,
            'price' => $order->final_price,
            'guest_label' => (int) $order->number_of_guests > 0 ? $order->number_of_guests . ' ' . __('messages.Guests') : __('messages.To be advised'),
            'detail_url' => $this->resolveFrontendOrderDetailUrl($order),
            'invoice_links' => ($order->completed_at || in_array($order->status, ['Paid', 'Completed'], true)) ? $this->buildOrderHistoryInvoiceLinks($order) : [],
            'search' => strtolower(implode(' ', array_filter([
                $order->orderno,
                $serviceLabel,
                $statusLabel,
                $order->servicename,
                $order->subservice,
                $order->location,
                strip_tags((string) $order->guest_detail),
            ]))),
        ];
    }

    private function buildWeddingOrderHistoryItem(OrderWedding $order): array
    {
        $serviceLabel = __('messages.Wedding Order');
        $statusLabel = __('messages.' . $order->status) !== 'messages.' . $order->status ? __('messages.' . $order->status) : $order->status;
        $eventDate = $order->wedding_date ?: $order->reception_date_start ?: $order->created_at;
        $coupleName = trim(collect([optional($order->bride)->groom, optional($order->bride)->bride])->filter()->implode(' & '));

        return [
            'key' => 'wedding-' . $order->id,
            'type' => 'wedding',
            'is_quote' => false,
            'orderno' => $order->orderno,
            'service' => 'Wedding',
            'service_label' => $serviceLabel,
            'status' => $order->status,
            'status_label' => $statusLabel,
            'title' => $coupleName ?: $serviceLabel,
            'subtitle' => optional($order->hotel)->name ?: optional($order->wedding)->name,
            'location' => optional($order->hotel)->region,
            'date_start' => $eventDate,
            'date_end' => null,
            'date_label' => dateFormat($eventDate),
            'created_label' => dateTimeFormat($order->created_at),
            'updated_at' => optional($order->updated_at)->timestamp ?? optional($order->created_at)->timestamp ?? 0,
            'price' => $order->final_price,
            'guest_label' => (int) $order->number_of_guests > 0 ? $order->number_of_guests . ' ' . __('messages.Guests') : __('messages.To be advised'),
            'detail_url' => route('view.detail-order-wedding', ['orderno' => $order->orderno]),
            'invoice_links' => [],
            'search' => strtolower(implode(' ', array_filter([
                $order->orderno,
                $serviceLabel,
                $statusLabel,
                $coupleName,
                optional($order->hotel)->name,
            ]))),
        ];
    }

    private function resolveAirportShuttlePriceForTransport($transport, $hotel)
    {
        $prices = collect($transport->prices ?? [])
            ->where('type', 'Airport Shuttle')
            ->sortBy('duration')
            ->values();

        if ($prices->isEmpty()) {
            return null;
        }

        $targetDuration = (int) (optional($hotel)->airport_duration ?? 0);
        $selectedPrice = $prices->first(function ($price) use ($targetDuration) {
            return (int) $price->duration >= $targetDuration;
        });

        if (! $selectedPrice) {
            $selectedPrice = $prices->last();
        }

        return $selectedPrice;
    }

    private function buildHotelBookingTransportOptions($hotel, $usdrates, $tax)
    {
        return Transports::with('prices')
            ->select('id', 'name', 'brand', 'capacity')
            ->where('status', 'Active')
            ->orderByDesc('capacity')
            ->get()
            ->map(function ($transport) use ($hotel, $usdrates, $tax) {
                $selectedPrice = $this->resolveAirportShuttlePriceForTransport($transport, $hotel);

                return [
                    'id' => $transport->id,
                    'label' => $transport->brand . ' ' . $transport->name . ' - (' . $transport->capacity . ')',
                    'price' => $selectedPrice ? (int) round($selectedPrice->calculatePrice($usdrates, $tax)) : 0,
                    'price_id' => $selectedPrice->id ?? 0,
                ];
            })
            ->values();
    }

    private function getBookingExtraBeds($hotelId, $roomId = null)
    {
        $query = ExtraBed::query()
            ->where('hotels_id', $hotelId);

        if ($roomId && Schema::hasColumn('extra_beds', 'rooms_id')) {
            $query->where(function ($builder) use ($roomId) {
                $builder->where('rooms_id', $roomId)
                    ->orWhereNull('rooms_id');
            })->orderByRaw('CASE WHEN rooms_id = ? THEN 0 ELSE 1 END', [$roomId]);
        }

        return $query
            ->orderBy('id')
            ->get()
            ->unique('id')
            ->values();
    }

    private function resolveSelectedExtraBed($extraBeds, $selectedId)
    {
        if ($selectedId !== null && $selectedId !== '') {
            return $extraBeds->firstWhere('id', (int) $selectedId);
        }

        return $extraBeds->first();
    }

    private function getRoomAdultCapacity($room): int
    {
        return (int) ($room->capacity_adult ?? $room->capacity ?? 0);
    }

    private function normalizeBookingDate($value, $fallback = null, $withTime = false)
    {
        if ($value === null || $value === '') {
            if ($fallback === null || $fallback === '') {
                return null;
            }

            return $this->normalizeBookingDate($fallback, null, $withTime);
        }

        if ($value instanceof Carbon) {
            return $withTime
                ? $value->format('Y-m-d H:i:s')
                : $value->format('Y-m-d');
        }

        $value = trim((string) $value);
        $formats = $withTime
            ? [
                'F/d/Y h:i a',
                'F/d/Y h:i A',
                'M/d/Y h:i a',
                'M/d/Y h:i A',
                'm/d/Y h:i a',
                'm/d/Y h:i A',
                'm/d/Y H:i',
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d\TH:i',
            ]
            : [
                'F/d/Y',
                'M/d/Y',
                'm/d/Y',
                'Y-m-d',
            ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                return $withTime
                    ? $date->format('Y-m-d H:i:s')
                    : $date->format('Y-m-d');
            } catch (\Throwable $e) {
                // Try the next accepted format.
            }
        }

        try {
            $date = Carbon::parse($value);

            return $withTime
                ? $date->format('Y-m-d H:i:s')
                : $date->format('Y-m-d');
        } catch (\Throwable $e) {
            if ($fallback !== null && $fallback !== '') {
                return $this->normalizeBookingDate($fallback, null, $withTime);
            }

            return null;
        }
    }

    private function buildAdditionalFlightsSummary(Request $request): ?string
    {
        $types = (array) $request->input('flight_type', []);
        $flightNumbers = (array) $request->input('flight_number', []);
        $times = (array) $request->input('flight_time', []);
        $transportLabels = (array) $request->input('flight_transport_label', []);
        $entryCount = max(count($types), count($flightNumbers), count($times), count($transportLabels));
        $entries = [];

        for ($index = 0; $index < $entryCount; $index++) {
            $type = trim((string) ($types[$index] ?? ''));
            $flightNumber = trim((string) ($flightNumbers[$index] ?? ''));
            $time = trim((string) ($times[$index] ?? ''));
            $transportLabel = trim((string) ($transportLabels[$index] ?? ''));

            if ($type === '' && $flightNumber === '' && $time === '' && $transportLabel === '') {
                continue;
            }

            $parts = [];

            if ($type !== '') {
                $parts[] = $type === 'departure' ? __('messages.Departure') : __('messages.Arrival');
            }

            if ($flightNumber !== '') {
                $parts[] = $flightNumber;
            }

            if ($time !== '') {
                $normalizedTime = $this->normalizeBookingDate($time, null, true);
                $parts[] = $normalizedTime ? dateTimeFormat($normalizedTime) : $time;
            }

            if ($transportLabel !== '') {
                $parts[] = $transportLabel;
            }

            if (!empty($parts)) {
                $entries[] = ($index + 1) . '. ' . implode(' | ', $parts);
            }
        }

        if (empty($entries)) {
            return null;
        }

        return __('messages.Flight and transport detail') . ":\n" . implode("\n", $entries);
    }

    private function buildAirportShuttleRowsFromRequest(Request $request, $hotel, int $numberOfGuests, $checkin, $checkout)
    {
        $types = (array) $request->input('flight_type', []);
        $flightNumbers = (array) $request->input('flight_number', []);
        $times = (array) $request->input('flight_time', []);
        $transportIds = (array) $request->input('flight_transport_id', []);
        $entryCount = max(count($types), count($flightNumbers), count($times), count($transportIds));
        $transportMap = Transports::with('prices')
            ->whereIn('id', collect($transportIds)->filter()->map(fn ($id) => (int) $id)->unique()->values())
            ->get()
            ->keyBy('id');
        $rows = collect();

        for ($index = 0; $index < $entryCount; $index++) {
            $type = trim((string) ($types[$index] ?? ''));
            $flightNumber = trim((string) ($flightNumbers[$index] ?? ''));
            $time = trim((string) ($times[$index] ?? ''));
            $transportId = (int) ($transportIds[$index] ?? 0);

            if ($type === '' && $flightNumber === '' && $time === '' && $transportId === 0) {
                continue;
            }

            if (!in_array($type, ['arrival', 'departure'], true) || $transportId <= 0) {
                continue;
            }

            $transport = $transportMap->get($transportId);
            if (! $transport) {
                continue;
            }

            $selectedPrice = $this->resolveAirportShuttlePriceForTransport($transport, $hotel);
            $fallbackDate = $type === 'departure' ? $checkout : $checkin;
            $date = $this->normalizeBookingDate($time, $fallbackDate . ' 11:00:00', true);

            $rows->push([
                'date' => $date,
                'flight_number' => $flightNumber !== '' ? $flightNumber : 'Insert flight number',
                'number_of_guests' => $numberOfGuests,
                'transport_id' => $transport->id,
                'price_id' => $selectedPrice->id ?? null,
                'src' => $type === 'departure' ? $hotel->name : 'Airport',
                'dst' => $type === 'departure' ? 'Airport' : $hotel->name,
                'duration' => $hotel->airport_duration,
                'distance' => $hotel->airport_distance,
                'price' => $selectedPrice ? $selectedPrice->calculatePrice(
                    Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first()),
                    Cache::remember('tax_1', 3600, fn() => Tax::find(1))
                ) : 0,
                'nav' => $type === 'departure' ? 'Out' : 'In',
            ]);
        }

        $rows = $rows->values();

        if ($rows->isNotEmpty()) {
            return $rows;
        }

        if ($request->filled('airport_shuttle_in')) {
            $transportIn = Transports::with('prices')->find((int) $request->airport_shuttle_in);
            if ($transportIn) {
                $selectedPrice = $this->resolveAirportShuttlePriceForTransport($transportIn, $hotel);
                $rows->push([
                    'date' => $this->normalizeBookingDate($request->arrival_time, $checkin . ' 11:00:00', true),
                    'flight_number' => trim((string) $request->arrival_flight) !== '' ? trim((string) $request->arrival_flight) : 'Insert flight number',
                    'number_of_guests' => $numberOfGuests,
                    'transport_id' => $transportIn->id,
                    'price_id' => $selectedPrice->id ?? null,
                    'src' => 'Airport',
                    'dst' => $hotel->name,
                    'duration' => $hotel->airport_duration,
                    'distance' => $hotel->airport_distance,
                    'price' => $selectedPrice ? $selectedPrice->calculatePrice(
                        Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first()),
                        Cache::remember('tax_1', 3600, fn() => Tax::find(1))
                    ) : 0,
                    'nav' => 'In',
                ]);
            }
        }

        if ($request->filled('airport_shuttle_out')) {
            $transportOut = Transports::with('prices')->find((int) $request->airport_shuttle_out);
            if ($transportOut) {
                $selectedPrice = $this->resolveAirportShuttlePriceForTransport($transportOut, $hotel);
                $rows->push([
                    'date' => $this->normalizeBookingDate($request->departure_time, $checkout . ' 11:00:00', true),
                    'flight_number' => trim((string) $request->departure_flight) !== '' ? trim((string) $request->departure_flight) : 'Insert flight number',
                    'number_of_guests' => $numberOfGuests,
                    'transport_id' => $transportOut->id,
                    'price_id' => $selectedPrice->id ?? null,
                    'src' => $hotel->name,
                    'dst' => 'Airport',
                    'duration' => $hotel->airport_duration,
                    'distance' => $hotel->airport_distance,
                    'price' => $selectedPrice ? $selectedPrice->calculatePrice(
                        Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first()),
                        Cache::remember('tax_1', 3600, fn() => Tax::find(1))
                    ) : 0,
                    'nav' => 'Out',
                ]);
            }
        }

        return $rows->values();
    }

    private function getPrimaryAirportShuttleFields($rows): array
    {
        $collection = collect($rows);
        $arrival = $collection->firstWhere('nav', 'In');
        $departure = $collection->firstWhere('nav', 'Out');

        return [
            'arrival_flight' => $arrival['flight_number'] ?? null,
            'arrival_time' => $arrival['date'] ?? null,
            'airport_shuttle_in' => $arrival['transport_id'] ?? null,
            'departure_flight' => $departure['flight_number'] ?? null,
            'departure_time' => $departure['date'] ?? null,
            'airport_shuttle_out' => $departure['transport_id'] ?? null,
        ];
    }

    private function mergeOrderNoteWithAdditionalFlights(?string $note, Request $request): ?string
    {
        $baseNote = trim((string) $note);
        $additionalFlightsSummary = $this->buildAdditionalFlightsSummary($request);

        if (!$additionalFlightsSummary) {
            return $baseNote !== '' ? $baseNote : null;
        }

        return trim($baseNote !== '' ? $baseNote . "\n\n" . $additionalFlightsSummary : $additionalFlightsSummary);
    }

    private function localizedJoinedField($items, string $field, string $separator = '<br>'): string
    {
        return collect($items)
            ->map(fn ($item) => localized_model_field($item, $field))
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->implode($separator);
    }

    private function buildHotelBookingRoomFormData($hotel, $room, $roomCapacity, $duration, $usdrates, $tax, $extraBedMode = 'nightly', $extraBedTriggerCapacity = null)
    {
        $extraBeds = $this->getBookingExtraBeds($hotel->id, $room->id);
        $triggerCapacity = $extraBedTriggerCapacity ?? ($room->capacity ?? $roomCapacity);
        $adultCapacity = (int) ($room->capacity_adult ?? $triggerCapacity);
        $childCapacity = (int) ($room->capacity_child ?? max($roomCapacity - $adultCapacity, 0));
        $adultLabel = $adultCapacity === 1 ? __('messages.Adult') : __('messages.Adults');
        $childLabel = $childCapacity === 1 ? __('messages.Child') : __('messages.Children');
        $guestPlaceholder = __('messages.Capacity') . ' ' . $adultCapacity . ' ' . strtolower($adultLabel);

        if ($childCapacity > 0) {
            $guestPlaceholder .= ' + ' . $childCapacity . ' ' . strtolower($childLabel);
        }

        return [
            'room_capacity' => $roomCapacity,
            'adult_capacity' => $adultCapacity,
            'child_capacity' => $childCapacity,
            'extra_bed_trigger_capacity' => $triggerCapacity,
            'guest_placeholder' => $guestPlaceholder,
            'extra_bed_options' => $extraBeds->map(function ($extraBed) use ($usdrates, $tax, $duration, $extraBedMode) {
                $price = $extraBed->calculatePrice($usdrates, $tax);
                $extraBedName = __('messages.' . $extraBed->name) !== 'messages.' . $extraBed->name
                    ? __('messages.' . $extraBed->name)
                    : $extraBed->name;
                $extraBedType = __('messages.' . $extraBed->type) !== 'messages.' . $extraBed->type
                    ? __('messages.' . $extraBed->type)
                    : $extraBed->type;

                if ($extraBedMode === 'stay') {
                    $price *= $duration;
                }

                return [
                    'id' => $extraBed->id,
                    'label' => trim($extraBedName . ' (' . $extraBedType . ')'),
                    'price' => (int) round($price),
                ];
            })->values(),
        ];
    }

    // VIEW ORDERS ================================================================================================> OK
    public function index()
    {   
        $ord = Orders::all();
        $orderno = count($ord);
        $business = BusinessProfile::where('id','=',1)->first();
        $now = Carbon::now();
        $archived = date('Y-m-d',strtotime('+7 days',strtotime($now)));
        $userid = Auth::user()->id;
        $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        $wedding_order = OrderWedding::all();
        $brides = Brides::all();
        $tourLifecycle = app(TourOrderLifecycleService::class);
        $activityLifecycle = app(ActivityOrderLifecycleService::class);
        $tourorders = $tourLifecycle->applyTourCurrentScope(
            Orders::query()
                ->where('sales_agent', $userid)
                ->whereNotIn('status', ['Removed', 'Archive']),
            $now
        )
            ->orderBy('updated_at', 'DESC')
            ->get();
        $lifecycle = app(AccommodationOrderLifecycleService::class);
        $transportLifecycle = app(TransportOrderLifecycleService::class);
        $hotelorders = $lifecycle->applyAccommodationCurrentScope(
            Orders::query()
                ->whereNotIn('status', ['Removed', 'Archive'])
                ->where('sales_agent', $userid),
            $now
        )
            ->orderBy('updated_at', 'DESC')
            ->get();
        $accommodationHistoryOrders = $lifecycle->applyAccommodationHistoryScope(
            Orders::with(['reservations'])
                ->where('sales_agent', $userid)
                ->whereNotIn('status', ['Removed', 'Archive']),
            $now
        )
            ->orderBy('updated_at', 'DESC')
            ->get();
        $activityorders = $activityLifecycle->applyActivityCurrentScope(
            Orders::query()
                ->where('sales_agent', $userid)
                ->whereNotIn('status', ['Removed', 'Archive']),
            $now
        )
            ->orderBy('updated_at', 'DESC')
            ->get();
        $transportorders = $transportLifecycle->applyTransportCurrentScope(
            Orders::query()
                ->where('sales_agent', $userid)
                ->whereNotIn('status', ['Removed', 'Archive']),
            $now
        )
            ->orderBy('updated_at', 'DESC')
            ->get();
        $villaorders = Orders::where('service', 'Private Villa')
            ->where('sales_agent', $userid)
            ->where('checkin', '>=', $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at', 'DESC')
            ->get();
        $orderhistories = Orders::with(['reservations'])
            ->where('sales_agent', $userid)
            ->where(function ($query) use ($now, $transportLifecycle, $tourLifecycle, $activityLifecycle) {
                $query->where(function ($legacyHistory) use ($now) {
                    $legacyHistory->whereNotIn('service', array_merge(Orders::ACCOMMODATION_SERVICES, [Orders::PUBLIC_TRANSPORT_SERVICE, Orders::PUBLIC_TOUR_SERVICE, Orders::PUBLIC_ACTIVITY_SERVICE]))
                        ->where('checkin', '<', $now);
                })->orWhere(function ($transportHistory) use ($now, $transportLifecycle) {
                    $transportLifecycle->applyTransportHistoryScope($transportHistory, $now);
                })->orWhere(function ($tourHistory) use ($now, $tourLifecycle) {
                    $tourLifecycle->applyTourHistoryScope($tourHistory, $now);
                })->orWhere(function ($activityHistory) use ($now, $activityLifecycle) {
                    $activityLifecycle->applyActivityHistoryScope($activityHistory, $now);
                });
            })
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->concat($accommodationHistoryOrders)
            ->sortByDesc('updated_at')
            ->values();

        $weddingorders = OrderWedding::where('agent_id', $userid)
            ->orderBy('updated_at','DESC')
            ->get();
        $vendorPackages = VendorPackage::all();
        $hotelRoo = HotelRoom::all();


        $standardCurrentOrders = Orders::where('sales_agent','=', $userid)
            ->whereNotIn('service', array_merge(Orders::ACCOMMODATION_SERVICES, [Orders::PUBLIC_TRANSPORT_SERVICE, Orders::PUBLIC_TOUR_SERVICE, Orders::PUBLIC_ACTIVITY_SERVICE]))
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->where('checkin', '>=' , $now)
            ->orderBy('updated_at','DESC')
            ->get();
        $orders = $standardCurrentOrders
            ->concat($hotelorders)
            ->concat($transportorders)
            ->concat($tourorders)
            ->concat($activityorders)
            ->sortByDesc('updated_at')
            ->values();
        
        if (isset($orders->checkin) == "")
            $checkin = $now;
        else
            $checkin = $orders->checkin;

        $activeorders = Orders::where('status','!=', 'Accepted')
            ->where('status','!=', 'Draft')
            ->where('sales_agent','=', $userid)
            ->where('checkin','>', $archived)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $archivedorders = Orders::where('sales_agent','=', $userid)
            ->where('checkin','<', $now)
            ->orderBy('created_at', 'desc')
            ->get();
        $rejectedorders = Orders::where('sales_agent','=', $userid)
            ->where('status', 'Rejected')
            ->orderBy('created_at', 'desc')
            ->get();
        $reservations = Reservation::all();
        
        $statusMap = [
            'Rejected'  => ['class' => 'status-rejected', 'label' => ''],
            'Paid'      => ['class' => 'status-paid', 'label' => __('messages.Paid')],
            'Approved'  => ['class' => 'status-approved', 'label' => __('messages.Approved')],
            'Confirmed' => ['class' => 'status-confirmed', 'label' => __('messages.Confirmed')],
            'Canceled'  => ['class' => 'status-canceled', 'label' => __('messages.Canceled')],
            'Rejected'  => ['class' => 'status-rejected', 'label' => __('messages.Rejected')],
            'Invalid'   => ['class' => 'status-invalid', 'label' => __('messages.Invalid')],
            'Active'    => ['class' => 'status-progress', 'label' => __('messages.Active')],
            'Pending'   => ['class' => 'status-waiting', 'label' => __('messages.Pending')],
            'Draft'     => ['class' => 'status-draft', 'label' => __('messages.Draft')],
        ];
        return view('frontend.home.orders.index',compact('orders'),[
            'orderno'=>$orderno,
            'optionalrates'=>$optionalrates,
            'optional_rate_order'=>$optional_rate_order,
            'archivedorders'=>$archivedorders,
            'rejectedorders'=>$rejectedorders,
            'business'=>$business,
            'now'=>$now,
            "checkin"=> $checkin,
            'orders'=> $orders,
            "activeorders"=>$activeorders,
            'weddingorders'=>$weddingorders,
            'vendorPackages'=>$vendorPackages,
            'transportorders'=>$transportorders,
            'villaorders'=>$villaorders,
            'orderhistories'=>$orderhistories,
            'activityorders'=>$activityorders,
            'hotelorders'=>$hotelorders,
            'tourorders'=>$tourorders,
            'reservations'=>$reservations,
            'wedding_order'=>$wedding_order,
            'brides'=>$brides,
            'userid'=>$userid,
            'statusMap'=>$statusMap,
        ]);
        
        
    }

    public function order_history(Request $request)
    {
        $userid = Auth::id();
        $now = Carbon::now();
        $query = trim((string) $request->query('q', ''));
        $service = $request->query('service', 'all');
        $status = $request->query('status', 'all');
        $year = $request->query('year', 'all');
        $sort = $request->query('sort', 'recent');
        $perPage = 12;

        $lifecycle = app(AccommodationOrderLifecycleService::class);
        $tourLifecycle = app(TourOrderLifecycleService::class);
        $activityLifecycle = app(ActivityOrderLifecycleService::class);
        $standardOrdersQuery = Orders::with(['reservations.invoice'])
            ->where('sales_agent', $userid)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->where(function ($builder) use ($now, $lifecycle, $tourLifecycle, $activityLifecycle) {
                $builder->where(function ($legacyHistory) use ($now) {
                    $legacyHistory->whereNotIn('service', array_merge(Orders::ACCOMMODATION_SERVICES, [Orders::PUBLIC_TRANSPORT_SERVICE, Orders::PUBLIC_TOUR_SERVICE, Orders::PUBLIC_ACTIVITY_SERVICE]))
                        ->where('checkin', '<', $now);
                })->orWhere(function ($accommodationHistory) use ($now, $lifecycle) {
                    $lifecycle->applyAccommodationHistoryScope($accommodationHistory, $now);
                })->orWhere(function ($transportHistory) use ($now) {
                    app(TransportOrderLifecycleService::class)->applyTransportHistoryScope($transportHistory, $now);
                })->orWhere(function ($tourHistory) use ($now, $tourLifecycle) {
                    $tourLifecycle->applyTourHistoryScope($tourHistory, $now);
                })->orWhere(function ($activityHistory) use ($now, $activityLifecycle) {
                    $activityLifecycle->applyActivityHistoryScope($activityHistory, $now);
                });
            });

        if ($service === 'Wedding') {
            $standardOrdersQuery->whereRaw('1 = 0');
        } elseif ($service !== 'all') {
            $standardOrdersQuery->where('service', $service);
        }

        if ($status !== 'all') {
            if ($status === 'Completed') {
                $standardOrdersQuery->where(function ($builder) {
                    $builder->where('status', 'Completed')
                        ->orWhere(function ($completedPublicService) {
                            $completedPublicService->whereIn('service', array_merge(Orders::ACCOMMODATION_SERVICES, [Orders::PUBLIC_TRANSPORT_SERVICE, Orders::PUBLIC_TOUR_SERVICE, Orders::PUBLIC_ACTIVITY_SERVICE]))
                                ->where('status', 'Paid')
                                ->when(Schema::hasColumn('orders', 'completed_at'), function ($query) {
                                    $query->whereNotNull('completed_at');
                                }, function ($query) {
                                    $query->whereRaw('1 = 0');
                                });
                        });
                });
            } else {
                $standardOrdersQuery->where('status', $status);
            }
        }

        if ($year !== 'all' && ctype_digit((string) $year)) {
            $standardOrdersQuery->whereYear('checkin', (int) $year);
        }

        if ($query !== '') {
            $standardOrdersQuery->where(function ($builder) use ($query) {
                $builder->where('orderno', 'like', "%{$query}%")
                    ->orWhere('servicename', 'like', "%{$query}%")
                    ->orWhere('subservice', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%")
                    ->orWhere('guest_detail', 'like', "%{$query}%");
            });
        }

        $weddingOrdersQuery = OrderWedding::with(['bride', 'hotel', 'wedding'])
            ->where('agent_id', $userid)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->where(function ($builder) use ($now) {
                $builder->where('wedding_date', '<', $now)
                    ->orWhere(function ($inner) use ($now) {
                        $inner->whereNull('wedding_date')
                            ->where('reception_date_start', '<', $now);
                    });
            });

        if ($service !== 'all' && $service !== 'Wedding') {
            $weddingOrdersQuery->whereRaw('1 = 0');
        }

        if ($status !== 'all') {
            $weddingOrdersQuery->where('status', $status);
        }

        if ($year !== 'all' && ctype_digit((string) $year)) {
            $weddingOrdersQuery->where(function ($builder) use ($year) {
                $builder->whereYear('wedding_date', (int) $year)
                    ->orWhere(function ($inner) use ($year) {
                        $inner->whereNull('wedding_date')
                            ->whereYear('reception_date_start', (int) $year);
                    });
            });
        }

        if ($query !== '') {
            $weddingOrdersQuery->where(function ($builder) use ($query) {
                $builder->where('orderno', 'like', "%{$query}%")
                    ->orWhereHas('bride', function ($brideQuery) use ($query) {
                        $brideQuery->where('groom', 'like', "%{$query}%")
                            ->orWhere('bride', 'like', "%{$query}%");
                    })
                    ->orWhereHas('hotel', function ($hotelQuery) use ($query) {
                        $hotelQuery->where('name', 'like', "%{$query}%");
                    });
            });
        }

        $standardItems = $standardOrdersQuery
            ->orderByDesc('updated_at')
            ->limit(600)
            ->get()
            ->map(fn ($order) => $this->buildStandardOrderHistoryItem($order));

        $weddingItems = $weddingOrdersQuery
            ->orderByDesc('updated_at')
            ->limit(300)
            ->get()
            ->map(fn ($order) => $this->buildWeddingOrderHistoryItem($order));

        $historyItems = $standardItems->concat($weddingItems);

        $historyItems = match ($sort) {
            'oldest' => $historyItems->sortBy('updated_at'),
            'highest' => $historyItems->sortByDesc('price'),
            'lowest' => $historyItems->sortBy('price'),
            default => $historyItems->sortByDesc('updated_at'),
        };

        $historyItems = $historyItems->values();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginatedItems = new LengthAwarePaginator(
            $historyItems->forPage($currentPage, $perPage)->values(),
            $historyItems->count(),
            $perPage,
            $currentPage,
            [
                'path' => route('orders.history'),
                'query' => $request->query(),
            ]
        );

        $availableYears = Orders::where('sales_agent', $userid)
            ->whereNotNull('checkin')
            ->where('checkin', '<', $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->selectRaw('YEAR(checkin) as year')
            ->pluck('year')
            ->merge(
                OrderWedding::where('agent_id', $userid)
                    ->whereNotIn('status', ['Removed', 'Archive'])
                    ->selectRaw('YEAR(COALESCE(wedding_date, reception_date_start)) as year')
                    ->pluck('year')
            )
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $statusOptions = collect(['Completed', 'Paid', 'Approved', 'Confirmed', 'Active', 'Canceled', 'Rejected', 'Invalid', 'Pending']);
        $serviceOptions = collect(['Hotel', 'Hotel Promo', 'Hotel Package', 'Tour Package', 'Activity', 'Transport', 'Private Villa', 'Wedding']);
        $summary = [
            'total' => $historyItems->count(),
            'paid' => $historyItems->where('status', 'Paid')->count(),
            'this_year' => $historyItems->filter(function ($item) use ($now) {
                return $item['date_start'] && Carbon::parse($item['date_start'])->year === $now->year;
            })->count(),
            'with_invoice' => $historyItems->filter(fn ($item) => count($item['invoice_links']) > 0)->count(),
        ];

        return view('frontend.home.orders.history', [
            'historyItems' => $paginatedItems,
            'summary' => $summary,
            'filters' => compact('query', 'service', 'status', 'year', 'sort'),
            'availableYears' => $availableYears,
            'statusOptions' => $statusOptions,
            'serviceOptions' => $serviceOptions,
        ]);
    }
    
    // VIEW USER ORDER HOTEL PROMO =================================================================================> OK
    public function order_hotel_promo(Request $request, $id)
    {
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = UsdRates::where('name', 'USD')->first();
        $room = HotelRoom::with('hotels')->findOrFail($id);
        $hotel = $room->hotels;
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/hotel-$hotel->code");
        }
        $checkin = session('booking_dates.checkin');
        $checkout = session('booking_dates.checkout');
        $service = "Hotel Promo";
        $orderNumber = $this->generateUniqueHotelOrderNumber('HPP', $now);
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $room_capacity = $room->capacity_adult + $room->capacity_child;
        $room->localized_include = localized_model_field($room, 'include');
        $room->localized_amenities = localized_model_field($room, 'amenities');
        $prIds = $request->promo_id;
        $uniqueHotelPromoIds = array_unique(json_decode($request->promo_id));
        $promos = HotelPromo::whereIn('id', $uniqueHotelPromoIds)->get();
        $promo_name = $this->localizedJoinedField($promos, 'name', ', ');
        $promo_benefits = $this->localizedJoinedField($promos, 'benefits');
        $promo_include = $this->localizedJoinedField($promos, 'include');
        $promo_additional_info = $this->localizedJoinedField($promos, 'additional_info');
        $transportOptions = $this->buildHotelBookingTransportOptions($hotel, $usdrates, $tax);
        $roomForm = $this->buildHotelBookingRoomFormData($hotel, $room, $room_capacity, $duration, $usdrates, $tax, 'nightly', $room->capacity_adult);
        $promo_price = $request->promo_price;
        $price_list = $request->price_list;
        $final_price = 0;
        $agents = $this->getBookingAgents();

        $optional_rates = OptionalRate::mustBuy($checkin, $checkout)->get();
        $totalPriceOptionalRates = $optional_rates->sum(function ($rate) use ($usdrates, $tax) {
            return $rate->calculatePrice($usdrates, $tax);
        });

        return view('frontend.home.booking.orders.hotel-promo', compact(
            'now', 'usdrates', 'tax',
            'service', 'orderNumber', 'checkin', 'checkout', 'duration',
            'hotel', 'promos', 'prIds', 'promo_name', 'room', 'room_capacity',
            'promo_benefits', 'promo_include', 'promo_additional_info',
            'transportOptions', 'final_price', 'promo_price',
            'uniqueHotelPromoIds', 'price_list', 'agents', 'optional_rates', 'totalPriceOptionalRates', 'roomForm'
        ));
    }

    // VIEW USER ORDER VILLA =================================================================================> OK
    public function order_villa(Request $request, $code)
    {
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/villas/{$request->villa_id};");
        }
        $checkin = session('booking_dates.checkin');
        $checkout = session('booking_dates.checkout');
        $user_id = Auth::id();
        $user = Auth::user();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $duration = session('booking_dates.duration');
        $orders = Orders::where('sales_agent', $user_id)
            ->whereDate('created_at', $now)
            ->get();
        $order_value = $orders->count() + 1;
        function numberToLetters($number) {
            $letters = '';
            while ($number > 0) {
                $remainder = ($number - 1) % 26;
                $letters = chr(65 + $remainder) . $letters;
                $number = intdiv($number - 1, 26);
            }
            return $letters;
        }
        $order_suffix = numberToLetters($order_value);
        $orderNumber = $user->code . date('ymd', strtotime($now)) . $order_suffix;
        $villa = Villas::with([
            'galleries',
            'rooms' => fn($q) => $q->where('status', 1),
            'prices' => fn($q) => $q->where('start_date', '<=', session('booking_dates.checkin'))
                                    ->where('end_date', '>=', session('booking_dates.checkin'))
                                    ->where('status', "Active")
                                    ->first()
        ])->where('code', $code)->firstOrFail();

        $rooms = $villa->rooms;
        $number_of_adult = $rooms->sum('guest_adult');
        $number_of_children = $rooms->sum('guest_child');
        $occupancy = $number_of_adult + $number_of_children;
        $transport_duration = $villa->airport_duration ?? 2;
        $transports = Transports::with(['prices' => function ($q) use ($transport_duration) {
                $q->where('duration',$transport_duration);
            }])
            ->get();

        foreach ($transports as $transport) {
        // Ambil hanya 1 price yang cocok
            $price = $transport->prices->first();

            if ($price) {
                $transport->calculated_price = $price->calculatePrice($usdrates, $tax);
                $transport->calculated_price_id = $price->id;
            } else {
                $transport->calculated_price = null;
                $transport->calculated_price_id = null;
            }
        }
        $agents = User::where('status', "Active")
            ->whereNotNull('email_verified_at')
            ->get();
        $price = $villa->prices()
            ->where('start_date', '<=', session('booking_dates.checkin'))
            ->where('end_date', '>=', session('booking_dates.checkin'))
            ->where('status', 'Active')
            ->first();
        $calculatedPrice = 0;
        $found_price = false;
        for ($date = Carbon::parse($checkin); $date->lt(Carbon::parse($checkout)); $date->addDay()) {
            $price_loop = VillaPrice::where('villa_id', $villa->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();

            if ($price_loop) {
                $night_price = $price_loop->calculatePrice($usdrates, $tax);
                $calculatedPrice += $night_price;
                $found_price = true;
            } else {
                $calculatedPrice = 0;
                break;
            }
        }
        $total_price = $calculatedPrice;
        $data = [
            'user' => $user,
            'price' => $price,
            'total_price' => $total_price,
            'transport_duration' => $transport_duration,
            'occupancy' => $occupancy,
            'number_of_adult' => $number_of_adult,
            'number_of_children' => $number_of_children,
            'now' => $now,
            'checkin' => session('booking_dates.checkin'),
            'checkout' => session('booking_dates.checkout'),
            'duration' => session('booking_dates.duration'),
            'villa' => $villa,
            'rooms' => $rooms,
            'transports' => $transports,
            'usdrates' => $usdrates,
            'tax' => $tax,
            'agents' => $agents,
            'orderNumber' => $orderNumber,
        ];
        return view('villas.order-villa',$data);
    }

    // CREATE ORDER VILLA =================================================================================> OK
    public function func_create_order_villa(Request $request){
        $user = Auth::user();
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $usd_rate = $usdrates->rate;
        $cny_rate = $cnyrates->rate;
        $twd_rate = $twdrates->rate;
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $duration = session('booking_dates.duration') ?? Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $villa = Villas::findOrFail($request->villa_id);
        $villa_price = VillaPrice::findOrFail($request->villa_price_id);
        $price = $villa_price->calculatePrice($usdrates, $tax);
        $calculatedPrice = 0;
        $found_price = false;
        for ($date = Carbon::parse($checkin); $date->lt(Carbon::parse($checkout)); $date->addDay()) {
            $price_loop = VillaPrice::where('villa_id', $villa->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();
            if ($price_loop) {
                $night_price = $price_loop->calculatePrice($usdrates, $tax);
                $calculatedPrice += $night_price;
                $found_price = true;
            } else {
                $calculatedPrice = 0;
                break;
            }
        }
        $normal_price = $calculatedPrice;
        $rooms = $villa->rooms;
        $number_of_room = $rooms->count() ?? 1;
        $number_of_adult = $rooms->sum('guest_adult') ?? 0;
        $number_of_children = $rooms->sum('guest_child') ?? 0;
        $occupancy = $number_of_adult + $number_of_children ?? 0;
        $transport_in_id = $request->airport_shuttle_in;
        $transport_out_id = $request->airport_shuttle_out;
        $airport_shuttle_in_price = TransportPrice::find($request->transport_in_price_id) ?? null;
        $airport_shuttle_out_price = TransportPrice::find($request->transport_out_price_id) ?? null;
        $guests = $request->input('guests', []);
        $number_of_guests = count($guests);
        $airport_shuttle_price = $request->airport_shuttle_price;
        $price_total = $normal_price + $airport_shuttle_price;

        $order = Orders::create([
            'user_id' => $user->id,
            'orderno' => $request->orderno,
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => $villa->name,
            'service' => 'Private Villa',
            'service_id' => $villa->id,
            'price_id' => $request->villa_price_id,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'location' => $villa->region,
            'number_of_guests' => $number_of_guests,
            'airport_shuttle_price' => $airport_shuttle_price,
            'capacity' => $occupancy,
            'benefits' => $villa_price->benefits,
            'additional_info' => $villa_price->additional_info,
            'cancellation_policy' => $villa_price->cancellation_policy,
            'note' => $request->note,
            'number_of_room' => $number_of_room,
            'duration' => $duration,
            'price_pax' => $calculatedPrice,
            'normal_price' => $normal_price,
            'price_total' => $price_total,
            'final_price' => $normal_price + $request->airport_shuttle_price,
            'usd_rate' => $usd_rate,
            'twd_rate' => $twd_rate,
            'cny_rate' => $cny_rate,
            'period_start' => $villa_price->start_date,
            'period_end' => $villa_price->end_date,
            'status' => $status,
            'sales_agent' => $sales_agent,
            'arrival_time' => $request->arrival_time,
            'arrival_flight' => $request->arrival_flight,
            'departure_flight' => $request->departure_flight,
            'departure_time' => $request->departure_time,
            'airport_shuttle_in' => $request->airport_shuttle_in,
            'airport_shuttle_out' => $request->airport_shuttle_out,
            'note' => $request->note
        ]);
        
        foreach ($guests as $guestData) {
            if (!empty($guestData['name'])) {
                Guests::create([
                    'name' => $guestData['name'],
                    'sex'  => $guestData['sex'] ?? null,
                    'age'  => $guestData['age'] ?? null,
                    'order_id'  => $order->id,
                ]);
            }
        }
        if ($request->airport_shuttle_in){
            $transport_in = Transports::find($request->airport_shuttle_in) ?? null;
            $arrival_time = date('Y-m-d H:i:s',strtotime($request->arrival_time));
            $arrival_flight = $request->arrival_flight??"Insert flight number!";
            AirportShuttle::create([
                "date"=>$arrival_time,
                "flight_number"=>$arrival_flight,
                "number_of_guests"=>$transport_in->capacity,
                "order_id"=>$order->id,
                "transport_id"=>$transport_in->id,
                "price_id"=>$request->transport_in_price_id,
                "src"=>"Airport",
                "dst"=>$villa->name,
                "duration"=>$villa->airport_duration,
                "distance"=>$villa->airport_distance,
                "price"=>$airport_shuttle_in_price->calculatePrice($usdrates, $tax),
                "nav"=>"In",
            ]);
        }
        if ($request->airport_shuttle_out){
            $transport_out = Transports::find($request->airport_shuttle_out) ?? null;
            $departure_time = date('Y-m-d H:i:s',strtotime($request->departure_time));
            $departure_flight = $request->departure_flight??"Insert flight number!";
            AirportShuttle::create([
                "date"=>$departure_time,
                "flight_number"=>$departure_flight,
                "number_of_guests"=>$transport_out->capacity,
                "order_id"=>$order->id,
                "transport_id"=>$transport_out->id,
                "price_id"=>$request->transport_out_price_id,
                "dst"=>"Airport",
                "src"=>$villa->name,
                "duration"=>$villa->airport_duration,
                "distance"=>$villa->airport_distance,
                "price"=>$airport_shuttle_out_price->calculatePrice($usdrates, $tax),
                "nav"=>"Out",
            ]);
        }

        $user_log_note = "Order created by {$user->name} ({$user->email}) for villa {$villa->name} ({$villa->code}) from {$checkin} to {$checkout}.";
        $user_log = UserLog::create([
            "action"=>"Create Order",
            "service"=>$order->service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"villa-price-".$villa->code,
            "user_id"=>$user->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$user_log_note, 
        ]);
        $order_log = OrderLog::create([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        session()->forget('booking_dates');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-villa', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }

    // VIEW EDIT ORDER VILLA =============================================================================================> OK
    public function edit_order_villa($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::with('guests')->where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        $agent = User::find($order->sales_agent);
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $villa = Villas::with(['galleries', 'rooms' => fn($q) => $q->where('status', 1)])
            ->findOrFail($order->service_id);
        $guests = Guests::where('order_id', $order->id)->get();
        $id_last_guest = Guests::max('id');
        $rooms = $villa->rooms;


        $airport_shuttles = AirportShuttle::where('order_id',$order->id)->get();
        $airport_shuttle_in = AirportShuttle::with('transport')->where('order_id',$order->id)->where('nav', 'In')->first();
        $airport_shuttle_out = AirportShuttle::with('transport')->where('order_id',$order->id)->where('nav', 'Out')->first();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);

        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $transport_in = Transports::find($airport_shuttle_in?->transport_id) ?? null;
        $transport_out = Transports::find($airport_shuttle_out?->transport_id) ?? null;

        $duration = $order->duration ?? Carbon::parse($order->checkin)->diffInDays(Carbon::parse($order->checkout));
        $hasInvalidOrder = !$order->number_of_room || !$order->number_of_guests_room || !$order->guest_detail;

        $number_of_adult = $rooms->sum('guest_adult');
        $number_of_children = $rooms->sum('guest_child');
        $occupancy = $order->capacity;
        $transport_duration = $villa->airport_duration ?? 2;
        $transports = Transports::with(['prices' => function ($q) use ($transport_duration) {
                $q->where('duration','>=',$transport_duration);
            }])
            ->get();

        foreach ($transports as $transport) {
            $price = $transport->prices->first();
            if ($price) {
                $transport->calculated_price = $price->calculatePrice($usdrates, $tax);
                $transport->calculated_price_id = $price->id;
            } else {
                $transport->calculated_price = null;
                $transport->calculated_price_id = null;
            }
        }
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);
        $statusMap = [
            'Invalid'   => ['class' => 'order-status-invalid', 'label' => __('messages.Invalid')],
            'Draft'     => ['class' => 'order-status-draft', 'label' => __('messages.Draft')],
        ];
        if ($canEditOrder) {
            return view('frontend.home.orders.edit-legacy', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'villa' => $villa,
                'guests' => $guests,
                'id_last_guest' => $id_last_guest,
                'occupancy' => $occupancy,
                'transports' => $transports,
                'transport_in' => $transport_in,
                'airport_shuttle_in' => $airport_shuttle_in,
                'airport_shuttle_out' => $airport_shuttle_out,
                'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
                'total_price_airport_shuttle' => $total_price_airport_shuttle,
                'hasInvalidOrder' => $hasInvalidOrder,
                'canEditOrder' => $canEditOrder,
                'agent' => $agent,
                'rooms' => $rooms,
                'number_of_adult' => $number_of_adult,
                'number_of_children' => $number_of_children,
                'duration' => $duration,
                'statusMap' => $statusMap,
            ]));
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }
    
    // CHECKOUT ORDER VILLA ================================================================================> OK
    public function func_checkout_order_villa(Request $request, $id){
        $user = Auth::user();
        $order=Orders::where('id',$id)
            ->where('sales_agent',$user->id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $villa = Villas::find($order->service_id);
        $status = "Pending";
        $inputGuests = $request->input('guests', []);
        $number_of_guests = count($inputGuests);
        $existingGuestIds = Guests::where('order_id', $order->id)->pluck('id')->toArray();
        $processedIds = [];
        $arrival_flight = $request->arrival_flight;
        $departure_flight = $request->departure_flight;
        $departure_time = date('Y-m-d H:i',strtotime($request->departure_time));
        $order->update([
            "status"=>$status,
            "note"=>$this->mergeOrderNoteWithAdditionalFlights($request->note, $request),
            "number_of_guests"=>$number_of_guests,
            "airport_shuttle_price" =>$request->airport_shuttle_price,
            "final_price" =>$request->final_price, 
            "arrival_flight" =>$arrival_flight,
            "arrival_time" =>$request->arrival_time,
            "airport_shuttle_in" =>$request->airport_shuttle_in,
            "departure_flight" =>$request->departure_flight,
            "departure_time" =>$request->departure_time,
            "airport_shuttle_out" =>$request->airport_shuttle_out,
        ]);
        // GUEST
        foreach ($inputGuests as $guestData) {
            if (!empty($guestData['id'])) {
                $guest = Guests::find($guestData['id']);
                if ($guest) {
                    $guest->update([
                        'name' => $guestData['name'],
                        'sex' => $guestData['sex'],
                        'age' => $guestData['age'],
                    ]);
                }
            } else {
                $newGuest = Guests::create([
                    'order_id' => $order->id,
                    'name' => $guestData['name'],
                    'sex' => $guestData['sex'],
                    'age' => $guestData['age'],
                ]);
            }
        }
        // AIRPORT SHUTTLE
        $arrival = AirportShuttle::where('order_id', $order->id)
            ->where('nav', 'In')
            ->first();

        $arrivalInputValid = $request->filled('airport_shuttle_in');
        $price_in = TransportPrice::find($request->transport_in_price_id);

        if ($arrival) {
            if ($arrivalInputValid) {
                $flight_in_number = $request->arrival_flight ?? null;
                $date_in = date('Y-m-d H:i',strtotime($request->arrival_time ?? $order->checkin));
                $arrival->update([
                    'flight_number' => $flight_in_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_in,
                    'transport_id' => $request->airport_shuttle_in,
                    'price_id' => $request->transport_in_price_id,
                    'price' => $price_in->calculatePrice($usdrates,$tax),
                    'nav' => 'In',
                ]);
            } else {
                $arrival->delete();
            }
        } else {
            if ($arrivalInputValid) {
                
                $flight_in_number = $request->arrival_flight ?? null;
                $date_in = date('Y-m-d H:i',strtotime($request->arrival_time ?? $order->checkin));
                AirportShuttle::create([
                    'order_id' => $order->id,
                    'flight_number' => $flight_in_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_in,
                    'transport_id' => $request->airport_shuttle_in,
                    'price_id' => $request->transport_in_price_id,
                    'src' => "Airport",
                    'dst' => $villa->name,
                    'duration' => $villa->airport_duration,
                    'distance' => $villa->airport_distance,
                    'price' => $price_in->calculatePrice($usdrates,$tax),
                    'nav' => 'In',
                ]);
            }
        }

        // Departure
        $departure = AirportShuttle::where('order_id', $order->id)
            ->where('nav', 'Out')
            ->first();

        $departureInputValid = $request->filled('airport_shuttle_out');
        $price_out = TransportPrice::find($request->transport_out_price_id);

        if ($departure) {
            if ($departureInputValid) {
                
                $flight_out_number = $request->departure_flight ?? null;
                $date_out = date('Y-m-d H:i',strtotime($request->departure_time ?? $order->checkout));
                $departure->update([
                    'flight_number' => $flight_out_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_out,
                    'transport_id' => $request->airport_shuttle_out,
                    'price_id' => $request->transport_out_price_id,
                    'price' => $price_out->calculatePrice($usdrates,$tax),
                'nav' => 'Out',
                ]);
            } else {
                $departure->delete();
            }
        } else {
            if ($departureInputValid) {
                $flight_out_number = $request->departure_flight ?? null;
                $date_out = date('Y-m-d H:i',strtotime($request->departure_time ?? $order->checkout));
                AirportShuttle::create([
                    'order_id' => $order->id,
                    'flight_number' => $flight_out_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_out,
                    'transport_id' => $request->airport_shuttle_out,
                    'price_id' => $request->transport_out_price_id,
                    'src' => $villa->name,
                    'dst' => "Airport",
                    'duration' => $villa->airport_duration,
                    'distance' => $villa->airport_distance,
                    'price' => $price_out->calculatePrice($usdrates,$tax),
                    'nav' => 'Out',
                ]);
            }
        }
        
        // dd($order);
        $rquotation = $request->request_quotation;
        $agent = User::where('id',$order->user_id)->first();
        Mail::to(config('app.reservation_mail'))->send(new ReservationMail($id,$rquotation));
        $note = "Submited order no: ".$order->orderno;
        
        $user_log =new UserLog([
            "action"=>"Submit Order",
            "service"=>$order->service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$id,
            "page"=>"edit-order-transport",
            "user_id"=>Auth::user()->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>'Submit Order',
            "url"=>$request->getClientIp(),
            "method"=>"Submit",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/detail-order-villa/$order->id")->with('success','Your order has been submited, and we will validate your order');
    }

    // VIEW DETAIL ORDER HOTEL ===============================================================================================> OK
    public function detail_order_villa($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::with(['optional_rate_orders', 'reservations.invoice'])
            ->where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $statusMap = [
            'Pending'   => ['class' => 'order-status-pending', 'label' => __('messages.Pending')],
            'Rejected'     => ['class' => 'order-status-rejected', 'label' => __('messages.Rejected')],
            'Approved'     => ['class' => 'order-status-approved', 'label' => __('messages.Approved')],
            'Paid'     => ['class' => 'order-status-paid', 'label' => __('messages.Paid')],
            'Canceled'     => ['class' => 'order-status-canceled', 'label' => __('messages.Canceled')],
        ];
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::firstWhere('name', 'USD'));
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $villa = Villas::with(['galleries', 'rooms' => fn($q) => $q->where('status', 1)])
            ->findOrFail($order->service_id);
        if ($villa) {
            $optional_rate = $villa->optionalrates;
        }else{
            $optional_rate = NULL;
        }
        $guests = Guests::where('order_id', $order->id)->get();
        $id_last_guest = Guests::max('id');
        $rooms = $villa->rooms;
        $number_of_adult = count($guests->where('age','Adult'));
        $number_of_children = count($guests->where('age','Child'));
        $occupancy_adult = $rooms->sum('guest_adult');
        $occupancy_children = $rooms->sum('guest_child');
        $occupancy = $order->capacity;



        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);
        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $optional_rate_orders = $order->optional_rate_orders;
        $optionalServiceTotalPrice = $optional_rate_orders->sum('price_total');
        $reservation = Reservation::find($order->rsv_id)??null;
        if ($reservation) {
            $inv_no = "INV-".$reservation->rsv_no;
        }else{
            $inv_no = null;
        }
        $invoice = InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $kickback = $order->kick_back ? $order->kick_back : null;
        $discounts = [
            'Kick Back' => $kickback > 0 ? $kickback : null,
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        if (in_array($order->status, ["Pending", "Rejected","Approved","Paid","Canceled"])) {
            return view('frontend.home.orders.details.legacy', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'invoice' => $invoice,
                'reservation' => $reservation,
                'inv_no' => $inv_no,
                'statusMap' => $statusMap,
                'villa' => $villa,
                'guests' => $guests,
                'id_last_guest' => $id_last_guest,
                'rooms' => $rooms,
                'number_of_adult' => $number_of_adult,
                'number_of_children' => $number_of_children,
                'occupancy_adult' => $occupancy_adult,
                'occupancy_children' => $occupancy_children,
                'occupancy' => $occupancy,

                
                'airport_shuttles' => $airport_shuttles,
                'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
                'total_price_airport_shuttle' => $total_price_airport_shuttle,
                'optional_rate' => $optional_rate,
                'optional_rate_orders' => $optional_rate_orders,
                'additionalServices' => $additionalServices,
                'additional_service_total_price' => $additional_service_total_price,
                'optionalServiceTotalPrice' => $optionalServiceTotalPrice,
                'total_promotion_disc' => $total_promotion_disc,
                'filteredDiscounts' => $filteredDiscounts,
                'normal_price' => $normal_price,
                'receipts' => $receipts,
                'agent' => $agent,
            ], $decodedData->toArray()));
        } else {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
    }




    // VIEW USER ORDER HOTEL PACKAGE ================================================================================> OK
    public function order_hotel_package(Request $request, $id)
    {
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $room = HotelRoom::with('hotels')->findOrFail($id);
        $hotel = $room->hotels;
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/hotel-$hotel->code");
        }
        $checkin = session('booking_dates.checkin');
        $checkout = session('booking_dates.checkout');
        $service = "Hotel Package";
        $orderNumber = $this->generateUniqueHotelOrderNumber('HPA', $now);
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $package = HotelPackage::find($request->package_id);
        $room->localized_include = localized_model_field($room, 'include');
        $room->localized_amenities = localized_model_field($room, 'amenities');
        $adultCapacity = $this->getRoomAdultCapacity($room);
        $childCapacity = (int) ($room->capacity_child ?? max(((int) $room->capacity) - $adultCapacity, 0));
        $room_capacity = $adultCapacity + $childCapacity;
        $transportOptions = $this->buildHotelBookingTransportOptions($hotel, $usdrates, $tax);
        $roomForm = $this->buildHotelBookingRoomFormData($hotel, $room, $room_capacity, $duration, $usdrates, $tax, 'nightly', $adultCapacity);
        $agents = $this->getBookingAgents();
        $final_price = $package->calculatePrice($usdrates,$tax);
        $package->localized_name = localized_model_field($package, 'name');
        $package->localized_benefits = localized_model_field($package, 'benefits');
        $package->localized_include = localized_model_field($package, 'include');
        $package->localized_additional_info = localized_model_field($package, 'additional_info');
        $data = ([
            'now' => $now,
            'usdrates' => $usdrates,
            'tax' => $tax,
            'service' => $service,
            'orderNumber' => $orderNumber,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'duration' => $duration,
            'hotel' => $hotel,
            'room' => $room,
            'room_capacity' => $room_capacity,
            'package' => $package,
            'transportOptions' => $transportOptions,
            'agents' => $agents,
            'final_price' => $final_price,
            'roomForm' => $roomForm,
        ]);
        return view('frontend.home.booking.orders.hotel-package',$data);
    }
    // VIEW USER ORDER HOTEL NORMAL =================================================================================> OK
    public function order_hotel_normal(Request $request, $id)
    {
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/hotel-{$request->hotel_id};");
        }
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $promotions = Promotion::where('periode_start','<=',$now)->where('periode_end','>=',$now)->where('status','Active')->get();
        $promotions_id = json_encode($promotions->pluck('id'));
        $promotions_name = $promotions->pluck('name')->implode(', ');
        $promotions_discount = json_encode($promotions->pluck('discounts'));
        $total_promotions_discount = $promotions->sum('discounts');
        $orders = Orders::where('sales_agent', $user_id)->pluck('booking_code');
        $orderNumber = $this->generateUniqueHotelOrderNumber('HNP', $now);
        $bk_code = BookingCode::where('code', $request->bookingcode)
            ->where('status', 'Active')
            ->first();
        [$bookingcode, $bookingcode_status] = $this->check_booking_code($bk_code, $orders, $now);
        $room = HotelRoom::with(['hotels'])->find($id);
        $hotel = $room->hotels;
        $room->localized_include = localized_model_field($room, 'include');
        $room->localized_amenities = localized_model_field($room, 'amenities');
        $adultCapacity = $this->getRoomAdultCapacity($room);
        $childCapacity = (int) ($room->capacity_child ?? max(((int) $room->capacity) - $adultCapacity, 0));
        $room_capacity = $adultCapacity + $childCapacity;
        $transportOptions = $this->buildHotelBookingTransportOptions($hotel, $usdrates, $tax);
        $roomForm = $this->buildHotelBookingRoomFormData($hotel, $room, $room_capacity, (int) $request->duration, $usdrates, $tax, 'stay', $adultCapacity);
        $price_list = $request->price_list;
        $normal_price = $request->normal_price;
        $agents = $this->getBookingAgents();
        $data = [
            'now' => $now,
            'duration' => $request->duration,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'price_pax' => $request->price_pax,
            'normal_price' => $normal_price,
            'kick_back' => $request->kick_back,
            'kick_back_per_pax' => $request->kick_back_per_pax,
            'service' => $request->service,
            'final_price' => $request->final_price,
            'promo_id' => $request->promo_id,
            'hotel' => $hotel,
            'room' => $room,
            'room_capacity' => $room_capacity,
            'transportOptions' => $transportOptions,
            'agents' => $agents,
            'promotions' => $promotions,
            'promotions_id' => $promotions_id,
            'bookingcode' => $bookingcode,
            'bookingcode_status' => $bookingcode_status,
            'orderNumber' => $orderNumber,
            'price_list' => $price_list,
            'promotions_name' => $promotions_name,
            'promotions_discount' => $promotions_discount,
            'total_promotions_discount' => $total_promotions_discount,
            'roomForm' => $roomForm,
        ];
        return view('frontend.home.booking.orders.hotel-normal',$data);
    }
    // FUNCTION USER CREATE ORDER HOTEL PACKAGE =====================================================================> OK
    public function func_create_order_hotel_package(StoreAccommodationOrderRequest $request, $id){
        $user = Auth::user();
        $submissionToken = $request->input('submission_token');
        if ($existingOrder = $this->findProcessedAccommodationOrderBySubmissionToken($submissionToken)) {
            return $this->redirectToExistingAccommodationOrder($existingOrder);
        }
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        if ($this->hasDuplicateHotelOrderNumber($request)) {
            $this->flashDuplicateOrderNumberRefresh();
            $package = HotelPackage::with('room')->findOrFail($id);
            $request->merge(['package_id' => $id]);

            return $this->order_hotel_package($request, $package->room->id);
        }
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $service = "Hotel Package";
        $package = HotelPackage::with(['hotels','room'])->findOrFail($id);
        $hotel = $package->hotels;
        $room = $package->room;
        $this->prepareAccommodationBookingData($request, $room);
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $arrivalTime = $this->normalizeBookingDate($request->arrival_time, $checkin . ' 11:00:00', true);
        $departureTime = $this->normalizeBookingDate($request->departure_time, $checkout . ' 11:00:00', true);
        $number_of_guests = array_sum($request->number_of_guests);
        $number_of_room = count($request->number_of_guests);
        $number_of_guests_room = json_encode($request->number_of_guests);
        $guest_detail = json_encode($request->guest_detail);
        $special_day = json_encode($request->special_day);
        $special_date = json_encode($request->special_date);
        $request_quotation = $this->resolveHotelQuotationValue($request);
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $cancellationPolicies = $this->hotelPackageCancellationPolicySnapshot($package, $hotel);
        $compiledNote = $this->mergeOrderNoteWithAdditionalFlights($request->note, $request);

        $extraBeds = $this->getBookingExtraBeds($hotel->id, $room->id);
        $adultCapacity = $this->getRoomAdultCapacity($room);
        $extra_bed_proses = [];
        $extra_bed_id_price = [];
        $extrabed_id = [];
        foreach ($request->number_of_guests as $index => $number_of_guest) {
            $isExtraBedNeeded = $number_of_guest > $adultCapacity;
            $extra_bed_proses[] = $isExtraBedNeeded ? 'Yes' : 'No';
            if ($isExtraBedNeeded) {
                $extraBed = $this->resolveSelectedExtraBed($extraBeds, $request->extra_bed_id[$index] ?? null);

                if ($extraBed) {
                    $price_extra_bed = $extraBed->calculatePrice($usdrates, $tax) * $duration;
                    $extra_bed_id_price[] = $price_extra_bed;
                    $extrabed_id[] = $extraBed->id;
                } else {
                    $extra_bed_id_price[] = 0;
                    $extrabed_id[] = NULL;
                }
            } else {
                $extra_bed_id_price[] = 0;
                $extrabed_id[] = NULL;
            }
        }
        $extra_bed_id = json_encode($extrabed_id);
        $extra_bed_price_list = json_encode($extra_bed_id_price);
        $extra_bed_status = json_encode($extra_bed_proses);
        $total_extra_bed_price = array_sum($extra_bed_id_price);

        $airportShuttleRows = $this->buildAirportShuttleRowsFromRequest($request, $hotel, $number_of_guests, $checkin, $checkout);
        $airport_shuttle_prices = $airportShuttleRows->sum('price');

        
        $pricing = app(HotelPricingService::class)->calculatePackageRate([
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'package_id' => $package->id,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rooms' => $number_of_room,
            'user_id' => $sales_agent,
            'usd_rate' => $usdrates,
            'tax' => $tax,
            'booking_code' => $this->activeAccommodationBookingCodeFromSession(),
            'extra_bed_total' => $total_extra_bed_price,
            'airport_shuttle_total' => $airport_shuttle_prices,
        ]);
        $price_pax = $pricing['price_pax'];
        $normal_price = $pricing['normal_price'];
        $price_total = $pricing['price_total'];
        $final_price = $pricing['grand_total'];
        $usd_rate = $usdrates->rate;
        $cny_rate = $cnyrates->rate;
        $twd_rate = $twdrates->rate;

        $order = DB::transaction(function () use ($request, $user, $user_id, $name, $email, $service, $hotel, $room, $package, $checkin, $checkout, $number_of_guests, $number_of_guests_room, $guest_detail, $request_quotation, $special_date, $special_day, $extra_bed_status, $number_of_room, $duration, $price_pax, $normal_price, $extra_bed_id, $extra_bed_price_list, $total_extra_bed_price, $price_total, $airport_shuttle_prices, $final_price, $usd_rate, $cny_rate, $twd_rate, $status, $sales_agent, $arrivalTime, $departureTime, $compiledNote, $cancellationPolicies, $airportShuttleRows, $pricing) {
            $this->revalidateAccommodationAvailability($hotel->id, $room->id, $checkin, $checkout, $number_of_room, true);

            $order = new Orders([
            'orderno'                   => $request->orderno,
            'service'                   => $service,
            'service_id'                => $hotel->id,
            'user_id'                   => $user->id,
            'name'                      => $name,
            'email'                     => $email,
            'servicename'               => $hotel->name,
            'subservice'                => $room->rooms,
            'subservice_id'             => $room->id,
            'package_name'              => $package->name,
            'checkin'                   => $checkin,
            'checkout'                  => $checkout,
            'location'                  => $hotel->region,
            'number_of_guests'          => $number_of_guests,
            'number_of_guests_room'     => $number_of_guests_room,
            'guest_detail'              => $guest_detail,
            'request_quotation'         => $request_quotation,
            'special_date'              => $special_date,
            'special_day'               => $special_day,
            'extra_bed'                 => $extra_bed_status,
            'capacity'                  => $room->capacity,
            'include'                   => $room->include,
            'benefits'                  => $package->benefits,
            'additional_info'           => $package->additional_info,
            'number_of_room'            => $number_of_room,
            'duration'                  => $duration,
            'price_pax'                 => $price_pax,
            'normal_price'              => $normal_price,
            'extra_bed_id'              => $extra_bed_id,
            'extra_bed_price'           => $extra_bed_price_list,
            'extra_bed_total_price'     => $total_extra_bed_price,
            'price_total'               => $price_total,
            'bookingcode'               => $pricing['booking_code_value'],
            'bookingcode_disc'          => $pricing['booking_code_discount'],
            'airport_shuttle_price'     => $airport_shuttle_prices,
            'final_price'               => $final_price,
            'usd_rate'                  => $usd_rate,
            'cny_rate'                  => $cny_rate,
            'twd_rate'                  => $twd_rate,
            'status'                    => $status,
            'sales_agent'               => $sales_agent,
            'arrival_flight'            => $request->arrival_flight,
            'arrival_time'              => $arrivalTime,
            'airport_shuttle_in'        => $request->airport_shuttle_in,
            'departure_flight'          => $request->departure_flight,
            'departure_time'            => $departureTime,
            'airport_shuttle_out'       => $request->airport_shuttle_out,
            'note'                      => $compiledNote,
            'cancellation_policy'       => $cancellationPolicies['cancellation_policy'],
            'cancellation_policy_traditional' => $cancellationPolicies['cancellation_policy_traditional'],
            'cancellation_policy_simplified' => $cancellationPolicies['cancellation_policy_simplified'],
            ]);
            $order->save();
            $reservation = app(AccommodationReservationService::class)->ensurePendingReservationForOrder($order);
            $this->storeAccommodationGuests($request, $order, $reservation);

            if ($airportShuttleRows->isNotEmpty()) {
                $this->create_order_airport_shuttle($order, $airportShuttleRows);
            }

            $note = "Created order hotel package with order no: ".$order->orderno;
            $user_log =new UserLog([
            "action"=>"Create Order",
            "service"=>$service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"hotel-price-".$hotel->code,
            "user_id"=>$user_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
            ]);
            $user_log->save();
            $order_log =new OrderLog([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            $this->consumeBookingCodeAfterOrder($pricing);

            return $order;
        });
        $this->rememberProcessedAccommodationOrderSubmission($submissionToken, $order);
        session()->forget('booking_dates');
        session()->forget('bookingcode');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            $this->submitCreatedHotelOrder($request, $order, $user);
            return redirect()->route('view.detail-order-hotel', ['id' => $order->id])->with('success', __('messages.Your order has been submitted'));
        }
    }

    private function hotelPackageCancellationPolicySnapshot(HotelPackage $package, Hotels $hotel): array
    {
        $base = trim((string) ($package->cancellation_policy ?: $hotel->cancellation_policy));

        return [
            'cancellation_policy' => $base ?: null,
            'cancellation_policy_traditional' => trim((string) (
                $package->cancellation_policy_traditional
                ?: $package->cancellation_policy
                ?: $hotel->cancellation_policy_traditional
                ?: $hotel->cancellation_policy
            )) ?: null,
            'cancellation_policy_simplified' => trim((string) (
                $package->cancellation_policy_simplified
                ?: $package->cancellation_policy
                ?: $hotel->cancellation_policy_simplified
                ?: $hotel->cancellation_policy
            )) ?: null,
        ];
    }
    // FUNCTION USER CREATE ORDER HOTEL PROMO =======================================================================> OK
    public function func_create_order_hotel_promo(StoreAccommodationOrderRequest $request){
        $user = Auth::user();
        $submissionToken = $request->input('submission_token');
        if ($existingOrder = $this->findProcessedAccommodationOrderBySubmissionToken($submissionToken)) {
            return $this->redirectToExistingAccommodationOrder($existingOrder);
        }
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        if ($this->hasDuplicateHotelOrderNumber($request)) {
            $this->flashDuplicateOrderNumberRefresh();

            return $this->order_hotel_promo($request, $request->room_id);
        }
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $arrivalTime = $this->normalizeBookingDate($request->arrival_time, $checkin . ' 11:00:00', true);
        $departureTime = $this->normalizeBookingDate($request->departure_time, $checkout . ' 11:00:00', true);
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $room = HotelRoom::with('hotels.extrabeds')->findOrFail($request->room_id);
        $this->prepareAccommodationBookingData($request, $room);
        $hotel = $room->hotels;
        $hotel_id = $hotel->id;
        $request_quotation = $this->resolveHotelQuotationValue($request);
        $agent_id = $sales_agent;
        $promo_ids = json_decode($request->promo_id);
        $promos = HotelPromo::whereIn('id', $promo_ids)->get()->keyBy('id');
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $pricingBase = app(HotelPricingService::class)->calculatePromoRate([
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'promo_ids' => $promo_ids,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rooms' => count($request->number_of_guests),
            'user_id' => $sales_agent,
            'usd_rate' => $usdrates,
            'tax' => $tax,
            'booking_code' => $this->activeAccommodationBookingCodeFromSession(),
        ]);
        $room_promo_price = $pricingBase['price_pax'];
        $service = "Hotel Promo";
        $orderData = [
            'book_period_start'         => $promos->pluck('book_periode_start')->toJson(),
            'book_period_end'           => $promos->pluck('book_periode_end')->toJson(),
            'period_start'              => $promos->pluck('periode_start')->toJson(),
            'period_end'                => $promos->pluck('periode_end')->toJson(),
            'name'                      => $promos->pluck('name')->implode(', '),
            'benefits'                  => $promos->pluck('benefits')->implode('<br>'),
            'include'                   => $promos->pluck('include')->implode('<br>'),
            'additional_info'           => $promos->pluck('additional_info')->implode('<br>'),
            'room_promo_price'          => $room_promo_price,
        ];
        $data_includes = [];
        $data_benefits = [];
        $data_additional_info = [];
        foreach ($promo_ids as $pro_id) {
            $hotel_promo = HotelPromo::find($pro_id);
            if ($hotel_promo) {
                $data_includes[] = $hotel_promo->include;
                $data_benefits[] = $hotel_promo->benefits;
                $data_additional_info[] = $hotel_promo->additional_info;
            }
        }
        $include = implode('<br>', $data_includes);
        $benefits = implode('<br>', $data_benefits);
        $additional_info = implode('<br>', $data_additional_info);
        $total_room = count($request->number_of_guests);
        $number_of_room = $total_room;
        $cancellation_policy = $room->hotels->cancellation_policy;
        $remark = $this->mergeOrderNoteWithAdditionalFlights($request->note, $request);
        $request->merge(['note' => $remark]);
        $usd_rate_sell = $usdrates->sell;
        $usd_rate_buy = $usdrates->buy;
        $cny_rate_sell = $cnyrates->sell;
        $cny_rate_buy = $cnyrates->buy;
        $twd_rate_sell = $twdrates->sell;
        $twd_rate_buy = $twdrates->buy;
        $extraBeds = $this->getBookingExtraBeds($hotel->id, $room->id);
        $adultCapacity = $this->getRoomAdultCapacity($room);
        $extra_bed_proses = [];
        $extra_bed_id_price = [];
        $extrabed_id = [];
        foreach ($request->number_of_guests as $index => $number_of_guest) {
            $isExtraBedNeeded = $number_of_guest > $adultCapacity;
            $extra_bed_proses[] = $isExtraBedNeeded ? 'Yes' : 'No';
            if ($isExtraBedNeeded) {
                $extraBed = $this->resolveSelectedExtraBed($extraBeds, $request->extra_bed_id[$index] ?? null);

                if ($extraBed) {
                    $price_extra_bed = $extraBed->calculatePrice($usdrates, $tax) * $duration;
                    $extra_bed_id_price[] = $price_extra_bed;
                    $extrabed_id[] = $extraBed->id;
                } else {
                    $extra_bed_id_price[] = 0;
                    $extrabed_id[] = NULL;
                }
            } else {
                $extra_bed_id_price[] = 0;
                $extrabed_id[] = NULL;
            }
        }
        $extra_bed_id = json_encode($extrabed_id);
        $extra_bed_price_list = json_encode($extra_bed_id_price);
        $extra_bed_status = json_encode($extra_bed_proses);
        $total_extra_bed_price= array_sum($extra_bed_id_price);
        $number_of_guests = array_sum($request->number_of_guests);
        
        
        $airportShuttleRows = $this->buildAirportShuttleRowsFromRequest($request, $hotel, $number_of_guests, $checkin, $checkout);
        $airport_shuttle_prices = $airportShuttleRows->sum('price');
        

        // ini
        $optional_rates = OptionalRate::mustBuy($checkin, $checkout)->get();
        $totalPriceOptionalRates = $optional_rates->sum(function ($rate) use ($usdrates, $tax) {
            return $rate->calculatePrice($usdrates, $tax);
        });
        $optional_price = $totalPriceOptionalRates * $number_of_guests;
        $pricing = app(HotelPricingService::class)->calculatePromoRate([
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'promo_ids' => $promo_ids,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rooms' => $number_of_room,
            'user_id' => $sales_agent,
            'usd_rate' => $usdrates,
            'tax' => $tax,
            'booking_code' => $this->activeAccommodationBookingCodeFromSession(),
            'extra_bed_total' => $total_extra_bed_price,
            'optional_rate_total' => $optional_price,
            'airport_shuttle_total' => $airport_shuttle_prices,
        ]);

        $order = DB::transaction(function () use ($request, $user_id, $name, $email, $orderData, $room, $request_quotation, $duration, $extra_bed_id, $extra_bed_proses, $extra_bed_price_list, $total_extra_bed_price, $extra_bed_status, $airport_shuttle_prices, $agent_id, $usdrates, $cnyrates, $twdrates, $tax, $status, $optional_price, $pricing, $arrivalTime, $departureTime, $airportShuttleRows, $optional_rates, $service, $hotel) {
            $this->revalidateAccommodationAvailability($hotel->id, $room->id, $pricing['checkin'], $pricing['checkout'], (int) $pricing['rooms'], true);

            $order = $this->create_order($request, $user_id, $name, $email, $orderData, $room, $request_quotation, $duration, $extra_bed_id, $extra_bed_proses, $extra_bed_price_list, $total_extra_bed_price, $extra_bed_status, $airport_shuttle_prices, $agent_id, $usdrates, $cnyrates, $twdrates, $tax, $status, $optional_price, $pricing);
            $reservation = app(AccommodationReservationService::class)->ensurePendingReservationForOrder($order);
            $this->storeAccommodationGuests($request, $order, $reservation);
            $order->update([
                'arrival_time' => $arrivalTime,
                'departure_time' => $departureTime,
            ]);
            if ($airportShuttleRows->isNotEmpty()) {
                $this->create_order_airport_shuttle($order, $airportShuttleRows);
            }

            if ($optional_rates) {
                foreach ($optional_rates as $optional_rate) {
                    $or_price_pax = $optional_rate->calculatePrice($usdrates, $tax);
                    $or_price_total = $or_price_pax * $order->number_of_guests;
                    $optional_rate_order =new OptionalRateOrder([
                        "order_id"=>$order->id,
                        "service"=>$order->service,
                        "optional_rate_id"=>$optional_rate->id,
                        "number_of_guest"=>$order->number_of_guests,
                        "service_date"=>$optional_rate->active_date,
                        "price_pax"=>$or_price_pax,
                        "price_total" =>$or_price_total,
                        "mandatory" =>1,
                    ]);
                    $optional_rate_order->save();
                }
            }

            $note = "Created order hotel promo with order no: ".$order->orderno;
            $user_log =new UserLog([
                "action"=>"Create Order",
                "service"=>$service,
                "subservice"=>$order->subservice,
                "subservice_id"=>$order->subservice_id,
                "page"=>"hotel-price-".$hotel->code,
                "user_id"=>$user_id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note,
            ]);
            $user_log->save();
            $order_log =new OrderLog([
                "order_id" => $order->id,
                "action"=>"Create Order",
                "url"=>$request->getClientIp(),
                "method"=>"Create",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            $this->consumeBookingCodeAfterOrder($pricing);

            return $order;
        });
        $this->rememberProcessedAccommodationOrderSubmission($submissionToken, $order);
        session()->forget('booking_dates');
        session()->forget('bookingcode');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            $this->submitCreatedHotelOrder($request, $order, $user);
            return redirect()->route('view.detail-order-hotel', ['id' => $order->id])->with('success', __('messages.Your order has been submitted'));
        }
    }
    // FUNCTION USER CREATE ORDER HOTEL =============================================================================> OK
    public function func_create_order_hotel_normal(StoreAccommodationOrderRequest $request){
        $user = Auth::user();
        $submissionToken = $request->input('submission_token');
        if ($existingOrder = $this->findProcessedAccommodationOrderBySubmissionToken($submissionToken)) {
            return $this->redirectToExistingAccommodationOrder($existingOrder);
        }
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        if ($this->hasDuplicateHotelOrderNumber($request)) {
            $this->flashDuplicateOrderNumberRefresh();

            return $this->order_hotel_normal($request, $request->room_id);
        }
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $room = HotelRoom::findOrFail($request->room_id);
        $this->prepareAccommodationBookingData($request, $room);
        $hotel = Hotels::findOrFail($request->hotel_id);
        $service = "Hotel";
        $service_id = $hotel->id;
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $arrivalTime = $this->normalizeBookingDate($request->arrival_time, $checkin . ' 11:00:00', true);
        $departureTime = $this->normalizeBookingDate($request->departure_time, $checkout . ' 11:00:00', true);
        $number_of_guests = array_sum($request->number_of_guests);
        $number_of_room = count($request->number_of_guests);
        $number_of_guests_room = json_encode($request->number_of_guests);
        $guest_detail = json_encode($request->guest_detail);
        $special_day = json_encode($request->special_day);
        $special_date = json_encode($request->special_date);
        $request_quotation = $this->resolveHotelQuotationValue($request);
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $cancellation_policy = $hotel->cancellation_policy;
        $compiledNote = $this->mergeOrderNoteWithAdditionalFlights($request->note, $request);

        $extraBeds = $this->getBookingExtraBeds($hotel->id, $room->id);
        $adultCapacity = $this->getRoomAdultCapacity($room);
        $extra_bed_proses = [];
        $extra_bed_id_price = [];
        $extrabed_id = [];
        foreach ($request->number_of_guests as $index => $number_of_guest) {
            $isExtraBedNeeded = $number_of_guest > $adultCapacity;
            $extra_bed_proses[] = $isExtraBedNeeded ? 'Yes' : 'No';
            if ($isExtraBedNeeded) {
                $extraBed = $this->resolveSelectedExtraBed($extraBeds, $request->extra_bed_id[$index] ?? null);

                if ($extraBed) {
                    $price_extra_bed = $extraBed->calculatePrice($usdrates, $tax) * $duration;
                    $extra_bed_id_price[] = $price_extra_bed;
                    $extrabed_id[] = $extraBed->id;
                } else {
                    $extra_bed_id_price[] = 0;
                    $extrabed_id[] = NULL;
                }
            } else {
                $extra_bed_id_price[] = 0;
                $extrabed_id[] = NULL;
            }
        }
        $extra_bed_id = json_encode($extrabed_id);
        $extra_bed_price_list = json_encode($extra_bed_id_price);
        $extra_bed_status = json_encode($extra_bed_proses);
        $total_extra_bed_price = array_sum($extra_bed_id_price);

        $airportShuttleRows = $this->buildAirportShuttleRowsFromRequest($request, $hotel, $number_of_guests, $checkin, $checkout);
        $airport_shuttle_prices = $airportShuttleRows->sum('price');
        $pricing = app(HotelPricingService::class)->calculateNormalRate([
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rooms' => $number_of_room,
            'user_id' => $sales_agent,
            'usd_rate' => $usdrates,
            'tax' => $tax,
            'booking_code' => $this->activeAccommodationBookingCodeFromSession(),
            'extra_bed_total' => $total_extra_bed_price,
            'airport_shuttle_total' => $airport_shuttle_prices,
        ]);
        $total_kick_back = $pricing['kick_back_total'];
        $total_promotions_discount = $pricing['promotion_discount_total'];
        $price_pax = $pricing['price_pax'];
        $normal_price = $pricing['normal_price'];
        $price_total = $pricing['price_total'];
        $final_price = $pricing['grand_total'];
        $usd_rate = $usdrates->rate;
        $cny_rate = $cnyrates->rate;
        $twd_rate = $twdrates->rate;
        $promotion = json_encode($pricing['promotion_names']);
        $promotion_disc = json_encode($pricing['promotion_discounts']);
        $order = DB::transaction(function () use ($request, $user, $hotel, $room, $service, $name, $email, $checkin, $checkout, $number_of_guests, $number_of_guests_room, $guest_detail, $request_quotation, $special_date, $special_day, $extra_bed_status, $number_of_room, $duration, $price_pax, $normal_price, $total_kick_back, $extra_bed_id, $extra_bed_price_list, $total_extra_bed_price, $price_total, $promotion, $promotion_disc, $airport_shuttle_prices, $final_price, $usd_rate, $cny_rate, $twd_rate, $status, $sales_agent, $arrivalTime, $departureTime, $compiledNote, $cancellation_policy, $airportShuttleRows, $pricing) {
            $this->revalidateAccommodationAvailability($hotel->id, $room->id, $checkin, $checkout, $number_of_room, true);

            $order = new Orders([
            'orderno'                   => $request->orderno,
            'service'                   => $service,
            'service_id'                => $hotel->id,
            'user_id'                   => $user->id,
            'name'                      => $name,
            'email'                     => $email,
            'servicename'               => $hotel->name,
            'subservice'                => $room->rooms,
            'subservice_id'             => $room->id,
            'checkin'                   => $checkin,
            'checkout'                  => $checkout,
            'location'                  => $hotel->region,
            'number_of_guests'          => $number_of_guests,
            'number_of_guests_room'     => $number_of_guests_room,
            'guest_detail'              => $guest_detail,
            'request_quotation'         => $request_quotation,
            'special_date'              => $special_date,
            'special_day'               => $special_day,
            'extra_bed'                 => $extra_bed_status,
            'capacity'                  => $room->capacity,
            'include'                   => $room->include,
            'additional_info'           => $room->additional_info,
            'number_of_room'            => $number_of_room,
            'duration'                  => $duration,
            'price_pax'                 => $price_pax,
            'normal_price'              => $normal_price,
            'kick_back'                 => $total_kick_back,
            'kick_back_per_pax'         => $request->var_kick_back_per_room,
            'extra_bed_id'              => $extra_bed_id,
            'extra_bed_price'           => $extra_bed_price_list,
            'extra_bed_total_price'     => $total_extra_bed_price,
            'price_total'               => $price_total,
            'bookingcode'               => $pricing['booking_code_value'],
            'bookingcode_disc'          => $pricing['booking_code_discount'],
            'promotion'                 => $promotion,
            'promotion_disc'            => $promotion_disc,
            'airport_shuttle_price'     => $airport_shuttle_prices,
            'final_price'               => $final_price,
            'usd_rate'                  => $usd_rate,
            'cny_rate'                  => $cny_rate,
            'twd_rate'                  => $twd_rate,
            'status'                    => $status,
            'sales_agent'               => $sales_agent,
            'arrival_flight'            => $request->arrival_flight,
            'arrival_time'              => $arrivalTime,
            'airport_shuttle_in'        => $request->airport_shuttle_in,
            'departure_flight'          => $request->departure_flight,
            'departure_time'            => $departureTime,
            'airport_shuttle_out'       => $request->airport_shuttle_out,
            'note'                      => $compiledNote,
            'cancellation_policy'       => $cancellation_policy,
            ]);
            $order->save();
            $reservation = app(AccommodationReservationService::class)->ensurePendingReservationForOrder($order);
            $this->storeAccommodationGuests($request, $order, $reservation);

            if ($airportShuttleRows->isNotEmpty()) {
                $this->create_order_airport_shuttle($order, $airportShuttleRows);
            }
            $note = "Created order hotel with order no: ".$order->orderno;
            $user_log =new UserLog([
            "action"=>"Create Order",
            "service"=>$service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"hotel-price-".$hotel->code,
            "user_id"=>$user->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
            ]);
            $user_log->save();
            $order_log =new OrderLog([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>$user->id,
            ]);
            $order_log->save();
            $this->consumeBookingCodeAfterOrder($pricing);

            return $order;
        });
        $this->rememberProcessedAccommodationOrderSubmission($submissionToken, $order);
        session()->forget('booking_dates');
        session()->forget('bookingcode');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            $this->submitCreatedHotelOrder($request, $order, $user);
            return redirect()->route('view.detail-order-hotel', ['id' => $order->id])->with('success', __('messages.Your order has been submitted'));
        }
    }

    // PRIVATE FUNCTION USER CREATE ORDER HOTEL ====================================================================> OK
    private function create_order($request, $user_id, $name, $email, $orderData, $room, $request_quotation, $duration, $extra_bed_id, $extra_bed_proses, $extra_bed_price_list, $total_extra_bed_price, $extra_bed_status, $airport_shuttle_prices, $agent_id, $usdrates, $cnyrates, $twdrates, $tax, $status, $optional_price, ?array $pricing = null){
        $hotel = $room->hotels;
        $number_of_room = count($request->number_of_guests);
        $number_of_guests = array_sum($request->number_of_guests);
        $number_of_guests_room = json_encode($request->number_of_guests);
        $guest_detail = json_encode($request->guest_detail);
        $special_day = json_encode($request->special_day);
        $special_date = json_encode($request->special_date);
        $checkin = date('Y-m-d', strtotime(session("booking_dates.checkin")));
        $checkout = date('Y-m-d', strtotime(session("booking_dates.checkout")));
        $arrivalTimeNormalized = $this->normalizeBookingDate($request->arrival_time, $checkin . ' 11:00:00', true);
        $departureTimeNormalized = $this->normalizeBookingDate($request->departure_time, $checkout . ' 11:00:00', true);
        $capacity = $room->capacity_adult;
        if ($request->airport_shuttle_in || $request->airport_shuttle_out) {
            $arrival_time = $arrivalTimeNormalized;
            $departure_time = $departureTimeNormalized;
            $arrival_flight = $request->arrival_flight ? $request->arrival_flight : "Insert flight number";
            $departure_flight = $request->departure_flight ? $request->departure_flight : "Insert flight number";
        }else {
            $arrival_time = NULL;
            $departure_time = NULL;
            $arrival_flight = NULL;
            $departure_flight = NULL;
        }
        $normal_price = $pricing['normal_price'] ?? ($orderData['room_promo_price'] * $number_of_room);
        $price_pax = $pricing['price_pax'] ?? ($normal_price / $number_of_room);
        $price_total = $pricing['price_total'] ?? ($normal_price + $total_extra_bed_price);
        $final_price = $pricing['grand_total'] ?? ($price_total + $airport_shuttle_prices);
        $promo_name = $orderData['name'];
        $book_period_start = $orderData['book_period_start'];
        $book_period_end = $orderData['book_period_end'];
        $period_start = $orderData['period_start'];
        $period_end = $orderData['period_end'];
        $include = $orderData['include'];
        $benefits = $orderData['benefits'];
        $additional_info = $orderData['additional_info'];
        $order =new Orders([
            "user_id"=>$user_id,
            "name"=>$name,
            "email"=>$email,
            "orderno"=>$request->orderno,
            "service"=>'Hotel Promo',
            "service_id"=>$hotel->id,
            "servicename" =>$hotel->name,
            "subservice"=>$room->rooms,
            "subservice_id"=>$room->id,
            "package_name"=>$request->package_name,
            "request_quotation"=>$request_quotation,
            "location"=>$hotel->region,
            "capacity"=>$capacity,
            "airport_shuttle_in"=>$request->airport_shuttle_in,
            "airport_shuttle_out"=>$request->airport_shuttle_out,
            "note"=>$request->note,
            "promo_id"=>$request->promo_id,
            'promo_name' => $promo_name,
            'book_period_start' => $book_period_start,
            'book_period_end' => $book_period_end,
            'period_start' => $period_start,
            'period_end' => $period_end,
            'include' => $include,
            'benefits' => $benefits,
            'additional_info' => $additional_info,
            "number_of_room"=>$number_of_room,
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "guest_detail"=>$guest_detail,
            "extra_bed"=>$extra_bed_status,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price_list,
            "extra_bed_total_price"=>$total_extra_bed_price,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "sales_agent"=>$agent_id,
            "cancellation_policy"=>$hotel->cancellation_policy,
            "duration"=>$duration,
            "price_pax" =>$price_pax,
            "normal_price" =>$normal_price,
            "optional_price" =>$optional_price,
            "price_total" =>$price_total, 
            "bookingcode" =>$pricing['booking_code_value'] ?? null,
            "bookingcode_disc" =>$pricing['booking_code_discount'] ?? 0,
            "final_price" =>$final_price, 
            "usd_rate" =>$usdrates->rate, 
            "cny_rate" =>$cnyrates->rate, 
            "twd_rate" =>$twdrates->rate, 
            "airport_shuttle_price"=>$airport_shuttle_prices,
            "arrival_flight"=>$arrival_flight,
            "arrival_time"=>$arrival_time,
            "departure_flight"=>$departure_flight,
            "departure_time"=>$departure_time,
            "status"=>$status,
        ]);
        $order->save();
        return $order;
    }

    // PRIVATE FUNCTION USER CREATE AIRPORT SHUTTLE ================================================================> OK
    private function create_order_airport_shuttle($order, $shuttles)
    {
        $rows = collect($shuttles)
            ->map(function ($shuttle) use ($order) {
                return array_merge($shuttle, [
                    'order_id' => $order->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })
            ->values()
            ->all();

        AirportShuttle::where('order_id', $order->id)->delete();

        if (!empty($rows)) {
            AirportShuttle::insert($rows);
            return $rows;
        }

        return [];
    }

    private function check_booking_code($bk_code, $orders, $now)
    {
        if (!$bk_code) {
            return [null, null];
        }
        if ($bk_code->used >= $bk_code->amount) {
            return [null, "Expired"];
        }
        if ($bk_code->expired_date < $now) {
            return [null, "Expired"];
        }
        if ($orders->contains($bk_code->code)) {
            return [null, "Used"];
        }
        return [$bk_code, "Valid"];
    }

    private function activeAccommodationBookingCodeFromSession(): ?string
    {
        return session('bookingcode.code') ?: null;
    }

    private function consumeBookingCodeAfterOrder(array $pricing): void
    {
        $bookingCode = $pricing['booking_code_model'] ?? null;

        if ($bookingCode) {
            $bookingCode->increment('used');
        }
    }

    // VIEW EDIT ORDER HOTEL =============================================================================================> OK
    public function edit_order_hotel($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
            ->accommodationService()
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $room = HotelRoom::find($order->subservice_id);
        $hotel = optional($room)->hotels;

        $optionalrates = OptionalRate::with('hotels')->get();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type', "Meals")->get();
        $optional_rate_orders = OptionalRateOrder::where('order_id', $id)->first();
        $tour_price = TourPrices::find($order->price_id);
        $tour_prices = TourPrices::where('tour_id', $order->subservice_id)
            ->where('status', "Active")
            ->orderBy('max_qty', 'ASC')
            ->get();
        $qty = TourPrices::max('max_qty');
        $tour = ($order->service == "Tour Package") ? Tours::find($order->service_id) : null;
        $order_optional_rates = OptionalRateOrder::with('optional_rate')
            ->where('service', 'Hotel Promo')
            ->where('order_id', $order->id)
            ->get();
        $optionalServiceTotalPrice = $order_optional_rates->sum('price_total');
        $airport_shuttles = AirportShuttle::where('order_id',$order->id)->get();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);
        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $transports = Transports::with('prices')
            ->where('status', 'Active')
            ->orderByDesc('capacity')
            ->get()
            ->map(function ($transport) use ($hotel, $usdrates, $tax) {
                $selectedPrice = $this->resolveAirportShuttlePriceForTransport($transport, $hotel);
                $transport->calculated_price = $selectedPrice ? $selectedPrice->calculatePrice($usdrates, $tax) : 0;
                $transport->calculated_price_id = $selectedPrice->id ?? null;
                return $transport;
            });
        $airport_shuttle_in = $airport_shuttles->firstWhere('nav', 'In');
        $airport_shuttle_out = $airport_shuttles->firstWhere('nav', 'Out');
        $decodedData = collect([
            'nor' => $order->number_of_room,
            'nogr' => json_decode($order->number_of_guests_room, true),
            'guest_detail' => json_decode($order->guest_detail, true),
            'special_day' => json_decode($order->special_day, true),
            'special_date' => json_decode($order->special_date, true),
            'extra_bed' => json_decode($order->extra_bed, true) ?? [],
            'extra_bed_id' => json_decode($order->extra_bed_id, true),
            'extra_bed_price' => json_decode($order->extra_bed_price, true),
        ]);
        $extra_bed_test = json_decode($order->extra_bed, true) ?? [];
        $extraBeds = ExtraBed::where('hotels_id', $hotel->id)->get();
        $extraBedPrices = collect(json_decode($order->extra_bed_price, true) ?? []);
        $serviceLabels = [
            'Hotel' => [['label' => 'messages.Hotel', 'value' => $order->servicename]],
            'Hotel Promo' => [
                ['label' => 'messages.Promo', 'value' => $order->promo_name],
                ['label' => 'messages.Hotel', 'value' => $order->servicename],
            ],
            'Hotel Package' => [
                ['label' => 'messages.Package', 'value' => $order->package_name],
                ['label' => 'messages.Hotel', 'value' => $order->servicename]
            ]
        ];
        $promotionName = json_decode($order->promotion);
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : null;
        $promotions_name = $promotionName ? implode(', ',$promotionName): null;
        $services = $serviceLabels[$order->service] ?? [['label' => 'messages.Hotel', 'value' => $order->servicename]];
        $hasInvalidOrder = !$order->number_of_room || !$order->number_of_guests_room || !$order->guest_detail;
        $showExtraBedPrice = $order->extra_bed_total_price > 0;
        $multipleRooms = $order->number_of_room > 1;
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);
        if ($canEditOrder) {
            return view('frontend.home.orders.edit-legacy', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'room' => $room,
                'hotel' => $hotel,
                'extraBeds' => $extraBeds,
                'optionalrates' => $optionalrates,
                'optional_rate_orders' => $optional_rate_orders,
                'optionalrate_meals' => $optionalrate_meals,
                'tour' => $tour,
                'tour_price' => $tour_price,
                'tour_prices' => $tour_prices,
                'transports' => $transports,
                'airport_shuttle_in' => $airport_shuttle_in,
                'airport_shuttle_out' => $airport_shuttle_out,
                'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
                'total_price_airport_shuttle' => $total_price_airport_shuttle,
                'optionalServiceTotalPrice' => $optionalServiceTotalPrice,
                'qty' => $qty,
                'hasInvalidOrder' => $hasInvalidOrder,
                'showExtraBedPrice' => $showExtraBedPrice,
                'multipleRooms' => $multipleRooms,
                'canEditOrder' => $canEditOrder,
                'services' => $services,
                'promotions_name' => $promotions_name,
                'promotion_discount' => $promotion_discount,
                'agent' => $agent,
                'extra_bed_test' => $extra_bed_test,
                'extraBedPrices' => $extraBedPrices,
                'airport_shuttles' => $airport_shuttles,
            ], $decodedData->toArray()));
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }
    // VIEW EDIT ORDER TOUR =============================================================================================> OK
    public function edit_order_tour($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();

        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }

        $agent = User::find($order->sales_agent);
        $tour = Tours::with('activeLocations')->find($order->service_id);

        if (!$tour) {
            return redirect()->route('view.detail-order-tour', ['id' => $order->id])
                ->with('warning', __('messages.Your order was not found').'!');
        }

        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $prices = $tour->prices()->where('status', 'Active')->get()->map(function ($p) use ($usdrates, $tax) {
            return [
                'id' => $p->id,
                'min_qty' => $p->min_qty,
                'max_qty' => $p->max_qty,
                'price' => $p->calculatePrice($usdrates, $tax),
            ];
        });
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langArea = match (config('app.locale')) {
            'zh' => 'area_traditional',
            'zh-CN' => 'area_simplified',
            default => 'area',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langPackageHighlights = match (config('app.locale')) {
            'zh' => 'package_highlights_traditional',
            'zh-CN' => 'package_highlights_simplified',
            default => 'package_highlights',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        $generatedTourItinerary = $this->buildTourLocationItineraryHtml(
            $tour,
            trim((string) ($tour->$langItinerary ?: $tour->itinerary))
        );
        $decodedData = collect([
            'nor' => $order->number_of_room,
            'nogr' => json_decode($order->number_of_guests_room, true),
            'guest_detail' => json_decode($order->guest_detail, true),
            'special_day' => json_decode($order->special_day, true),
            'special_date' => json_decode($order->special_date, true),
            'extra_bed' => json_decode($order->extra_bed, true) ?? [],
            'extra_bed_id' => json_decode($order->extra_bed_id, true),
            'extra_bed_price' => json_decode($order->extra_bed_price, true),
        ]);
        $canEditOrder = in_array($order->status, ["Draft", "Invalid", "Rejected"]);

        if ($canEditOrder) {
            return view('frontend.home.orders.edit-tour', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'tour' => $tour,
                'agent' => $agent,
                'prices' => $prices,
                'langType'=>$langType,
                'langName'=>$langName,
                'langArea'=>$langArea,
                'langShortDescription'=>$langShortDescription,
                'langDescription'=>$langDescription,
                'langPackageHighlights'=>$langPackageHighlights,
                'langItinerary'=>$langItinerary,
                'langInclude'=>$langInclude,
                'langExclude'=>$langExclude,
                'langAdditionalInfo'=>$langAdditionalInfo,
                'langCancellationPolicy'=>$langCancellationPolicy,
                'generatedTourItinerary'=>$generatedTourItinerary,
            ], $decodedData->toArray()));
        }
        return redirect()->route('view.detail-order-tour', ['id' => $order->id])
            ->with('warning', __('messages.This order can no longer be edited.'));
    }

    // VIEW ORDER TRANSPORT ==============================================================================================> OK
    public function order_transport(Request $request,$id){
        $now = Carbon::now();
        $orderno = Orders::count() + 1;
        $price = TransportPrice::findOrFail($id);
        $transport = Transports::where('id',$price->transports_id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $business = BusinessProfile::where('id','=',1)->first();
        $tax = Tax::where('id',1)->first();
        $transport_price = $price->calculatePrice($usdrates,$tax);
        $normal_price = $transport_price;
        $agents = $this->getBookingAgents();
        $promotions = Promotion::where('status',"Active")->get();
        if (isset($promotions)){
            $pr = count($promotions);
            $promotion_price = 0;
            for ($i=0; $i < $pr; $i++) { 
                $promotion_price = $promotion_price + $promotions[$i]->discounts;
            }
        }else{
            $promotion_price = 0;
        }
        $bcode = session('bookingcode.code');
        $bdisc = session('bookingcode.discounts');
        $bookingcode = BookingCode::where('code',$bcode)->first();
        $bookingcode_disc = $bookingcode ? $bookingcode->discounts : 0;

        if (isset($bookingcode->code) or isset($promotions)) {
            if (isset($bookingcode->code)) {
                $price_per_pax = $normal_price;
                
                if (isset($promotions)) {
                    $final_price = $normal_price - $bookingcode->discounts - $promotion_price;
                }else{
                    $final_price = $normal_price - $bookingcode->discounts;
                }
            }else{
                $price_per_pax = $normal_price ;
                $final_price = $normal_price  - $promotion_price;
            }
        }else {
            $price_per_pax = $normal_price;
            $final_price = $normal_price;
        }
        return view('frontend.home.booking.orders.transport',[
            'now'=>$now,
            'orderno'=>$orderno,
            'price'=>$price,
            'transport'=>$transport,
            'usdrates'=>$usdrates,
            'business'=>$business,
            'agents'=>$agents,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
            'bookingcode'=>$bookingcode,
            'normal_price'=>$normal_price,
            'final_price'=>$final_price,
            'transport_price'=>$transport_price,
            'bookingcode_disc'=>$bookingcode_disc,
            'bookingcode'=>$bookingcode,
        ]);
    }
    // FUNCTION USER CREATE ORDER TRANSPORT =========================================================================> OK
    public function func_create_order_transport(Request $request,$id){
        $user = Auth::user();
        $submissionToken = $request->input('submission_token');
        if ($existingOrderId = $this->findProcessedFormSubmission(self::TRANSPORT_ORDER_SUBMISSION_SCOPE, $submissionToken)) {
            return redirect()->route('view.detail-order-transport', ['id' => $existingOrderId])
                ->with('info', 'Your transport reservation has already been submitted.');
        }

        $transportPriceId = (int) $request->input('transport_price_id', $id);
        $transport_price = TransportPrice::findOrFail($transportPriceId);
        $transport = Transports::where('status', 'Active')->findOrFail($request->transport_id);
        if ((int) $transport_price->transports_id !== (int) $transport->id) {
            throw ValidationException::withMessages([
                'transport_id' => 'The selected transport rate is not available for this transport.',
            ]);
        }
        $isDetailModalFlow = $request->input('transport_booking_flow') === 'detail_modal';
        $developerRoles = ["developer", "reservation", "author"];

        $validationRules = [
            'transport_id' => ['required', 'integer', 'exists:transports,id'],
            'transport_price_id' => ['nullable', 'integer', 'exists:transport_prices,id'],
            'duration' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
            'terms_accepted' => ['accepted'],
            'airport_shuttle_type' => ['nullable', 'in:Arrival,Departure'],
            'flight_number' => ['nullable', 'string', 'max:255'],
            'flight_date' => ['nullable', 'date', 'after_or_equal:today'],
            'arrival_flight' => ['nullable', 'string', 'max:255'],
            'arrival_time' => ['nullable', 'date', 'after_or_equal:today'],
            'departure_flight' => ['nullable', 'string', 'max:255'],
            'departure_time' => ['nullable', 'date', 'after_or_equal:today'],
            'transport_booking_flow' => ['nullable', 'string', 'max:100'],
            'submission_token' => ['nullable', 'string', 'max:120'],
        ];

        if ($isDetailModalFlow) {
            $validationRules['flight_date'] = ['required', 'date', 'after_or_equal:today'];
            $validationRules['guest_entries'] = ['required', 'array', 'min:1', 'max:' . max((int) $transport->capacity, 1)];
            $validationRules['guest_entries.*.name'] = ['required', 'string', 'max:255'];
            $validationRules['guest_entries.*.age'] = ['required', 'in:Adult,Child'];
            $validationRules['guest_entries.*.sex'] = ['required', 'in:Male,Female'];
            $validationRules['guest_entries.*.phone'] = ['nullable', 'string', 'max:50'];
        } else {
            $validationRules['pickup_date'] = ['required', 'date', 'after_or_equal:today'];
            $validationRules['pickup_location'] = ['required', 'string', 'max:255'];
            $validationRules['dropoff_location'] = ['required', 'string', 'max:255'];
            $validationRules['number_of_guests'] = ['required', 'integer', 'min:1', 'max:' . max((int) $transport->capacity, 1)];
            $validationRules['guest_detail'] = ['required', 'string'];
        }

        if ($transport_price->type === 'Airport Shuttle') {
            $validationRules['airport_shuttle_type'] = ['required', 'in:Arrival,Departure'];

            if ($isDetailModalFlow) {
                $validationRules['flight_number'] = ['required', 'string', 'max:255'];
            } else {
                if ($request->input('airport_shuttle_type', 'Arrival') === 'Departure') {
                    $validationRules['departure_flight'] = ['required', 'string', 'max:255'];
                    $validationRules['departure_time'] = ['required', 'date', 'after_or_equal:today'];
                } else {
                    $validationRules['arrival_flight'] = ['required', 'string', 'max:255'];
                    $validationRules['arrival_time'] = ['required', 'date', 'after_or_equal:today'];
                }
            }
        }

        if ($isDetailModalFlow && $transport_price->type === 'Daily Rent') {
            $validationRules['pickup_location'] = ['required', 'string', 'max:255'];
            $validationRules['dropoff_location'] = ['required', 'string', 'max:255'];
        }

        if (in_array($user->position, $developerRoles)) {
            $validationRules['user_id'] = ['required', 'integer', 'exists:users,id'];
        }

        $validated = $request->validate($validationRules);

        if ($isDetailModalFlow) {
            // The visible Flight Date field is the canonical timestamp for the transport service.
            $validated['service_date'] = $validated['flight_date'];
        }

        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id ?: $user->id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = $isDetailModalFlow ? "Pending" : "Draft";
        }
        $salesAgentUser = User::find($sales_agent) ?: $user;
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $idrrates = Cache::remember('idr_rate', 3600, fn() => UsdRates::where('name', 'IDR')->first());
        $service = "Transport";
        $service_id = $transport->id;
        $service_type = $transport->type;
        $service_name = $transport->brand." ".$transport->name;

        $promotions = Promotion::where('periode_start','<=',$now)->where('periode_end','>=',$now)->where('status','Active')->get();
        $promotions_id = json_encode($promotions->pluck('id'));
        $promotions_name = json_encode($promotions->pluck('name'));
        $promotions_discount = json_encode($promotions->pluck('discounts'));
        $total_promotions_discount = $promotions->sum('discounts');
        
        $bcode = session('bookingcode.code');
        $bdisc = session('bookingcode.discounts');
        $bookingcode = BookingCode::where('code',$bcode)->first();
        $bookingcode_disc = $bdisc ? $bdisc : 0;
        $bookingcode_id = $bookingcode ? $bookingcode->id : null;
       
        
        $duration = (int) $validated['duration'];
        $serviceDateValue = $isDetailModalFlow
            ? $validated['service_date']
            : $validated['pickup_date'];
        $pickupDateTime = Carbon::parse($serviceDateValue);
        $orderNumber = $this->generateTransportOrderNumber($salesAgentUser, $now);
        $price_pax = $transport_price->calculatePrice($usdrates,$tax);
        if ($transport_price->type == "Daily Rent") {
            $checkin = $pickupDateTime->copy()->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax * $duration;
            $price_total = $normal_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addDays($duration);
        } elseif ($transport_price->type == "Airport Shuttle") {
            $checkin = $pickupDateTime->copy()->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }else{
            $checkin = $pickupDateTime->copy()->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }

        if ($isDetailModalFlow) {
            $guestEntries = collect($validated['guest_entries'] ?? [])
                ->map(function ($guest) {
                    return [
                        'name' => trim((string) ($guest['name'] ?? '')),
                        'age' => trim((string) ($guest['age'] ?? '')),
                        'sex' => trim((string) ($guest['sex'] ?? '')),
                        'phone' => trim((string) ($guest['phone'] ?? '')),
                    ];
                })
                ->filter(fn ($guest) => $guest['name'] !== '')
                ->values();

            if ($guestEntries->isEmpty()) {
                throw ValidationException::withMessages([
                    'guest_entries' => 'At least one guest is required before submitting the reservation.',
                ]);
            }

            $number_of_guests = $guestEntries->count();
            $guest_detail = $guestEntries->map(function ($guest, $index) {
                $parts = [
                    '<strong>Guest ' . ($index + 1) . ':</strong> ' . e($guest['name']),
                    e($guest['age']),
                    e($guest['sex']),
                ];

                if ($guest['phone'] !== '') {
                    $parts[] = 'Phone: ' . e($guest['phone']);
                }

                return implode(' | ', $parts);
            })->implode('<br>');
        } else {
            $guest_detail = $validated['guest_detail'];
            $number_of_guests = (int) $validated['number_of_guests'];
        }
        $include = $transport->include;
        $additional_info = $transport->additional_info;
        $cancellation_policy = $transport->cancellation_policy;
        $order_tax = 0;
        $arrivalFlight = $request->arrival_flight;
        $arrivalTime = $request->arrival_time;
        $departureFlight = $request->departure_flight;
        $departureTime = $request->departure_time;

        if ($isDetailModalFlow && $transport_price->type === "Airport Shuttle") {
            if ($request->airport_shuttle_type == "Departure") {
                $departureFlight = $validated['flight_number'];
                $departureTime = $validated['service_date'];
                $arrivalFlight = null;
                $arrivalTime = null;
            } else {
                $arrivalFlight = $validated['flight_number'];
                $arrivalTime = $validated['service_date'];
                $departureFlight = null;
                $departureTime = null;
            }
        }

        $isAirportShuttleOrder = $transport_price->type === "Airport Shuttle";
        $shuttleDirection = $isAirportShuttleOrder ? ($request->airport_shuttle_type ?: 'Arrival') : null;

        if ($isAirportShuttleOrder && $shuttleDirection == "Arrival") {
            $airport_shuttle_in = $transport->id;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location = $transport_price->src ?: $transport_price->dst;
            $dropoff_date = $checkout;
            $dropoff_location = $transport_price->dst ?: $transport_price->src;
        } elseif ($isAirportShuttleOrder && $shuttleDirection == "Departure") {
            $pickup_date = $checkin;
            $airport_shuttle_in = null;
            $airport_shuttle_out = $transport->id;
            $pickup_location =  $transport_price->src ?: $transport_price->dst;
            $dropoff_date = $checkout;
            $dropoff_location = $transport_price->dst ?: $transport_price->src;
        } else {
            $airport_shuttle_in = null;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location = trim((string) ($validated['pickup_location'] ?? $request->input('pickup_location', '')));
            $dropoff_date = $checkout;
            $dropoff_location = trim((string) ($validated['dropoff_location'] ?? $request->input('dropoff_location', '')));
        }
        $transportGuestEntries = $guestEntries ?? null;
        app(TransportAvailabilityService::class)->ensureCanBook($transport->id, $checkin, $checkout);
        $order = DB::transaction(function () use ($request, $bookingcode, $bookingcode_id, $user, $name, $email, $orderNumber, $service, $service_id, $isAirportShuttleOrder, $shuttleDirection, $transport_price, $service_name, $number_of_guests, $guest_detail, $transport, $sales_agent, $bookingcode_disc, $include, $additional_info, $cancellation_policy, $duration, $price_total, $promotions_name, $promotions_discount, $final_price, $usdrates, $cnyrates, $twdrates, $normal_price, $price_pax, $arrivalFlight, $arrivalTime, $airport_shuttle_in, $departureFlight, $departureTime, $airport_shuttle_out, $pickup_location, $pickup_date, $dropoff_date, $dropoff_location, $checkin, $checkout, $status, $validated, $isDetailModalFlow, $transportGuestEntries) {
            app(TransportAvailabilityService::class)->ensureCanBook($transport->id, $checkin, $checkout, null, true);

            if ($bookingcode) {
                BookingCode::whereKey($bookingcode->id)->lockForUpdate()->increment('used');
            }

            $order = new Orders([
            "user_id"=>$user->id,
            "name"=>$name,
            "email"=>$email,
            "orderno"=>$orderNumber,
            "service"=>$service,
            "service_id"=>$service_id,
            "service_type"=>$isAirportShuttleOrder ? $shuttleDirection : $transport_price->type,
            "servicename" =>$service_name,
            "subservice"=>$transport_price->type,
            "subservice_id"=>$transport_price->id,
            "number_of_guests"=>$number_of_guests,
            "guest_detail"=>$guest_detail,
            "extra_time"=>$transport_price->extra_time,
            "price_id"=>$transport_price->id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "src"=>$transport_price->src,
            "dst"=>$transport_price->dst,
            "sales_agent"=>$sales_agent,
            "pickup_name"=>$request->pickup_name,
            "bookingcode"=>$bookingcode_id,
            "bookingcode_disc"=>$bookingcode_disc,
            "capacity"=>$transport->capacity,
            "include" =>$include,
            "additional_info"=>$additional_info,
            "cancellation_policy"=>$cancellation_policy,
            "duration"=>$duration,
            "price_total" =>$price_total, 
            "promotion" =>$promotions_name, 
            "promotion_disc" =>$promotions_discount, 
            "final_price" =>$final_price, 
            "usd_rate" =>$usdrates->rate, 
            "cny_rate" =>$cnyrates->rate, 
            "twd_rate" =>$twdrates->rate, 
            "normal_price" =>$normal_price,
            "price_pax" =>$price_pax,
            "arrival_flight" =>$arrivalFlight,
            "arrival_time" =>$arrivalTime,
            "airport_shuttle_in" =>$airport_shuttle_in,
            "departure_flight" =>$departureFlight,
            "departure_time" =>$departureTime,
            "airport_shuttle_out" =>$airport_shuttle_out,
            "pickup_location" =>$pickup_location,
            "pickup_date" =>$pickup_date,
            "dropoff_date" =>$dropoff_date,
            "dropoff_location" =>$dropoff_location,
            "status"=>$status,
            "note"=>$validated['note'] ?? null,
            ]);
            $order->save();

            if ($isDetailModalFlow && $transportGuestEntries) {
                $transportGuestEntries->each(function ($guest) use ($order) {
                    Guests::create([
                        'order_id' => $order->id,
                        'name' => $guest['name'],
                        'age' => $guest['age'],
                        'sex' => $guest['sex'],
                        'phone' => $guest['phone'] !== '' ? $guest['phone'] : null,
                    ]);
                });
            }

            $note = "Created Order with order no: ".$orderNumber;
            $user_log = new UserLog([
                "action"=>"Create Order",
                "service"=>"Transport",
                "subservice"=>$order->subservice,
                "subservice_id"=>$order->id,
                "page"=>"order-transport",
                "user_id"=>$user->id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note,
            ]);
            $user_log->save();
            $order_log = new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Create Order",
                "url"=>$request->getClientIp(),
                "method"=>"Create",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();

            if ($status === 'Pending') {
                app(TransportReservationService::class)->ensurePendingReservationForOrder($order);
            }

            return $order->fresh();
        });

        if ($submissionToken) {
            $this->rememberProcessedFormSubmission(self::TRANSPORT_ORDER_SUBMISSION_SCOPE, $submissionToken, $order->id);
        }
        $subject = $orderNumber;
        if ($isDetailModalFlow) {
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id, null));

            if (in_array(Auth::user()->position, $developerRoles, true)) {
                return redirect()->route('view.detail-order-admin', ['id' => $order->id])
                    ->with('success', __('messages.The order has been successfully created'));
            }

            return redirect()->route('view.detail-order-transport', ['id' => $order->id])
                ->with('success', 'Your transport reservation has been submitted and is now pending review.');
        }

        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id,$rquotation));
            return redirect('/orders-admin-'.$order->id)->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-transport', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }
    // VIEW EDIT ORDER TRANSPORT =============================================================================================> OK
    public function edit_order_transport($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $transport = Transports::with(['prices'])->find($order->service_id);
        $transports = Transports::with('prices')
            ->where('status', 'Active')
            ->orderByDesc('capacity')
            ->get();
        $price = TransportPrice::find($order->price_id);
        $promotionName = json_decode($order->promotion);
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : null;
        $promotions_name = $promotionName ? implode(', ',$promotionName): null;
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);
        if ($canEditOrder) {
            return view('frontend.home.orders.edit-legacy', [
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'transport' => $transport,
                'transports' => $transports,
                'canEditOrder' => $canEditOrder,
                'promotions_name' => $promotions_name,
                'promotion_discount' => $promotion_discount,
                'price' => $price,
            ]);
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }
    // FUNCTION =============================================================================================================> OK
    public function func_submit_order_transport(Request $request,$id){
        $user = Auth::user();
        $submissionToken = $request->input('submission_token');
        if ($existingOrderId = $this->findProcessedFormSubmission('transport-order-submit:' . $id, $submissionToken)) {
            return redirect()->route('view.detail-order-transport', ['id' => $existingOrderId])
                ->with('info', 'Your transport order has already been submitted.');
        }
        $order=Orders::where('id',$id)
            ->where('sales_agent',$user->id)
            ->where('service', Orders::PUBLIC_TRANSPORT_SERVICE)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $validationRules = [
            'duration' => ['required', 'integer', 'min:1'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:today'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'dropoff_location' => ['nullable', 'string', 'max:255'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:' . max((int) $order->capacity, 1)],
            'guest_detail' => ['required', 'string'],
            'airport_shuttle_type' => ['nullable', 'in:Arrival,Departure'],
            'arrival_flight' => ['nullable', 'string', 'max:255'],
            'arrival_time' => ['nullable', 'date', 'after_or_equal:today'],
            'departure_flight' => ['nullable', 'string', 'max:255'],
            'departure_time' => ['nullable', 'date', 'after_or_equal:today'],
            'note' => ['nullable', 'string'],
            'submission_token' => ['nullable', 'string', 'max:120'],
        ];

        if ($order->subservice == "Airport Shuttle") {
            $validationRules['airport_shuttle_type'] = ['required', 'in:Arrival,Departure'];
            if ($request->input('airport_shuttle_type', 'Arrival') === 'Departure') {
                $validationRules['departure_flight'] = ['required', 'string', 'max:255'];
                $validationRules['departure_time'] = ['required', 'date', 'after_or_equal:today'];
            } else {
                $validationRules['arrival_flight'] = ['required', 'string', 'max:255'];
                $validationRules['arrival_time'] = ['required', 'date', 'after_or_equal:today'];
            }
        } else {
            $validationRules['pickup_date'] = ['required', 'date', 'after_or_equal:today'];
            $validationRules['pickup_location'] = ['required', 'string', 'max:255'];
            $validationRules['dropoff_location'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($validationRules);

        $total_promotions_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc,true)):0;
        $bookingcode_disc = $order->bookingcode_disc ? $order->bookingcode_disc : 0;
        $additional_service_price = $order->additional_service_price ? array_sum(json_decode($order->additional_service_price,true)) : 0;
        $status = "Pending";
        $duration = (int) $validated['duration'];
        $price_pax = $order->price_pax;
        $transport = Transports::where('status', 'Active')->findOrFail($order->service_id);
        $transport_price = TransportPrice::findOrFail($order->subservice_id);
        if ((int) $transport_price->transports_id !== (int) $transport->id || $transport_price->type !== $order->subservice) {
            throw ValidationException::withMessages([
                'transport_id' => 'The selected transport rate is not available for this transport.',
            ]);
        }
        $price_pax = $transport_price->calculatePrice(
            Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first()),
            Cache::remember('tax_1', 3600, fn() => Tax::find(1))
        );
        if ($order->subservice == "Daily Rent") {
            $checkin = Carbon::parse($validated['pickup_date'])->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax * $duration;
            $price_total = $normal_price + $additional_service_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addDays($duration);
        } elseif ($order->subservice == "Airport Shuttle") {
            if ($request->airport_shuttle_type == "Arrival") {
                $checkin = Carbon::parse($validated['arrival_time'])->format('Y-m-d H:i');
            }else{
                $checkin = Carbon::parse($validated['departure_time'])->format('Y-m-d H:i');
            }
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price + $additional_service_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }else{
            $checkin = Carbon::parse($validated['pickup_date'])->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price + $additional_service_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }

        $isAirportShuttleOrder = $order->subservice == "Airport Shuttle";
        $shuttleDirection = $isAirportShuttleOrder ? ($request->airport_shuttle_type ?: 'Arrival') : null;

        if ($isAirportShuttleOrder && $shuttleDirection == "Arrival") {
            $airport_shuttle_in = $order->service_id;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location = "Airport";
            $dropoff_date = $checkout;
            $dropoff_location = $order->dropoff_location;
        } elseif ($isAirportShuttleOrder && $shuttleDirection == "Departure") {
            $pickup_date = date('Y-m-d H:i',strtotime('-'.($duration + 2).'hours',strtotime($request->departure_time)));
            $airport_shuttle_in = null;
            $airport_shuttle_out = $transport->id;
            $pickup_location =  $transport_price->dst;
            $dropoff_date = date('Y-m-d H:i',strtotime('+'.($duration).'hours',strtotime($pickup_date)));
            $dropoff_location = "Airport";
        } else {
            $airport_shuttle_in = null;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location = trim((string) $request->pickup_location);
            $dropoff_date = $checkout;
            $dropoff_location = trim((string) $request->dropoff_location);
        }
        // dd($request->pickup_date, $checkin, $checkout);
        app(TransportAvailabilityService::class)->ensureCanBook((int) $order->service_id, $checkin, $checkout, (int) $order->id);
        DB::transaction(function () use ($request, $order, $status, $checkin, $checkout, $validated, $duration, $price_total, $final_price, $normal_price, $isAirportShuttleOrder, $shuttleDirection, $airport_shuttle_in, $airport_shuttle_out, $pickup_location, $pickup_date, $dropoff_date, $dropoff_location) {
            app(TransportAvailabilityService::class)->ensureCanBook((int) $order->service_id, $checkin, $checkout, (int) $order->id, true);

            $order->update([
            "status"=>$status,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "guest_detail"=>$validated['guest_detail'],
            "note"=>$validated['note'] ?? null,
            "number_of_guests"=>$validated['number_of_guests'],
            "pickup_name"=>$request->pickup_name,
            "pickup_phone"=>$request->pickup_phone,
            "service_type"=>$isAirportShuttleOrder ? $shuttleDirection : $order->subservice,
            "duration"=>$duration,
            "price_total" =>$price_total, 
            "final_price" =>$final_price, 
            "normal_price" =>$normal_price,
            "arrival_flight" =>$request->arrival_flight,
            "arrival_time" =>$request->arrival_time,
            "airport_shuttle_in" =>$airport_shuttle_in,
            "departure_flight" =>$request->departure_flight,
            "departure_time" =>$request->departure_time,
            "airport_shuttle_out" =>$airport_shuttle_out,
            "pickup_location" =>$pickup_location,
            "pickup_date" =>$pickup_date,
            "dropoff_date" =>$dropoff_date,
            "dropoff_location" =>$dropoff_location,
            ]);

            app(TransportReservationService::class)->ensurePendingReservationForOrder($order);

            $note = "Submited order no: ".$order->orderno;
            $user_log = new UserLog([
                "action"=>"Submit Order",
                "service"=>$order->service,
                "subservice"=>$order->subservice,
                "subservice_id"=>$order->id,
                "page"=>"edit-order-transport",
                "user_id"=>Auth::user()->id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note,
            ]);
            $user_log->save();
            $order_log = new OrderLog([
                "order_id"=>$order->id,
                "action"=>'Submit Order',
                "url"=>$request->getClientIp(),
                "method"=>"Submit",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
        });
        // dd($order);
        $rquotation = $request->request_quotation;
        $agent = User::where('id',$order->user_id)->first();
        Mail::to(config('app.reservation_mail'))->send(new ReservationMail($id,$rquotation));
        if ($submissionToken) {
            $this->rememberProcessedFormSubmission('transport-order-submit:' . $id, $submissionToken, $order->id);
        }
        return redirect("/detail-order-transport/$order->id")->with('success','Your order has been submited, and we will validate your order');
    }
    // VIEW DETAIL ORDER TRANSPORT ===============================================================================================> OK
    public function detail_order_transport($id)
    {
        $user_id = Auth::user()->id; 
        $now = Carbon::now();
        $order = Orders::with(['guests', 'optional_rate_orders', 'reservations.invoice'])
            ->where('sales_agent', $user_id)
            ->where('service', Orders::PUBLIC_TRANSPORT_SERVICE)
            ->where('id',$id)
            ->first();
        if (!$order || in_array($order->status, ["Draft", "Invalid", "Rejected"])) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::firstWhere('name', 'USD'));
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $reservation = Reservation::find($order->rsv_id);
        $invoice = InvoiceAdmin::with(['payment', 'bank', 'currency'])->firstWhere('rsv_id', $order->rsv_id);
        $order = $this->autoCancelExpiredApprovedOrder($order, $invoice);
        $invoice = InvoiceAdmin::with(['payment', 'bank', 'currency'])->firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        $paymentDeadline = $this->getInvoicePaymentDeadline($invoice);
        $paymentSubmissionExists = $this->orderHasPaymentSubmission($invoice);
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $transport = Transports::with('prices')->find($order->service_id);
        $price = TransportPrice::find($order->price_id);
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $decodedData = collect([
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $discounts = [
            'Kick Back' => $order->kick_back > 0 ? $order->kick_back : null,
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null,
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        
        return view('frontend.home.orders.details.legacy',[
            'order' => $order,
            'tax' => $tax,
            'now' => $now,
            'usdrates' => $usdrates,
            'business' => $business,
            'invoice' => $invoice,
            'reservation' => $reservation,
            'total_promotion_disc' => $total_promotion_disc,
            'normal_price' => $normal_price,
            'receipts' => $receipts,
            'paymentDeadline' => $paymentDeadline,
            'paymentSubmissionExists' => $paymentSubmissionExists,
            'discounts' => $discounts,
            'filteredDiscounts' => $filteredDiscounts,
            'transport' => $transport,
            'price' => $price,
            'additionalServices' => $additionalServices,
            'additional_service_total_price' => $additional_service_total_price,
            'agent' => $agent,
        ]);
    }

    // USER EDIT ORDER ROOM ==============================================================================================> OK
    public function edit_order_room($id)
    {   
        $agent = Auth::user();
        $order = Orders::where('sales_agent', $agent->id)
            ->whereIn('status', ['Draft', 'Invalid'])
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
        }
        $now = Carbon::now();
        $usdrates = Cache::remember('usd_rates', 60, fn() => UsdRates::where('name', 'USD')->first());
        $tax = Cache::remember('tax', 60, fn() => Tax::where('id', 1)->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $hotel = Hotels::find($order->service_id);
        $room = HotelRoom::find($order->subservice_id);
        $duration = Carbon::parse($order->checkin)->diffInDays(Carbon::parse($order->checkout));
        $extrabeds = $hotel->extrabeds->map(fn($eb) => tap($eb, function ($eb) use ($usdrates, $tax, $order) {
            $eb->price = $eb->calculatePrice($usdrates, $tax) * $order->duration;
        }));
        $date_stay = collect(range(0, $duration - 1))->map(fn($a) => date('Y-m-d', strtotime("+$a days", strtotime($order->checkin))));
        $decodedData = [
            'nor' => $order->number_of_room,
            'nogr' => json_decode($order->number_of_guests_room),
            'guest_name' => json_decode($order->guest_detail),
            'guest_detail' => json_decode($order->guest_detail),
            'special_day' => json_decode($order->special_day),
            'special_date' => json_decode($order->special_date),
            'extra_bed' => json_decode($order->extra_bed),
            'extra_bed_id' => json_decode($order->extra_bed_id),
            'extra_bed_price' => json_decode($order->extra_bed_price),
            'price_pax' => json_decode($order->price_pax),
        ];
        return view('frontend.home.orders.edit-room', array_merge([
            'order' => $order,
            'extrabeds' => $extrabeds,
            'tax' => $tax,
            'now' => $now,
            'usdrates' => $usdrates,
            'business' => $business,
            'hotel' => $hotel,
            'room' => $room,
            'date_stay' => $date_stay,
        ], $decodedData));
    }

    // Function Update Order Room ========================================================================================> OK
    public function func_update_order_room(Request $request,$id){
        $user = Auth::user();
        $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
        }
        $croom = count($request->number_of_guests_room);
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $duration = $order->duration;
        $optional_price = $order->optional_price;
        $price_pax = $order->price_pax;
        $kick_back = ($order->kick_back_per_pax * $duration)*$croom;
        
        if ($request->number_of_guests_room > 0) {
            $number_of_guests = array_sum($request->number_of_guests_room);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests_room as $jk) {
                if ($jk <= $order->capacity ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }

            $extra_bed_id_price = [];          
            $extraBedId = [];          
            for ($i=0; $i < $croom; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i]) {
                        $extraBed = ExtraBed::find($request->extra_bed_id[$i]);
                        if ($extraBed) {
                            $price_extra_bed = ($extraBed->calculatePrice($usdrates, $tax)) * $duration; 
                            array_push($extra_bed_id_price,$price_extra_bed);
                            array_push($extraBedId,$request->extra_bed_id[$i]);
                        }else{
                            array_push($extra_bed_id_price,0);
                            array_push($extraBedId,$request->extra_bed_id[$i]);
                        }
                    }else{
                        $extraBed = ExtraBed::where('hotels_id',$order->service_id)->first();
                        $price_extra_bed = ($extraBed->calculatePrice($usdrates, $tax)) * $duration; 
                        if ($extraBed) {
                            array_push($extra_bed_id_price,$price_extra_bed);
                            array_push($extraBedId,$extraBed->id);
                        }else{
                            array_push($extra_bed_id_price,0);
                            array_push($extraBedId,null);
                        }
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                    array_push($extraBedId,null);
                }
            }
            $extra_bed_id = json_encode($extraBedId);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed_process = json_encode($extra_bed_proses);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $pro_disc = json_decode($order->promotion_disc);
            $number_of_guests_room = json_encode($request->number_of_guests_room);
            $total_extra_bed = array_sum($extra_bed_id_price);
            if (isset($pro_disc)) {
                $promotion_disc = array_sum($pro_disc);
            }else{
                $promotion_disc = 0;
            }
            
            $price_pax = $order->price_pax;
            $price_total = ($price_pax * $croom) + $total_extra_bed;
            $final_price = ((($price_total + $optional_price + $order->additional_service_price + $order->airport_shuttle_price) - $order->discounts) - $order->bookingcode_disc) - $promotion_disc;
        
        }else{
            $number_of_guests = 0;
            $number_of_guests_room = 0;
            $croom = 0;
            $extra_bed_proses = 0;
            $extra_bed_id = 0;
            $extra_bed_price = 0;
            $extra_bed_process = 0;
            $price_total = 0;
            $kick_back = 0;
            $guest_detail = 0;
            $special_day = 0;
            $special_date = 0;
            $normal_price = 0;
            $airport_shuttle_price = 0;
            $final_price = 0;
        }

        $order->update([
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "number_of_room"=>$croom,
            "guest_detail"=>$guest_detail,
            "request_quotation"=>$request->request_quotation,
            "extra_bed"=>$extra_bed_process,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price,
            "extra_bed_total_price"=>$total_extra_bed,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "price_total"=>$price_total,
            "final_price"=>$final_price,
            "kick_back"=>$kick_back,
        
        ]);
        return redirect()->route('view.edit-order-hotel', ['id' => $id])->with('success',__('messages.Your order has been updated'));
    }
    

    // VIEW USER EDIT ORDER ADDITIONAL CHARGE =============================================================================> OK
    public function edit_order_additional_charge($id){   
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));

        $order = Orders::with(['optional_rate_orders'])->where('id', $id)->where('sales_agent', $user_id)->first();
        $optional_rate_orders = $order->optional_rate_orders;
        $optional_services = OptionalRate::where('hotels_id', $order->service_id)->get();
        $in=Carbon::parse($order->checkin);
        $out=Carbon::parse($order->checkout);
        $duration = $in->diffInDays($out);
        $date_stay = collect(range(0, $duration - 1))
                ->map(fn($a) => Carbon::parse($in)->addDays($a)->toDateString());
                
        $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
        
        if ($order != "" or $order->status != "Pending" or $order->status != "Active"){
            return view('frontend.home.orders.edit-additional-charge',compact('order'),[
                'tax'=>$tax,
                'now'=>$now,
                'usdrates'=>$usdrates,
                'business'=>$business,
                'order'=>$order,
                'optional_rate_orders'=>$optional_rate_orders,
                'duration'=>$duration,
                'optional_services'=>$optional_services,
                'order_wedding'=>$order_wedding,
                'date_stay'=>$date_stay,
            ]);
        }else{
            return redirect('/orders')->with('error',__('messages.Your order was not found').'!');
        }
  
    }

    // FUNCTION CREATE ORDER ADDITIONAL CHARGE ============================================================================> OK
    public function func_create_order_additional_charge(Request $request, $id)
    {
        $now = Carbon::now();
        $cacheTTL = 3600;
        $tax = Cache::remember('tax_1', $cacheTTL, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', $cacheTTL, fn() => UsdRates::where('name', 'USD')->first());
        $agent = Auth::user();
        $order = Orders::where('sales_agent', $agent->id)
            ->whereIn('status', ['Draft', 'Invalid', 'Rejected'])
            ->where('id',$id)->first();
        if (!$order) {
            return redirect("/orders")->with('warning', __('messages.Your order cannot be changed'));
        }
        $optional_rates = OptionalRate::where('hotels_id', $order->service_id)->get();
        $optional_rate_order = [];
        $total_optional_rate = 0;

        if ($request->optional_rate_id) {
            foreach ($request->optional_rate_id as $index => $optional_rate_id) {
                $optional_rate = $optional_rates->firstWhere('id', $optional_rate_id);
                if (!$optional_rate) continue;
                $price_pax = $optional_rate->calculatePrice($usdrates, $tax);
                $price_total = $price_pax * $request->number_of_guest[$index];
                $optional_rate_order[] = [
                    'optional_rate_id' => $optional_rate_id,
                    'service' => $order->service,
                    'orders_id' => $order->id,
                    'number_of_guest' => $request->number_of_guest[$index],
                    'service_date' => $request->service_date[$index],
                    'price_pax' => $price_pax,
                    'price_total' => $price_total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
    
                $total_optional_rate += $price_total;
            }
        }
        if (!empty($optional_rate_order)) {
            OptionalRateOrder::insert($optional_rate_order);
        }
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
        $final_price = $order->price_total + $total_optional_rate - $order->discounts - $order->bookingcode_disc - $promotion_discount + $order->airport_shuttle_price;
        $order->update([
            "optional_price" => $total_optional_rate,
            "final_price" => $final_price,
        ]);
        OrderLog::create([
            "order_id" => $order->id,
            "action" => "Create order additional charge",
            "url" => $request->getClientIp(),
            "method" => "Create",
            "agent" => $agent->name,
            "admin" => $agent->id,
        ]);
        return redirect()->route('view.edit-order-hotel', ['id' => $order->id])
            ->with('success', __('messages.Your order has been updated'));
    }


     // FUNCTION UPDATE ORDER ADDITIONAL CHARGE ============================================================================> OK
     public function func_update_order_additional_charge(Request $request,$id){
        $now = Carbon::now();
        $cacheTTL = 3600;
        $tax = Cache::remember('tax_1', $cacheTTL, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', $cacheTTL, fn() => UsdRates::where('name', 'USD')->first());
        $agent = Auth::user();
        $request->validate([
            'optional_rate_order_id' => 'array',
            'optional_rate_id' => 'required|array',
            'number_of_guest' => 'required|array',
            'service_date' => 'required|array',
        ]);
        $order = Orders::with(['optional_rate_orders.optional_rate'])
            ->where('sales_agent', $agent->id)
            ->where('checkin', '>=', $now)
            ->whereIn('status', ['Draft', 'Invalid'])
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect("/hotel-promo-edit-order/$order->id")
                ->with('warning', __('messages.Your order cannot be changed'));
        }
        if (in_array($order->status, ['Draft', 'Invalid'])){
            DB::transaction(function () use ($request, $order, $usdrates, $tax, $agent, $id) {
                $service = $order->service;
                $total_optional_rate = 0;
                $add_optional_rate_order = [];
                $optional_rate_orders = collect($request->optional_rate_order_id ?? []);
                $optional_rate_ids = collect($request->optional_rate_id);
                $number_of_guests = collect($request->number_of_guest);
                $service_dates = collect($request->service_date);
                foreach ($optional_rate_ids as $index => $optional_rate_id) {
                    $optional_rate_order = OptionalRateOrder::find($optional_rate_orders[$index] ?? null);
                    $optional_rate = OptionalRate::find($optional_rate_id);
                    if (!$optional_rate) {
                        continue;
                    }
                    $price_pax = $optional_rate->calculatePrice($usdrates, $tax);
                    $price_total = $price_pax * $number_of_guests[$index];
                    if ($optional_rate_order) {
                        $optional_rate_order->update([
                            'optional_rate_id' => $optional_rate_id,
                            'number_of_guest' => $number_of_guests[$index],
                            'service_date' => $service_dates[$index],
                            'price_pax' => $price_pax,
                            'price_total' => $price_total,
                        ]);
                    } else {
                        $add_optional_rate_order[] = [
                            'optional_rate_id' => $optional_rate_id,
                            'service' => $service,
                            'order_id' => $id,
                            'number_of_guest' => $number_of_guests[$index],
                            'service_date' => $service_dates[$index],
                            'price_pax' => $price_pax,
                            'price_total' => $price_total,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    $total_optional_rate += $price_total;
                }
                if (!empty($add_optional_rate_order)) {
                    OptionalRateOrder::insert($add_optional_rate_order);
                }
                $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
                $final_price = $order->price_total + $total_optional_rate - $order->discounts - $order->bookingcode_disc - $promotion_discount + $order->airport_shuttle_price;
                $order->update([
                    "optional_price" => $total_optional_rate,
                    "final_price" => $final_price,
                ]);
                OrderLog::create([
                    "order_id" => $order->id,
                    "action" => "Update order optional rate",
                    "url" => request()->ip(),
                    "method" => "Update",
                    "agent" => $agent->name,
                    "admin" => Auth::id(),
                ]);
            });
            return redirect()->route('view.edit-order-hotel', ['id' => $order->id])->with('success',__('messages.Your order has been updated'));
        }else{
            return redirect("/orders")->with('warning', __('messages.Your order cannot be changed'));
        }
    }


    // FUNCTION DELETE ORDER ADDITIONAL CHARGE =============================================================================> OK
    public function func_delete_order_additional_charge($id)
    {
        $user = Auth::user();
        $order_optional_rate = OptionalRateOrder::findOrFail($id);
        $order_id = $order_optional_rate->order_id;
        $order = Orders::where('id',$order_id)->first();
        if (!$order) {
            return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
        }
        $order_optional_rate->delete();
        $optional_rate_price = OptionalRateOrder::where('orders_id', $order_id)->sum('price_total');
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
        $final_price = $order->price_total + $optional_rate_price - $order->discounts - $order->bookingcode_disc - $promotion_discount + $order->airport_shuttle_price;
        $order->update([
            "optional_price" => $optional_rate_price,
            "final_price" => $final_price,
        ]);
        return response()->json(['success' => true]);
        
    }    

    // FUNCTION SUBMIT ORDER ================================================================================================> OK
    public function func_submit_order_hotel(Request $request, $id)
    {
        $now = Carbon::now();
        $cacheTTL = 3600;
        $tax = Cache::remember('tax_1', $cacheTTL, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', $cacheTTL, fn() => UsdRates::where('name', 'USD')->first());
        $agent = Auth::user();
        $order = Orders::select('id', 'service', 'service_id', 'checkin', 'checkout', 'number_of_guests','price_total','optional_price','additional_service_price','airport_shuttle_price','discounts','bookingcode_disc','promotion_disc','final_price')
            ->where('sales_agent', $agent->id)
            ->accommodationService()
            ->where(function ($query) {
                $query->where('status', 'Draft')->orWhere('status', 'Invalid');
            })
            ->where('id', $id)
            ->first();

        if (!$order) {
            return redirect("/orders")->with('warning', __('messages.Your order cannot be changed'));
        }

        $hotel = Hotels::find($order->service_id);
        $number_of_guests = $order->number_of_guests;

        DB::transaction(function () use ($request, $order, $hotel, $number_of_guests, $agent) {
            $airportShuttleRows = $this->buildAirportShuttleRowsFromRequest($request, $hotel, $number_of_guests, $order->checkin, $order->checkout);
            $primaryShuttleFields = $this->getPrimaryAirportShuttleFields($airportShuttleRows);
            $this->create_order_airport_shuttle($order, $airportShuttleRows);
            $additional_service_price = $order->additional_service_price ? array_sum(json_decode($order->additional_service_price)) : 0;
            $airport_shuttle_price = collect($airportShuttleRows)->sum('price');
            $promotion_disc = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
            $final_price = ($order->price_total + $order->optional_price + $additional_service_price + $airport_shuttle_price) - $order->discounts - $order->bookingcode_disc - $promotion_disc;
            $order->update([
                "airport_shuttle_price" => $airport_shuttle_price ?: null,
                "final_price" => $final_price,
                "arrival_flight" => $primaryShuttleFields['arrival_flight'],
                "arrival_time" => $primaryShuttleFields['arrival_time'],
                "airport_shuttle_in" => $primaryShuttleFields['airport_shuttle_in'],
                "departure_flight" => $primaryShuttleFields['departure_flight'],
                "departure_time" => $primaryShuttleFields['departure_time'],
                "airport_shuttle_out" => $primaryShuttleFields['airport_shuttle_out'],
                "note" => $request->note,
                "request_quotation" => $request->request_quotation,
                "status" => 'Pending',
            ]);
            OrderLog::create([
                "order_id" => $order->id,
                "action" => "Submit Order ".$order->service,
                "url" => request()->ip(),
                "method" => "Submit",
                "agent" => $agent->name,
                "admin" => Auth::id(),
            ]);
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id, $request->request_quotation));
        });
        return redirect()->route('view.detail-order-hotel', ['id' => $id])->with('success', __('messages.Your order has been submitted'));
    }


    
    // VIEW DETAIL ORDER HOTEL ===============================================================================================> OK
    public function detail_order_hotel($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::with(['optional_rate_orders', 'reservations.invoice'])
            ->where('sales_agent', $user_id)
            ->where(function ($query) {
                $query->accommodationService()
                    ->orWhere('service', Orders::PUBLIC_ACTIVITY_SERVICE);
            })
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order || in_array($order->status, ["Draft", "Invalid", "Rejected"])) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::firstWhere('name', 'USD'));
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $room = HotelRoom::find($order->subservice_id);
        $hotel = Hotels::with(['optionalrates'])->where('id',$order->service_id)->first();
        if ($hotel) {
            $optional_rate = $hotel->optionalrates;
        }else{
            $optional_rate = NULL;
        }
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);
        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $optional_rate_orders = $order->optional_rate_orders;
        $optionalServiceTotalPrice = $optional_rate_orders->sum('price_total');
        $reservation = Reservation::find($order->rsv_id);
        $invoice = InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $discounts = [
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langBenefits = match (config('app.locale')) {
            'zh' => 'benefits_traditional',
            'zh-CN' => 'benefits_simplified',
            default => 'benefits',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        return view('frontend.home.orders.details.legacy', array_merge([
            'order' => $order,
            'tax' => $tax,
            'now' => $now,
            'usdrates' => $usdrates,
            'business' => $business,
            'invoice' => $invoice,
            'reservation' => $reservation,
            'hotel' => $hotel,
            'room' => $room,
            'airport_shuttles' => $airport_shuttles,
            'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
            'total_price_airport_shuttle' => $total_price_airport_shuttle,
            'optional_rate' => $optional_rate,
            'optional_rate_orders' => $optional_rate_orders,
            'additionalServices' => $additionalServices,
            'additional_service_total_price' => $additional_service_total_price,
            'optionalServiceTotalPrice' => $optionalServiceTotalPrice,
            'total_promotion_disc' => $total_promotion_disc,
            'filteredDiscounts' => $filteredDiscounts,
            'normal_price' => $normal_price,
            'receipts' => $receipts,
            'agent' => $agent,
            'langInclude'=>$langInclude,
            'langExclude'=>$langExclude,
            'langBenefits'=>$langBenefits,
            'langAdditionalInfo'=>$langAdditionalInfo,
            'langCancellationPolicy'=>$langCancellationPolicy,
        ], $decodedData->toArray()));
    }


    // public function detail_order($id)
    // {   
    //     $user = Auth::user();
    //     $order = Orders::with(['guests', 'activePricingSnapshot'])
    //         ->where('sales_agent', $user->id)
    //         ->where('id', $id)
    //         ->first();
    //     if (!$order) {
    //         return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    //     }
    //     $business = BusinessProfile::where('id','=',1)->first();
    //     $optional_rate_order = OptionalRateOrder::all();
    //     $optionalrates = OptionalRate::all();
    //     if ($order->status == "Draft") {
    //         return redirect('/orders')->with('warning',"Submit your order to see order detail");
    //     }else{
    //         return view('frontend.home.orders.detail',compact('order'),[
    //             'usdrates'=>$usdrates,
    //             'order'=> $order,
    //             'business'=>$business,
    //             'optional_rate_order'=>$optional_rate_order,
    //             'optionalrates'=>$optionalrates,
    //         ]);
    //     }
        
    // }



    // USER ADD ORDER ---------------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_add_order(Request $request){
        if ($request->input('service') === Orders::PUBLIC_TOUR_SERVICE) {
            throw ValidationException::withMessages([
                'service' => 'Tour Package orders must use the authoritative Tour Package order flow.',
            ]);
        }

        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $sales_agent = $request->user_id;
            $user_id = Auth::user()->id;
            $agent = User::where('id',$user_id)->first();
            $email = $agent->email;
            $name = $agent->name;
            $status = "Pending";
        }else{
            $sales_agent = Auth::user()->id;
            $user_id = Auth::user()->id;
            $name= Auth::user()->name;
            $email= Auth::user()->email;
            $status = "Draft";
        }

        $now = Carbon::now();
        $nog = $request->number_of_guests;
        $service = $request->service;
        $service_type = $request->service_type;
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $idrrates = UsdRates::where('name','IDR')->first();
        $tax = Tax::where('id',1)->first();
        $prms = Promotion::where('status','Active')
        ->where("periode_start",'<=',$now)
        ->where('periode_end','>=',$now)
        ->get();
        if(count($prms)>0){
            $p_name = [];
            $p_disc = [];
            foreach ($prms as $prm) {
                array_push($p_name,$prm->name);
                array_push($p_disc,$prm->discounts);
            }
            $promotion_total_disc = array_sum($p_disc);
            $promotion = json_encode($p_name);
            $promotion_disc = json_encode($p_disc);
        }else{
            $promotion_total_disc = 0;
            $promotion= null;
            $promotion_disc = null;
        }
        
        $bcode = BookingCode::where('id',$request->bookingcode_id)->first();
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date))." ".date('H.i',strtotime($request->wedding_date));
        if (isset($bcode)) {
            if ($bcode->expired_date > $now) {
                if ($bcode->amount == 0) {
                    $bookingcode = $bcode->code;
                    $bookingcode_disc = $bcode->discounts;
                    $bookingcode_status = "Valid";
                }elseif ($bcode->used < $bcode->amount) {
                    $ordercode = Orders::where('sales_agent',$user_id)
                    ->where('bookingcode', $bcode->code)->first();
                    if (isset($ordercode)) {
                        $bookingcode = null;
                        $bookingcode_disc = 0;
                        $bookingcode_status = "Used"; //code telah digunakan
                    }else{
                        $bookingcode = $bcode->code;
                        $bookingcode_disc = $bcode->discounts;
                        $bookingcode_status = "Valid";
                    }
                }else{
                    $bookingcode = null;
                    $bookingcode_disc = 0;
                    $bookingcode_status = "Expired"; //code habis digunakan
                }
            }else{
                $bookingcode = null;
                $bookingcode_disc = 0;
                $bookingcode_status = "Expired"; //code kedaluarsa
            }
        }else{
            $bookingcode = null;
            $bookingcode_disc = 0;
            $bookingcode_status = "Invalid"; //code habis digunakan
        }

        if ($service == "Tour Package") {
            throw new \LogicException('Tour Package must use the authoritative order flow.');
        } elseif ($service == "Activity") {
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $number_of_guests = $request->number_of_guests;
            $extra_bed = $request->extra_bed;
            $price_total = $request->price_pax * $nog;
            $checkin = date('Y-m-d', strtotime($request->travel_date));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $duration = $request->duration;
            $price_pax = $request->price_pax;
            $kick_back = $request->kick_back;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $travel_date = $checkin;
            $extra_bed_price = $request->extra_bed_price;
            $normal_price = $price_total;
            $final_price = $normal_price - $bookingcode_disc - $promotion_total_disc;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $pickup_name = null;
            $orderWedding_id = "";
        } elseif ($service == "Hotel") {
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,0);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        if (isset($extrabeds->contract_rate)) {
                            $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                            $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                            $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                            array_push($extra_bed_id_price,$price_extra_bed);
                        }else{
                            array_push($extra_bed_id_price,0);
                        }
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $extra_bed_id = json_encode($request->extra_bed_id);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $number_of_guests_room_array = array_sum($request->number_of_guests);
            $number_of_guests_room = json_encode($request->number_of_guests);
            $number_of_guests = json_encode($number_of_guests_room_array);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $extra_bed_sum= array_sum($extra_bed_id_price);
            $extra_bed_total = json_encode($extra_bed_sum);
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $pickup_name = null;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $kick_back = $request->kick_back;
            $normal_price = $request->normal_price;
            $price_pax = $normal_price / $duration;
            $price_total = ($normal_price * $number_of_room) + $extra_bed_sum - $kick_back ;
            $final_price = $price_total - $bookingcode_disc - $promotion_total_disc;
            $orderWedding_id = "";
        } elseif ($service == "Hotel Promo") {
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,null);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                        $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                        $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                        array_push($extra_bed_id_price,$price_extra_bed);
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $extra_bed_id = json_encode($request->extra_bed_id);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $number_of_guests_room_array = array_sum($request->number_of_guests);
            $number_of_guests_room = json_encode($request->number_of_guests);
            $number_of_guests = json_encode($number_of_guests_room_array);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $extra_bed_sum= array_sum($extra_bed_id_price);
            $extra_bed_total = json_encode($extra_bed_sum);
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $pickup_name = null;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $kick_back = $request->kick_back;
            $normal_price = $request->normal_price;
            $price_pax = $normal_price / $duration;
            $price_total = ($normal_price * $number_of_room) + $extra_bed_sum ;
            $final_price = $price_total - $bookingcode_disc - $promotion_total_disc;
            $orderWedding_id = "";
            if (isset($request->include)) {
                // $inc = json_decode($request->include);
                $inc = json_decode($request->include);
                if (isset($inc)) {
                    if (count($inc)>0) {
                        $include = implode($inc);
                    }else{
                        $include = $request->include;
                    }
                }else{
                    $include = $request->include;
                }
                $include = $request->include;
            }else{
                $include = $request->include;
            }
            if (isset($request->benefits)) {
                $bnf = json_decode($request->benefits);
                if (isset($bnf)) {
                    if (count($bnf)>0) {
                        $benefits = implode($bnf);
                    }else{
                        $benefits = $request->benefits;
                    }
                }else{
                    $benefits = $request->benefits;
                }
                $benefits = $request->benefits;
            }else{
                $benefits = $request->benefits;
            }
            if (isset($request->additional_info)) {
                $addinf = json_decode($request->additional_info);
                if (isset($addinf)) {
                    if (count($addinf)>0) {
                        $additional_info = implode($addinf);
                    }else{
                        $additional_info = $request->cancellation_policy;
                    }
                }else{
                    $additional_info = $request->additional_info;
                }
            }else{
                $additional_info = $request->additional_info;
            }
            if (isset($request->cancellation_policy)) {
                $canpol = json_decode($request->cancellation_policy);
                if (isset($canpol)) {
                    if (count($canpol)>0) {
                        $cancellation_policy = implode($canpol);
                    }else{
                        $cancellation_policy = $request->cancellation_policy;
                    }
                }else{
                    $cancellation_policy = $request->cancellation_policy;
                }
            }else{
                $cancellation_policy = $request->cancellation_policy;
            }

        } elseif ($service == "Hotel Package") {
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,null);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                        $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                        $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                        array_push($extra_bed_id_price,$price_extra_bed);
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,null);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                        $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                        $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                        array_push($extra_bed_id_price,$price_extra_bed);
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $extra_bed_id = json_encode($request->extra_bed_id);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $number_of_guests_room_array = array_sum($request->number_of_guests);
            $number_of_guests_room = json_encode($request->number_of_guests);
            $number_of_guests = json_encode($number_of_guests_room_array);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $extra_bed_sum= array_sum($extra_bed_id_price);
            $extra_bed_total = json_encode($extra_bed_sum);
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $pickup_name = null;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $kick_back = $request->kick_back;
            $normal_price = $request->normal_price;
            $price_pax = $normal_price / $duration;
            $price_total = ($normal_price * $number_of_room) + $extra_bed_sum ;
            $final_price = $price_total - $bookingcode_disc - $promotion_total_disc;
            $orderWedding_id = "";
        } elseif ($service == "Transport") {
            $checkin = date('Y-m-d H.i', strtotime($request->travel_date));
            if ($service_type == "Daily Rent") {
                $price_pax = $request->price_pax;
                $normal_price = $request->normal_price * $request->duration;
                $price_total = $request->price_total * $request->duration;
                $final_price = $price_total - $promotion_total_disc - $bookingcode_disc;
                $checkout = date('Y-m-d H.i',strtotime('+'.$request->duration.'days',strtotime($checkin)));
            } else {
                $normal_price = $request->normal_price;
                $price_pax = $request->price_pax;
                $price_total = $request->price_total;
                $final_price = $price_total - $promotion_total_disc - $bookingcode_disc;
                $checkout = date('Y-m-d H.i', strtotime('+'.$request->duration.'hours',strtotime($checkin)));
            }
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $extra_bed = $request->extra_bed;
            $number_of_guests = $request->number_of_guests;
            $duration = $request->duration;
            $pickup_name = null;
            $kick_back = $request->kick_back;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $extra_bed_price = $request->extra_bed_price;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $orderWedding_id = "";
        } elseif ($service == "Wedding Package") {
            $brides =new Brides([
                "bride"=>$request->bride_name,
                "bride_chinese"=>$request->bride_chinese,
                "bride_contact"=>$request->bride_contact,
                "groom"=>$request->groom_name,
                "groom_chinese"=>$request->groom_chinese,
                "groom_contact"=>$request->groom_contact,
            ]);
            $brides->save();
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests = $request->number_of_guests;
            $checkin = date('Y-m-d', strtotime($request->checkin));
            if ($request->duration == "1D"){
                $checkout = date('Y-m-d',strtotime($checkin));
            } elseif ($request->duration == "2D/1N"){
                $checkout = date('Y-m-d',strtotime('+1 days',strtotime($checkin)));
            } elseif ($request->duration == "3D/2N"){
                $checkout = date('Y-m-d',strtotime('+2 days',strtotime($checkin)));
            } elseif ($request->duration == "4D/3N"){
                $checkout = date('Y-m-d',strtotime('+3 days',strtotime($checkin)));
            } elseif ($request->duration == "5D/4N"){
                $checkout = date('Y-m-d',strtotime('+4 days',strtotime($checkin)));
            } elseif ($request->duration == "6D/5N"){
                $checkout = date('Y-m-d',strtotime('+5 days',strtotime($checkin)));
            } elseif ($request->duration == "7D/6N"){
                $checkout = date('Y-m-d',strtotime('+6 days',strtotime($checkin)));
            } elseif ($request->duration == "8D/7N"){
                $checkout = date('Y-m-d',strtotime('+7 days',strtotime($checkin)));
            } else {
                $checkout = date('Y-m-d',strtotime('+8 days',strtotime($checkin)));
            }

            
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $extra_bed = $request->extra_bed;
            $in=Carbon::parse($checkin);
            $out=Carbon::parse($checkout);
            $duration = $in->diffInDays($out);
            $pickup_name = $brides->id;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $wedding = Weddings::where('id',$request->wedding_id)->first();
            $hotel = Hotels::where('id',$wedding->hotel_id)->firstOrFail();
            if ($wedding->fixed_services_id !== null or $wedding->fixed_services_id) {
                $wed_fixed_services_id = json_decode($wedding->fixed_services_id);
                $fixed_services_p = [];
                if ($wed_fixed_services_id) {
                    foreach ($wed_fixed_services_id as $w_fixed_service_id) {
                        $wedding_fixed_services = VendorPackage::where('id',$w_fixed_service_id)->first();
                        if ($wedding_fixed_services) {
                            array_push($fixed_services_p,$wedding_fixed_services->publish_rate);
                        }
                    }
                    if ($fixed_services_p) {
                        $fixed_service_price = array_sum($fixed_services_p);
                    }else{
                        $fixed_service_price = 0;
                    }
                }else{
                    $fixed_service_price = 0;
                }
            }else{
                $fixed_service_price = 0;
            }
            if ($request->wedding_venue_id !== null or $request->wedding_venue_id) {
                $wedding_venue_id = json_encode($request->wedding_venue_id);
                $wed_venue_id = $request->wedding_venue_id;
                $venue_p = [];
                if ($wed_venue_id) {
                    foreach ($wed_venue_id as $w_venue_id) {
                        $wedding_venue = VendorPackage::where('id',$w_venue_id)->first();
                        if ($wedding_venue) {
                            array_push($venue_p,$wedding_venue->publish_rate);
                        }
                    }
                    if ($venue_p) {
                        $venue_price = array_sum($venue_p);
                    }else{
                        $venue_price = 0;
                    }
                }else{
                    $venue_price = 0;
                }
            }else{
                $venue_price = 0;
                $wedding_venue_id = $request->wedding_venue_id;
            }
            // WEDDING MAKEUP
            if ($request->makeup_id !== null or $request->makeup_id) {
                $makeup_id = json_encode($request->makeup_id);
                $wed_makeup_id = $request->makeup_id;
                if ($wed_makeup_id) {
                    $makeup_p = [];
                    foreach ($wed_makeup_id as $w_makeup_id) {
                        $wedding_makeup = VendorPackage::where('id',$w_makeup_id)->first();
                        if ($wedding_makeup) {
                            array_push($makeup_p,$wedding_makeup->publish_rate);
                        }
                    }
                    if ($makeup_p) {
                        $makeup_price = array_sum($makeup_p);
                    }else{
                        $makeup_price = 0;
                    }
                }else{
                    $makeup_price = 0;
                }
            }else{
                $makeup_price = 0;
                $makeup_id = $request->makeup_id;
            }
            // WEDDING SUITES AND VILLAS
            if ($request->suite_and_villas_id !== null or $request->suite_and_villas_id) {
                $suite_and_villas_id = json_encode($request->suite_and_villas_id);
                $wed_room_id = $request->suite_and_villas_id;
                if ($wed_room_id) {
                    $room_p = [];
                    foreach ($wed_room_id as $w_room_id) {
                        $hotel_room_price = HotelPrice::where('rooms_id',$w_room_id)
                        ->where('start_date','<',$wedding_date)
                        ->where('end_date','>',$wedding_date)
                        ->first();
                        if ($hotel_room_price) {
                            $cr_mr_room = ($hotel_room_price->contract_rate / $usdrates->rate) + $hotel_room_price->markup;
                            $room_tax = $cr_mr_room * ($tax->tax/100);
                            $hotel_r_p = ceil($cr_mr_room + $room_tax) * $duration;
                            array_push($room_p,$hotel_r_p);
                        }
                    }
                    if ($room_p) {
                        $room_price = array_sum($room_p);
                    }else{
                        $room_price = 0;
                    }
                }else{
                    $room_price = 0;
                }
            }else{
                $room_price = 0;
                $suite_and_villas_id = $request->suite_and_villas_id;
            }
            // WEDDING DOCUMENTATION
            if ($request->documentations_id !== null or $request->documentations_id) {
                $documentations_id = json_encode($request->documentations_id);
                $wed_documentations_id = $request->documentations_id;
                if ($wed_documentations_id) {
                    $documentations_p = [];
                    foreach ($wed_documentations_id as $w_documentations_id) {
                        $wedding_documentations = VendorPackage::where('id',$w_documentations_id)->first();
                        if ($wedding_documentations) {
                            array_push($documentations_p,$wedding_documentations->publish_rate);
                        }
                    }
                    if ($documentations_p) {
                        $documentation_price = array_sum($documentations_p);
                    }else{
                        $documentation_price = 0;
                    }
                }else{
                    $documentation_price = 0;
                }
            }else{
                $documentation_price = 0;
                $documentations_id = $request->documentations_id;
            }
            // WEDDING DECORATION
            if ($request->decorations_id !== null or $request->decorations_id) {
                $decorations_id = json_encode($request->decorations_id);
                $wed_decorations_id = $request->decorations_id;
                if ($wed_decorations_id) {
                    $decorations_p = [];
                    foreach ($wed_decorations_id as $w_decorations_id) {
                        $wedding_decorations = VendorPackage::where('id',$w_decorations_id)->first();
                        if ($wedding_decorations) {
                            array_push($decorations_p,$wedding_decorations->publish_rate);
                        }
                    }
                    if ($decorations_p) {
                        $decoration_price = array_sum($decorations_p);
                    }else{
                        $decoration_price = 0;
                    }
                }else{
                    $decoration_price = 0;
                }
            }else{
                $decoration_price = 0;
                $decorations_id = $request->decorations_id;
            }
            // WEDDING DINNER VENUE
            if ($request->dinner_venue_id !== null or $request->dinner_venue_id) {
                $dinner_venue_id = json_encode($request->dinner_venue_id);
                $wed_dinner_venue_id = $request->dinner_venue_id;
                if ($wed_dinner_venue_id) {
                    $dinner_venue_p = [];
                    foreach ($wed_dinner_venue_id as $w_dinner_venue_id) {
                        $wedding_dinner_venue = VendorPackage::where('id',$w_dinner_venue_id)->first();
                        if ($wedding_dinner_venue) {
                            array_push($dinner_venue_p,$wedding_dinner_venue->publish_rate);
                        }
                    }
                    if ($dinner_venue_p) {
                        $dinner_venue_price = array_sum($dinner_venue_p);
                    }else{
                        $dinner_venue_price = 0;
                    }
                }else{
                    $dinner_venue_price = 0;
                }
            }else{
                $dinner_venue_price = 0;
                $dinner_venue_id = $request->dinner_venue_id;
            }
            // WEDDING ENTERTAINMENT
            if ($request->entertainments_id !== null or $request->entertainments_id) {
                $entertainments_id = json_encode($request->entertainments_id);
                $wed_entertainment_id = $request->entertainments_id;
                if ($wed_entertainment_id) {
                    $entertainment_p = [];
                    foreach ($wed_entertainment_id as $w_entertainment_id) {
                        $wedding_entertainment = VendorPackage::where('id',$w_entertainment_id)->first();
                        if ($wedding_entertainment) {
                            array_push($entertainment_p,$wedding_entertainment->publish_rate);
                        }
                    }
                    if ($entertainment_p) {
                        $entertainment_price = array_sum($entertainment_p);
                    }else{
                        $entertainment_price = 0;
                    }
                }else{
                    $entertainment_price = 0;
                }
            }else{
                $entertainment_price = 0;
                $entertainments_id = $request->entertainments_id;
            }

            // WEDDING TRANSPORT
            if ($request->transport_id !== null or $request->transport_id) {
                $transport_id = json_encode($request->transport_id);
                $wed_transport_id = $request->transport_id;
                if ($wed_transport_id) {
                    $transport_p = [];
                    foreach ($wed_transport_id as $w_transport_id) {
                        $wedding_transport = TransportPrice::where('transports_id',$w_transport_id)
                        ->where('type','Airport Shuttle')
                        ->where('duration',$hotel->airport_duration)
                        ->first();
                        if ($wedding_transport) {
                            $trans_cr_mr = ceil($wedding_transport->contract_rate / $usdrates->rate)+$wedding_transport->markup;
                            $trans_tax = $trans_cr_mr * ($tax->tax/100);
                            $transport_p_price = ceil($trans_cr_mr + $trans_tax);
                            array_push($transport_p,$transport_p_price);
                        }
                    }
                    if ($transport_p) {
                        $transport_price = array_sum($transport_p);
                    }else{
                        $transport_price = 0;
                    }
                }else{
                    $transport_price = 0;
                }
            }else{
                $transport_price = 0;
                $transport_id = $request->transport_id;
            }

            // WEDDING OTHER
            if ($request->other_service_id !== null or $request->other_service_id) {
                $other_service_id = json_encode($request->other_service_id);
                $wed_other_id = $request->other_service_id;
                if ($wed_other_id) {
                    $other_p = [];
                    foreach ($wed_other_id as $w_other_id) {
                        $wedding_other = VendorPackage::where('id',$w_other_id)->first();
                        if ($wedding_other) {
                            array_push($other_p,$wedding_other->publish_rate);
                        }
                    }
                    if ($other_p) {
                        $other_price = array_sum($other_p);
                    }else{
                        $other_price = 0;
                    }
                }else{
                    $other_price = 0;
                }
            }else{
                $other_price = 0;
                $other_service_id = $request->other_service_id;
            }

            $kick_back_per_pax = $request->kick_back_per_pax;
            $extra_bed_price = $request->extra_bed_price;
            $price_pax = $request->price_pax;
            $kick_back = $request->kick_back;
            $normal_price = $wedding->markup + $fixed_service_price + $venue_price + $makeup_price + $room_price + $documentation_price  + $decoration_price + $dinner_venue_price + $entertainment_price + $transport_price + $other_price - $bookingcode_disc - $promotion_total_disc;
            $price_total = $normal_price;
            $final_price = $normal_price;
            $markup = $wedding->markup;

            $orderWedding =new OrderWedding([
                "wedding_id"=>$wedding->id,
                "hotel_id"=>$wedding->hotel_id,
                "duration"=>$duration,
                "wedding_date"=>$wedding_date,
                "brides_id"=>$brides->id,
                "number_of_invitation"=>$number_of_guests,

                "wedding_fixed_service_id"=>$wedding->fixed_services_id,
                "wedding_venue_id"=>$wedding_venue_id,
                "wedding_makeup_id"=>$makeup_id,
                "wedding_room_id"=>$suite_and_villas_id,
                "wedding_documentation_id"=>$documentations_id,
                "wedding_decoration_id"=>$decorations_id,
                "wedding_dinner_venue_id"=>$dinner_venue_id,
                "wedding_entertainment_id"=>$entertainments_id,
                "wedding_transport_id"=>$transport_id,
                "wedding_other_id"=>$other_service_id,

                "fixed_service_price"=>$fixed_service_price,
                "venue_price"=>$venue_price,
                "makeup_price"=>$makeup_price,
                "room_price"=>$room_price,
                "documentation_price"=>$documentation_price,
                "decoration_price"=>$decoration_price,
                "dinner_venue_price"=>$dinner_venue_price,
                "entertainment_price"=>$entertainment_price,
                "transport_price"=>$transport_price,
                "other_price"=>$other_price,
                "markup"=>$markup,
            ]);
            // dd($orderWedding);
            $orderWedding->save();
            $orderWedding_id = $orderWedding->id;
        } else {
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $extra_bed = $request->extra_bed;
            $number_of_guests = $request->number_of_guests;
            $price_total = $request->price_pax;
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->travel_date));
            $duration = $request->duration;
            $price_pax = $request->price_pax;
            $kick_back = $request->kick_back;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $extra_bed_price = $request->extra_bed_price;
            $final_price = $request->final_price - $promotion_total_disc;
            $normal_price = $request->normal_price;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $orderWedding_id = "";
            $order_tax = 0;
        }
        $airport_shuttle_in = $request->airport_shuttle_in;
        $airport_shuttle_out = $request->airport_shuttle_out;
        $travel_date = date('Y-m-d H.i', strtotime($request->travel_date));
        $extra_bed_id = json_encode($request->extra_bed_id);
        $price_id = $request->price_id;
        $order =new Orders([
            "user_id"=>$user_id,
            "name"=>$name,
            "email"=>$email,
            "orderno"=>$request->orderno,
            "service"=>$request->service,
            "service_id"=>$request->service_id,
            "service_type"=>$request->service_type,
            "servicename" =>$request->servicename,
            "subservice"=>$request->subservice,
            "subservice_id"=>$request->subservice_id,
            "package_name"=>$request->package_name,
            "promo_name"=>$request->promo_name,
            "book_period_start"=>$request->book_period_start,
            "book_period_end"=>$request->book_period_end,
            "period_start"=>$request->period_start,
            "period_end"=>$request->period_end,
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "number_of_room"=>$number_of_room,
            "guest_detail"=>$guest_detail,
            "request_quotation"=>$request->request_quotation,
            "extra_bed"=>$extra_bed,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "extra_time"=>$request->extra_time,
            "price_id"=>$request->price_id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "src"=>$request->src,
            "dst"=>$request->dst,
            "sales_agent"=>$sales_agent,
            "airport_shuttle_in"=>$airport_shuttle_in,
            "airport_shuttle_out"=>$airport_shuttle_out,
            "pickup_name"=>$pickup_name,
            "pickup_date"=>$checkin,
            "pickup_location"=>$request->pickup_location,
            "dropoff_date"=>$checkout,
            "dropoff_location"=>$request->dropoff_location,
            "bookingcode"=>$bookingcode,
            "bookingcode_disc"=>$bookingcode_disc,
            "travel_date"=>$travel_date,
            "tour_type"=>$request->tour_type,
            "location"=>$request->location,
            "capacity"=>$request->capacity,
            "destinations" =>$request->destinations,
            "include" =>$include,
            "benefits" =>$benefits,
            "additional_info"=>$additional_info,
            "cancellation_policy"=>$cancellation_policy,
            "duration"=>$duration,
            "price_total" =>$price_total, 
            "promotion" =>$promotion, 
            "promotion_disc" =>$promotion_disc, 
            "final_price" =>$final_price, 
            "usd_rate" =>$usdrates->rate, 
            "cny_rate" =>$cnyrates->rate, 
            "twd_rate" =>$twdrates->rate, 
            "normal_price" =>$normal_price,
            "price_pax" =>$price_pax,
            "kick_back" =>$kick_back, 
            "kick_back_per_pax" =>$kick_back_per_pax, 
            "status"=>$status,
            "itinerary"=>$request->itinerary,
            "wedding_order_id"=>$orderWedding_id,
            "wedding_date"=>$wedding_date,
            "bride_name"=>$request->bride_name,
            "groom_name"=>$request->groom_name,
            
            "arrival_flight"=>$request->arrival_flight,
            "arrival_time"=>$request->arrival_time,
            "departure_flight"=>$request->departure_flight,
            "departure_time"=>$request->departure_time,
            "note"=>$request->note,
        ]);
        $order->save();
        if (isset($bcode)) {
            $cbcode = $bcode->used + 1;
            $bcode->update([
                "used"=>$cbcode,
            ]);
        }
        $note = "Created Order with order no: ".$request->orderno;
        if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package") {
            $hotel = Hotels::where('id',$order->service_id)->first();
            $transport_in = Transports::where('id',$request->airport_shuttle_in)->first();
            if ($transport_in) {
                $transport_in_price = TransportPrice::where('transports_id',$transport_in->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                if ($transport_in_price) {
                    $c_price_usd = ceil($transport_in_price->contract_rate/$usdrates->rate);
                    $c_price_markup = $c_price_usd + $transport_in_price->markup;
                    $c_price_tax = ceil($c_price_markup*($tax->tax/100));
                    $airport_shuttle_in_price = $c_price_markup + $c_price_tax;
                }else{
                    $airport_shuttle_in_price = 0;
                }
                $airport_shuttle_in =new AirportShuttle([
                    "date"=>$request->arrival_time,
                    "transport"=>$transport_in->name,
                    "src"=>"Airport",
                    "dst"=>$hotel->name,
                    "duration"=>$hotel->airport_duration,
                    "distance"=>$hotel->airport_distance,
                    "price"=>$airport_shuttle_in_price,
                    "order_id"=>$order->id,
                    "nav"=>"In",
                ]);
                $airport_shuttle_in->save();
                
            }
            $transport_out = Transports::where('id',$request->airport_shuttle_out)->first();
            if ($transport_out) {
                $transport_out_price = TransportPrice::where('transports_id',$transport_out->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                if ($transport_out_price) {
                    $o_price_usd = ceil($transport_out_price->contract_rate/$usdrates->rate);
                    $o_price_markup = $o_price_usd + $transport_out_price->markup;
                    $o_price_tax = ceil($o_price_markup*($tax->tax/100));
                    $airport_shuttle_out_price = $o_price_markup + $o_price_tax;
                }else{
                    $airport_shuttle_out_price = 0;
                }
                if ($request->airport_shuttle_out) {
                    $airport_shuttle_out =new AirportShuttle([
                        "date"=>$request->departure_time,
                        "transport"=>$transport_out->name,
                        "src"=>$hotel->name,
                        "dst"=>"Airport",
                        "duration"=>$hotel->airport_duration,
                        "distance"=>$hotel->airport_distance,
                        "price"=>$airport_shuttle_out_price,
                        "order_id"=>$order->id,
                        "nav"=>"Out",
                    ]);
                    $airport_shuttle_out->save();
                }
            }
          
        }

        $user_log =new UserLog([
            "action"=>$request->action,
            "service"=>$request->service,
            "subservice"=>$request->subservice,
            "subservice_id"=>$order->id,
            "page"=>$request->page,
            "user_id"=>$user_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        $subject = $request->orderno;
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id,$rquotation));
            return redirect('/orders-admin-'.$order->id)->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect("/edit-order/$order->id")->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }

    

    private function normaliseTourGuestRows(array $guests): array
    {
        return collect($guests)
            ->map(function ($guest) {
                return [
                    'name' => trim((string) ($guest['name'] ?? '')),
                    'phone' => trim((string) ($guest['phone'] ?? '')),
                    'age' => trim((string) ($guest['age'] ?? '')),
                    'sex' => trim((string) ($guest['sex'] ?? '')),
                    'date_of_birth' => trim((string) ($guest['date_of_birth'] ?? '')),
                    'identification_type' => trim((string) ($guest['identification_type'] ?? '')),
                    'identification_no' => trim((string) ($guest['identification_no'] ?? '')),
                    'is_leader' => (bool) ($guest['is_leader'] ?? false),
                ];
            })
            ->filter(function ($guest) {
                return collect($guest)
                    ->except('is_leader')
                    ->contains(fn ($value) => $value !== '');
            })
            ->values()
            ->all();
    }

    private function saveTourOrderGuests(Orders $order, array $guests): void
    {
        foreach ($guests as $guest) {
            Guests::create([
                'order_id' => $order->id,
                'name' => $guest['name'] ?: null,
                'phone' => $guest['phone'] ?: null,
                'age' => $guest['age'] ?: null,
                'sex' => $guest['sex'] ?: null,
                'date_of_birth' => $guest['date_of_birth'] ?: null,
                'identification_type' => $guest['identification_type'] ?: null,
                'identification_no' => $guest['identification_no'] ?: null,
            ]);
        }
    }

    private function normaliseTourPackageGuestRows(array $guests): array
    {
        return collect($guests)
            ->map(fn ($guest) => [
                'name' => trim((string) ($guest['name'] ?? '')),
                'phone' => trim((string) ($guest['phone'] ?? '')),
                'age' => trim((string) ($guest['age'] ?? '')),
                'sex' => trim((string) ($guest['sex'] ?? '')),
            ])
            ->filter(fn ($guest) => collect($guest)->contains(fn ($value) => $value !== ''))
            ->values()
            ->all();
    }

    private function saveTourPackageOrderGuests(Orders $order, array $guests): void
    {
        foreach ($guests as $guest) {
            Guests::create([
                'order_id' => $order->id,
                'name' => $guest['name'] ?: null,
                'phone' => $guest['phone'] ?: null,
                'age' => $guest['age'] ?: null,
                'sex' => $guest['sex'] ?: null,
            ]);
        }
    }

    private function filterOrderPayloadByExistingColumns(array $payload): array
    {
        $orderColumns = collect(Schema::getColumnListing('orders'))
            ->flip()
            ->all();

        return collect($payload)
            ->filter(function ($value, $key) use ($orderColumns) {
                return array_key_exists($key, $orderColumns);
            })
            ->all();
    }

    private function findProcessedTourOrderBySubmissionToken(?string $token, ?int $userId = null): ?Orders
    {
        if ($token && $userId && Schema::hasColumn('orders', 'submission_token_hash')) {
            $order = Orders::query()
                ->where('service', Orders::PUBLIC_TOUR_SERVICE)
                ->where('user_id', $userId)
                ->where('submission_token_hash', hash('sha256', $token))
                ->first();

            if ($order) {
                return $order;
            }
        }

        $orderId = $this->findProcessedFormSubmission(self::TOUR_ORDER_SUBMISSION_SCOPE, $token);

        return $orderId ? Orders::find($orderId) : null;
    }

    private function rememberProcessedTourOrderSubmission(string $token, Orders $order): void
    {
        $this->rememberProcessedFormSubmission(self::TOUR_ORDER_SUBMISSION_SCOPE, $token, $order->id);
    }

    private function buildTourGuestManifestHtml(array $tourGuests = []): string
    {
        if (empty($tourGuests)) {
            return '';
        }

        $items = collect($tourGuests)
            ->values()
            ->map(function ($guest, $index) {
                $parts = array_filter([
                    $guest['name'] ?: __('tour-detail.guest') . ' ' . ($index + 1),
                    $guest['age'] ?: null,
                    $guest['sex'] ?: null,
                    $guest['phone'] ? __('messages.Phone') . ': ' . $guest['phone'] : null,
                    $guest['identification_type'] ? __('tour-detail.guest_id_type') . ': ' . $guest['identification_type'] : null,
                    $guest['identification_no'] ? __('tour-detail.guest_id_number') . ': ' . $guest['identification_no'] : null,
                    $guest['is_leader'] ? __('tour-detail.guest_leader') : null,
                ]);

                return '<li>' . e(($index + 1) . '. ') . nl2br(e(implode(' | ', $parts))) . '</li>';
            })
            ->implode('');

        return '<ol>' . $items . '</ol>';
    }

    private function buildTourGuestManifestHtmlFromOrder(Orders $order): string
    {
        $guests = $order->relationLoaded('guests')
            ? $order->guests
            : $order->guests()->get();

        $guestRows = $guests->map(function ($guest) {
            return [
                'name' => trim((string) $guest->name),
                'phone' => trim((string) $guest->phone),
                'age' => trim((string) $guest->age),
                'sex' => trim((string) $guest->sex),
            ];
        })->all();

        return $this->buildTourPackageGuestManifestHtml($guestRows);
    }

    private function buildTourPackageGuestManifestHtml(array $tourGuests = []): string
    {
        if (empty($tourGuests)) {
            return '';
        }

        $items = collect($tourGuests)
            ->values()
            ->map(function ($guest, $index) {
                $parts = array_filter([
                    $guest['name'] ?: __('tour-detail.guest').' '.($index + 1),
                    $guest['age'] ?: null,
                    $guest['sex'] ?: null,
                    $guest['phone'] ? __('messages.Phone').': '.$guest['phone'] : null,
                ]);

                return '<li>'.e(($index + 1).'. ').nl2br(e(implode(' | ', $parts))).'</li>';
            })
            ->implode('');

        return '<ol>'.$items.'</ol>';
    }

    private function resolveTourPackageHighlightsSnapshot(Tours $tour): string
    {
        $field = match (config('app.locale')) {
            'zh' => 'package_highlights_traditional',
            'zh-CN' => 'package_highlights_simplified',
            default => 'package_highlights',
        };

        return trim((string) ($tour->$field ?: $tour->package_highlights ?: ''));
    }

    public function func_create_order_tour_package(StoreTourPackageOrderRequest $request, $id){
        try {
            $validated = $request->validated();
            $user = $request->user();
            $userId = (int) $user->id;
            $existingOrder = $this->findProcessedTourOrderBySubmissionToken(
                $validated['submission_token'],
                $userId
            );

            if ($existingOrder) {
                return redirect()
                    ->route('view.detail-order-tour', ['id' => $existingOrder->id])
                    ->with('success', __('tour-detail.order_already_submitted'));
            }
            $salesAgent = in_array($user->position, ['developer', 'reservation', 'author'], true)
                ? (int) ($validated['user_id'] ?? $userId)
                : $userId;
            $checkin = Carbon::parse($validated['travel_date']);
            $tourGuests = $this->normaliseTourPackageGuestRows($validated['guests'] ?? []);
            $guestCount = count($tourGuests);
            $primaryGuest = $tourGuests[0];

            $submissionHash = hash('sha256', $validated['submission_token']);
            $submissionLock = Cache::lock(
                'tour-order-create:'.$userId.':'.$submissionHash,
                30
            );

            if (!$submissionLock->get()) {
                throw ValidationException::withMessages([
                    'submission_token' => __('tour-detail.order_submission_processing'),
                ]);
            }

            try {
                $durableOrder = $this->findProcessedTourOrderBySubmissionToken(
                    $validated['submission_token'],
                    $userId
                );

                if ($durableOrder) {
                    $order = $durableOrder;
                } else {
                    $order = DB::transaction(function () use (
                        $id,
                        $validated,
                        $user,
                        $userId,
                        $salesAgent,
                        $guestCount,
                        $checkin,
                        $tourGuests,
                        $primaryGuest,
                        $submissionHash
                    ) {
                $tour = Tours::with('activeLocations')
                    ->where('status', 'Active')
                    ->lockForUpdate()
                    ->findOrFail($id);
                $quote = app(TourPackagePricingService::class)->quote(
                    $tour,
                    $guestCount,
                    $checkin,
                    isset($validated['tour_price_id']) ? (int) $validated['tour_price_id'] : null,
                    isset($validated['promotion_id']) ? (int) $validated['promotion_id'] : null,
                    $validated['booking_code'] ?? null,
                    $userId,
                    true
                );
                $pricing = $quote->toArray();
                $tourPrice = TourPrices::findOrFail($pricing['price_id']);
                $selectedDiscount = $pricing['selected_discount'] ?? null;
                $bookingCode = ($selectedDiscount['source'] ?? null) === 'booking_code'
                    ? (string) $selectedDiscount['identifier']
                    : null;
                $promotionId = ($selectedDiscount['source'] ?? null) === 'promotion'
                    ? (int) $selectedDiscount['identifier']
                    : null;
                $formatter = app(MoneyFormatter::class);
                $unitUsd = $formatter->decimal(Money::usdCents($pricing['unit_price_usd_minor']));
                $grossUsd = $formatter->decimal(Money::usdCents($pricing['gross_total_usd_minor']));
                $discountUsd = $formatter->decimal(Money::usdCents($pricing['discount_total_usd_minor']));
                $finalUsd = $formatter->decimal(Money::usdCents($pricing['final_total_usd_minor']));
                $checkout = $checkin->copy()->addDays($tour->duration_nights ?? 0);
                $orderCount = Orders::where('user_id', $userId)
                    ->whereDate('created_at', now()->toDateString())
                    ->lockForUpdate()
                    ->count();
                $orderNumber = strtoupper((string) $user->code)
                    .now()->format('ymd')
                    .chr(65 + $orderCount);
                $orderPayload = [
                    'user_id' => $userId,
                    'orderno' => $orderNumber,
                    'name' => $user->name,
                    'email' => $user->email,
                    'servicename' => $tour->name,
                    'service' => Orders::PUBLIC_TOUR_SERVICE,
                    'service_id' => $tour->id,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'travel_date' => $checkin,
                    'location' => $tour->area,
                    'tour_type' => $tour->type?->type,
                    'number_of_guests' => $guestCount,
                    'destinations' => $this->resolveTourPackageHighlightsSnapshot($tour),
                    'itinerary' => $this->buildTourLocationItineraryHtml($tour, trim((string) $tour->itinerary)),
                    'include' => $tour->include,
                    'include_traditional' => $tour->include_traditional,
                    'include_simplified' => $tour->include_simplified,
                    'exclude' => $tour->exclude,
                    'exclude_traditional' => $tour->exclude_traditional,
                    'exclude_simplified' => $tour->exclude_simplified,
                    'additional_info' => $tour->additional_info,
                    'cancellation_policy' => $tour->cancellation_policy,
                    'guest_detail' => $this->buildTourPackageGuestManifestHtml($tourGuests),
                    'pickup_location' => $validated['pickup_location'],
                    'pickup_name' => $primaryGuest['name'],
                    'pickup_phone' => $primaryGuest['phone'] ?: null,
                    'pickup_date' => $checkin,
                    'dropoff_date' => $checkout,
                    'dropoff_location' => $validated['dropoff_location'],
                    'note' => $validated['note'] ?? $validated['special_request'] ?? null,
                    // Reserved for agent-facing terminal status reasons only.
                    'msg' => null,
                    'duration' => $tour->duration_days.'D/'.$tour->duration_nights.'N',
                    'price_id' => $tourPrice->id,
                    'price_pax' => $unitUsd,
                    'normal_price' => $grossUsd,
                    'price_total' => $grossUsd,
                    'discounts' => $discountUsd,
                    'bookingcode' => $bookingCode,
                    'bookingcode_disc' => $bookingCode ? $discountUsd : '0.00',
                    'promotion' => $promotionId ? json_encode([$promotionId]) : null,
                    'promotion_disc' => $promotionId ? json_encode([$discountUsd]) : null,
                    'final_price' => $finalUsd,
                    'order_tax' => (string) $pricing['tax_amount_idr'],
                    'usd_rate' => FixedScale::formatDecimal(
                        $pricing['rate_value_scaled'],
                        $pricing['rate_value_scale']
                    ),
                    'sales_agent' => $salesAgent,
                    'status' => 'Pending',
                    'submission_token_hash' => $submissionHash,
                ];
                $order = Orders::create($this->filterOrderPayloadByExistingColumns($orderPayload));

                $this->saveTourPackageOrderGuests($order, $tourGuests);
                app(OrderPricingSnapshotWriter::class)->commit($order, $quote, $userId);

                if ($bookingCode !== null) {
                    BookingCode::where('code', $bookingCode)->increment('used');
                }

                app(TourReservationService::class)->ensurePendingReservationForOrder($order);

                        return $order->fresh('activePricingSnapshot');
                    });
                }
            } finally {
                $submissionLock->release();
            }

            $this->rememberProcessedTourOrderSubmission($validated['submission_token'], $order);
            return redirect()->route('view.detail-order-tour', ['id' => $order->id])
                ->with('success', __('messages.The order has been successfully created'));
            // ✅ 6. Kembalikan respons sukses ke AJAX
        } catch (PricingException $e) {
            throw ValidationException::withMessages([
                'tour_price_id' => __('tour-detail.no_active_price'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (!$request->expectsJson()) {
                throw $e;
            }

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Tour Package order creation failed.', [
                'exception' => $e,
                'user_id' => $request->user()?->id,
                'tour_id' => $id,
            ]);

            if (!$request->expectsJson()) {
                return back()->withInput()->with('danger', __('messages.Something went wrong, please try again.'));
            }

            return response()->json([
                'success' => false,
                'message' => __('messages.Something went wrong, please try again.'),
            ], 500);
        }
    }

    // View Order Tour ========================================================================================> OK
    public function detail_order_tour($id)
    {   
        $now = Carbon::now();
        $user = Auth::user();
        $order = Orders::with(['guests', 'activePricingSnapshot'])
            ->where('sales_agent',$user->id)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tour = Tours::with('activeLocations')->find($order->service_id);
        $business = BusinessProfile::where('id','=',1)->first();
        $reservation = Reservation::find($order->rsv_id)??null;
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $kickback = $order->kick_back ? $order->kick_back : null;
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $discounts = [
            'Kick Back' => $kickback > 0 ? $kickback : null,
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $invoice = InvoiceAdmin::with(['payment', 'bank', 'currency'])->firstWhere('rsv_id', $order->rsv_id);
        $tourPricing = app(OrderPricingSnapshotReader::class)->historicalValues($order, $invoice);
        $order = $this->autoCancelExpiredApprovedOrder($order, $invoice);
        $invoice = InvoiceAdmin::with(['payment', 'bank', 'currency'])->firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        $paymentDeadline = $this->getInvoicePaymentDeadline($invoice);
        $paymentSubmissionExists = $this->orderHasPaymentSubmission($invoice);
        $paymentState = app(\App\Services\Orders\PublicPaymentConfirmationService::class)->state($order, $invoice);
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langArea = match (config('app.locale')) {
            'zh' => 'area_traditional',
            'zh-CN' => 'area_simplified',
            default => 'area',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langPackageHighlights = match (config('app.locale')) {
            'zh' => 'package_highlights_traditional',
            'zh-CN' => 'package_highlights_simplified',
            default => 'package_highlights',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        $generatedTourItinerary = $tour
            ? $this->buildTourLocationItineraryHtml(
                $tour,
                trim((string) ($tour->$langItinerary ?: $tour->itinerary))
            )
            : '';
        $usesLocalizedTourContent = in_array(config('app.locale'), ['zh', 'zh-CN'], true);
        $localizedTourItinerary = trim((string) ($tour?->$langItinerary));
        $localizedTourAdditionalInfo = trim((string) ($tour?->$langAdditionalInfo));
        $packageOverviewItinerary = $usesLocalizedTourContent && $localizedTourItinerary !== ''
            ? $localizedTourItinerary
            : trim((string) ($order->itinerary ?: $generatedTourItinerary ?: $tour?->itinerary));
        $packageOverviewAdditionalInfo = $usesLocalizedTourContent && $localizedTourAdditionalInfo !== ''
            ? $localizedTourAdditionalInfo
            : trim((string) ($order->additional_info ?: $localizedTourAdditionalInfo ?: $tour?->additional_info));
        return view('frontend.home.orders.details.tour-modern',compact('order'),[
            'order'=> $order,
            'business'=>$business,
            'tour'=>$tour,
            'invoice'=>$invoice,
            'receipts'=>$receipts,
            'paymentDeadline' => $paymentDeadline,
            'paymentSubmissionExists' => $paymentSubmissionExists,
            'paymentState' => $paymentState,
            'reservation'=>$reservation,
            'now'=>$now,
            'langType'=>$langType,
            'langName'=>$langName,
            'langArea'=>$langArea,
            'langShortDescription'=>$langShortDescription,
            'langDescription'=>$langDescription,
            'langPackageHighlights'=>$langPackageHighlights,
            'langItinerary'=>$langItinerary,
            'langInclude'=>$langInclude,
            'langExclude'=>$langExclude,
            'langAdditionalInfo'=>$langAdditionalInfo,
            'langCancellationPolicy'=>$langCancellationPolicy,
            'generatedTourItinerary'=>$generatedTourItinerary,
            'packageOverviewItinerary'=>$packageOverviewItinerary,
            'packageOverviewAdditionalInfo'=>$packageOverviewAdditionalInfo,
            'filteredDiscounts'=>$filteredDiscounts,
            'additionalServices'=>$additionalServices,
            'tourPricing'=>$tourPricing,
        ]);
        
        
    }

        public function storeFrontendActivityOrder(Request $request, string $code)
    {
        $validated = $request->validate([
            'submission_token' => ['required', 'string', 'max:120'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:200'],
            'travel_date' => ['required', 'date', 'after_or_equal:now'],
            'guests' => ['required', 'array', 'min:1', 'max:200'],
            'guests.*.name' => ['required', 'string', 'max:255'],
            'guests.*.phone' => ['nullable', 'string', 'max:50'],
            'guests.*.age' => ['required', 'in:Adult,Child'],
            'guests.*.sex' => ['required', 'in:Male,Female'],
            'guests.*.date_of_birth' => ['nullable', 'date'],
            'guests.*.identification_type' => ['nullable', 'string', 'max:50'],
            'guests.*.identification_no' => ['nullable', 'string', 'max:100'],
            'guests.*.is_leader' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
            'activity_order_source' => ['nullable', 'string'],
            'terms_accepted' => ['accepted'],
        ]);
        // dd($validated);
        $user = Auth::user();
        $existingOrder = $this->findProcessedActivityOrderBySubmissionToken($validated['submission_token']);

        if ($existingOrder) {
            return redirect($this->resolveFrontendOrderDetailUrl($existingOrder))
                ->with('success', 'This order was already submitted. We reopened the existing order detail.');
        }

        $activity = Activities::with('partners')
            ->where('status', 'Active')
            ->where('code', $code)
            ->firstOrFail();

        $travelDate = Carbon::parse($validated['travel_date']);
        if (filled($activity->validity) && Carbon::parse($activity->validity)->lt($travelDate->copy()->startOfDay())) {
            throw ValidationException::withMessages([
                'travel_date' => __('messages.The selected activity is no longer valid for this date.'),
            ]);
        }

        $guestCount = (int) $validated['number_of_guests'];
        $minimumPax = max((int) ($activity->min_pax ?: 1), 1);
        if ($guestCount < $minimumPax) {
            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Number of guests is below the activity minimum.'),
            ]);
        }

        $capacity = (int) ($activity->qty ?: 0);
        if ($capacity > 0 && $guestCount > $capacity) {
            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Number of guests exceeds activity capacity.'),
            ]);
        }

        if ((float) $activity->contract_rate <= 0 && (float) $activity->markup <= 0) {
            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Activity pricing is not available.'),
            ]);
        }

        $activityGuests = $this->normaliseTourGuestRows($validated['guests'] ?? []);
        if (count($activityGuests) < $guestCount) {
            throw ValidationException::withMessages([
                'guests' => __('activities.detail.order.guest_count_mismatch'),
            ]);
        }

        $durationHours = $this->extractActivityDurationHours($activity->duration);
        $checkout = $travelDate->copy()->addHours(max($durationHours, 1));
        $usdrates = UsdRates::where('name', 'USD')->first();
        $cnyrates = UsdRates::where('name', 'CNY')->first();
        $twdrates = UsdRates::where('name', 'TWD')->first();
        $tax = Tax::where('name', 'tax')->first() ?: Tax::find(1);
        $promotions = Promotion::where('status', 'Active')
            ->where('periode_start', '<=', $travelDate)
            ->where('periode_end', '>=', $travelDate)
            ->get();

        $promotionDiscounts = $promotions->pluck('discounts')->map(fn ($value) => (float) $value)->values();
        $promotionTotalDiscount = (float) $promotionDiscounts->sum();
        $priceNonTax = $usdrates
            ? ceil(((float) $activity->contract_rate) / max((float) $usdrates->rate, 1)) + (float) $activity->markup
            : ((float) $activity->contract_rate + (float) $activity->markup);
        $taxAmount = $tax ? ceil(((float) $tax->tax / 100) * $priceNonTax) : 0;
        $pricePerPax = max($priceNonTax + $taxAmount, 0);
        $normalPrice = $pricePerPax * $guestCount;
        $finalPrice = max($normalPrice - $promotionTotalDiscount, 0);
        $guestLeader = collect($activityGuests)->first(fn ($guest) => $guest['is_leader'] && $guest['phone']);

        if (!$guestLeader) {
            throw ValidationException::withMessages([
                'guests' => __('tour-detail.guest_leader_required'),
            ]);
        }

        $guestDetail = $this->buildTourGuestManifestHtml($activityGuests);

        $orderPayload = [
            'orderno' => $this->generateActivityOrderNumber($travelDate),
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'service' => Orders::PUBLIC_ACTIVITY_SERVICE,
            'service_type' => $activity->type,
            'service_id' => $activity->id,
            'servicename' => optional($activity->partners)->name ?: '-',
            'subservice' => $activity->name,
            'subservice_id' => $activity->id,
            'sales_agent' => $user->id,
            'checkin' => $travelDate->format('Y-m-d H:i:s'),
            'checkout' => $checkout->format('Y-m-d H:i:s'),
            'pickup_date' => $travelDate->format('Y-m-d H:i:s'),
            'dropoff_date' => $checkout->format('Y-m-d H:i:s'),
            'travel_date' => $travelDate->format('Y-m-d H:i:s'),
            'pickup_name' => $guestLeader['name'] ?? null,
            'pickup_phone' => $guestLeader['phone'] ?? null,
            'location' => $activity->location,
            'capacity' => $capacity ?: $guestCount,
            'number_of_guests' => $guestCount,
            'guest_detail' => $guestDetail,
            'note' => filled($validated['note'] ?? null) ? trim((string) $validated['note']) : null,
            'include' => $activity->include,
            'additional_info' => $activity->additional_info,
            'cancellation_policy' => $activity->cancellation_policy,
            'itinerary' => $activity->itinerary,
            'duration' => $activity->duration,
            'price_total' => $normalPrice,
            'normal_price' => $normalPrice,
            'price_pax' => $pricePerPax,
            'final_price' => $finalPrice,
            'promotion' => $promotions->isNotEmpty() ? json_encode($promotions->pluck('name')->values()->all()) : null,
            'promotion_disc' => $promotions->isNotEmpty() ? json_encode($promotionDiscounts->all()) : null,
            'usd_rate' => $usdrates?->rate,
            'cny_rate' => $cnyrates?->rate,
            'twd_rate' => $twdrates?->rate,
            'status' => 'Pending',
        ];

        $order = DB::transaction(function () use ($orderPayload, $activityGuests, $activity, $request, $user, $validated) {
            $order = Orders::create($this->filterOrderPayloadByExistingColumns($orderPayload));

            $this->saveTourOrderGuests($order, $activityGuests);
            app(ActivityReservationService::class)->ensurePendingReservationForOrder($order);

            UserLog::create([
                'action' => 'Create Order',
                'service' => Orders::PUBLIC_ACTIVITY_SERVICE,
                'subservice' => $activity->name,
                'subservice_id' => $order->id,
                'page' => 'activity-detail-modern',
                'user_id' => $user->id,
                'user_ip' => $request->getClientIp(),
                'note' => 'Created activity order with order no: ' . $order->orderno,
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'action' => 'Create Order',
                'url' => $request->getClientIp(),
                'method' => 'Create',
                'agent' => $order->name,
                'admin' => Auth::id(),
            ]);

            $this->rememberProcessedActivityOrderSubmission($validated['submission_token'], $order);

            return $order->fresh();
        });

        Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id, null));

        return redirect($this->resolveFrontendOrderDetailUrl($order))
            ->with('success', __('messages.The activity order has been successfully created and submitted.'));
    }
    // View Order Activity ========================================================================================> OK
    public function detail_order_activity($id)
    {   
        $now = Carbon::now();
        $user = Auth::user();
        $order = Orders::with(['guests', 'activePricingSnapshot'])
            ->where('sales_agent',$user->id)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $activity = Tours::with('activeLocations')->find($order->service_id);
        $business = BusinessProfile::where('id','=',1)->first();
        $reservation = Reservation::find($order->rsv_id)??null;
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $kickback = $order->kick_back ? $order->kick_back : null;
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $discounts = [
            'Kick Back' => $kickback > 0 ? $kickback : null,
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $invoice = InvoiceAdmin::with(['payment', 'bank', 'currency'])->firstWhere('rsv_id', $order->rsv_id);
        $activityPricing = app(OrderPricingSnapshotReader::class)->historicalValues($order, $invoice);
        $order = $this->autoCancelExpiredApprovedOrder($order, $invoice);
        $invoice = InvoiceAdmin::with(['payment', 'bank', 'currency'])->firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        $paymentDeadline = $this->getInvoicePaymentDeadline($invoice);
        $paymentSubmissionExists = $this->orderHasPaymentSubmission($invoice);
        $paymentState = app(\App\Services\Orders\PublicPaymentConfirmationService::class)->state($order, $invoice);
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langArea = match (config('app.locale')) {
            'zh' => 'area_traditional',
            'zh-CN' => 'area_simplified',
            default => 'area',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langPackageHighlights = match (config('app.locale')) {
            'zh' => 'package_highlights_traditional',
            'zh-CN' => 'package_highlights_simplified',
            default => 'package_highlights',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        $generatedTourItinerary = $activity
            ? $this->buildTourLocationItineraryHtml(
                $activity,
                trim((string) ($activity->$langItinerary ?: $activity->itinerary))
            )
            : '';
        $usesLocalizedTourContent = in_array(config('app.locale'), ['zh', 'zh-CN'], true);
        $localizedTourItinerary = trim((string) ($activity?->$langItinerary));
        $localizedTourAdditionalInfo = trim((string) ($activity?->$langAdditionalInfo));
        $packageOverviewItinerary = $usesLocalizedTourContent && $localizedTourItinerary !== ''
            ? $localizedTourItinerary
            : trim((string) ($order->itinerary ?: $generatedTourItinerary ?: $activity?->itinerary));
        $packageOverviewAdditionalInfo = $usesLocalizedTourContent && $localizedTourAdditionalInfo !== ''
            ? $localizedTourAdditionalInfo
            : trim((string) ($order->additional_info ?: $localizedTourAdditionalInfo ?: $activity?->additional_info));
        return view('frontend.home.orders.details.activity-modern',compact('order'),[
            'order'=> $order,
            'business'=>$business,
            'activity'=>$activity,
            'invoice'=>$invoice,
            'receipts'=>$receipts,
            'paymentDeadline' => $paymentDeadline,
            'paymentSubmissionExists' => $paymentSubmissionExists,
            'paymentState' => $paymentState,
            'reservation'=>$reservation,
            'now'=>$now,
            'langType'=>$langType,
            'langName'=>$langName,
            'langArea'=>$langArea,
            'langShortDescription'=>$langShortDescription,
            'langDescription'=>$langDescription,
            'langPackageHighlights'=>$langPackageHighlights,
            'langItinerary'=>$langItinerary,
            'langInclude'=>$langInclude,
            'langExclude'=>$langExclude,
            'langAdditionalInfo'=>$langAdditionalInfo,
            'langCancellationPolicy'=>$langCancellationPolicy,
            'generatedTourItinerary'=>$generatedTourItinerary,
            'packageOverviewItinerary'=>$packageOverviewItinerary,
            'packageOverviewAdditionalInfo'=>$packageOverviewAdditionalInfo,
            'filteredDiscounts'=>$filteredDiscounts,
            'additionalServices'=>$additionalServices,
            'activityPricing'=>$activityPricing,
        ]);
        
        
    }
    // Function Update Order Tour ========================================================================================> OK
    public function func_update_order_tour(Request $request,$id){
        try {
            $validated = $request->validate([
                'submission_token' => 'required|string|max:120',
                'number_of_guests' => 'required|integer|min:2|max:200',
                'travel_date' => 'required|date',
                'pickup_location' => 'required|string|max:255',
                'dropoff_location' => 'required|string|max:255',
                'note' => 'nullable|string',
            ]);
            $user = Auth::user();
            $order=Orders::with('guests')->where('sales_agent',$user->id)->where('id',$id)->first();
            if (!$order) {
                return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
            }
            $existingOrderId = $this->findProcessedFormSubmission('tour-order-update:' . $order->id, $validated['submission_token']);

            if ($existingOrderId) {
                return redirect()->route('view.detail-order-tour', ['id' => $order->id])
                    ->with('success', __('messages.Your order has already been updated. We reopened the latest detail page.'));
            }
            $checkin = Carbon::parse($validated['travel_date']);
            $nog = $validated['number_of_guests'];

            if ($nog !== (int) $order->number_of_guests
                || !$checkin->isSameDay(Carbon::parse($order->travel_date ?? $order->checkin))) {
                throw ValidationException::withMessages([
                    'number_of_guests' => __('Pricing-affecting changes require the approved repricing workflow.'),
                ]);
            }

            $order->update($this->filterOrderPayloadByExistingColumns([
                'guest_detail' => $this->buildTourGuestManifestHtmlFromOrder($order) ?: $order->guest_detail,
                'pickup_location' => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'],
                'note' => $validated['note'] ?? null,
            ]));
            $this->rememberProcessedFormSubmission('tour-order-update:' . $order->id, $validated['submission_token'], $order->id);

            return redirect()->route('view.detail-order-tour', ['id' => $order->id])
                ->with('success',__('messages.The order has been successfully updated'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            return back()->withInput()->with('danger', 'An error occurred while updating order: ' . $e->getMessage());
        }
    }
   

    // FUNCTION REMOVE ORDER ---------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_remove_order(Request $request,$id){
        $user = Auth::user();
        $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $orderno = $order->orderno;
        $service = "Order";
        $action = "Remove Order";
        $msg = "User = Remove Order <br>Admin = ".$request->admin_msg;
        $order->update([
            "status"=>$request->status,
        ]);
        $log= new LogData ([
            'service' =>$orderno,
            'service_name'=>$service,
            'action'=>$action,
            'user_id'=>$request->author,
        ]);
        $log->save();
        return redirect("/orders")->with('success','Your order has been successfully removed!');
        
    }

    // FUNCTION DESTROY ORDER --------------------------------------------------------------------------------------------------------------------------------------------->
    public function destroy_order(Request $request,$id) {
        $user = Auth::user();
        $order = Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }

        if ($order->service === Orders::PUBLIC_TOUR_SERVICE) {
            if (!in_array($order->status, ['Draft', 'Invalid', 'Rejected'], true)) {
                return redirect()->route('view.detail-order-tour', ['id' => $order->id])
                    ->with('error', __('messages.This order can no longer be deleted in its current status.'));
            }

            DB::transaction(function () use ($order, $request) {
                $order->update(['status' => 'Deleted']);

                if ($order->rsv_id) {
                    Reservation::whereKey($order->rsv_id)->update(['status' => 'Canceled']);
                }

                OrderLog::create([
                    'order_id' => $order->id,
                    'action' => 'Delete Tour Order',
                    'url' => $request->getClientIp(),
                    'method' => 'Update',
                    'agent' => $order->name,
                    'admin' => Auth::id(),
                ]);
            });

            return redirect()->route('view.orders')->with('success', __('messages.Order has been deleted.'));
        }

        $order->delete();
        return redirect("/orders")->with('success','Order has been deleted!');
        
    }

    // Function Remove optional service =============================================================================================================>
    public function destroy_opser_order(Request $request,$id) {
        $user = Auth::user();
        $order = Orders::where('sales_agent',$user->id)->where('id',$request->order_id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $optional_rate_order = OptionalRateOrder::findOrFail($id);
        $optional_rate_order->delete();
        return redirect("/order-$order")->with('success','Optional service has been removed!');
    }


    

    // Function Updated Activated =============================================================================================================>
    public function func_update_order(Request $request,$id){
        try {
            $user = Auth::user();
            ini_set('max_execution_time', 60);
            $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
            if (!$order) {
                return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
            }
            $orderno = $order->orderno;
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $usdrate = UsdRates::where('id',1)->first();
            $tax = Tax::where('id',1)->first();
        
            $travel_date = date('Y-m-d H.i',strtotime($request->travel_date));
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date))." ".date('H.i',strtotime($request->wedding_time));
            if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package") {
                $hotel = Hotels::where('id',$order->service_id)->first();
                $transport_in = Transports::where('id',$request->airport_shuttle_in)->first();
                if ($transport_in) {
                    $transport_in_price = TransportPrice::where('transports_id',$transport_in->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_in_price) {
                        $c_price_usd = ceil($transport_in_price->contract_rate/$usdrate->rate);
                        $c_price_markup = $c_price_usd + $transport_in_price->markup;
                        $c_price_tax = ceil($c_price_markup*($tax->tax/100));
                        $airport_shuttle_in_price = $c_price_markup + $c_price_tax;
                    }else{
                        $airport_shuttle_in_price = 0;
                    }
                }else {
                    $transport_in_price = TransportPrice::where('transports_id',$request->airport_shuttle_in)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_in_price) {
                        $c_price_usd = ceil($transport_in_price->contract_rate/$usdrate->rate);
                        $c_price_markup = $c_price_usd + $transport_in_price->markup;
                        $c_price_tax = ceil($c_price_markup*($tax->tax/100));
                        $airport_shuttle_in_price = $c_price_markup + $c_price_tax;
                    }else{
                        $airport_shuttle_in_price = 0;
                    }
                }
                $transport_out = Transports::where('id',$request->airport_shuttle_out)->first();
                if ($transport_out) {
                    $transport_out_price = TransportPrice::where('transports_id',$transport_out->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_out_price) {
                        $o_price_usd = ceil($transport_out_price->contract_rate/$usdrate->rate);
                        $o_price_markup = $o_price_usd + $transport_out_price->markup;
                        $o_price_tax = ceil($o_price_markup*($tax->tax/100));
                        $airport_shuttle_out_price = $o_price_markup + $o_price_tax;
                    }else{
                        $airport_shuttle_out_price = 0;
                    }
                }else{
                    $transport_out_price = TransportPrice::where('transports_id',$request->airport_shuttle_out)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_out_price) {
                        $o_price_usd = ceil($transport_out_price->contract_rate/$usdrate->rate);
                        $o_price_markup = $o_price_usd + $transport_out_price->markup;
                        $o_price_tax = ceil($o_price_markup*($tax->tax/100));
                        $airport_shuttle_out_price = $o_price_markup + $o_price_tax;
                    }else{
                        $airport_shuttle_out_price = 0;
                    }
                }
                if ($airport_shuttle_in_price == 0 or $airport_shuttle_out_price == 0) {
                    $airport_shuttle_price = 0;
                }else{
                    $airport_shuttle_price = $airport_shuttle_in_price + $airport_shuttle_out_price;
                }
            }else{
                $airport_shuttle_price = 0;
            }
            if ($order->service == "Activity") {
                $normal_price =$order->price_pax * $request->number_of_guests;
                $price_total = $normal_price;
                $final_price = $price_total - $order->bookingcode_disc - $request->promotion_disc - $order->discounts;
                $number_of_guests = $request->number_of_guests;
                $price_pax = $order->price_pax;
            }elseif($order->service == "Tour Package"){
                $normal_price = $order->normal_price;
                $price_total = $order->price_total;
                $number_of_guests = $order->number_of_guests;
                $price_pax = $order->price_pax;
                $final_price = $order->final_price;
            }elseif($order->service == "Wedding Package"){
                $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->firstOrFail();
                $bride = Brides::where('id',$order_wedding->brides_id)->firstOrFail();
                $number_of_guests = $request->number_of_guests;
                $final_price = $request->final_price;
                $price_total = $request->price_total;
                $normal_price = $request->normal_price;
                $price_pax = $request->price_pax;

                $order_wedding->update([
                    "wedding_date"=>$wedding_date,
                    "number_of_invitation"=>$number_of_guests,
                ]);
                $bride->update([
                    "bride"=>$request->bride_name,
                    "bride_chinese"=>$request->bride_chinese,
                    "bride_contact"=>$request->bride_contact,
                    "groom"=>$request->groom_name,
                    "groom_chinese"=>$request->groom_chinese,
                    "groom_contact"=>$request->groom_contact,
                ]);

            }else{
                $normal_price = $order->normal_price;
                $price_total = $order->price_total;
                $final_price = $order->final_price + $airport_shuttle_price;
                $number_of_guests = $order->number_of_guests;
                $price_pax = $order->price_pax;
            }
            $order->update([
                "status"=>$request->status,
                "guest_detail"=>$request->guest_detail,
                "arrival_flight"=>$request->arrival_flight,
                "arrival_time"=>$request->arrival_time,
                "departure_flight"=>$request->departure_flight,
                "departure_time"=>$request->departure_time,
                "airport_shuttle_in"=>$request->airport_shuttle_in,
                "airport_shuttle_out"=>$request->airport_shuttle_out,
                "note"=>$request->note,
                "kick_back"=>$request->kick_back,
                "request_quotation"=>$request->request_quotation,
                "travel_date"=>$travel_date,
                "number_of_guests"=>$number_of_guests,
                "final_price"=>$final_price,
                "bookingcode"=>$order->bookingcode,
                "bookingcode_disc"=>$order->bookingcode_disc,
                "airport_shuttle_price"=>$airport_shuttle_price,
                "price_total"=>$price_total,
                "normal_price"=>$normal_price,
                "price_pax"=>$price_pax,
                "pickup_location"=>$request->pickup_location,
                "dropoff_location"=>$request->dropoff_location,
                "groom_name"=>$request->groom_name,
                "bride_name"=>$request->bride_name,
                "wedding_date"=>$wedding_date,
                
            ]);
            // dd($order,$order_wedding,$bride);
            
            if (isset($request->airport_shuttle_in) or isset($request->airport_shuttle_out)) {
                $asins = AirportShuttle::where('order_id',$order->id)->get();
                if(isset($asins)){
                    foreach ($asins as $asin) {
                        $in_asin = $asins->where('nav',"In")->first();
                        $out_asin = $asins->where('nav',"Out")->first();
                        if (isset($in_asin)) {
                            if ($asin->nav == "In") {
                                $asin->update([
                                    "date"=>$request->arrival_time,
                                    "transport"=>$transport_in->name,
                                    "src"=>"Airport",
                                    "dst"=>$hotel->name,
                                    "duration"=>$hotel->airport_duration,
                                    "distance"=>$hotel->airport_distance,
                                    "price"=>$airport_shuttle_in_price,
                                ]);
                            }
                        }else{
                            $airport_shuttle_in =new AirportShuttle([
                                "date"=>$request->arrival_time,
                                "transport"=>$transport_in->name,
                                "src"=>"Airport",
                                "dst"=>$hotel->name,
                                "duration"=>$hotel->airport_duration,
                                "distance"=>$hotel->airport_distance,
                                "price"=>$airport_shuttle_in_price,
                                "order_id"=>$order->id,
                                "nav"=>"In",
                            ]);
                            $airport_shuttle_in->save();
                        }
                        if (isset($out_asin)) {
                            $out_asin->update([
                                "date"=>$request->departure_time,
                                "transport"=>$transport_out->name,
                                "src"=>$hotel->name,
                                "dst"=>"Airport",
                                "duration"=>$hotel->airport_duration,
                                "distance"=>$hotel->airport_distance,
                                "price"=>$airport_shuttle_out_price,
                            ]);
                        }else{
                            $airport_shuttle_out =new AirportShuttle([
                                "date"=>$request->departure_time,
                                "transport"=>$transport_out->name,
                                "src"=>$hotel->name,
                                "dst"=>"Airport",
                                "duration"=>$hotel->airport_duration,
                                "distance"=>$hotel->airport_distance,
                                "price"=>$airport_shuttle_out_price,
                                "order_id"=>$order->id,
                                "nav"=>"Out",
                            ]);
                            $airport_shuttle_out->save();
                        }
                    }
                }else {
                    if ($request->airport_shuttle_in) {
                        $airport_shuttle_in =new AirportShuttle([
                            "date"=>$request->arrival_time,
                            "transport"=>$transport_in->name,
                            "src"=>"Airport",
                            "dst"=>$hotel->name,
                            "duration"=>$hotel->airport_duration,
                            "distance"=>$hotel->airport_distance,
                            "price"=>$airport_shuttle_in_price,
                            "order_id"=>$order->id,
                            "nav"=>"In",
                        ]);
                        $airport_shuttle_in->save();
                    }
                    if ($request->airport_shuttle_out) {
                        $airport_shuttle_out =new AirportShuttle([
                            "date"=>$request->departure_time,
                            "transport"=>$transport_out->name,
                            "src"=>$hotel->name,
                            "dst"=>"Airport",
                            "duration"=>$hotel->airport_duration,
                            "distance"=>$hotel->airport_distance,
                            "price"=>$airport_shuttle_out_price,
                            "order_id"=>$order->id,
                            "nav"=>"Out",
                        ]);
                        $airport_shuttle_out->save();
                    }
                }
            }
            // dd($order, $asins);
            //Mail
            $rquotation = $request->request_quotation;
            $agent = User::where('id',$order->sales_agent)->first();
            Mail::to(config('app.reservation_mail'))
            // Mail::to(['reservation@balikamitour.com',config('app.reservation_mail')])
            ->send(new ReservationMail($id,$rquotation));
            $note = "Submited order no: ".$order->orderno;
            //dd($order);
            $user_log =new UserLog([
                "action"=>$request->action,
                "service"=>$order->service,
                "subservice"=>$order->subservice,
                "subservice_id"=>$id,
                "page"=>$request->page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>'Submit Order',
                "url"=>$request->getClientIp(),
                "method"=>"Archive",
                "agent"=>$agent->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect("/detail-order-$order->id")->with('success','Your order has been submited, and we will validate your order');
            
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            if ($e instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException) {
                return redirect()->back()->with('error', 'Please try again, your order has not been submitted due to a network issue.');
            } else {
                return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
            }
        }
    }


    public function func_approve_order(Request $request,$id){
        $user = Auth::user();
        $order = Orders::where('sales_agent', $user->id)->where('id', $id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $order->update([
            "status"=>"Approved",
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Approve Order",
            "url"=>$request->getClientIp(),
            "method"=>"Approve",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/detail-order-$id")->with('success','Your order has been approved');
        
    }

    // Function Reupload accepted =============================================================================================================>
    public function func_reupload_order(Request $request,$id){
        $user = Auth::user();
        $order = Orders::where('sales_agent', $user->id)->where('id', $id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $orderno = $order->orderno;
        $service = "Order";
        $action = "Reupload Order";
        $checkin = date('Y-m-d', strtotime($request->checkin));
        $checkout = date('Y-m-d', strtotime($request->checkout));
        $msg = "";

        $order->update([
            "status"=>$request->status,
            "msg"=>$msg,
        ]);

        // USER LOG
        $note = "Resubmit order no: ".$order->orderno;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$request->subservice,
            "subservice_id"=>$request->subservice_id,
            "page"=>$request->page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/orders")->with('success','Your order has been resubmited, and we will validate your order');
    }

    // Function add optional rate to Order ======================================================================================= ==>
    public function func_add_optional_rate(Request $request){

        $usdrates = UsdRates::where('name','USD')->first();
        $opti_rate = OptionalRate::where('id','=',$request->optional_rate_id)->first();
        $type = $opti_rate->type;
        $name = $opti_rate->name;
        $price_unit = (ceil($opti_rate->contract_rate / $usdrates->rate))+$opti_rate->markup;
        $total_price = $price_unit * $request->qty;
        $description = $opti_rate->description;
        $status = "Active";
        $service_date = date("Y-m-d",strtotime($request->service_date));
        $optionalrateorder =new OptionalRateOrder([
            "orders_id"=>$request->order_id,
            "type"=>$type,
            "name"=>$name,
            "qty"=>$request->qty,
            "price_unit"=>$total_price,
            "description" =>$description,
            "note"=>$request->note,
            "status"=>$status,
            "author"=>$request->author,
            "service_date"=>$service_date,
            "optional_rate_id"=>$request->optional_rate_id,
        ]);
        // @dd($order);
        $optionalrateorder->save();
        return redirect("/order-$request->order_id")->with('success','Optional service added successfully');
    
    }
    

}
