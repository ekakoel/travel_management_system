<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\Hotels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIHotelController extends Controller
{
    /**
     * Search active hotels for the AI service.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Hotels::query()
            ->active()
            ->select([
                'id',
                'code',
                'name',
                'region',
                'address',
                'airport_duration',
                'airport_distance',
                'description',
                'description_traditional',
                'description_simplified',
                'facility',
                'facility_traditional',
                'facility_simplified',
                'additional_info',
                'additional_info_traditional',
                'additional_info_simplified',
                'status',
                'cover',
                'web',
                'min_stay',
                'max_stay',
                'check_in_time',
                'check_out_time',
                'map',
                'benefits',
                'benefits_traditional',
                'benefits_simplified',
                'cancellation_policy',
                'cancellation_policy_traditional',
                'cancellation_policy_simplified',
            ]);

        if ($request->filled('hotel_name')) {
            $query->where(
                'name',
                'like',
                '%' . trim($request->input('hotel_name')) . '%'
            );
        }

        if ($request->filled('hotel_region')) {
            $query->where(
                'region',
                'like',
                '%' . trim($request->input('hotel_region')) . '%'
            );
        }

        if ($request->filled('code')) {
            $query->where('code', $request->input('code'));
        }

        $perPage = min(
            max((int) $request->input('per_page', 20), 1),
            50
        );

        $hotels = $query
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $hotels->items(),
            'pagination' => [
                'current_page' => $hotels->currentPage(),
                'per_page' => $hotels->perPage(),
                'total' => $hotels->total(),
                'last_page' => $hotels->lastPage(),
            ],
        ]);
    }

    /**
     * Get a single active hotel by hotel code.
     */
    public function show(string $code): JsonResponse
    {
        $hotel = Hotels::query()
            ->active()
            ->select([
                'id',
                'code',
                'name',
                'region',
                'address',
                'airport_duration',
                'airport_distance',
                'description',
                'description_traditional',
                'description_simplified',
                'facility',
                'facility_traditional',
                'facility_simplified',
                'additional_info',
                'additional_info_traditional',
                'additional_info_simplified',
                'status',
                'cover',
                'web',
                'min_stay',
                'max_stay',
                'check_in_time',
                'check_out_time',
                'map',
                'benefits',
                'benefits_traditional',
                'benefits_simplified',
                'cancellation_policy',
                'cancellation_policy_traditional',
                'cancellation_policy_simplified',
            ])
            ->where('code', $code)
            ->first();

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hotel,
        ]);
    }
}