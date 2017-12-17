<?php

namespace LiteFrame;

use Closure;
use LiteFrame\Http\Middleware\CompressResponse;
use LiteFrame\Http\Request;
use LiteFrame\Http\Response;
use LiteFrame\Http\Routing\Route;
use LiteFrame\Http\Routing\Router;
use OnionFork\Onion;

/**
 * Description of Kernel.
 *
 * @author Victor Anuebunwa
 */
final class Kernel
{
    private static $kernelInstance;
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
        //Set error configuration and logging options
        $this->setErrorConfigurations();

        //Match routes
        $request = Request::getInstance();
        $defFile = base_path('components/routes/web.php');
        $route = Router::getInstance()->matchRequest($request, $defFile);
        abort_unless($route instanceof Route, 404);

        //Get target controller or closure
        $target = $route->getTarget();
        if (!$target instanceof Closure) {
            $target = $this->getControllerLogic($target);
        }

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

        //Run middleware(s) around controller logic and output response
        $response = $this->runMiddlewaresInOnion($logic, $route);
        die($response);
    }

    private function getControllerLogic($action)
    {
        $split = explode('@', $action);
        $controllerClass = "\\Controllers\\{$split[0]}";
        $method = $split[1];

        return function ($request) use ($controllerClass, $method) {
            //Setup controller
            //Todo: Dependency Injection
            $controller = new $controllerClass($request);

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

        $onion = new Onion($middlewares);
        $request = Request::getInstance();
        $response = $onion->peel($logic, $request);

        return $response;
    }

    private function setErrorConfigurations($cli = false)
    {
        set_error_handler('errorHandler');
        set_exception_handler('exceptionHandler');

        $local = config('app.env') === 'local';
        ini_set('display_errors', $local ? 1 : 0);
        ini_set('display_startup_errors', $local ? 1 : 0);
        ini_set('log_errors', 1);
//        ini_set('error_log', '');
        ini_set('log_errors_max_length', $local ? 1024 : 0);
        error_reporting(E_ALL);
        register_shutdown_function('shutdownHandler');
    }

    public function handleJob()
    {
        $this->setErrorConfigurations(true);
        $defFile = base_path('components/routes/cli.php');
        //log to file
        //resolve command based on parameter
        //Check job schedule
        //Run if on schedule
    }
}
