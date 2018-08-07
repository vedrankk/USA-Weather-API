<?php
//echo floatval('-12.2');
//echo '<br><br>';
//API URL
$url = 'http://localhost/FishingBooker/';

//create a new cURL resource
$ch = curl_init($url);

//setup request to send json via POST
$data = array(
    'weather' => [
        '{
 "id": 1,
 "date": "1985-01-01",
 "location": {
       "lat": 35.1442,
       "lon": -111.6664,
       "city": "Flagstaff",
       "state": "Arizona"
   },
   "temperature": [
      28.5, 27.6, 26.7, 25.9, 25.3, 24.7,
      24.3, 24.0, 27.1, 34.0, 38.6, 41.3,
      43.2, 44.4, 45.0, 45.3, 45.1, 44.2,
      41.9, 38.0, 35.0, 33.0, 31.1, 29.9
   ]
}','{
 "id": 2, 
 "date": "1985-01-02",
 "location": {
      "lat": 36.1189,
      "lon": -86.6892,
      "city": "Nashville",
      "state": "Tennessee"
   },
 "temperature": [
      37.5, 37.0, 36.6, 36.2, 35.9, 35.5,
      35.3, 35.2, 36.1, 38.3, 40.6, 42.7,
      44.2, 45.3, 46.0, 46.1, 45.3, 43.3,
      42.0, 41.2, 40.3, 39.6, 39.0, 38.4
 ]
} '
    ]
);
$payload = json_encode(array("weather" => $data));

//attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute the POST request
$result = curl_exec($ch);
print_r($result);

//close cURL resource
curl_close($ch);
