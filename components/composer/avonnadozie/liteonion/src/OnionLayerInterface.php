<?php

namespace LiteOnion;

use Closure;

/**
 * Onion architecture.
 *
 */
interface OnionLayerInterface
{
    public function peel(Closure $next, $object = null);
}
