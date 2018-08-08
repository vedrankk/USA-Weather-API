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
    
    private function formatDataForOutput(array $data) : array
    {
        $json = [];
        $json['id'] = $data['id'];
        $json['date'] = date('Y-m-d', $data['date']);
        $json['location'] = [
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'city' => $data['city'],
            'state' => $data['state']
        ];
        $json['temperature'] = json_decode($data['temperature']);
        return $json;
        
    }
    
    public function returnAllData(){
        $data = $this->select()->leftJoin(['location_data', 'location_data.location_id', 'weather.location'])->orderBy('record_id ASC');
        $data = isset($_GET['lat']) && isset($_GET['lon']) ? $data->where(['lat' => $_GET['lat'], 'lon' => $_GET['lon']])->all() : $data->all();
        $this->json = [];
        foreach($data as $key => $val){
                $json[$key] = $this->formatDataForOutput($val);
        }
        return json_encode($json, JSON_PRETTY_PRINT);
    }
    
    public function returnTemperatureRanges(){
        
    }
    
}

