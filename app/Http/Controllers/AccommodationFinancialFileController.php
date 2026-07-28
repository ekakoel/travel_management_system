<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Services\AccommodationFinancialFileService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccommodationFinancialFileController extends Controller
{
    private $files;

    private const ADMIN_POSITIONS = [
        'developer',
        'reservation',
        'weddingRsv',
    ];

    public function __construct(AccommodationFinancialFileService $files)
    {
        $this->files = $files;
    }

    public function customerReceipt(int $order, int $payment)
    {
        $orderModel = $this->findCustomerAccommodationOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function adminReceipt(int $order, int $payment)
    {
        $orderModel = $this->findAdminAccommodationOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function customerTransportReceipt(int $order, int $payment)
    {
        $orderModel = $this->findCustomerTransportOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function adminTransportReceipt(int $order, int $payment)
    {
        $orderModel = $this->findAdminTransportOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function customerTourReceipt(int $order, int $payment)
    {
        $orderModel = $this->findCustomerTourOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function adminTourReceipt(int $order, int $payment)
    {
        $orderModel = $this->findAdminTourOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function customerActivityReceipt(int $order, int $payment)
    {
        $orderModel = $this->findCustomerActivityOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function adminActivityReceipt(int $order, int $payment)
    {
        $orderModel = $this->findAdminActivityOrder($order);
        $paymentModel = $this->findPaymentForOrder($orderModel, $payment);
        $file = $this->files->resolveReceiptFile($orderModel, $paymentModel);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $this->receiptDisposition($file));
    }

    public function customerInvoicePreview(int $order, string $locale = 'en')
    {
        return $this->customerInvoiceResponse($order, $locale, 'inline');
    }

    public function customerInvoiceDownload(int $order, string $locale = 'en')
    {
        return $this->customerInvoiceResponse($order, $locale, 'attachment');
    }

    public function adminInvoicePreview(int $order, string $locale = 'en')
    {
        return $this->adminInvoiceResponse($order, $locale, 'inline');
    }

    public function adminInvoiceDownload(int $order, string $locale = 'en')
    {
        return $this->adminInvoiceResponse($order, $locale, 'attachment');
    }

    private function customerInvoiceResponse(int $order, string $locale, string $disposition)
    {
        $orderModel = $this->findCustomerProtectedPublicOrder($order);
        $invoice = $this->invoiceForOrder($orderModel);

        abort_unless($invoice && in_array($orderModel->status, ['Approved', 'Paid'], true), 404);

        $file = $this->files->resolveInvoiceFile($orderModel, $invoice, $locale);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $disposition);
    }

    private function adminInvoiceResponse(int $order, string $locale, string $disposition)
    {
        $orderModel = $this->findAdminProtectedPublicOrder($order);
        $invoice = $this->invoiceForOrder($orderModel);

        abort_unless($invoice, 404);

        $file = $this->files->resolveInvoiceFile($orderModel, $invoice, $locale);

        abort_unless($file, 404);

        return $this->secureFileResponse($file, $disposition);
    }

    private function findCustomerAccommodationOrder(int $orderId): Orders
    {
        return Orders::with(['reservations.invoice.payment'])
            ->ownedBy(Auth::id())
            ->accommodationService()
            ->findOrFail($orderId);
    }

    private function findAdminAccommodationOrder(int $orderId): Orders
    {
        $order = Orders::with(['reservations.invoice.payment'])
            ->accommodationService()
            ->findOrFail($orderId);

        $this->ensureAdminCanAccess($order);

        return $order;
    }

    private function findCustomerTransportOrder(int $orderId): Orders
    {
        return Orders::with(['reservations.invoice.payment'])
            ->ownedBy(Auth::id())
            ->where('service', Orders::PUBLIC_TRANSPORT_SERVICE)
            ->findOrFail($orderId);
    }

    private function findAdminTransportOrder(int $orderId): Orders
    {
        $order = Orders::with(['reservations.invoice.payment'])
            ->where('service', Orders::PUBLIC_TRANSPORT_SERVICE)
            ->findOrFail($orderId);

        $this->ensureAdminCanAccess($order);

        return $order;
    }

    private function findCustomerTourOrder(int $orderId): Orders
    {
        return Orders::with(['reservations.invoice.payment'])
            ->ownedBy(Auth::id())
            ->where('service', Orders::PUBLIC_TOUR_SERVICE)
            ->findOrFail($orderId);
    }

    private function findAdminTourOrder(int $orderId): Orders
    {
        $order = Orders::with(['reservations.invoice.payment'])
            ->where('service', Orders::PUBLIC_TOUR_SERVICE)
            ->findOrFail($orderId);

        $this->ensureAdminCanAccess($order);

        return $order;
    }

    private function findCustomerActivityOrder(int $orderId): Orders
    {
        return Orders::with(['reservations.invoice.payment'])
            ->ownedBy(Auth::id())
            ->where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
            ->findOrFail($orderId);
    }

    private function findAdminActivityOrder(int $orderId): Orders
    {
        $order = Orders::with(['reservations.invoice.payment'])
            ->where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
            ->findOrFail($orderId);

        $this->ensureAdminCanAccess($order);

        return $order;
    }

    private function findCustomerProtectedPublicOrder(int $orderId): Orders
    {
        return Orders::with(['reservations.invoice.payment'])
            ->ownedBy(Auth::id())
            ->where(function ($query) {
                $query->accommodationService()
                    ->orWhere('service', Orders::PUBLIC_TRANSPORT_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_TOUR_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_ACTIVITY_SERVICE);
            })
            ->findOrFail($orderId);
    }

    private function findAdminProtectedPublicOrder(int $orderId): Orders
    {
        $order = Orders::with(['reservations.invoice.payment'])
            ->where(function ($query) {
                $query->accommodationService()
                    ->orWhere('service', Orders::PUBLIC_TRANSPORT_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_TOUR_SERVICE)
                    ->orWhere('service', Orders::PUBLIC_ACTIVITY_SERVICE);
            })
            ->findOrFail($orderId);

        $this->ensureAdminCanAccess($order);

        return $order;
    }

    private function ensureAdminCanAccess(Orders $order): void
    {
        $user = Auth::user();

        abort_unless($user && in_array($user->position, self::ADMIN_POSITIONS, true), 403);

        if ($user->position !== 'developer' && $order->handled_by && (int) $order->handled_by !== (int) $user->id) {
            abort(403);
        }
    }

    private function findPaymentForOrder(Orders $order, int $paymentId): PaymentConfirmation
    {
        $invoice = $this->invoiceForOrder($order);

        abort_unless($invoice, 404);

        return PaymentConfirmation::where('id', $paymentId)
            ->where('inv_id', $invoice->id)
            ->firstOrFail();
    }

    private function invoiceForOrder(Orders $order): ?InvoiceAdmin
    {
        $reservation = $order->reservations;

        if (!$reservation || (int) $reservation->id !== (int) $order->rsv_id) {
            return null;
        }

        $invoice = $reservation->invoice ?: InvoiceAdmin::firstWhere('rsv_id', $reservation->id);

        if (!$invoice || (int) $invoice->rsv_id !== (int) $reservation->id) {
            return null;
        }

        return $invoice;
    }

    private function receiptDisposition(array $file): string
    {
        return in_array($file['mime'], ['image/jpeg', 'image/png'], true) ? 'inline' : 'attachment';
    }

    private function secureFileResponse(array $file, string $disposition): BinaryFileResponse
    {
        $response = response()->file($file['absolute_path']);

        foreach ($this->files->fileResponseHeaders($file, $disposition) as $header => $value) {
            $response->headers->set($header, $value, true);
        }

        return $response->setPrivate();
    }
}
