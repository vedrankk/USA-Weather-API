<?php

class LocationData extends Model
{
    /**
     * Overrides parent::tableName()
     * @return string
     */
    public function tableName() : string
    {
        return 'location_data';
    }
    
    /**
     * Overrides parent::attributes()
     * @return array
     */
    public function attributes() : array
    {
        return ['location_id', 'lat', 'lon', 'city', 'state'];
    }
    
    /**
     * Overrides parent::filters()
     * @return array
     */
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
    
    /**
     * Overrides parent::types()
     * @return array
     */
    public function types() : array 
    {
        return ['lat' => 'float', 'lon' => 'float', 'city' => 'string', 'state' => 'string'];
    }
    
    /*
     * Loads the JSON values from the object supplied in WeatherAPI class
     */
    public function loadJson($json)
    {
        $this->innerSet('lat', $json->lat);
        $this->innerSet('lon', $json->lon);
        $this->innerSet('city', $json->city);
        $this->innerSet('state', $json->state);
        return $this;
    }
}
