<?php

//include '../Database/Model.php';

class WeatherAPI extends Model{
    
    /**
     * Overrides parent::tableName()
     * @return string
     */
    public function tableName() : string
    {
        return 'weather';
    }
    
    /**
     * Overrides parent::attributes()
     * @return array
     */
    public function attributes() : array
    {
        return ['record_id', 'id', 'date', 'location', 'temperature'];
    }
    
    /**
     * Overrides parent::filters()
     * @return array
     */
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
    
     /**
     * Overrides parent::types()
     * @return array
     */
    public function types() : array
    {
        return ['id' => 'int', 'date' => 'string', 'location' => 'int', 'temperature' => 'array'];
    }
    
    /*
     * Loads the JSON from the request, checks if the ID exists and proccesses the data
     * Creates a new entry in Location Data and validates the data formats in both classes
     */
    public function loadJson($val)
    {
            $val = (array) json_decode($val);
            if(empty($val) || !empty($this->select('id')->where(['id' => $val['id']])->asArray()->one())){
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
                if($v_key == 'temperature' && sizeof($v_val) !== 24){
                    if(isset($location_data)){
                        $location_data->delete();
                    }
                    http_response_code(400);
                    return json_encode(['response' => 'Temperature does not have 24 values for location id: '.$val['id']]);
                }
                $this->innerSet($v_key, $v_val);
            }
            if($this->save()){
              http_response_code(201);
            }
            else{
                $location_data->delete();
                http_response_code(400);
                return json_encode(['response' => $this->error]);
            }
    }
    
    /*
     * Formats data gotten from the database so it is outputed to the user correctly
     */
    private function formatDataForOutput(array $data) : array
    {
        $json = [];
        $json['id'] = $data['id'];
        $json['date'] = date('Y-m-d', $data['date']);
        $json['location'] = [
            'lat' => number_format(floatval($data['lat']), 4),
            'lon' => number_format(floatval($data['lon']), 4),
            'city' => $data['city'],
            'state' => $data['state']
        ];
        $json['temperature'] = array_map(function($e){
            return number_format(floatval($e), 1);
        }, json_decode($data['temperature']));
        return $json;
        
    }
    
    /*
     * @param array $params - GET request params(if they exist)
     * Based on the params, gets all data, or data specific for lat/lon
     */
    public function returnAllData($params){
        $data = $this->select()->leftJoin(['location_data', 'location_data.location_id', 'weather.location'])->orderBy('id ASC');
        $data = !empty($params) ? $data->where(['lat' => $params['lat'], 'lon' => $params['lon']])->all() : $data->all();
        
        $json = [];
        foreach($data as $key => $val){
                $json[$key] = $this->formatDataForOutput($val);
        }
        if(empty($json)){
            if(!empty($params)){
                http_response_code(404);
                return;
            }
            return json_encode(['message' => 'There is no data available.']);
        }
        else{
            http_response_code(200);
            return json_encode($json, JSON_PRETTY_PRINT);
        }
//        return json_encode(empty($json) ? ['notice' => 'No data available'] : $json, JSON_PRETTY_PRINT);
    }
    
    /*
     * @param array $params - GET request params
     * Gets the temperature based on date and location, groups by city and counts min/max for every city in the given range
     */
    public function returnTemperatureRanges($params){
        $data = $this->select('weather.record_id, weather.temperature, location_data.city, location_data.state, location_data.lat, location_data.lon')
                ->leftJoin(['location_data', 'location_data.location_id', 'weather.location'])
                ->where(sprintf('date BETWEEN %s AND %s', strtotime($params['start']), strtotime($params['end'])))->orderBy('city ASC, state ASC')->all();
        if(empty($data)){
            return json_encode(['lat' => '', 'lon' => '', 'city' => '', 'state' => '', 'message' => 'There is no weather data in the given date range']);
        }
        $state_data = [];
        foreach($data as $key => $val){
            $state_data[$val['city']]['lat'] = number_format(floatval($val['lat']), 4);
            $state_data[$val['city']]['lon'] = number_format(floatval($val['lon']), 4);
            $state_data[$val['city']]['city'] = $val['city'];
            if(!isset($state_data[$val['city']]['temperature'])){
                $state_data[$val['city']]['temperature'] = [];
            }
            $state_data[$val['city']]['temperature'] = array_merge($state_data[$val['city']]['temperature'], json_decode($val['temperature']));
            
            $state_data[$val['city']]['state'] = $val['state'];
        }
        foreach($state_data as $key => $state){
            $state_data[$key]['lowest'] = number_format(floatval(min($state['temperature'])), 1);
            $state_data[$key]['highest'] = number_format(floatval(max($state['temperature'])),1);
            unset($state_data[$key]['temperature']);
        }
        
        return json_encode(array_values($state_data));
    }
    
    /*
     * @param array $params - GET request params(if they exist)
     * Depening on the params, deletes all data or data based on lat/lon and date
     */
    public function eraseData($params){
        if(empty($params)){
            if($this->deleteAll()){
                http_response_code(200);
                return json_encode(['response' => 'Data deleted']);
            }
        }
        else{
            if($this->deleteFromParams($params)){
                http_response_code(200);
                return json_encode(['response' => 'Data deleted']);
            }
        }
    }
    
    
    /*
     * Deletes all the data from the table
     */
    private function deleteAll(){
//            $sql = 'DELETE weather, location_data FROM weather LEFT JOIN location_data ON weather.location = location_data.location_id';
        $sql = 'TRUNCATE weather; TRUNCATE location_data';
            try{
                    $this->conn->exec($sql);
                    return true;
            }
            catch(Exception $e){
                    echo $e->getMessage();
                    exit;
            }
    }
    
    /*
     * @param array $params - GET request params
     * Deletes data from the table based on params
     */
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

