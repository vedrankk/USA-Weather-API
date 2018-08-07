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
    
}

