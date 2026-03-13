<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproved
{
    /**
     * Redirect unapproved users (non-admins) to the pending approval page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && ! $user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}
