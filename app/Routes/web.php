<?php

use LiteFrame\Http\Request;
use LiteFrame\Http\Routing\Router;

//Controller based route
Router::get('/', 'AppController@index')
        //Set route name
        ->setName('home')
        //Let's set a middleware for this route
        //We can pass an array or paramter list to specify more than one middleware
        //They will be executed in the order passed
        ->setMiddlewares('sample');

//Closure based route with parameter id which accepts only integer
Router::get('/user/[i:id]', function (Request $request) {
    return "Closure works too! Your user id is $request->id";
})->setName('user');

//Load a view directly
Router::view('redirect-view', 'home', [])->setName('home-view');

//Redirect to route permanently
Router::redirect('redirect', 'home-view');

//Redirect route to a URL permanently
Router::redirect('example', 'https://example.com');

if (appIsLocal()) {
    Router::get('/test', 'AppController@test');
}
