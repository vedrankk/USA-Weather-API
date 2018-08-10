<?php

class Controller
{
    private $route = '';
    private $route_params = [];
    private $action = '';
    
    public function __construct(){
        $this->api = new WeatherAPI();
       $this->action = $_SERVER['REQUEST_METHOD'] == 'POST' ? 'actionCreate' : 'action' .ucfirst(isset($_GET['a']) ? $_GET['a'] : 'Default');
    }
    
    public function returnAction(){
        return method_exists($this, $this->action) ? $this->{$this->action}() : $this->action404();
    }
    
    public function actionCreate(){
        return $this->api->loadJson(json_decode(file_get_contents('php://input'), true));
    }
    
    public function actionDefault(){
        return 111;
    }
    
    public function actionTemperature(){
        return 222;
    }
    
    public function action404(){
        header("HTTP/1.0 404 Not Found");
        die();
    }
}

