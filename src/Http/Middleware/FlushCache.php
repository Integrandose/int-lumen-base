<?php

namespace Int\Lumen\Core\Http\Middleware;

use Closure;
use Int\Services\Client\Factory;

class FlushCache
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if (in_array($request->getMethod(), ['PUT', 'PATCH', 'DELETE', 'POST'])) {
            $clientGateway = Factory::make('gateway');
            $clientGateway->flushCache();
        }

        return $response;
    }
}
