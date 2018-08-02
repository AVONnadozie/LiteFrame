<?php

namespace LiteFrame;

use Closure;
use Exception;
use LiteFrame\CLI\Args;
use LiteFrame\CLI\Command;
use LiteFrame\CLI\Output;
use LiteFrame\CLI\Routing\Router as CLIRouter;
use LiteFrame\Exception\Logger;
use LiteFrame\Http\Controller;
use LiteFrame\Http\Middlewares\CompressResponse;
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

    private function __construct()
    {
    }

    /**
     * Core Controller Middlewares
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
        try {
            list($route, $target) = $this->bootForRequest();

            $response = $this->runForRequest($route, $target);
        } catch (Exception $exception) {
            $response = (new Logger($exception))->log();
        }
        $this->terminateRequest($response);
    }

    private function bootForRequest()
    {

        //Set error configuration and logging options
        $this->setErrorConfigurations();

        //Match routes
        $request = Request::getInstance();
        $route = Router::getInstance()->matchRequest($request);
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
        list($class, $method) = getClassAndMethodFromString($action);
        $controllerClass = fixClassname(Router::$namespace, $class);

        //Todo: Dependency Injection
        /* @var $controller Controller */
        $controller = new $controllerClass();
        if (!$controller instanceof Controller) {
            throw new Exception("$class does not extend Controller");
        }
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
            if (array_search($middleware, $middlewares) !== false) {
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
            $isDebug = appIsOnDebugMode();
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', $isDebug ? 1 : 0);
            ini_set('log_errors', 1);
            ini_set('expose_php', 0);
//        ini_set('error_log', '');
            ini_set('log_errors_max_length', $isDebug ? 1024 : 0);
        }
    }

    public function handleJob()
    {
        list($args, $target) = $this->bootForJob();

        list($output, $code) = $this->runForJob($args, $target);

        $this->terminateJob($output, $code);
    }

    private function bootForJob()
    {
        if (!isCLI()) {
            abort(500, "Somehow, you are not running this from a command line interface.");
        }

        $this->setErrorConfigurations(true);

        //Match routes
        $args = Args::parse();
        $target = CLIRouter::getInstance()->matchCommand($args);
        
        return [$args, $target];
    }

    private function runForJob(Args $args, $target)
    {
        $output = '';
        $code = 0;
        if ($target instanceof Command) {
            //Run command
            $output = $target->run();
            if (is_array($output)) {
                list($output, $code) = $output;
            }
        } else {
            $output = $args->getCommand(). " is not a command";
            $code = 1;
        }
        return [$output, $code];
    }

    private function terminateJob($output = '', $code = 0)
    {
        if ($code) {
            Output::error($output ?: "Exited with error code $code");
        } else {
            Output::write($output);
        }
        exit($code);
    }
}
