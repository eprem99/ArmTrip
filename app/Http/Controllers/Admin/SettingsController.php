<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Keys used for organization settings.
     */
    protected array $organizationKeys = [
        'organization_name',
        'organization_logo_light',
        'organization_logo_dark',
        'organization_email',
        'organization_phone',
        'timezone',
        'date_format',
    ];

    /**
     * Get organization options as key-value.
     */
    public function organization(): JsonResponse
    {
        $options = [];
        foreach ($this->organizationKeys as $key) {
            $options[$key] = Option::get($key, '');
        }

        return response()->json($options);
    }

    /**
     * Save organization options.
     */
    public function saveOrganization(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'organization_logo_light' => ['nullable', 'string', 'max:500'],
            'organization_logo_dark' => ['nullable', 'string', 'max:500'],
            'organization_email' => ['nullable', 'email', 'max:255'],
            'organization_phone' => ['nullable', 'string', 'max:50'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'date_format' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($validated as $key => $value) {
            Option::set($key, $value ?? '');
        }

        return response()->json(['success' => true]);
    }
}
