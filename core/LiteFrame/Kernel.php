<?php

namespace LiteFrame;

use Closure;
use LiteFrame\Http\Controller;
use LiteFrame\Http\Middleware\CompressResponse;
use LiteFrame\Http\Request;
use LiteFrame\Http\Response;
use LiteFrame\Http\Routing\Route;
use LiteFrame\Http\Routing\Router;
use LiteOnion\Onion;

/**
 * Description of Kernel.
 *
 * @author Victor Anuebunwa
 */
final class Kernel
{

    private static $kernelInstance;
    private $controllerMiddlewares = [];

    /**
     * Controller Middleware
     * @var array 
     */
    private $middlewares = [
        CompressResponse::class,
    ];

    /**
     * Return singleton class instance.
     *
     * @return Kernel
     */
    public static function getInstance()
    {
        if (empty(self::$kernelInstance)) {
            self::$kernelInstance = new self();
        }

        return self::$kernelInstance;
    }

    /**
     * Handle request to this application.
     */
    public function handleRequest()
    {
        list($route, $target) = $this->bootForRequest();

        $response = $this->runForRequest($route, $target);

        $this->terminateRequest($response);
    }

    private function bootForRequest()
    {

        //Set error configuration and logging options
        $this->setErrorConfigurations();

        //Match routes
        $request = Request::getInstance();
        $routeFile = base_path('components/routes/web.php');
        $route = Router::getInstance()->matchRequest($request, $routeFile);
        abort_unless($route instanceof Route, 404);

        //Get target controller or closure
        $target = $route->getTarget();
        if (!$target instanceof Closure) {
            $target = $this->getControllerLogic($target);
        }

        return [$route, $target];
    }

    private function runForRequest($route, $target)
    {

        //Ensure target's output is a response object
        $logic = function ($request) use ($target) {
            //Handle text output within target
            ob_start();
            //Run target and get response
            $response = $target($request);
            //Get other text output (if any)
            $output = ob_get_clean();

            if (!$response instanceof Response) {
                $response = Response::getInstance()->setContent($response);
            }
            //Return a response prepending any text output
            return $response->prependContent($output);
        };

        //Run middleware(s) around controller logic and return response
        return $this->runMiddlewaresInOnion($logic, $route);
    }

    private function terminateRequest(Response $response)
    {
        die($response);
    }

    private function getControllerLogic($action)
    {
        $split = explode('@', $action);
        $controllerClass = "\\Controllers\\{$split[0]}";
        $method = $split[1];

        //Todo: Dependency Injection
        /* @var $controller Controller */
        $controller = new $controllerClass();
        $this->controllerMiddlewares = $controller->getMiddlewares();
        return function ($request) use ($controller, $method) {
            //Todo: Dependency Injection
            return $controller->$method($request);
        };
    }

    private function runMiddlewaresInOnion(Closure $logic, Route $route)
    {
        $b_m = config('middlewares.before_core');
        $a_m = config('middlewares.after_core');
        $middlewares = array_merge($b_m, $this->middlewares, $a_m);
        //Get route middlewares
        foreach ($route->getMiddlewares() as $name) {
            $middleware = config("middlewares.$name");
            if ($middleware) {
                $middlewares[] = $middleware;
            }
        }
        //Controller middlewares
        foreach ($this->controllerMiddlewares as $middleware) {
            if (array_search($middleware, $middlewares) !== FALSE) {
                continue;
            }

            $middlewares[] = $middleware;
        }

        $onion = new Onion($middlewares);
        $request = Request::getInstance();
        $response = $onion->peel($logic, $request);

        return $response;
    }

    private function setErrorConfigurations($cli = false)
    {
        set_error_handler('errorHandler');
        set_exception_handler('exceptionHandler');
        error_reporting(E_ALL);
        register_shutdown_function('shutdownHandler');

        if (!$cli) {
            $local = config('app.env') === 'local';
            ini_set('display_errors', $local ? 1 : 0);
            ini_set('display_startup_errors', $local ? 1 : 0);
            ini_set('log_errors', 1);
//        ini_set('error_log', '');
            ini_set('log_errors_max_length', $local ? 1024 : 0);
        }
    }

    public function handleJob()
    {
        $this->bootForJob();

        $this->runForJob();

        $this->terminateJob();
    }

    private function bootForJob()
    {
        $this->setErrorConfigurations(true);
    }

    private function runForJob()
    {
        $scheduler = new Scheduler();
        $routeFile = base_path('components/routes/cli.php');
        require $routeFile;
        $scheduler->run();
    }

    private function terminateJob()
    {
        exit;
    }

}
