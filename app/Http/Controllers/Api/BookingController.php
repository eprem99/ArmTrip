<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Rental;
use App\Services\RentalAvailabilityService;
use App\Services\RentalStayPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(
        private readonly RentalAvailabilityService $availability,
        private readonly RentalStayPricingService $pricing,
    ) {}

    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->where('user_id', $request->user()->id)
            ->with(['rental' => fn ($q) => $q->with([
                'type:id,name,slug',
                'location:id,name,slug,kind,parent_id',
                'images' => fn ($iq) => $iq->select(['id', 'rental_id', 'path', 'alt', 'sort_order', 'is_primary'])->orderBy('sort_order'),
            ])])
            ->orderByDesc('check_in')
            ->paginate(min(max((int) $request->query('per_page', 15), 1), 100));

        return BookingResource::collection($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();

        $rental = Rental::query()->findOrFail($data['rental_id']);

        if (! $rental->is_active || $rental->published_at === null || $rental->published_at->isFuture()) {
            throw ValidationException::withMessages([
                'rental_id' => __('This rental is not available for booking.'),
            ]);
        }

        if ((int) $data['guests'] > (int) $rental->max_guests) {
            throw ValidationException::withMessages([
                'guests' => __('Guests exceed the maximum allowed for this rental.'),
            ]);
        }

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();

        $this->availability->assertNoOverlap($rental->id, $checkIn, $checkOut);

        $quote = $this->pricing->quote($rental, $checkIn, $checkOut);

        $booking = DB::transaction(function () use ($request, $rental, $data, $checkIn, $checkOut, $quote) {
            return Booking::query()->create([
                'rental_id' => $rental->id,
                'user_id' => $request->user()->id,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'guests' => $data['guests'],
                'total_amount' => $quote['total'],
                'currency' => $quote['currency'],
                'status' => Booking::STATUS_PENDING,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        $booking->load(['rental.type', 'rental.location', 'rental.images']);

        return (new BookingResource($booking))->response()->setStatusCode(201);
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['rental.type', 'rental.location', 'rental.images']);

        return new BookingResource($booking);
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if (! in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)) {
            throw ValidationException::withMessages([
                'status' => __('This booking cannot be cancelled.'),
            ]);
        }

        $booking->status = Booking::STATUS_CANCELLED;
        $booking->save();

        return new BookingResource($booking);
    }
}
