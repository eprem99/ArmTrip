<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\RentalPrice;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RentalStayPricingService
{
    /**
     * @return array{nights: int, total: string, currency: string, breakdown: array<int, array{date: string, price: string}>}
     */
    public function quote(Rental $rental, Carbon $checkIn, Carbon $checkOut): array
    {
        if ($checkIn->greaterThanOrEqualTo($checkOut)) {
            throw ValidationException::withMessages([
                'check_out' => __('Check-out must be after check-in.'),
            ]);
        }

        $currency = $rental->currency;
        $breakdown = [];
        $total = '0';

        $cursor = $checkIn->copy()->startOfDay();
        $end = $checkOut->copy()->startOfDay();

        $dates = [];
        while ($cursor->lt($end)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $overrides = RentalPrice::query()
            ->where('rental_id', $rental->id)
            ->whereIn('date', $dates)
            ->get()
            ->keyBy(fn (RentalPrice $p) => $p->date->toDateString());

        foreach ($dates as $dateStr) {
            $override = $overrides->get($dateStr);
            $night = $override !== null
                ? (string) $override->price
                : (string) $rental->base_price;
            if ($override !== null && $override->currency) {
                $currency = $override->currency;
            }

            $breakdown[] = [
                'date' => $dateStr,
                'price' => $night,
            ];

            $total = $this->addMoney($total, $night);
        }

        return [
            'nights' => count($dates),
            'total' => $total,
            'currency' => $currency,
            'breakdown' => $breakdown,
        ];
    }

    private function addMoney(string $a, string $b): string
    {
        if (function_exists('bcadd')) {
            return bcadd($a, $b, 2);
        }

        return number_format((float) $a + (float) $b, 2, '.', '');
    }
}
