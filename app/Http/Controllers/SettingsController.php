<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SettingsController extends Controller
{
    public function business(): View
    {
        $user = auth()->user();
        $organization = $user->organization;

        return view('settings.business', [
            'organization' => $organization,
        ]);
    }

    public function billing(): View
    {
        $user = auth()->user();
        $organization = $user->organization;

        return view('settings.billing', [
            'organization' => $organization,
        ]);
    }

    public function notifications(): View
    {
        return view('settings.notifications');
    }
}
