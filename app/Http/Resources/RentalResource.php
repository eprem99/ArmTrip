<?php

namespace App\Http\Resources;

use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Rental */
class RentalResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'currency' => $this->currency,
            'max_guests' => $this->max_guests,
            'bedrooms' => $this->bedrooms,
            'beds' => $this->beds,
            'bathrooms' => $this->bathrooms,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'rating_average' => $this->rating_average,
            'ratings_count' => $this->ratings_count,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => $this->whenLoaded('type', fn () => [
                'id' => $this->type->id,
                'name' => $this->type->name,
                'slug' => $this->type->slug,
            ]),
            'location' => $this->whenLoaded('location', fn () => [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'slug' => $this->location->slug,
                'kind' => $this->location->kind,
                'parent_id' => $this->location->parent_id,
            ]),
            'images' => RentalImageResource::collection($this->whenLoaded('images')),
            'amenities' => $this->whenLoaded('amenities', fn () => $this->amenities->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'slug' => $a->slug,
                'icon' => $a->icon,
            ])),
            'is_favorited' => $this->when(
                $request->user() !== null,
                fn () => (bool) $this->resource->getAttribute('is_favorited')
            ),
            'owner' => $this->whenLoaded('owner', function () use ($request) {
                $base = [
                    'id' => $this->owner->id,
                    'name' => $this->owner->name,
                ];
                if ($request->user()?->type === 'admin') {
                    $base['email'] = $this->owner->email;
                }

                return $base;
            }),
        ];
    }
}
