<?php
/**
 * Map web routes to controllers or closures here
 */

use LiteFrame\Http\Request;
use LiteFrame\Http\Routing\Router;

//Controller based route
Router::get('/', 'SampleController@index')
        //Let's set a middleware for this route
        //We can pass an array or paramter list to specify more than one middleware
        //They will be executed in the order passed
        ->setMiddlewares('sample');

//Closure based route with parameter id which accepts only integers
Router::get('/user/[i:id]', function (Request $request) {
    return "Closure works too! Your user id is $request->id";
});

//Load a view directly
Router::view('redirect-view', 'home', [])
        //Set route name
        ->setName('home-view');

//Redirect to route permanently
Router::redirect('redirect', 'home-view');

//Redirect to URL permanently
Router::redirect('example', 'https://example.com');
