<?php

namespace Middlewares;

use Closure;
use LiteFrame\Http\Request;

/**
 * Description of MyMiddleware
 *
 * @author Victor Anuebunwa
 */
class MyNamedSampleMiddleware extends Middleware
{

    public function run(Closure $next = null, Request $request = null)
    {
        if ($next) {
            //Do something before controller
            $response = $next($request);
            //Do something after controller
            return $response;
        }
    }

}
