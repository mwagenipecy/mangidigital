<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function updateBusiness(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return back()->with('error', __('No organization linked to your account.'));
        }

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        $organization->address = $validated['address'] ?? $organization->address;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $contents = file_get_contents($file->getRealPath());

            $png = $this->removeNearWhiteBackgroundToPng($contents);
            $filename = 'org-' . $organization->id . '-' . Str::random(10) . '.png';
            $storagePath = 'org-logos/' . $filename;
            Storage::disk('public')->put($storagePath, $png);

            $organization->logo_path = 'storage/' . $storagePath;
        }

        $organization->save();

        return back()->with('success', __('Business settings updated.'));
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

    private function removeNearWhiteBackgroundToPng(string $imageBytes): string
    {
        $img = @imagecreatefromstring($imageBytes);
        if (! $img) {
            return $imageBytes;
        }

        $w = imagesx($img);
        $h = imagesy($img);

        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefill($out, 0, 0, $transparent);

        // Make near-white pixels transparent; keeps colored logos intact.
        $threshold = 242;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                if ($r >= $threshold && $g >= $threshold && $b >= $threshold) {
                    imagesetpixel($out, $x, $y, $transparent);
                    continue;
                }

                $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
                imagesetpixel($out, $x, $y, $color);
            }
        }

        ob_start();
        imagepng($out);
        $png = ob_get_clean();

        imagedestroy($img);
        imagedestroy($out);

        return $png ?: $imageBytes;
    }
}
