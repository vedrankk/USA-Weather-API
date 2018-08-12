<?php

class EraseController extends Request
{
    private $action = '';
    
    public function __construct(){
       $this->api = new WeatherAPI();
       $this->action = 'actionDefault';
    }
    
    public function actionDefault(){
        $requestParams = $this->getEraseRequestParams($_GET);
        if(!isset($requestParams['error'])){
            return $this->api->eraseData($requestParams);
        }
        else{
            return json_encode($requestParams);
        }
    }
}
