<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscribersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = max(5, min(100, (int) $request->get('per_page', 15)));

        $query = Subscriber::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $term = '%'.$request->get('search').'%';
            $query->where(function ($q) use ($term) {
                $q->where('email', 'like', $term)
                    ->orWhere('source', 'like', $term)
                    ->orWhere('ip', 'like', $term);
            });
        }

        return response()->json($query->paginate($perPage));
    }

    public function destroy(Subscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json(['message' => 'deleted']);
    }
}

