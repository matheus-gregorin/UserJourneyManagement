<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class Automations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $hash = env('N8N_HASH');
        $apikey = $request->header('apikey');

        if ($apikey != $hash) {
            return ApiResponse::error(
                [],
                "You do not have permission for this action",
                401
            );
        }

        return $next($request);
    }
}
