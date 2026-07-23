<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Guide;
use App\Models\Review;
use App\Models\Drivers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\WeddingReview;
use App\Models\TemporaryReviewLink;
use Illuminate\Support\Facades\Storage;
use App\Models\TemporaryWeddingReviewLink;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReviewController extends Controller
{
    public function index()
    {
        $guides = Guide::where('status', 'active')->get();
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

    public function wedding_review_index()
    {
        $reviews = WeddingReview::latest()->paginate(20);
        $weddingStats = WeddingReview::weddingStats();
        $pendingReviews = WeddingReview::where('status', 'pending')
            ->orderBy('booking_code', 'desc')
            ->latest()
            ->get();
        $approvedReviews = WeddingReview::where('status', 'accepted')
            ->orderBy('created_at', 'desc')
            ->latest()
            ->paginate(10, ['*'], 'approved_page');
        $rejectedReviews = WeddingReview::where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->latest()
            ->paginate(10, ['*'], 'rejected_page');
        return view('frontend.home.reviews.wedding-index', compact('reviews', 'weddingStats',  'pendingReviews', 'approvedReviews','rejectedReviews'));
    }

    public function print_wedding_reviews($bookingCode)
    {
        $reviews = WeddingReview::where('booking_code', $bookingCode)
            ->where('status', 'accepted')
            ->get();
        $temporary_link = TemporaryWeddingReviewLink::where('booking_code', $bookingCode)->first();
        $averageRatings = [
            'communication_efficiency' => round($reviews->avg('communication_efficiency'), 1),
            'workflow_planning' => round($reviews->avg('workflow_planning'), 1),
            'material_preparation' => round($reviews->avg('material_preparation'), 1),
            'service_attitude' => round($reviews->avg('service_attitude'), 1),
            'execution_of_workflow' => round($reviews->avg('execution_of_workflow'), 1),
            'time_management' => round($reviews->avg('time_management'), 1),
            'guest_care' => round($reviews->avg('guest_care'), 1),
            'team_coordination' => round($reviews->avg('team_coordination'), 1),
            'third_party_coordination' => round($reviews->avg('third_party_coordination'), 1),
            'problem_solving_ability' => round($reviews->avg('problem_solving_ability'), 1),
            'wrap_up_and_item_check' => round($reviews->avg('wrap_up_and_item_check'), 1),
        ];
        $moodValues = [
            'Very Satisfied' => 4,
            'Satisfied' => 3,
            'Normal' => 2,
            'Need Improvement' => 1,
        ];

        $validMoodScores = $reviews->pluck('couple_mood')
            ->filter()
            ->map(fn($mood) => array_key_exists($mood, $moodValues) ? $moodValues[$mood] : null)
            ->filter();

        if ($validMoodScores->count() > 0) {
            $averageMoodScore = round($validMoodScores->avg(), 1);

            $reverseMoodValues = array_flip($moodValues);
            $roundedScore = round($averageMoodScore);
            $averageMoodLabel = $reverseMoodValues[$roundedScore] ?? 'Unknown';
        } else {
            $averageMoodScore = null;
            $averageMoodLabel = 'No data';
        }
        return view('frontend.home.reviews.print-wedding-reviews', compact('reviews', 'bookingCode', 'averageRatings','averageMoodLabel', 'averageMoodScore','temporary_link'));
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
                '🌟 We appreciate your feedback!',
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
    public function store_wedding_review(Request $request)
    {
        try {
            $booking_code = $request->booking_code;
            $validated = $request->validate([
                'booking_code' => 'nullable|string',
                'communication_efficiency' => 'nullable|integer|between:1,5',
                'workflow_planning' => 'nullable|integer|between:1,5',
                'material_preparation' => 'nullable|integer|between:1,5',
                'service_attitude' => 'nullable|integer|between:1,5',
                'execution_of_workflow' => 'nullable|integer|between:1,5',
                'time_management' => 'nullable|integer|between:1,5',
                'guest_care' => 'nullable|integer|between:1,5',
                'team_coordination' => 'nullable|integer|between:1,5',
                'third_party_coordination' => 'nullable|integer|between:1,5',
                'problem_solving_ability' => 'nullable|integer|between:1,5',
                'wrap_up_and_item_check' => 'nullable|integer|between:1,5',
                'couple_mood' => 'nullable|string',
                'customer_name' => 'nullable|string',
                'customer_review' => 'nullable|string',
            ]);
            \Log::info('Validated review data:', $validated); // Log untuk debugging
            $temporary_review_link = TemporaryWeddingReviewLink::where('booking_code', $booking_code)->first();
            $validated['wedding_organizer'] = $temporary_review_link->wedding_organizer;
            $validated['wedding_date'] = $temporary_review_link->wedding_date;
            $validated['groom'] = $temporary_review_link->groom;
            $validated['bride'] = $temporary_review_link->bride;
            $review = WeddingReview::create($validated);
            if (!$review) {
                throw new \Exception('Failed to create review');
            }
            \Log::info('Review successfully saved:', $review->toArray());
            $link = TemporaryWeddingReviewLink::where('booking_code', $request->booking_code)->first();
            if ($link) {
                $link->increment('submitted_count');
                \Log::info("Review count updated for booking_code: {$request->booking_code}");
            } else {
                \Log::warning("TemporaryWeddingReviewLink not found for booking_code: {$request->booking_code}");
            }
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Review submitted successfully.']);
            }
            return redirect()->back()->with('success', [
                '🌟 We appreciate your feedback!',
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
    public function updateWeddingStatus(Request $request, WeddingReview $wedding_review)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);
        $wedding_review->status = $request->status;
        $wedding_review->save();
        return redirect()->back()->with('success', 'Review status updated successfully.');
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
    public function showWeddingForm()
    {
        $weddingReviewLinks = TemporaryWeddingReviewLink::where('expires_at', '>=', Carbon::now())
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('frontend.home.reviews.wedding_review_link_form', compact('weddingReviewLinks'));
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

    public function generate_wedding_review_link(Request $request)
    {
        $request->validate([
            'wedding_organizer' => 'required|string',
            'booking_code' => 'required|string|unique:temporary_wedding_review_links,booking_code',
            'jumlah_review' => 'required|integer|min:1',
        ]);
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
        $booking_code = Str::upper($request->booking_code);
        $jumlah_review = $request->jumlah_review;
        $groom = $request->groom;
        $bride = $request->bride;
        $link = "http://reviewyourwedding.fwh.is/{$booking_code}/{$jumlah_review}";
        $expiresAt = Carbon::now()->addHours(168);
        $qrSvg = QrCode::format('svg')->size(300)->generate($link);
        $filename = "{$booking_code}.svg";
        Storage::disk('public')->put("wedding-reviews/qrcodes/{$filename}", $qrSvg);
        TemporaryWeddingReviewLink::create([
            'wedding_organizer' => $request->wedding_organizer,
            'booking_code' => $booking_code,
            'groom' => $groom,
            'bride' => $bride,
            'jumlah_review' => $request->jumlah_review,
            'expires_at' => $expiresAt,
            'qr_code_path' => $filename,
            'link' => $link,
            'wedding_date' => $wedding_date,
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

    public function validate_wedding_review_link($booking_code, $jumlah_review)
    {
        $link = TemporaryWeddingReviewLink::where('booking_code', $booking_code)->first();
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
