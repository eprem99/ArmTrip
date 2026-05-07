<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RentalAvailabilityService
{
    public function hasOverlap(int $rentalId, Carbon $checkIn, Carbon $checkOut, ?int $ignoreBookingId = null): bool
    {
        return $this->overlapQuery($rentalId, $checkIn, $checkOut, $ignoreBookingId)->exists();
    }

    public function assertNoOverlap(int $rentalId, Carbon $checkIn, Carbon $checkOut, ?int $ignoreBookingId = null): void
    {
        if ($this->hasOverlap($rentalId, $checkIn, $checkOut, $ignoreBookingId)) {
            throw ValidationException::withMessages([
                'check_in' => __('The selected dates are not available.'),
            ]);
        }
    }

    private function overlapQuery(int $rentalId, Carbon $checkIn, Carbon $checkOut, ?int $ignoreBookingId)
    {
        return Booking::query()
            ->where('rental_id', $rentalId)
            ->whereIn('status', Booking::ACTIVE_OVERLAP_STATUSES)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn);
    }
}
