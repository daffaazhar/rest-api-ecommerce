<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole {
    use HttpResponses;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response {
        try {
            $userRole = auth()->user()->role;

            if ($userRole == $role) {
                return $next($request);
            }

            return $this->error(null, 'You don\'t have permission to access this page', 403);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
