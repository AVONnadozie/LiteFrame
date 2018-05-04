<?php

namespace Middlewares;

use Closure;
use LiteFrame\Http\Request;

class MyNamedSampleMiddleware extends Middleware
{
    public function run(Closure $next = null, Request $request = null)
    {
        if ($next) {
            //Do something before target controller or closure executes
            $response = $next($request);
            //Do something after target controller or closure executes
            return $response;
        }
    }
}
