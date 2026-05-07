<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        return view('front.account.dashboard', compact('user'));
    }

    public function bookings(Request $request): View
    {
        $user = $request->user();

        return view('front.account.bookings', compact('user'));
    }

    public function listings(Request $request): View
    {
        $user = $request->user();

        return view('front.account.listings', compact('user'));
    }

    public function settings(Request $request): View
    {
        $user = $request->user();

        return view('front.account.settings', compact('user'));
    }
}

