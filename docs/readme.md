## Getting Started
>Quotes like this one are comments for developers.

### Installation
Download the latest release [here](https://github.com/AVONnadozie/LiteFrame/releases) and unzip. That's all!

Still need it the Composer way?

```bash
composer create-project avonnadozie/liteframe
```

Ensure the storage folder is writable, preferably 777.

On UNIX systems, you can do this with the command
```bash
chmod 0777 -R storage
```

### Running LiteFrame
#### On Production
No extra setup required, simply place the application files on your server document root folder, most likely to be public_html

#### On Local
Run the `cli serve` command to start the local server and optionally specify a port
```bash
php cli serve --port=5000
```
This will start the local server at address *127.0.0.1:5000*

## Architecture Concept
### Request Lifecycle
This part of the document aims to give you an overall view on how the framework works to help you understand the framework better. If you find some terms strange, it's okay, you will understand them and get familiar as you read on.

Like most PHP applications, all requests to the framework is directed to the index.php file. The index.php file does not contain much. Rather, it is a starting point for loading the rest of the framework.

The index.php file loads all files required by the framework (including Composer files if present) and then creates a `\LiteFrame\Kernel` object to handle the request.

The Kernel instance gets the `LiteFrame\Http\Request` and `LiteFrame\Http\Routing\Route` objects for the current request, and obtains the target Closure or Controller from the Route object. See [this section](#Routing) on how to define routes using `LiteFrame\Http\Routing\Router`.

All Middleware attached to the target (if any) are then executed before executing the target, and the Kernel finally terminates the framework.

### Directory Structure
Important directories and files
```
app
  |____ Commands //Application commands
  |____ Controllers //Application controllers
  |____ Middlewares //Application middlewares
  |____ Models //Application model
  |____ Routes //Application route files
  |____ Views //Application view files
  |____ .htaccess //Prevents direct access to files in this folder and subfolders
assets //(Optional) public assets such as css, images and JavaScript files
components
  |____ bootstrap //App bootstrap files
  |____ composer //(Optional) Composer files
  |____ config //Config files
  |____ helpers //Helper files
  |____ libraries //3rd party libraries
  |____ env.php //Environment specific configurations (to be created by you)
  |____ env.sample //Sample env.php file
  |____ .htaccess //Prevents direct access to files in this folder and subfolders
docs //(Optional) Documentation
storage 
  |____ logs //Error logs
  |____ private //Private application data
  |____ public //Public application data
  |       |____ .htaccess //Allows direct access to files in this folder and subfolders
  |____ .htaccess //Prevents direct access to files in this folder and subfolders
tests //Test files
.htaccess //Important .htaccess file
cli //Entry point for command line requests
index.php //Entry point for HTTP requests
```

## The Basics
### Routing
#### Basics
All routes are defined in your route files, which are located in the *app/Routes* directory. 
These files are automatically loaded by the framework. 
The *app/Routes/web.php* file defines routes that are for your web interface. 
The routes in *app/Routes/api.php* are stateless and are suited for API calls, 
while *app/Routes/cli.php* are for commands.

The simpler method for web and api routes accepts just a URI and a Closure
```php
<?php

Router::get('foo', function () {
    return 'Hello World';
});
```
Alternatively, you can specify a controller action in place of the closure.
```php
<?php

Router::get('foo', 'AppController@helloworld');
```
This will be explained further down


#### Available Router Methods
The router allows you to register routes that respond to the common HTTP verbs:
```php
<?php

//Match GET requests
Router::get($route, $target);

//match POST requests
Router::post($route, $target);

//Match PUT requests
Router::put($route, $target);

//Match PATCH requests
Router::patch($route, $target);

//Match DELETE requests
Router::delete($route, $target);
```
Sometimes you may need to register a route that responds to multiple HTTP verbs. 
You can do so using the `Router::anyOf` method.

Or, you may even register a route that responds to all HTTP verbs using the `Router::all` method:
```php
<?php

//Match any of the request method specified
Router::anyOf('POST|PUT|GET', $route, $target);

//Match all request methods
Router::all($route, $target);
```

#### Mapping
To map your routes, use any of the methods.
```php
<?php

//Match GET requests
Router::get($route, $target);

//match POST requests
Router::post($route, $target);

//Match PUT requests
Router::put($route, $target);

//Match PATCH requests
Router::patch($route, $target);

//Match DELETE requests
Router::delete($route, $target);

//Match any of the request method specified in the first parameter
Router::anyOf($method, $route, $target);

//Match all request methods
Router::all($route, $target);
```

##### Parameters
- `$method` - This is a pipe-delimited string of the accepted HTTP requests methods. Example: `GET|POST|PATCH|PUT|DELETE`
- `$route`  - This is the route pattern to match against. This can be a plain string, one of the predefined regex filters or a custom regex. Custom regexes must start with @.

Examples:

| Route | Example Match | Variables |
|---|---|---|
| /contact/ | /contact/ | nil |
| /users/[i:id]/ | /users/12/ | $id = 12 |

`$target` - This can be either a function callback or a Controller@action string.

Example using a function callback:
```php
<?php

Router::get('user/profile', function () {
    //Do something
});
```

Example using a Controller@action string: 
```php
<?php

Router::get('user/profile', 'UserController@showProfile');
```

Interestingly, we can also match multiple routes at once by supplying an array of routes to the `Router::matchAll` method. 
For example,
```php
<?php

Router::matchAll(array(
    array('POST|PUT', 'profile/create', 'ProfileController@create'),
    array('GET', 'profile', 'ProfileController@show'),
    array('DELETE', 'profile/[i:id]', 'ProfileController@delete'),
));
```

##### Match Types
You can use the following limits on your named parameters. 
The framework will create the correct regexes for you
```
*                    // Match all request URIs
[i]                  // Match an integer
[i:id]               // Match an integer as 'id'
[a:action]           // Match alphanumeric characters as 'action'
[h:key]              // Match hexadecimal characters as 'key'
[:action]            // Match anything up to the next / or end of the URI as 'action'
[*]                  // Catch all (lazy, stops at the next trailing slash)
[*:trailing]         // Catch all as 'trailing' (lazy)
[**:trailing]        // Catch all (possessive - will match the rest of the URI)
.[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional
```
     
The character before the colon (the 'match type') is a shortcut for one of the following regular expressions
```
'i'  => '[0-9]++'
'a'  => '[0-9A-Za-z]++'
'h'  => '[0-9A-Fa-f]++'
'*'  => '.+?'
'**' => '.++'
''   => '[^/\.]++'
```
You can register your own match types using the `addMatchTypes()` method.
```php
<?php

Router::getInstance()->addMatchTypes(array('cId' => '[a-zA-Z]{2}[0-9](?:_[0-9]++)?'));
```

Once your routes are all mapped you can start matching requests and continue processing the request.

#### Named Routes
If you want to use reversed routing, Named routes allow you to conveniently specify a name parameter so you can later generate URL's using this route. 
You may specify a name for a route by chaining the `setName` method onto the router:
```php
<?php

Router::get('user/profile', function () {
    //To do
})->setName('profile');
```
Or you may specify the route name after the target:
```php
<?php

//For anyOf
Router::anyOf('GET|POST','user/profile', 'AppController@showProfile','profile');

//For other Router methods
Router::get('user/profile', 'AppController@showProfile','profile');
```

To reverse a route, use the `route($routeName, $params);` helper with optional parameters

##### Parameters
`$routeName` | string - Name of route

`$params` | array - Optional parameters to build the URL with

#### Redirect Route
With Redirect Route you can redirect a route permanently to another route or a url.

##### Redirecting to a route
```php
<?php

Router::redirect($route, 'another-route-name');
```
This route will redirect to the route named `another-route-name`

##### Redirecting to a url
```php
<?php

Router::redirect($route, 'https://example.com');
```
This route will redirect to the URL http://example.com


#### View Route
This allows you to return a view as response directly without the need for closures or controllers.
```php
<?php

Router::view($route, 'view-name');
```
Where `view-name` is the name of the view.

Additionally, you can pass data to the view as such.
```php
<?php

$data = array('name'=>'John Doe');
Router::view($route, 'view-name', $data);
```
#### Route Grouping
Route grouping is a feature that allows you to group routes with related properties such as route names, URL prefix, middlewares and/or controller namespaces.

##### Behaviours to note:
- Groups can be nested infinitely.
- Nested groups and routes inherit the properties of their parent groups.
- Name of routes inside a group will be a concatenation of it's parent group(s) names and it's assigned name
- URI of routes inside a group will be a concatenation of it's parent groups prefixes using the character '/'
- Namespace of controllers in a group will be a concatenation of it's parent groups namespace using the character '\'
- Middleware of a route in group(s) will be a union of it's parent group(s) middleware and it's middleware

Code Sample:
```php
<?php
Router::group(['prefix' => 'sample-prefix', 'middlewares' => ['sampleMiddleware']], function() {

    Router::get('/', function (Request $request) {
        return "Grouped route with name index";
    })->setName('index');

    //Nested Group to inherit properties of parent group
    Router::group(['namespace' => 'Nested', 'name' => 'nested.'], function() {
        Router::get('/nested', 'MyController@index')->setName('index');
    });
});       
```

This example defines two groups and two routes.

The first route which inherits the first group will have it's name as `index`, 
middleware as `sampleMiddleware`, URI as */sample-prefix*

The second route which inherits properties of the two groups will therefore have 
it's name as `nested.index`, controller's namespace as `Nested\MyController`, 
middleware as sampleMiddleware, and URI as */sample-prefix/nested*

### Requests
Each http request is represented by a `LiteFrame\Http\Request` object.
The current Request object is passed to the request target controller 
or closure as the first parameter, from which you can access information 
for the current request through the object.
For example, given a request object `$request` and a GET or POST request parameter `id`, 
to access the parameter you can simple call `$request->input('id')` 
or conveniently access it as a property of the $request object as `$request->id`

Alternatively, you can access the request object using `Request::getInstance()`

Code Sample:
```php
<?php

Router::get('/post/[:id]', function (Request $request) {
    return 'Post id is ' . $request->id;
});
```

### Middleware
#### The Basics
Middleware provide a convenient mechanism for filtering HTTP requests 
entering your application and responses sent by the application.

All of these middleware are located in the *app/Middlewares* directory 
and should extend `Middlewares/Middleware` class in the same directory.
```php
<?php

namespace Middlewares;

use Closure;
use LiteFrame\Http\Request;

class MySampleMiddleware extends Middleware
{
    public function run(Closure $next = null, Request $request = null)
    {

        //Do something before controller

        $response = $next($request);

        //Do something after controller

        return $response;
    }
}
```
For the framework to run your middleware, you have to register it in the 
*components/config/middleware.php* file. Simply add your middleware class to 
the `before_core` or `after_core` key of the array.
```php
<?php

return [
     /*
     * Array of middleware classes that should be executed before core middleware
     * classes are executed on every request.
     */
    'before_core' => [
       Middlewares\MySampleMiddleware ::class
    ],
    /*
     * Array of middleware classes that should be executed after core middleware
     * classes are executed.
     */
    'after_core' => [
        Middlewares\MySampleMiddleware ::class
    ],
];
```

### Named/Route Middleware
You may also specify middleware to run only for specific routes. 
To do this, you have to register your middleware with a name
```php
<?php

return [
    /*
     * Example of a route/named middleware
     */
    'sample' => Middlewares\MySampleMiddleware::class
];
```
Then set it for the required routes like this
```php
<?php

Router::get('user/profile', 'AppController@showProfile')
        ->setMiddlewares("sample");
```
To juice things up, the `setMiddlewares` method can accept multiple middleware names:
```php
<?php

Router::get('user/profile', 'AppController@showProfile')
        ->setMiddlewares("sample1", "sample2");
```
Or
```php
<?php

Router::get('user/profile', 'AppController@showProfile')
        ->setMiddlewares(["sample1", "sample2"]);
```

### Controllers
Instead of defining all of your request handling logic as Closures in route files, 
you may wish to organize this behavior using Controller classes. 
Controllers can group related request handling logic into a single class. 
Controllers are stored in the *app/Controllers* directory and extends 
the `Controllers/Controller` class.

#### Routing to a controller action/method
```php
<?php

Router::get('user/profile', 'AppController@showHelloWorld');
```

#### Controller Middleware:
Middleware may be assigned to the controller's routes in your route files:
```php
<?php

Route::get('profile', 'UserController@show')
        ->setMiddlewares('sample');
```

However, if you need to bind all or most of the function of a controller to a 
middleware, it will be more convenient to specify the middleware 
within your controller's constructor and optionally supply a list of 
whitelist functions. Using the `middleware` method from the controller's constructor:
```php
<?php

class AppController extends Controller
{

    public function __construct()
    {
        $this->middleware('sample', ['index']);
    }
}
```
This will apply the `sample` middleware to all controller functions in 
this class except the `index` function.

### Response
#### Basics
All routes and controllers should return a response to be sent back to the user's browser.

They are several different ways to return responses. 
The most basic response is returning a string from a route or controller. 
The application will automatically convert the string into a `LiteFrame\Http\Response` object 
which translates to a full HTTP response.
```php
<?php                                                          

Router::get('/', function () {
    return 'Hello World';
});
```

You may also return arrays. 
The application will automatically convert the array into a JSON response.
```php
<?php

Router::get('/', function () {
    return [1, 2, 3];
});
```

Alternatively, you can choose to return the response object directly.
```php
<?php

Router::get('/', function () {
    return Response::getInstance()
            ->setContent('Hello World')
            ->setStatusCode(200);
});
```

The `LiteFrame\Http\Response` object provides other useful functions such as:
- `appendContent($content, $code = 200)` appends content to response
- `download($path, $name = null)` forces browser to download file
- `file($path)` opens a file in browser
- `header($key, $value)` sets HTTP header for the response
- `isJson()` returns true if the response will be sent as JSON
- `json($content)` sets a JSON content
- `prependContent($content, $code)` prepends content to the response
- `redirect($url, $code = 302)` sends a 302 redirect to a url
- `redirectToRoute($name, $code = 302)` sends a 302 redirect to the route with the given name
- `setStatusCode($code)` sets HTTP status code for the reponse
- `setContent($content, $code = 200)` sets response content
- `toHtml()` - converts response to HTML
- `toJson()` converts response to JSON
- `toPlain()` converts response to Plain text
- `view($name, $data = [])` sends a view as response

#### Views
Views are static files which the application returns as response.
At runtime, the application replaces variables in these files with actual values, 
and transforms the content into an HTML file sent to the client.

Traditionally, view files are located in *app/Views*, but this can be changed in the views configuration file.

Example View 1 (layouts/head.php)
```html
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap -->
<link rel="stylesheet" type="text/css" href="<?= asset('css/materialize.min.css') ?>">
```

Example View 2 (layouts/page.php)
```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <?= includeView('layouts.head') ?>
        <title><?= $name?></title>
    </head>
    <body>
        <h1>Hello World</h1>
    </body>
</html>
```
`includeView()` includes a view in another.

To return a view as response, use the `view()` function, specifying the name of the view you wish to return.
The name of the view is basically the filename relative to the views folder without the extension.
It's recommended to replace the directory separator in the filenames with dots.

For example, a file with filename *app/Views/example/base.php* will have the name `example.base` and a file with filename
app/Views/base.php will have it's name as just `base`

Example
```php
<?php

return view('layouts.page');
```
Note: LiteFrame supports having multiple view locations.

##### Templates
Templates are views with improved syntax and features. 
They provide benefits such as easy output escaping, view inheritance and sections.

LiteFrame supports templating using [BladeOne](https://github.com/EFTEC/BladeOne), 
a standalone version of the Blade Template Engine.

Blade files should end with the `.blade.php` extension in order to be compiled.

Example View 1 (layouts/base.blade.php)
```blade
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="<?= asset('css/materialize.min.css') ?>">
        <title>{{$name}}</title>
    </head>
    <body>
        @yield('body')
    </body>
</html>
```

Example View 2 (layouts/page.blade.php)
```html
@extends('layouts.base')
@section('body')
    <h1>Hello World</h1>
@endsection
```

To return this view as response in your controller
```php
<?php

return view('layouts.page', $dataIfAny);
```

Note: Templates are used the same way as other views, if you have a blade file e.g `base.blade.php`, 
and you have another view with the same name although with a different extension
e.g `base.php`, 
the blade version `base.blade.php` will override the other view `base.php`

You can read more about blade templating [here](blade.md)

### Commands
#### Basics
>Explain the basics in creating commands.

#### Scheduling
>Explain scheduling.

### Errors & Logging
#### Environment Variables
>Explain how to configure app environment variables.

#### Error Pages
>Explain how to create error pages to override the default error page on production.

#### Helpers
>Explain how to add helper functions.

## Database
### Getting Started
LiteFrame Database is based on [RedBeanPHP](https://redbeanphp.com/), an easy to use ORM for PHP.

It's a Zero Config ORM library that automatically builds your database schema on the fly.
By Zero Config, we mean, you need no verbose XML files, no annoying annotations, no YAML or INI, just start coding.

If you're already familiar with RedBeanPHP, this should be a piece of cake for you.
If you're not familiar with RedBeanPHP, we're sorry, it's still the same piece of cake for you.
Summary, you do not need to know how to use RedBeanPHP to understand how to use LiteFrame Database.

Remember to update *components/config/database.php* with details of your database connection.

#### Requirements
- PDO, plus the driver you wish to use for your database
- Multibyte String Support

### Models
Models are classes corresponding to database tables which is used to interact with that table. Models allow you to query for data in your tables, as well as insert new records into the table.

To make a model, simply create a class in app/Models and extend `Models\Model`.
The class name of a model will be automatically used to create and access it's table. 
If you wish to use a different table name from your model class name simple override the `$table` property.

For example:
```php
<?php

namespace Models;

class SampleModel extends Model
{
    protected static $table = 'posts';
}
```
Here, the `SampleModel` class is now set to be accessing and manipulating the posts table. 
This will be used in other examples on this documentation.

### Creating Records
Now you're ready to start using Database. 
Liteframe Database makes it really easy to store stuff in the database. For instance, to store a blog post in the database you write:
```php
<?php

$post = SampleModel::dispense();
$post->title = 'My holiday';
$id = $post->save();
```
Now, a table called post and a column called title, big enough to hold your text will be created for you in the database.
The `save()` function will also return the primary key ID of the record, which we capture in the variable $id.
The table and columns will be created automatically if they do not already exist.

### Model vs OODBean
An OODBean or bean is an object mapping of a record in your database.
Models internally wraps a bean, and provides more functions that makes it easier to manipulate bean objects.
You can call `Model->getBean()` to get the underlying bean

##### Code Difference

Using Model (The Liteframe Way)
```php
<?php

$post = SampleModel::dispense();
$post->title = 'My holiday';
$id = $post->save();
```

Using RedBeanPHP's R (The RedBeanPHP Way)
```php
<?php

$post = R::dispense('posts');
$post->title = 'My holiday';
R::store($post);
```

Both examples above creates a new bean, sets it's title to 'My holiday' and saves it to the database.
Whichever method you prefer to use is fine.

### DB vs R
`R` is the standard class by ReadBeanPHP for manipulating beans
`DB` is an `R` with more functions

### Finding Records
Finding stuff in the database is easy:
```php
<?php

$post = SampleModel::withId(3);
```
This will get the post with the id of 3
```php
<?php

$posts = SampleModel::find('title LIKE ?', [ 'holiday' ] );
```
This will search for all posts having the word 'holiday' in the title and will return an collection containing all the relevant beans as a result. As you see, we don't use a fancy query builder, just good old SQL.
We like to keep things simple.

Besides using the `find()` functions, you can also use **raw** SQL queries:
```php
<?php

$posts = DB::getAll('SELECT * FROM posts WHERE comments < ? ', [ 50 ] );
```

### Alright, freeze!
As you have seen, the structure of the database dynamically changes during development. This is a very nice feature, but you don't want that to happen on your production server! So, when you set your application to production, we freeze the database.

Before you deploy, review your database schema.
Maybe you added a column you no longer use, or you want an extra index. Always make sure you review the final database schema before you put it on a production server!
After freezing the database, your database structure will no longer be changed, so you have the best of both worlds.
NoSQL-like flexibility during development and a reliable schema on your production server!

This was just a quick tour, showcasing some basic usage of Liteframe Database with RedBeanPHP.
For more details please explore the [documentation](database.md) on database.

## Working with Libraries
### Autoloading Files
>Explain how to autoload custom files.

### 3rd-Party Libraries
> Explain how to autoload 3rd party libraries.

### Composer
In as much as we "really" avoided the need for commands, we could not help supporting composer. 
Composer files are auto-detected by the framework if available but we changed the vendor directory to *components/composer* for security reasons.

## Security
### URI Security
>Explain URI security using type hints

### Use of .htaccess
>Explain strategic .htaccess files

### Request Validation
>Explain `Validator`

### Filtering Output
>Explain output filtering using `e()`

## Testing
> Explain how to run tests

## Contributing
To contribute,

1. Fork the project on Github
2. Make your bug fix or feature addition.
3. Add tests for it. This is important so we don't break it in a future version unintentionally.
4. Send a pull request.
