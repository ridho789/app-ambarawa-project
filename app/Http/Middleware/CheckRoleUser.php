<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roles = func_get_args();
        array_shift($roles);
    
        if (Auth::check()) {
            $userRole = Auth::user()->level;

            if (in_array($userRole, $roles)) {
                return $next($request);
            }

            // Redirect based on the user's level if they are authenticated but don't have the required role
            return $this->redirectToDashboard($userRole);
        }
    }

    /**
     * Redirect the user based on their role.
     *
     * @param  int  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToDashboard(int $role)
    {
        switch ($role) {
            case 1:
                return redirect('dashboard');
            default:
                return redirect('dashboard');
        }
    }
}
