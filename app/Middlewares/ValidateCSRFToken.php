<?php

namespace Middlewares;

use LiteFrame\Http\Middlewares\ValidateCSRFToken as CoreValidateCSRFToken;

class ValidateCSRFToken extends CoreValidateCSRFToken {

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
     * List of route names to exclude from CSRF token validation
     * @var array
     */
    protected $except = [
    ];

}
