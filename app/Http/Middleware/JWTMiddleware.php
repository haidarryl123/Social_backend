<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        try {
            JWTAuth::parseToken()->authenticate();
            return $next($request);
        } catch (TokenExpiredException $exception){
            $message = "Session is expired. Please login again.";
        } catch (TokenInvalidException $exception){
            $message = "Session is invalid. Please login again.";
        } catch (JWTException $exception){
            $message = "Something is missing. Please login again.";
        }
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
        ]);
    }
}
