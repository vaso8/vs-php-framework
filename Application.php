<?php

namespace App\Core;

use App\Core\DB\Database;
use App\Models\User;

class Application
{
    public string $userClass;
    public static string $ROOT_DIR;
    public static $APP;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public BaseController $controller;
    public $db;
    public ?UserModel $user;
    public string $layout = 'main';
    public View $view;

    public function __construct($rootPath, array $config)
    {
        self::$APP = $this;

        $this->userClass = $config['userClass'];

        self::$ROOT_DIR = $rootPath;
        
        $this->request = new Request();

        $this->response = new Response();

        $this->session = new Session();

        $this->router = new Router($this->request, $this->response);

        $this->view = new View();

        $this->db = new Database($config['db']);


        $pv = $this->session->get('user');
        if($pv) {
            $pk = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$pk => $pv]);
        } else {
            $this->user = null;
        }


        
    }


    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('support/_error', [
                'exception' => $e
            ]);
        }

    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController(BaseController $controller)
    {
        $this->controller = $controller;
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$APP->user;
    }
}