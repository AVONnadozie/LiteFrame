<?php

if (!function_exists('windows_os')) {

    /**
     * Determine whether the current environment is Windows based.
     *
     * @return bool
     */
    function windows_os()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }
}

if (!function_exists('value')) {

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('trait_uses_recursive')) {

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param string $trait
     *
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (!function_exists('retry')) {

    /**
     * Retry an operation a given number of times.
     *
     * @param int      $times
     * @param callable $callback
     * @param int      $sleep
     *
     * @return mixed
     *
     * @throws Exception
     */
    function retry($times, callable $callback, $sleep = 0)
    {
        --$times;

        beginning:
        try {
            return $callback();
        } catch (Exception $e) {
            if (!$times) {
                throw $e;
            }

            --$times;

            if ($sleep) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }
}

if (!function_exists('preg_replace_array')) {

    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param string $pattern
     * @param array  $replacements
     * @param string $subject
     *
     * @return string
     */
    function preg_replace_array($pattern, array $replacements, $subject)
    {
        return preg_replace_callback($pattern, function () use (&$replacements) {
            foreach ($replacements as $key => $value) {
                return array_shift($replacements);
            }
        }, $subject);
    }
}

if (!function_exists('object_get')) {

    /**
     * Get an item from an object using "dot" notation.
     *
     * @param object $object
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (!function_exists('last')) {

    /**
     * Get the last element from an array.
     *
     * @param array $array
     *
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (!function_exists('head')) {

    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param array $array
     *
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (!function_exists('class_basename')) {

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param string|object $class
     *
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}
