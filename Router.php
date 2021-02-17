<?php

namespace App\Core;

// use App\SiteController;

use App\Core\Exceptions\MissingPageException;

class Router
{
    protected Request $request;
    public Response $response;
    protected $routes = [];


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;

        $this->response = $response;
    }


    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $callback = $this->routes[$method][$path] ?? false;

        if($callback === false) {

            throw new MissingPageException();
        }

        if(is_string($callback)) {
            return Application::$APP->view->renderView($callback);
        }
        if(is_array($callback)) {
            /**
             * @var \App\Core\BaseController $controller
             */
            $controller = new $callback[0]();
            Application::$APP->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }
        return call_user_func($callback, $this->request, $this->response);
    }
}