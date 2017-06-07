<?php

namespace Int\Lumen\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class AcceptsJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!$request->acceptsJson()) {

            $data = [
                'message' => 'Header Accept: application/json is necessary',
            ];

            Log::warning($data['error']['message']);

            return response()->json($data, Response::HTTP_BAD_REQUEST);

        }
        return $next($request);
    }
}
