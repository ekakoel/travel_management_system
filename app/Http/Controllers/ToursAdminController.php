<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Tours;
use App\Models\UserLog;
use App\Models\Partners;
use App\Models\TourType;
use App\Models\UsdRates;
use App\Models\ActionLog;
use App\Models\TourPrices;
use App\Models\ToursImages;
use App\Models\TourLocationReference;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ToursAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:isAdmin']);
    }
    public function index()
    {
        if (Gate::allows('posDev') || Gate::allows('posAuthor')) {
            // Ambil tax dan kurs USD dari cache
            $tax = Cache::remember('tax', 3600, function () {
                return Tax::select('name', 'tax')->where('name', 'tax')->first();
            });

            $usdrates = Cache::remember('usd_rates', 3600, function () {
                return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
            });
            $activetours = Tours::with(['images', 'prices', 'type'])
                ->where('status', 'Active')
                ->get();
            $archivetours = Tours::with(['prices', 'type'])->where('status', 'Archived')->get();
            $drafttours = Tours::with(['prices', 'type'])->where('status', 'Draft')->get();
            $totalTours = $activetours->count() + $archivetours->count() + $drafttours->count();
            $activetours->each(function ($tour) use ($usdrates, $tax) {
                $tour->prices->each(function ($price) use ($usdrates, $tax) {
                    $price->calculated_price = $price->calculatePrice($usdrates, $tax);
                });
            });
            return view('backend.operations.tours.index', compact('activetours','archivetours','drafttours','totalTours','tax', 'usdrates'));
        }
    }
// View Admin Detail Tour =========================================================================================>
    public function view_detail_tour($id)
    {
        $now = Carbon::now();
        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        
        $user = Auth::user()->all();
        $tour = Tours::with([
            'images',
            'prices' => function($q) use ($now) {
                $q->where('expired_date', '>=', $now);
            }
        ])->findOrFail($id);
        $tour->prices->transform(function ($price) use ($usdrates, $tax) {
            $price->calculated_price = $price->calculatePrice($usdrates, $tax);
            return $price;
        });
        
        $action_log = ActionLog::where('service',"Tour Package")
        ->where('service_id',$id)->get();
        return view('backend.operations.tours.detail',[
            'usdrates'=>$usdrates,
            'tour'=>$tour, 
            'action_log'=>$action_log,
            'user'=>$user,
            'tax'=>$tax,
        ]);
    }
// View Tour Edit =============================================================================================================>
    public function view_edit_tour($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour=Tours::with(['locations' => fn ($query) => $query->ordered()])->findOrFail($id);
            $usdrates = UsdRates::where('name','USD')->first();
            $types = TourType::all();
            return view('backend.operations.tours.forms.edit', compact("types"),[
                'usdrates'=>$usdrates,
            ])->with('tour',$tour);
        }else{
            return redirect()->route('admin.tour-packages.index')->with('error',__('messages.You are not authorized to perform this action.'));
        }
    }
// View Add Tours =========================================================================================>
    public function view_add_tour()
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tours = Tours::all();
            $partners = Partners::all();
            $types = TourType::all();
            return view('backend.operations.tours.forms.create',compact("types"),[
                'partners'=>$partners,
            ])->with('tours',$tours);
        }else{
            return redirect()->route('admin.tour-packages.index')->with('error',__('messages.You are not authorized to perform this action.'));
        }
    }

// Function Add Tours =========================================================================================>
    public function func_add_tour(Request $request)
    {
        // 🔹 Validasi form input
        $locations = $this->validatedTourLocations($request);
        $validated = $request->validate($this->tourValidationRules());

        // 🔹 Upload Cover Image
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            // $filePath = $file->move('storage/tours/tours-cover', $filename, 'public');
            $file->storeAs('tours/tours-cover', $filename);
            $validated['cover'] = $filename;
        }

        // 🔹 Simpan ke Database
        $tour = DB::transaction(function () use ($validated, $locations) {
            $tour = new Tours();
            $this->fillTourDetails($tour, $validated);
            $tour->save();

            $this->syncTourLocations($tour, $locations);

            return $tour;
        });

        // 🔹 Redirect dengan pesan sukses
        return redirect()->route('admin.tours.show', $tour->id)->with('success','New Tour Package has been successfully created!');
        // return redirect()->back()->with('success', 'Tour package has been successfully created!');
    }

