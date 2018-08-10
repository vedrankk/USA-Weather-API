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
    
    public function types() : array
    {
        return ['id' => 'int', 'date' => 'string', 'location' => 'int', 'temperature' => 'array'];
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
            $val = (array) json_decode($json);
            if(!empty($this->select('id')->where(['id' => $val['id']])->asArray()->one())){
                 header("HTTP/1.0 400 ID Exists");
                 return;
            }
            
            foreach($val as $v_key => $v_val){
                if($v_key == 'location'){
                    $location_data = new LocationData();
                    if($location_data->loadJson($v_val)->save()){
                        $v_val = $location_data->getLastInsertId();
                    }
                    else{
                        return json_encode(['response_message' => $location_data->error]);
                    }
                }
                $this->innerSet($v_key, $v_val);
            }
            return $this->save() ? $this->getLastInsertId() : false;
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
        $data = $this->select('weather.record_id, weather.temperature, location_data.city, location_data.state, location_data.lat, location_data.lon')
                ->leftJoin(['location_data', 'location_data.location_id', 'weather.location'])
                ->where(sprintf('date BETWEEN %s AND %s', 473554800, 474159600))->all();
        $state_data = [];
        foreach($data as $key => $val){
            $state_data[$val['city']]['lat'] = $val['lat'];
            if(!isset($state_data[$val['city']]['temperature'])){
                $state_data[$val['city']]['temperature'] = [];
            }
            $state_data[$val['city']]['temperature'] = array_merge($state_data[$val['city']]['temperature'], json_decode($val['temperature']));
            $state_data[$val['city']]['lon'] = $val['lon'];
            $state_data[$val['city']]['city'] = $val['city'];
            $state_data[$val['city']]['state'] = $val['state'];
        }
        foreach($state_data as $key => $state){
            $state_data[$key]['max'] = max($state['temperature']);
            $state_data[$key]['min'] = min($state['temperature']);
            unset($state_data[$key]['temperature']);
        }
        return json_encode(array_values($state_data));
    }
    
}

