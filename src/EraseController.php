<?php

class EraseController extends Request
{
    private $action = '';
    
    public function __construct(){
       $this->api = new WeatherAPI();
    }
    
    public function actionDefault(){
        return $this->api->eraseData($_GET);
    }
}
