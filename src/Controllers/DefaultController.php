<?php

namespace Dannerz\Api\Controllers;

use Illuminate\Http\Request;

class DefaultController
{
    protected $request;

    protected $modelName;

    protected $controller;

    protected $id;

    protected $action;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->modelName = studly_case(str_singular($request->segment(2)));

        $this->controller = $this->getController();

        $this->id = $this->getId();

        $this->action = $this->getAction();
    }

    public function handle()
    {
        return call_user_func([$this->controller, $this->action], $this->id);
    }

    protected function getController()
    {
        $controllerName = 'App\Http\Controllers\\'.$this->modelName.'Controller';

        if (! class_exists($controllerName)) {
            return resolve(BaseController::class);
        }

        return resolve($controllerName);
    }

    protected function getId()
    {
        $id = $this->request->segment(3);

        return is_numeric($id) ? $id : null;
    }

    protected function getAction()
    {
        switch ($this->request->method()) {
            case 'POST':
                return 'create';
                break;
            case 'DELETE':
                return 'delete';
                break;
            case 'GET':
                if ($this->id) return 'find';
                return 'get';
                break;
            case 'PUT':
                return 'update';
                break;
        }
    }

    // TODO: Automate request handling.
}