// Function Add Tours =========================================================================================>
    public function func_add_tour_price(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour = Tours::where('id',$id)->first();
            $expired_date = date('Y-m-d',strtotime($request->expired_date));
            $status = "Draft";
            $price =new TourPrices([
                "tour_id"=>$id,
                "min_qty"=>$request->min_qty,
                "max_qty"=>$request->max_qty,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "expired_date"=>$expired_date,
                "status"=>$status,
            ]);
            $price->save();
            // USER LOG
            $author = Auth::user()->id;
            $action = "Add Tour Price";
            $service = "Tour";
            $subservice = "Tour Package";
            $page = "detail-tour";
            $note = "Add Tour Price: ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect()->route('admin.tours.show', $id)->withFragment('prices')->with('success','New Tour Package Price has been successfully created!');
        }else{
            return redirect()->route('admin.tour-packages.index')->with('error',__('messages.You are not authorized to perform this action.'));
        }
    }


// function Update Tour PRICE =============================================================================================================>
    public function func_update_tour_price(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour_price=TourPrices::findOrFail($id);
            $tour_price->update([
                "min_qty"=>$request->min_qty,
                "max_qty"=>$request->max_qty,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "expired_date"=>$request->expired_date,
                "status"=>$request->status,
            ]);

            // USER LOG
            $author = Auth::user()->id;
            $action = "Update Tour Price";
            $service = "Tour";
            $subservice = "Price";
            $page = "detail-tour";
            $note = "Update Tour Price: ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect()->route('admin.tours.show', $tour_price->tour_id)->withFragment('prices')->with('success','The Tour Price has been successfully updated!');
        }else{
            return redirect()->route('admin.tour-packages.index')->with('error',__('messages.You are not authorized to perform this action.'));
        }
    }
// FUNCTION DELETE TOUR PRICE
    public function func_delete_tour_price(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour_price=TourPrices::findOrFail($id);
            $action="Delete Tour Price";
            $author= Auth::user()->id;
            $tour_price->delete();
            // USER LOG
            $action = "Remove";
            $service = "Tour Package";
            $subservice = "Price";
            $page = "detail-tour";
            $note = "Remove Tour Price on Tour : ".$tour_price->tour_id.", Price id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect()->route('admin.tours.show', $tour_price->tour_id)->withFragment('prices')->with('success','The Tour Price has been successfully deleted!');
        }else{
            return redirect()->route('admin.tour-packages.index')->with('error',__('messages.You are not authorized to perform this action.'));
        }
    }
// function Update Tour =============================================================================================================>
    
    public function func_update_tour(Request $request,$id)
    {
        $tour = Tours::findOrFail($id);
        $locations = $this->validatedTourLocations($request);
        $validated = $request->validate($this->tourValidationRules(true));

        
        if($request->hasFile("cover")){
            if ($tour->cover && Storage::disk('public')->exists('tours/tours-cover/' . $tour->cover)) {
                Storage::disk('public')->delete('tours/tours-cover/' . $tour->cover);
            }
            $file = $request->file('cover');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('tours/tours-cover', $filename);
            $validated['cover'] = $filename;
        
        } else {
            $validated['cover'] = $tour->cover;
        }
        DB::transaction(function () use ($tour, $validated, $locations) {
            $this->fillTourDetails($tour, $validated, true);
            $tour->save();

            $this->syncTourLocations($tour, $locations);
        });
        return redirect()->route('admin.tours.show', $tour->id)->with('success','The Tour Package has been successfully updated!');
    }
