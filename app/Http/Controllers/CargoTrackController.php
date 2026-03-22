<?php

namespace App\Http\Controllers;

use App\Models\CargoShipment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CargoTrackController extends Controller
{
    /**
     * Landing-friendly form: customer enters the tracking number from their email.
     */
    public function form(): View
    {
        return view('pages.track-cargo');
    }

    /**
     * Resolve public tracking code (CG…) or legacy logistics UUID, then redirect to the status page.
     */
    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'min:8', 'max:64'],
        ], [
            'tracking_number.required' => __('Please enter your tracking number.'),
        ]);

        $flowToken = $this->resolveFlowTokenFromCustomerInput($validated['tracking_number']);

        if ($flowToken === null) {
            return back()
                ->withErrors(['tracking_number' => __('We could not find an active delivery for this tracking number.')])
                ->withInput();
        }

        return redirect()->route('cargo.track', ['flow_token' => $flowToken]);
    }

    /**
     * Public read-only cargo status. {flow_token} may be the internal UUID or the public CG… code.
     */
    public function show(string $flow_token): View
    {
        $canonical = $this->resolveFlowTokenFromCustomerInput($flow_token);

        if ($canonical === null) {
            abort(404);
        }

        $sale = Sale::query()
            ->where('logistics_flow_token', $canonical)
            ->where('delivery_requested', true)
            ->with(['organization', 'items.product', 'deliveryServiceProvider'])
            ->first();

        if ($sale) {
            return view('cargo.track', [
                'trackType' => 'sale',
                'sale' => $sale,
                'cargo' => null,
            ]);
        }

        $cargo = CargoShipment::query()
            ->where('logistics_flow_token', $canonical)
            ->with(['organization', 'deliveryServiceProvider'])
            ->firstOrFail();

        return view('cargo.track', [
            'trackType' => 'cargo',
            'sale' => null,
            'cargo' => $cargo,
        ]);
    }

    /**
     * Accepts: public code (CG + 24 hex, with optional dashes), standard UUID, or 32-char hex UUID without dashes.
     */
    private function resolveFlowTokenFromCustomerInput(string $raw): ?string
    {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return null;
        }

        if (Str::isUuid($trimmed)) {
            return $this->findFlowTokenByLogisticsUuid(Str::lower($trimmed));
        }

        $alnum = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $trimmed));

        if (str_starts_with($alnum, 'CG') && strlen($alnum) === 26) {
            $suffix = substr($alnum, 2);
            if (ctype_xdigit($suffix)) {
                return $this->findFlowTokenByPublicCode($alnum);
            }
        }

        $uuidHyphenated = $this->hyphenate32HexToUuid($alnum);
        if ($uuidHyphenated !== null) {
            return $this->findFlowTokenByLogisticsUuid($uuidHyphenated);
        }

        return null;
    }

    private function hyphenate32HexToUuid(string $compact): ?string
    {
        if (strlen($compact) !== 32 || ! ctype_xdigit($compact)) {
            return null;
        }

        $uuid = substr($compact, 0, 8).'-'
            .substr($compact, 8, 4).'-'
            .substr($compact, 12, 4).'-'
            .substr($compact, 16, 4).'-'
            .substr($compact, 20, 12);

        return Str::isUuid($uuid) ? Str::lower($uuid) : null;
    }

    private function findFlowTokenByLogisticsUuid(string $lowercaseUuid): ?string
    {
        $sale = Sale::query()
            ->where('logistics_flow_token', $lowercaseUuid)
            ->where('delivery_requested', true)
            ->value('logistics_flow_token');

        if ($sale) {
            return $sale;
        }

        return CargoShipment::query()
            ->where('logistics_flow_token', $lowercaseUuid)
            ->value('logistics_flow_token');
    }

    private function findFlowTokenByPublicCode(string $code26): ?string
    {
        $sale = Sale::query()
            ->where('public_tracking_code', $code26)
            ->where('delivery_requested', true)
            ->value('logistics_flow_token');

        if ($sale) {
            return $sale;
        }

        return CargoShipment::query()
            ->where('public_tracking_code', $code26)
            ->value('logistics_flow_token');
    }
}
