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
    
    public function types() : array
    {
        return ['id' => 'float', 'date' => 'string', 'location' => 'int', 'temperature' => 'array'];
    }
    
    public function loadJson($val){
            $val = (array) json_decode($val);
            
            if(!empty($this->select('id')->where(['id' => $val['id']])->asArray()->one())){
                 http_response_code(400);
                 return;
            }
            
            foreach($val as $v_key => $v_val){
                if($v_key == 'location'){
                    $location_data = new LocationData();
                    if($location_data->loadJson($v_val)->save()){
                        $v_val = (int)$location_data->getLastInsertId();
                    }
                    else{
                        return json_encode(['response' => $location_data->error]);
                    }
                }
                $this->innerSet($v_key, $v_val);
            }
            if($this->save()){
              http_response_code(201);
              return true;
            }
            else{
                $location_data->delete();
                http_response_code(400);
//                die();
//                return false;
                return json_encode(['response' => $this->error]);
            }
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
    
    public function returnAllData($params){
        $data = $this->select()->leftJoin(['location_data', 'location_data.location_id', 'weather.location'])->orderBy('record_id ASC');
        $data = !empty($params) ? $data->where(['lat' => $params['lat'], 'lon' => $params['lon']])->all() : $data->all();
        
        $json = [];
        foreach($data as $key => $val){
                $json[$key] = $this->formatDataForOutput($val);
        }
        if(empty($json)){
            http_response_code(404);
        }
        else{
            http_response_code(200);
            return json_encode($json, JSON_PRETTY_PRINT);
        }
//        return json_encode(empty($json) ? ['notice' => 'No data available'] : $json, JSON_PRETTY_PRINT);
    }
    
    public function returnTemperatureRanges($params){
        $data = $this->select('weather.record_id, weather.temperature, location_data.city, location_data.state, location_data.lat, location_data.lon')
                ->leftJoin(['location_data', 'location_data.location_id', 'weather.location'])
                ->where(sprintf('date BETWEEN %s AND %s', strtotime($params['start']), strtotime($params['end'])))->orderBy('city ASC, state ASC')->all();
        if(empty($data)){
            return json_encode(['lat' => '', 'lon' => '', 'city' => '', 'state' => '', 'message' => 'There is no weather data in the given date range']);
        }
        $state_data = [];
        foreach($data as $key => $val){
            $state_data[$val['city']]['lat'] = $val['lat'];
            $state_data[$val['city']]['lon'] = $val['lon'];
            $state_data[$val['city']]['city'] = $val['city'];
            if(!isset($state_data[$val['city']]['temperature'])){
                $state_data[$val['city']]['temperature'] = [];
            }
            $state_data[$val['city']]['temperature'] = array_merge($state_data[$val['city']]['temperature'], json_decode($val['temperature']));
            
            $state_data[$val['city']]['state'] = $val['state'];
        }
        foreach($state_data as $key => $state){
            $state_data[$key]['lowest'] = min($state['temperature']);
            $state_data[$key]['highest'] = max($state['temperature']);
//            if($state['city'] == 'Nashville'){
//                print_r($state['temperature']);
//            }
            unset($state_data[$key]['temperature']);
        }
//        exit;
        return json_encode(array_values($state_data));
    }
    
    public function eraseData($params){
        if(empty($params)){
            if($this->deleteAll()){
                http_response_code(200);
                return true;
            }
        }
        else{
            if($this->deleteFromParams($params)){
                http_response_code(200);
                return true;
            }
        }
    }
    
    
        
    private function deleteAll(){
            $sql = 'DELETE weather, location_data FROM weather LEFT JOIN location_data ON weather.location = location_data.location_id';
			try{
				$this->conn->exec($sql);
				return true;
			}
			catch(Exception $e){
				echo $e->getMessage();
				exit;
			}
    }
    
    private function deleteFromParams($params){
        $sql = sprintf(
                "DELETE weather, location_data FROM weather LEFT JOIN location_data ON weather.location = location_data.location_id WHERE location_data.lat = %s AND location_data.lon = %s AND weather.date BETWEEN %s AND %s", $params['lat'], $params['lon'], strtotime($params['start']), strtotime($params['end']));
   try{
				$this->conn->exec($sql);
				return true;
			}
			catch(Exception $e){
				echo $e->getMessage();
				exit;
			}
        }
    
}

