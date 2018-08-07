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
                    $location_data->loadJson($v_val);
                    print_r($location_data->save()); exit;
                    continue;
                }
                $this->innerSet($v_key, $v_val);
            }
        }
    }
    
//    public function types() : array
//    {
//        return [
//            'date' => 'string',
//            'location' => [
//                'type' => 'array',
//                'length' => 4,
//                'keys' => ['lat', 'long', 'city', 'state'],
//                'key_types' => [
//                    'lat' => 'float',
//                    'long' => 'float',
//                    'city' => 'string',
//                    'state' => 'string',
//                ],
//                'special_filters' => [
//                    'lat' => [
//                        'filter' => 'decimalSpaces',
//                        'decimalSpaces' => 2
//                    ],
//                    'long' => [
//                        'filter' => 'decimalSpaces',
//                        'decimalSpaces' => 2
//                    ]
//                ]
//            ],
//            'temperature' => [
//                'type' => 'array',
//                'length' => '24',
//                'key_types' => [
//                    'all' => 'float'
//                ],
//                'special_filters' => [
//                    'all' => 'decimalSpaces',
//                    'decimalSpaces' => 2
//                ]
//            ]
//        ];
//    }
    
}

