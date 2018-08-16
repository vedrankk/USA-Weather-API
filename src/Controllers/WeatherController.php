<?php

class Controller extends Request
{
    protected $action = '';
    
    public function __construct(){
       $this->api = new WeatherAPI();
       $this->action = $_SERVER['REQUEST_METHOD'] == 'POST' ? 'actionCreate' : 'action' .ucfirst(isset($_GET['a']) ? $_GET['a'] : 'Default');
    }
    
    /*
     * Calls the create function and uses the POST request
     */
    public function actionCreate(){
        return $this->api->loadJson(json_decode(file_get_contents('php://input'), true));
    }
    
    /*
     * Returns the default action, all weather data or data based on lat/lon
     */
    public function actionDefault(){
        $requestParams = $this->getWeatherRequestParams($_GET);
        if(!isset($requestParams['error'])){
            return $this->api->returnAllData($requestParams);
        }
        else{
            return json_encode($requestParams);
        }
    }
    
    /*
     * Returns the min/max temperatures
     */
    public function actionTemperature(){
        $requestParams = $this->getWeatherTemperatureRequestParams($_GET);
        if(!empty($requestParams) && !isset($requestParams['error'])){
            return $this->api->returnTemperatureRanges($requestParams);
        }
        else{
            return json_encode($requestParams);
        }
    }
    
    /*
     * If the request is invalid, return 404 error
     */
    public function action404(){
        http_response_code(404);
        die();
    }
}
