<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReviewRequest;
use App\Http\Requests\Api\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query()->with(['user:id,name']);

        if ($request->filled('rental_id')) {
            $query->where('rental_id', (int) $request->query('rental_id'));
        }

        if ($request->filled('rental_slug')) {
            $slug = (string) $request->query('rental_slug');
            $query->whereHas('rental', fn ($q) => $q->where('slug', $slug));
        }

        $query->orderByDesc('id');

        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);

        return ReviewResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(Review $review)
    {
        $review->load(['user:id,name']);

        return new ReviewResource($review);
    }

    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();

        $rental = Rental::query()->findOrFail($data['rental_id']);

        if ($data['booking_id'] ?? null) {
            $this->assertBookingEligibleForReview($request, $rental, (int) $data['booking_id']);
        }

        $review = DB::transaction(function () use ($request, $data) {
            return Review::query()->create([
                'rental_id' => $data['rental_id'],
                'user_id' => $request->user()->id,
                'booking_id' => $data['booking_id'] ?? null,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'body' => $data['body'] ?? null,
            ]);
        });

        $review->load(['user:id,name']);

        return (new ReviewResource($review))->response()->setStatusCode(201);
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $data = $request->validated();

        $review->fill($data);
        $review->save();

        $review->load(['user:id,name']);

        return new ReviewResource($review);
    }

    public function destroy(Request $request, Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(null, 204);
    }

    private function assertBookingEligibleForReview(StoreReviewRequest $request, Rental $rental, int $bookingId): void
    {
        $booking = Booking::query()->findOrFail($bookingId);

        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        if ((int) $booking->rental_id !== (int) $rental->id) {
            throw ValidationException::withMessages([
                'booking_id' => __('The booking does not belong to this rental.'),
            ]);
        }

        $eligible = $booking->status === Booking::STATUS_COMPLETED
            || (
                $booking->status === Booking::STATUS_CONFIRMED
                && $booking->check_out !== null
                && $booking->check_out->isBefore(now()->startOfDay())
            );

        if (! $eligible) {
            throw ValidationException::withMessages([
                'booking_id' => __('This booking is not eligible for a review yet.'),
            ]);
        }

        if (Review::query()->where('booking_id', $bookingId)->exists()) {
            throw ValidationException::withMessages([
                'booking_id' => __('A review already exists for this booking.'),
            ]);
        }
    }
}
