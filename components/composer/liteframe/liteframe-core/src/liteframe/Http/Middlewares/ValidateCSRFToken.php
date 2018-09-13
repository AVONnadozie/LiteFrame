<?php

namespace LiteFrame\Http\Middlewares;

use Closure;
use DateInterval;
use DateTime;
use LiteFrame\Http\Middleware;
use LiteFrame\Http\Request;
use LiteFrame\Http\Request\Session;
use function abort_unless;

class ValidateCSRFToken extends Middleware
{

    /**
     * Token key
     * @var string
     */
    public static $tokenKey = '__token';

    /**
     * Token expire key
     * @var string
     */
    public static $tokenExpireKey = '__token_expire';

    /**
     * Route names to ignore
     * @var array
     */
    protected $except = [
    ];

    /**
     * Handle an incoming request.
     *
     * @param Closure $next
     * @param Request  $request
     *
     * @return mixed
     */
    public function run(Closure $next = null, Request $request = null)
    {
        if (!$next) {
            return;
        }

        $this->generate();
        abort_unless($this->validate($request), 403, 'Token Validation Failed');
        return $next($request);
    }

    public function validate(Request $request)
    {
        if ($request->getMethod() == 'GET') {
            return true;
        }

        //Validate token
        $route = $request->getRoute();
        if ($route) {
            $routeName = $route->getName();
            $ignore = in_array($routeName, $this->except);
        } else {
            $ignore = false;
        }
        return $ignore || (!$this->isTokenExpired() && !$this->match($request->{static::$tokenKey}));
    }

    public function generate()
    {
        if ($this->isTokenExpired()) {
            //Generate new token
            $token = md5(uniqid());
            Session::put(static::$tokenKey, $token);
        }

        $minutes = config('app.token_expire', 60);
        $now = new DateTime;
        $now->add(new DateInterval("PT{$minutes}M"));
        Session::put(static::$tokenExpireKey, $now);
    }

    public function isTokenExpired()
    {
        $tokenTime = Session::fetch(static::$tokenExpireKey);
        $now = new DateTime;
        return !is_object($tokenTime) || $now > $tokenTime;
    }

    public static function getSessionToken()
    {
        return Session::fetch(static::$tokenKey);
    }

    public function match($token)
    {
        return strcasecmp($token, static::getSessionToken());
    }
}
