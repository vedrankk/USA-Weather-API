<?php

class EraseController extends Request
{
    private $action = '';
    
    public function __construct(){
       $this->api = new WeatherAPI();
       $this->action = 'actionDefault';
    }
    
    public function returnAction(){
        return method_exists($this, $this->action) ? $this->{$this->action}() : $this->action404();
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
