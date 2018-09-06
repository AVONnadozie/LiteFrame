<?php

namespace LiteFrame\Http;

use Closure;
use LiteOnion\OnionLayerInterface;

/**
 * @author Victor Anuebunwa
 */
abstract class Middleware implements OnionLayerInterface
{
    public function peel(Closure $next, $object = null)
    {
        return $this->run($next, $object);
    }

    abstract public function run(Closure $next = null, Request $request = null);
}
