<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = mb_strtolower(trim((string) $validated['email']));

        Subscriber::updateOrCreate(
            ['email' => $email],
            [
                'source' => (string) ($request->get('source') ?? 'home_newsletter'),
                'ip' => (string) ($request->ip() ?? ''),
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('front.newsletter_subscribed'),
            ]);
        }

        return back()->with('subscribed', true);
    }
}

