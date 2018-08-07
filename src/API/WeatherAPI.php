<?php

//include '../Database/Model.php';

class WeatherAPI extends Model{
    public function tableName() : string
    {
        return 'weather';
    }
    
    public function attributes() : array
    {
        return ['record_id', 'id', 'date', 'location', 'temperature'];
    }
    
    public function filters() : array
    {
        return [
            'date' => 'UnixDate',
            'temperature' => [
                'filter' => 'JsonArray',
                'params' => [
                    'all' => 'FloatValue',
                    'FloatValue' => ['1'],
                ],
            ],
        ];
    }
    
    public function loadJson($json){
        foreach($json as $key => $val){
            $val = (array) json_decode($val);
            foreach($val as $v_key => $v_val){
                if($v_key == 'location'){
                    $location_data = new LocationData();
                    $location_data->loadJson($v_val)->save();
                    $v_val = $location_data->getLastInsertId();
                }
                $this->innerSet($v_key, $v_val);
            }
            $this->save();
        }
        return $this;
    }
    
    public function returnAllData(){
        $data = $this->select()->orderBy('record_id ASC')->all();
        $json = [];
        foreach($data as $key => $val){
            foreach($val as $v_key => $v_val){
                if($v_key == 'location'){
                    $location = new LocationData();
                    $v_val = $location->select()->where(['location_id' => $v_val])->asArray()->one();
                }
                if($v_key == 'temperature'){
                    $v_val = json_decode($v_val);
                }
                $json[$key][$v_key] = $v_val;
            }
        }
        return json_encode($json, JSON_PRETTY_PRINT);
    }
    
}