// function Tour Remove =============================================================================================================>
    public function remove_tour(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour=Tours::findOrFail($id);
            $status = "Removed";
            $author = Auth::user()->id;
            $tour->update([
                "status"=>$status,
            ]);
            // USER LOG
            $action = "Remove Tour";
            $service = "Tour";
            $subservice = "Tour Package";
            $page = "tours-admin";
            $note = "Remove Tour Package: ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return back()->with('success','The Tour Package has been successfully deleted!');
        }else{
            return redirect()->route('admin.tour-packages.index')->with('error',__('messages.You are not authorized to perform this action.'));
        }
    }

    // Function Add Image Galery
    public function add_galery_img(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'tour_id' => 'required|integer|exists:tours,id',
        ]);

        // Ambil file yang diupload
        $file = $request->file('file');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // Simpan file ke storage
        $filePath = $file->move('storage/tours/tours-galleries', $filename, 'public');
        // Simpan ke database
        $image = ToursImages::create([
            'tour_id' => $request->tour_id,
            'image' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'image_id' => $image->id,
            'path' => asset('storage/' . $filePath),
        ]);
    }

    public function destroy_galery_img($id)
    {
        $image = ToursImages::findOrFail($id);

        // Hapus file dari storage
        // if (File::exists($tour->cover)) {
        //     File::delete($tour->cover);
        // }
        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        // Hapus record database
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }

    private function tourValidationRules(bool $isUpdate = false): array
    {
        return [
            'cover' => ($isUpdate ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => $isUpdate ? 'required|string|max:255' : 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:125',
            'name_traditional' => 'required|string|max:255',
            'name_simplified' => 'required|string|max:255',
            'type' => 'required|integer|exists:tour_types,id',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
            'short_description' => 'required|string',
            'short_description_traditional' => 'required|string',
            'short_description_simplified' => 'required|string',
            'description' => 'required|string',
            'description_traditional' => 'required|string',
            'description_simplified' => 'required|string',
            'package_highlights' => 'nullable|string',
            'package_highlights_traditional' => 'nullable|string',
            'package_highlights_simplified' => 'nullable|string',
            'itinerary' => 'required|string',
            'itinerary_traditional' => 'required|string',
            'itinerary_simplified' => 'required|string',
            'include' => 'required|string',
            'include_traditional' => 'required|string',
            'include_simplified' => 'required|string',
            'exclude' => 'required|string',
            'exclude_traditional' => 'required|string',
            'exclude_simplified' => 'required|string',
            'additional_info' => 'required|string',
            'additional_info_traditional' => 'required|string',
            'additional_info_simplified' => 'required|string',
            'cancellation_policy' => 'required|string',
            'cancellation_policy_traditional' => 'required|string',
            'cancellation_policy_simplified' => 'required|string',
        ];
    }

    private function fillTourDetails(Tours $tour, array $validated, bool $isUpdate = false): void
    {
        $tour->cover = $validated['cover'];

        if ($isUpdate) {
            $tour->status = $validated['status'];
        }

        $tour->type_id = $validated['type'];

        foreach ($this->tourDetailFields() as $field) {
            $tour->{$field} = $validated[$field] ?? null;
        }
    }

    private function tourDetailFields(): array
    {
        return [
            'code',
            'name',
            'name_traditional',
            'name_simplified',
            'duration_days',
            'duration_nights',
            'short_description',
            'short_description_traditional',
            'short_description_simplified',
            'description',
            'description_traditional',
            'description_simplified',
            'package_highlights',
            'package_highlights_traditional',
            'package_highlights_simplified',
            'itinerary',
            'itinerary_traditional',
            'itinerary_simplified',
            'include',
            'include_traditional',
            'include_simplified',
            'exclude',
            'exclude_traditional',
            'exclude_simplified',
            'additional_info',
            'additional_info_traditional',
            'additional_info_simplified',
            'cancellation_policy',
            'cancellation_policy_traditional',
            'cancellation_policy_simplified',
        ];
    }

    public function resolveTourLocationCoordinates(Request $request)
    {
        $request->validate([
            'google_maps_url' => 'required|url|max:2048',
        ]);

        $url = trim((string) $request->input('google_maps_url'));

        if (!$this->isAllowedGoogleMapsUrl($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps link must be a valid Google Maps URL.',
            ], 422);
        }

        $coordinates = $this->extractGoogleMapsCoordinates($url);

        if (!$coordinates) {
            return response()->json([
                'success' => false,
                'message' => 'Coordinates could not be read from this link. Please use a Google Maps URL containing coordinates, or fill latitude and longitude manually.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'latitude' => round((float) $coordinates['latitude'], 7),
            'longitude' => round((float) $coordinates['longitude'], 7),
        ]);
    }

    public function searchTourLocationReferences(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        if (Str::length($query) < 2) {
            return response()->json([]);
        }

        return TourLocationReference::query()
            ->where('destination_name', 'like', '%' . $query . '%')
            ->orderBy('destination_name')
            ->limit(12)
            ->get()
            ->map(fn (TourLocationReference $location) => [
                'id' => $location->id,
                'destination_name' => $location->destination_name,
                'location_type' => $location->location_type,
                'google_maps_url' => $location->google_maps_url,
                'marker_image' => $location->marker_image,
                'marker_image_url' => $location->marker_image ? asset('storage/tours/tour-location-markers/' . $location->marker_image) : null,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'description' => $location->description,
            ]);
    }

    private function validatedTourLocations(Request $request): array
    {
        $locations = (array) $request->input('locations', []);
        $errors = [];
        $normalized = [];

        foreach ($locations as $inputIndex => $location) {
            if (collect($location)->filter(fn ($value) => filled($value))->isEmpty() && !$request->hasFile("locations.{$inputIndex}.marker_image")) {
                continue;
            }

            $prefix = "locations.{$inputIndex}";
            $locationReferenceId = $location['location_reference_id'] ?? null;
            $name = trim((string) ($location['destination_name'] ?? ''));
            $locationType = trim((string) ($location['location_type'] ?? 'Attraction'));
            $googleMapsUrl = trim((string) ($location['google_maps_url'] ?? ''));
            $existingMarkerImage = trim((string) ($location['existing_marker_image'] ?? ''));
            $latitude = $location['latitude'] ?? null;
            $longitude = $location['longitude'] ?? null;
            $dayNumber = $location['day_number'] ?? null;
            $visitOrder = $location['visit_order'] ?? null;
            $visitTime = $location['visit_time'] ?? null;
            $markerImage = $existingMarkerImage ?: null;
            $markerImageFile = $request->file("locations.{$inputIndex}.marker_image");

            if ($name === '') {
                $errors["{$prefix}.destination_name"] = 'Destination name is required when adding a map location.';
            }

            if (!in_array($locationType, ['Attraction', 'Activity', 'F&B', 'Pickup/Dropoff'], true)) {
                $errors["{$prefix}.location_type"] = 'Location type must be Attraction, Activity, F&B, or Pickup/Dropoff.';
            }

            if (!filter_var($dayNumber, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                $errors["{$prefix}.day_number"] = 'Day number must be a positive integer.';
            }

            if (!filter_var($visitOrder, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                $errors["{$prefix}.visit_order"] = 'Visit order must be a positive integer.';
            }

            if (filled($visitTime) && !preg_match('/^\d{2}:\d{2}$/', (string) $visitTime)) {
                $errors["{$prefix}.visit_time"] = 'Visit time must use HH:mm format.';
            }

            if ((!is_numeric($latitude) || !is_numeric($longitude)) && $googleMapsUrl !== '' && $this->isAllowedGoogleMapsUrl($googleMapsUrl)) {
                $coordinates = $this->extractGoogleMapsCoordinates($googleMapsUrl);

                if ($coordinates) {
                    $latitude = $coordinates['latitude'];
                    $longitude = $coordinates['longitude'];
                }
            }

            if (!is_numeric($latitude) || (float) $latitude < -90 || (float) $latitude > 90) {
                $errors["{$prefix}.latitude"] = 'Latitude must be a number between -90 and 90, or use a Google Maps URL that exposes coordinates.';
            }

            if (!is_numeric($longitude) || (float) $longitude < -180 || (float) $longitude > 180) {
                $errors["{$prefix}.longitude"] = 'Longitude must be a number between -180 and 180, or use a Google Maps URL that exposes coordinates.';
            }

            if ($googleMapsUrl !== '' && !$this->isAllowedGoogleMapsUrl($googleMapsUrl)) {
                $errors["{$prefix}.google_maps_url"] = 'Google Maps link must be a valid Google Maps URL.';
            }

            if ($markerImageFile) {
                if (!$markerImageFile->isValid() || !in_array($markerImageFile->extension(), ['jpg', 'jpeg', 'png', 'webp'], true) || $markerImageFile->getSize() > 2048 * 1024) {
                    $errors["{$prefix}.marker_image"] = 'Marker image must be a valid JPG, PNG, or WEBP image with maximum size 2MB.';
                } else {
                    $filename = time() . '_' . Str::random(10) . '.' . $markerImageFile->getClientOriginalExtension();
                    $markerImageFile->storeAs('tours/tour-location-markers', $filename, 'public');
                    $markerImage = $filename;
                }
            }

            $normalized[] = [
                'location_reference_id' => $locationReferenceId ? (int) $locationReferenceId : null,
                'destination_name' => $name,
                'location_type' => $locationType,
                'google_maps_url' => $googleMapsUrl ?: null,
                'marker_image' => $markerImage,
                'latitude' => is_numeric($latitude) ? round((float) $latitude, 7) : null,
                'longitude' => is_numeric($longitude) ? round((float) $longitude, 7) : null,
                'day_number' => (int) $dayNumber,
                'visit_order' => (int) $visitOrder,
                'visit_time' => filled($visitTime) ? $visitTime : null,
                'description' => filled($location['description'] ?? null) ? trim((string) $location['description']) : null,
                'is_active' => true,
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return $normalized;
    }

    private function syncTourLocations(Tours $tour, array $locations): void
    {
        $tour->locations()->delete();

        foreach ($locations as $location) {
            $location['location_reference_id'] = $this->syncTourLocationReference($location);
            $tour->locations()->create($location);
        }
    }

    private function syncTourLocationReference(array $location): int
    {
        if (!empty($location['location_reference_id'])) {
            $reference = TourLocationReference::find($location['location_reference_id']);

            if ($reference
                && $reference->destination_name === $location['destination_name']
                && $reference->location_type === $location['location_type']
                && round((float) $reference->latitude, 7) === round((float) $location['latitude'], 7)
                && round((float) $reference->longitude, 7) === round((float) $location['longitude'], 7)
            ) {
                return $reference->id;
            }
        }

        $lookupKey = TourLocationReference::lookupKey(
            $location['destination_name'],
            $location['location_type'],
            (float) $location['latitude'],
            (float) $location['longitude']
        );

        $reference = TourLocationReference::updateOrCreate(
            ['lookup_key' => $lookupKey],
            [
                'destination_name' => $location['destination_name'],
                'location_type' => $location['location_type'],
                'google_maps_url' => $location['google_maps_url'],
                'marker_image' => $location['marker_image'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'description' => $location['description'],
            ]
        );

        return $reference->id;
    }

    private function isAllowedGoogleMapsUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return in_array($host, [
            'google.com',
            'www.google.com',
            'maps.google.com',
            'maps.app.goo.gl',
            'goo.gl',
        ], true);
    }

    private function extractGoogleMapsCoordinates(string $url): ?array
    {
        foreach ($this->candidateMapUrls($url) as $candidateUrl) {
            $coordinates = $this->parseCoordinatesFromMapUrl($candidateUrl);

            if ($coordinates) {
                return $coordinates;
            }
        }

        return null;
    }

    private function candidateMapUrls(string $url): array
    {
        $urls = [$url];

        try {
            $response = Http::timeout(4)
                ->connectTimeout(3)
                ->withHeaders(['User-Agent' => 'BaliKamiTour/1.0'])
                ->get($url);

            $effectiveUrl = method_exists($response, 'effectiveUri')
                ? (string) $response->effectiveUri()
                : null;

            if ($effectiveUrl) {
                $urls[] = $effectiveUrl;
            }

            if ($response->header('Location')) {
                $urls[] = $response->header('Location');
            }

            if ($response->body()) {
                $urls[] = $response->body();
            }
        } catch (\Throwable $exception) {
            // Short Google Maps URLs may require external redirect resolution.
            // If that is unavailable, manual latitude/longitude remains the safe fallback.
        }

        return array_values(array_unique(array_filter($urls)));
    }

    private function parseCoordinatesFromMapUrl(string $value): ?array
    {
        $decoded = urldecode($value);

        $patterns = [
            '/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/',
            '/[?&](?:q|ll)=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/',
            '/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $decoded, $matches)) {
                $latitude = (float) $matches[1];
                $longitude = (float) $matches[2];

                if ($latitude >= -90 && $latitude <= 90 && $longitude >= -180 && $longitude <= 180) {
                    return [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ];
                }
            }
        }

        return null;
    }
}
