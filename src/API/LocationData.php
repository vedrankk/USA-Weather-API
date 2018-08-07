<?php

class LocationData extends Model
{
    public function tableName() : string
    {
        return 'location_data';
    }
    
    public function attributes() : array
    {
        return ['location_id', 'lat', 'lon', 'city', 'state'];
    }
    
    public function filters() : array
    {
        return [
            'lat' => [
                'filter' => 'FloatValue',
                'param' => 4,
            ],
            'lon' => [
                'filter' => 'FloatValue',
                'param' => 4,
            ],
        ];
    }
    
    public function loadJson($json)
    {
        $this->innerSet('lat', $json->lat);
        $this->innerSet('lon', $json->lon);
        $this->innerSet('city', $json->city);
        $this->innerSet('state', $json->state);
    }
}
