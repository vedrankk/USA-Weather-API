<?php

class Controller extends Request
{
    protected $action = '';
    
    public function __construct(){
       $this->api = new WeatherAPI();
       $this->action = $_SERVER['REQUEST_METHOD'] == 'POST' ? 'actionCreate' : 'action' .ucfirst(isset($_GET['a']) ? $_GET['a'] : 'Default');
    }
    
    public function actionCreate(){
        return $this->api->loadJson(json_decode(file_get_contents('php://input'), true));
    }
    
    public function actionDefault(){
        $requestParams = $this->getWeatherRequestParams($_GET);
        if(!isset($requestParams['error'])){
            return $this->api->returnAllData($requestParams);
        }
        else{
            return json_encode($requestParams);
        }
    }
    
    public function actionTemperature(){
        $requestParams = $this->getWeatherTemperatureRequestParams($_GET);
        if(!empty($requestParams) && !isset($requestParams['error'])){
            return $this->api->returnTemperatureRanges($requestParams);
        }
        else{
            return json_encode($requestParams);
        }
    }
    
    public function action404(){
        http_response_code(404);
        die();
    }
}