<?php

namespace App\Observers;

use App\Models\Rental;
use App\Models\Review;

class ReviewObserver
{
    public function saved(Review $review): void
    {
        $this->refreshAggregate($review->rental_id);
    }

    public function deleted(Review $review): void
    {
        $this->refreshAggregate($review->rental_id);
    }

    public function restored(Review $review): void
    {
        $this->refreshAggregate($review->rental_id);
    }

    public function forceDeleted(Review $review): void
    {
        $this->refreshAggregate($review->rental_id);
    }

    private function refreshAggregate(?int $rentalId): void
    {
        if ($rentalId === null) {
            return;
        }

        Rental::query()->find($rentalId)?->syncRatingAggregate();
    }
}
