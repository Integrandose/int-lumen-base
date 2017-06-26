<?php

namespace Int\Lumen\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;


class DefaultLanguageMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        if (!$request->has('language')) {
            $request['language'] = 'pt_BR';
        }

        return $next($request);
    }
}
