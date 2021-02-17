<?php


namespace App\Core\Middleware;


use App\Core\Application;
use App\Core\Exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions;

    /**
     * AuthMiddleware constructor.
     * @param array $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if(empty($this->actions) || in_array(Application::$APP->controller->action, $this->actions)) {
            throw new ForbiddenException();
        }
    }
}