<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

    protected array $globalKeys = [
        'media_storage_disk',
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

    public function global(): JsonResponse
    {
        $disks = array_keys(config('filesystems.disks', []));
        sort($disks);

        $cacheStores = array_keys(config('cache.stores', []));
        sort($cacheStores);

        $values = [];
        $values['debug'] = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL);
        foreach ($this->globalKeys as $key) {
            $values[$key] = Option::get($key, '');
        }
        $values['media_storage_disk'] = $values['media_storage_disk'] ?: 'uploads';

        $values['aws_access_key_id'] = (string) env('AWS_ACCESS_KEY_ID', '');
        $values['aws_secret_access_key'] = (string) env('AWS_SECRET_ACCESS_KEY', '');
        $values['aws_default_region'] = (string) env('AWS_DEFAULT_REGION', '');
        $values['aws_bucket'] = (string) env('AWS_BUCKET', '');
        $values['aws_url'] = (string) env('AWS_URL', '');
        $values['aws_endpoint'] = (string) env('AWS_ENDPOINT', '');
        $values['aws_use_path_style_endpoint'] = filter_var(env('AWS_USE_PATH_STYLE_ENDPOINT', false), FILTER_VALIDATE_BOOL);

        $values['cache_store'] = (string) env('CACHE_STORE', config('cache.default', 'file'));
        $values['cache_prefix'] = (string) env('CACHE_PREFIX', config('cache.prefix', ''));

        $values['redis_host'] = (string) env('REDIS_HOST', '127.0.0.1');
        $values['redis_port'] = (string) env('REDIS_PORT', '6379');
        $values['redis_password'] = (string) (env('REDIS_PASSWORD') ?? '');
        $values['redis_db'] = (string) env('REDIS_DB', '0');
        $values['redis_cache_db'] = (string) env('REDIS_CACHE_DB', '1');

        return response()->json([
            'disks' => $disks,
            'cache_stores' => $cacheStores,
            'values' => $values,
        ]);
    }

    public function saveGlobal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'debug' => ['nullable', 'boolean'],
            'media_storage_disk' => ['required', 'string', 'max:50'],
            'aws_access_key_id' => ['nullable', 'string', 'max:200'],
            'aws_secret_access_key' => ['nullable', 'string', 'max:200'],
            'aws_default_region' => ['nullable', 'string', 'max:100'],
            'aws_bucket' => ['nullable', 'string', 'max:200'],
            'aws_url' => ['nullable', 'string', 'max:500'],
            'aws_endpoint' => ['nullable', 'string', 'max:500'],
            'aws_use_path_style_endpoint' => ['nullable', 'boolean'],
            'cache_store' => ['required', 'string', 'max:50'],
            'cache_prefix' => ['nullable', 'string', 'max:200'],
            'redis_host' => ['nullable', 'string', 'max:200'],
            'redis_port' => ['nullable', 'string', 'max:20'],
            'redis_password' => ['nullable', 'string', 'max:200'],
            'redis_db' => ['nullable', 'string', 'max:20'],
            'redis_cache_db' => ['nullable', 'string', 'max:20'],
        ]);

        $allowedDisks = array_keys(config('filesystems.disks', []));
        if (! in_array($validated['media_storage_disk'], $allowedDisks, true)) {
            return response()->json(['message' => 'Invalid disk'], 422);
        }

        Option::set('media_storage_disk', $validated['media_storage_disk']);
        $this->setEnvValue('FILESYSTEM_DISK', $validated['media_storage_disk']);

        $debug = (bool) ($validated['debug'] ?? false);
        $this->setEnvValue('APP_DEBUG', $debug ? 'true' : 'false');
        config(['app.debug' => $debug]);

        $allowedCacheStores = array_keys(config('cache.stores', []));
        if (! in_array($validated['cache_store'], $allowedCacheStores, true)) {
            return response()->json(['message' => 'Invalid cache store'], 422);
        }

        $this->setEnvValue('CACHE_STORE', $validated['cache_store']);
        $this->setEnvValue('CACHE_PREFIX', (string) ($validated['cache_prefix'] ?? ''));
        config([
            'cache.default' => $validated['cache_store'],
            'cache.prefix' => (string) ($validated['cache_prefix'] ?? ''),
        ]);

        if ($validated['cache_store'] === 'redis') {
            $this->setEnvValue('REDIS_HOST', (string) ($validated['redis_host'] ?? '127.0.0.1'));
            $this->setEnvValue('REDIS_PORT', (string) ($validated['redis_port'] ?? '6379'));
            $this->setEnvValue('REDIS_PASSWORD', (string) ($validated['redis_password'] ?? ''));
            $this->setEnvValue('REDIS_DB', (string) ($validated['redis_db'] ?? '0'));
            $this->setEnvValue('REDIS_CACHE_DB', (string) ($validated['redis_cache_db'] ?? '1'));
        }

        if ($validated['media_storage_disk'] === 's3') {
            $this->setEnvValue('AWS_ACCESS_KEY_ID', $validated['aws_access_key_id'] ?? '');
            $this->setEnvValue('AWS_SECRET_ACCESS_KEY', $validated['aws_secret_access_key'] ?? '');
            $this->setEnvValue('AWS_DEFAULT_REGION', $validated['aws_default_region'] ?? '');
            $this->setEnvValue('AWS_BUCKET', $validated['aws_bucket'] ?? '');
            $this->setEnvValue('AWS_URL', $validated['aws_url'] ?? '');
            $this->setEnvValue('AWS_ENDPOINT', $validated['aws_endpoint'] ?? '');
            $this->setEnvValue('AWS_USE_PATH_STYLE_ENDPOINT', ! empty($validated['aws_use_path_style_endpoint']) ? 'true' : 'false');
        }

        return response()->json(['success' => true]);
    }

    protected function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');
        if (! File::exists($path)) {
            return;
        }

        $contents = File::get($path);
        $normalizedValue = $this->envEscapeValue($value);
        $pattern = '/^'.preg_quote($key, '/').'=.*/m';

        if (preg_match($pattern, $contents)) {
            $contents = preg_replace($pattern, $key.'='.$normalizedValue, $contents);
        } else {
            $contents = rtrim($contents).PHP_EOL.$key.'='.$normalizedValue.PHP_EOL;
        }

        File::put($path, $contents);
    }

    protected function envEscapeValue(string $value): string
    {
        if ($value === '') {
            return '""';
        }
        if (preg_match('/\s|#|=|"|\'/', $value)) {
            $escaped = str_replace('"', '\"', $value);

            return '"'.$escaped.'"';
        }

        return $value;
    }

    /**
     * List languages for settings.
     */
    public function languages(): JsonResponse
    {
        $languages = Language::orderBy('name')->get();

        return response()->json($languages);
    }

    /**
     * Store a new language.
     */
    public function storeLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lcode' => ['required', 'string', 'max:10', 'unique:languages,lcode'],
            'name' => ['required', 'string', 'max:255'],
            'native_name' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:20'],
            'direction' => ['nullable', 'string', 'in:ltr,rtl'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $validated['direction'] = $validated['direction'] ?? 'ltr';
        $validated['status'] = $validated['status'] ?? 'active';

        Language::create($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Get single language.
     */
    public function getLanguage(Language $language): JsonResponse
    {
        return response()->json($language);
    }

    /**
     * Update a language.
     */
    public function updateLanguage(Request $request, Language $language): JsonResponse
    {
        $validated = $request->validate([
            'lcode' => ['required', 'string', 'max:10', 'unique:languages,lcode,'.$language->id],
            'name' => ['required', 'string', 'max:255'],
            'native_name' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:20'],
            'direction' => ['nullable', 'string', 'in:ltr,rtl'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $validated['direction'] = $validated['direction'] ?? 'ltr';
        $validated['status'] = $validated['status'] ?? 'active';

        $language->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a language.
     */
    public function destroyLanguage(Language $language): JsonResponse
    {
        $language->delete();

        return response()->json(['success' => true]);
    }
}
