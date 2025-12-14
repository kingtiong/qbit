<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class RequireInvitationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $invite = $request->query('invite')
            ?: $request->input('invite')
            ?: $request->session()->get('invite_code');

        if (!$invite) {
            return redirect()
                ->route('login')
                ->with('status', 'Invitation link required to register.');
        }

        $invite = strtoupper((string) $invite);

        $inviterExists = User::query()->where('invite_code', $invite)->exists();
        if (!$inviterExists) {
            return redirect()
                ->route('login')
                ->with('status', 'Invalid invitation link.');
        }

        $request->session()->put('invite_code', $invite);
        $request->merge(['invite' => $invite]);

        return $next($request);
    }
}
