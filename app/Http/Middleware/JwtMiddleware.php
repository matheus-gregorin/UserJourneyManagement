<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtMiddleware
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
        try{
            $data = $request->header();

            $tokenHeader = $data['authorization'][0] ?? false;

            if($tokenHeader){
                $explodeString = explode("Bearer ", $tokenHeader, 2) ?? false;
                if(!$explodeString){
                    throw new Exception("Token not content string 'Bearer'", 401);
                }

                $token = $explodeString[1] ?? false;
                if(!$token){
                    throw new Exception('Invalid', 401);
                }
    
                $token = JWT::decode($token, new Key(env('JWTKEY'), 'HS256'));

                if(!$token){
                    throw new Exception("Action not permited, contact support");
                }

                return $next($request);
            }

            throw new Exception('headers invalid', 401);

        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    $e->getMessage()
                ],
                'Unauthorized',
                401
            );
        }
    }
}
