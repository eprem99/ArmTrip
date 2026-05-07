<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rental_type_id' => ['required', 'integer', 'exists:rental_types,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'max_guests' => ['required', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'beds' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:512'],
            'published_at' => ['nullable', 'date'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
            'images' => ['nullable', 'array'],
            'images.*.path' => ['required_with:images', 'string', 'max:2048'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'images.*.is_primary' => ['nullable', 'boolean'],
            'prices' => ['nullable', 'array'],
            'prices.*.date' => ['required_with:prices', 'date'],
            'prices.*.price' => ['required_with:prices', 'numeric', 'min:0'],
            'prices.*.currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
