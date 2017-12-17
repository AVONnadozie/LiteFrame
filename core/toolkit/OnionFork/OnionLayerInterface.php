<?php

namespace OnionFork;

use Closure;

/**
 * Onion architecture.
 * A fork of https://github.com/esbenp/onion.
 *
 * @author Victor Anuebunwa
 */
interface OnionLayerInterface
{
    public function peel(Closure $next, $object = null);
}
