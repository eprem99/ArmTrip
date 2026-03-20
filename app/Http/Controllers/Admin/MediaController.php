<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = max(5, min(100, (int) $request->get('per_page', 15)));
            $query = Media::orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                $term = '%'.$request->get('search').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('alt', 'like', $term)
                        ->orWhere('filename', 'like', $term);
                });
            }

            $media = $query->paginate($perPage);

            return response()->json($media);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Server error: '.$e->getMessage(),
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 0,
                'from' => null,
                'to' => null,
            ], 500);
        }
    }

    public function show(Media $media): JsonResponse
    {
        return response()->json($media);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:'.(1024 * 50), // 50 MB
            'title' => 'nullable|string|max:255',
            'alt' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $dir = date('Y/m');
        $ext = $file->getClientOriginalExtension() ?: $file->guessExtension();
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName ?: 'file';
        $name = $baseName.'-'.Str::random(6).($ext ? '.'.$ext : '');
        $disk = (string) (Option::get('media_storage_disk', '') ?: 'uploads');
        $path = $file->storeAs($dir, $name, $disk);

        $media = Media::create([
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'title' => $request->input('title'),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt' => $request->input('alt'),
            'caption' => $request->input('caption'),
            'created_by' => $request->user()?->id,
        ]);

        return response()->json($media, 201);
    }

    public function update(Request $request, Media $media): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'alt' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
        ]);

        $media->update($validated);

        return response()->json($media);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json(['message' => 'OK']);
    }
}
