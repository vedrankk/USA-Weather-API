<?php

class Request{
    
    protected $requestErrorMessage = '';
    protected function getEraseRequestParams($get) : array
    {
        if($this->validateRequestParams($get, ['start', 'end', 'lat', 'lon'], ['start' => 'date', 'end' => 'date', 'lat' => 'float', 'lon' => 'float'])){
          return $get;   
        }
        return empty($this->requestErrorMessage) ? [] : ['error' => $this->requestErrorMessage];
    }
    
    public function getWeatherRequestParams($get) : array
    {
        if($this->validateRequestParams($get, ['lat', 'lon'], ['lat' => 'float', 'lon' => 'float'])){
            return ['lat' => $get['lat'], 'lon' => $get['lon']];
        }
        
        return empty($this->requestErrorMessage) ? [] : ['error' => $this->requestErrorMessage];
    }
    
    public function getWeatherTemperatureRequestParams($get) : array
    {
        if($this->validateRequestParams($get, ['start', 'end'], ['start' => 'date', 'end' => 'date'])){
            return ['start' => $get['start'], 'end' => $get['end']];
        }
        return ['error' => empty($this->requestErrorMessage) ? 'The request is invalid.' : $this->requestErrorMessage];
    }
    
    private function validateRequestParams($get, $expected, $types) : bool
    {
      return !empty($_GET) && $this->arrayKeysExists($get, $expected) && $this->validateRequestTypes($get, $types);
    }
    
    private function validateRequestTypes($get, $types) : bool
    {
        $valid = true;
        foreach($types as $key => $val){
            $func = 'is_'.$val;
            switch($val){
                case 'string':
                    $req = $func((string)$get[$key]);
                break;
                case 'int':
                    $req = $func((int)$get[$key]);
                break;
                case 'float':
                    $req = $func((float)$get[$key]);
                break;
                case 'date':
                   $date = DateTime::createFromFormat('Y-m-d', $get[$key]);
                   $req = $date && $date->format('Y-m-d') === $get[$key];
                break;
                default:
                    $req = $func($get[$key]);
                break;
            }
            
            if(!$req){
                $valid = false;
                $this->requestErrorMessage = sprintf('Invalid type for: %s. Expected: %s, recieved: %s', $key, $val, gettype($get[$key]));
                break;
            }
        }
        return $valid;
    }
    
    private function arrayKeysExists($array, $keys){
        $exists = true;
        foreach($keys as $key){
            if(!filter_input(INPUT_GET, $key)){
                $exists = false;
                $this->requestErrorMessage = sprintf('Key does not exist: %s', $key);
                break;
            }
        }
        return $exists;
    }
    
    public function returnAction(){
        return method_exists($this, $this->action) ? $this->{$this->action}() : $this->action404();
    }
}
