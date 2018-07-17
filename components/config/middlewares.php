<?php

return [
    /*
     * Example of a route/named middlewares
     */
    'sample' => Middlewares\SampleMiddleware::class,
    /*
     * Array of middleware classes that should be executed before core middleware
     * classes are executed on every request.
     */
    'before_core' => [
//        Middlewares\RunBeforeCoreMiddleware::class
    ],
    /*
     * Array of middleware classes that should be executed after core middleware
     * classes are executed.
     */
    'after_core' => [
//        Middlewares\RunAfterCoreMiddleware::class
    ],
];
