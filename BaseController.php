<?php

namespace App\Core;

use App\Core\Middleware\BaseMiddleware;

class BaseController
{
    public string $layout = 'main';
    public string $action = '';

    /**
     * @var \App\Core\Middleware\BaseMiddleware
     */
    protected array $middlewares = [];

    /**
     * @return BaseMiddleware
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
    public function render($view, $params = [])
    {
        return Application::$APP->view->renderView($view, $params);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }
}