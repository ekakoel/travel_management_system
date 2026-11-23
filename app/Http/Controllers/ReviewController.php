<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Guide;
use App\Models\Review;
use App\Models\Drivers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TemporaryReviewLink;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReviewController extends Controller
{
    public function index()
    {
        $guides = Guide::all();
        $drivers = Drivers::where('status', 'active')->get();
        $reviews = Review::with(['guide', 'driver'])->latest()->paginate(20);
        $serviceStats = Review::serviceStats();
        $transportStats = Review::transportStats();
        $guideStats = Review::guideStats();
        $driverStats = Review::driverStats();
        $pendingReviews = Review::with(['guide', 'driver'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'pending_page');
        $approvedReviews = Review::with(['guide', 'driver'])
            ->where('status', 'accepted')
            ->latest()
            ->paginate(10, ['*'], 'approved_page');
        $rejectedReviews = Review::with(['guide', 'driver'])
            ->where('status', 'rejected')
            ->latest()
            ->paginate(10, ['*'], 'rejected_page');
        $summary = [
            'total' => Review::count(),
            'pending' => Review::where('status', 'pending')->count(),
            'accepted' => Review::where('status', 'accepted')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('backend.admin.reviews.index', compact('reviews', 'serviceStats', 'pendingReviews', 'approvedReviews', 'rejectedReviews', 'transportStats', 'guideStats', 'driverStats', 'guides', 'drivers', 'summary'));
    }



    public function create()
    {
        $driver_questions = [
            'driver' => 'Service',
            'transportation' => 'Transportation',
        ];
        
        $guide_questions = [
            'guide_service' => 'Service',
            'time_control' => 'Time Control',
            'knowledge' => 'Knowledge',
            'explanation' => 'Explanation',
        ];
        
        $service_questions = [
            'accommodation' => 'Accommodation',
            'meal' => 'Meal',
            'tour_sites' => 'Tour Sites',
        ];

        $options = [
            1 => 'Very Bad',
            2 => 'Need Improvement',
            3 => 'Neutral',
            4 => 'Satisfied',
            5 => 'Very Satisfied',
        ];
        $moods = [
            'Very Satisfied' => 'Very Satisfied',
            'Satisfied' => 'Satisfied',
            'Neutral' => 'Neutral',
            'Need Improvement' => 'Need Improvement',
        ];
        $guides = Guide::where('status', 'active')->get();
        $drivers = Drivers::where('status', 'active')->get();
        return view('frontend.home.reviews.create', compact('driver_questions','guide_questions','service_questions', 'options', 'moods', 'guides','drivers'));
    }

    public function store(Request $request)
    {
        try {
            $booking_code = $request->booking_code;
            $validated = $request->validate([
                'booking_code' => 'nullable|string',
                'accommodation' => 'nullable|integer|between:1,5',
                'meals' => 'nullable|integer|between:1,5',
                'tour_sites' => 'nullable|integer|between:1,5',
                'transportation_cleanliness' => 'nullable|integer|between:1,5',
                'transportation_air_condition' => 'nullable|integer|between:1,5',
                'driver_name' => 'nullable|string',
                'driver_punctuality' => 'nullable|integer|between:1,5',
                'driver_driving_skills' => 'nullable|integer|between:1,5',
                'driver_neatness' => 'nullable|integer|between:1,5',
                'guide_name' => 'nullable|string',
                'attitude' => 'nullable|integer|between:1,5',
                'explanation' => 'nullable|integer|between:1,5',
                'knowledge' => 'nullable|integer|between:1,5',
                'time_control' => 'nullable|integer|between:1,5',
                'guide_neatness' => 'nullable|integer|between:1,5',
                'travel_mood' => 'nullable|string',
                'customer_name' => 'nullable|string',
                'customer_review' => 'nullable|string',
            ]);
            \Log::info('Validated review data:', $validated); // Log untuk debugging
            $temporary_review_link = TemporaryReviewLink::where('booking_code', $booking_code)->first();
            $validated['travel_agent'] = $temporary_review_link->agent;
            $validated['arrival_date'] = $temporary_review_link->arrival_date;
            $validated['departure_date'] = $temporary_review_link->departure_date;
            $review = Review::create($validated);
            if (!$review) {
                throw new \Exception('Failed to create review');
            }
            \Log::info('Review successfully saved:', $review->toArray());
            $link = TemporaryReviewLink::where('booking_code', $request->booking_code)->first();
            if ($link) {
                $link->increment('submitted_count');
                \Log::info("Review count updated for booking_code: {$request->booking_code}");
            } else {
                \Log::warning("TemporaryReviewLink not found for booking_code: {$request->booking_code}");
            }
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Review submitted successfully.']);
            }
            return redirect()->back()->with('success', [
                'ðŸŒŸ We appreciate your feedback!',
                'Your voice matters. Thank you for helping us become better, one review at a time.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error while saving review: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to save review: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Failed to save review: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);
        $review->status = $request->status;
        $review->save();
        return redirect()->back()->with('success', 'Review status updated successfully.');
    }

    public function destroy(Review $review)
    {
        if ($review->status === 'accepted') {
            return redirect()->back()->withErrors(['review' => 'Approved reviews cannot be deleted. Reject the review first if it must be removed.']);
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    public function generateReviewLink(Request $request)
    {
        $request->validate([
            'jumlah_review' => 'required|integer|min:1',
        ]);
        do {
            $bookingCode = Str::upper(Str::random(8));
        } while (TemporaryReviewLink::where('booking_code', $bookingCode)->exists());
        $expiresAt = Carbon::now()->addHours(168);
        $link = TemporaryReviewLink::create([
            'booking_code' => $bookingCode,
            'jumlah_review' => $request->jumlah_review,
            'expires_at' => $expiresAt,
        ]);
        $url = url("/{$bookingCode}/{$request->jumlah_review}");
        return response()->json([
            'booking_code' => $bookingCode,
            'jumlah_review' => $request->jumlah_review,
            'expires_at' => $expiresAt,
            'link' => $url,
        ]);
    }
    
    public function showForm()
    {
        $reviewLinks = TemporaryReviewLink::where('expires_at', '>=', Carbon::now())
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('backend.admin.reviews.link-form', compact('reviewLinks'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'agent' => 'required|string|max:150',
            'booking_code' => 'required|string|max:50|unique:temporary_review_links,booking_code',
            'jumlah_review' => 'required|integer|min:1',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after_or_equal:arrival_date',
        ]);
        $arrival_date = Carbon::parse($validated['arrival_date'])->toDateString();
        $departure_date = Carbon::parse($validated['departure_date'])->toDateString();
        $booking_code = Str::upper($validated['booking_code']);
        $jumlah_review = $validated['jumlah_review'];
        $link = "http://tourreview.site/{$booking_code}/{$jumlah_review}";
        $expiresAt = Carbon::now()->addHours(168);
        $qrSvg = QrCode::format('svg')->size(300)->generate($link);
        $filename = "{$booking_code}.svg";
        Storage::disk('public')->put("reviews/qrcodes/{$filename}", $qrSvg);
        TemporaryReviewLink::create([
            'agent' => $validated['agent'],
            'booking_code' => $booking_code,
            'jumlah_review' => $jumlah_review,
            'expires_at' => $expiresAt,
            'qr_code_path' => $filename,
            'link' => $link,
            'arrival_date' => $arrival_date,
            'departure_date' => $departure_date,
        ]);
        return back()->with('success', "The review link has been successfully generated and is now ready to be shared with your customer.");
    }


    public function validateLink($booking_code, $jumlah_review)
    {
        $link = TemporaryReviewLink::where('booking_code', $booking_code)->first();
        if (!$link) {
            return response()->json(['valid' => false, 'message' => 'Link not found.'], 404);
        }
        if ($link->jumlah_review != $jumlah_review) {
            return response()->json(['valid' => false, 'message' => 'Invalid review count.'], 400);
        }
        if (now()->greaterThan($link->expires_at)) {
            return response()->json(['valid' => false, 'message' => 'Link expired.'], 410);
        }
        if ($link->submitted_count >= $link->jumlah_review) {
            return response()->json(['valid' => false, 'message' => 'Review limit reached.'], 403);
        }
        return response()->json(['valid' => true, 'message' => 'Link valid.']);
    }


}
