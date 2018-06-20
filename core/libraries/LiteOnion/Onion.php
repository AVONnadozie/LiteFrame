<?php

namespace LiteOnion;

use Closure;
use InvalidArgumentException;

/**
 * Onion architecture.
 * A fork of https://github.com/esbenp/onion.
 *
 * @author Victor Anuebunwa
 */
class Onion
{
    private $layers;

    public function __construct(array $layers = [])
    {
        $this->layers = $layers;
    }

    /**
     * Add layer(s) or Onion.
     *
     * @param mixed $layers
     *
     * @return Onion
     */
    public function layer($layers)
    {
        if ($layers instanceof self) {
            $layers = $layers->toArray();
        }
        if ($layers instanceof OnionLayerInterface) {
            $layers = [$layers];
        }
        if (!is_array($layers)) {
            throw new InvalidArgumentException(get_class($layers).' is not a valid onion layer.');
        }

        return new static(array_merge($this->layers, $layers));
    }

    /**
     * Run middleware around core function and pass an
     * object through it.
     *
     * @param mixed   $object
     * @param Closure $core
     *
     * @return mixed
     */
    public function peel(Closure $core, $object = null)
    {
        $coreFunction = $this->createCoreFunction($core);
        // Since we will be "currying" the functions starting with the first
        // in the array, the first function will be "closer" to the core.
        // This also means it will be run last. However, if the reverse the
        // order of the array, the first in the list will be the outer layers.
        $layers = array_reverse($this->layers);
        // We create the onion by starting initially with the core and then
        // gradually wrap it in layers. Each layer will have the next layer "curried"
        // into it and will have the current state (the object) passed to it.
        $completeOnion = array_reduce($layers, function ($nextLayer, $layer) {
            return $this->createLayer($nextLayer, $layer);
        }, $coreFunction);
        // We now have the complete onion and can start passing the object
        // down through the layers.
        return $completeOnion($object);
    }

    /**
     * Get the layers of this onion, can be used to merge with another onion.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->layers;
    }

    /**
     * The inner function of the onion.
     * This function will be wrapped on layers.
     *
     * @param Closure $core the core function
     *
     * @return Closure
     */
    private function createCoreFunction(Closure $core)
    {
        return function ($object) use ($core) {
            return $core($object);
        };
    }

    /**
     * Get an onion layer function.
     * This function will get the object from a previous layer and pass it inwards.
     *
     * @param LayerInterface $nextLayer
     * @param LayerInterface $layer
     *
     * @return Closure
     */
    private function createLayer($nextLayer, $layer)
    {
        return function ($object) use ($nextLayer, $layer) {
            if (!is_subclass_of($layer, OnionLayerInterface::class)) {
                $message = 'Onion layers must implement the OnionLayerInterface interface';
                throw new \Exception($message);
            }

            return (new $layer())->peel($nextLayer, $object);
        };
    }
}
