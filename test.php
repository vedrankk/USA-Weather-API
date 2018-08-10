<?php

//$url = 'http://localhost/FishingBooker/index.php?lat=35.1234&lon=-88.5897';
$url = 'http://localhost/FishingBooker/weather?a=hello&lat=35.1234&lon=-88.5897';

$ch = curl_init($url);

//setup request to send json via POST
$data = 
        '{
 "id": 22,
 "date": "1985-01-07",
 "location": {
       "lat": 35.12313132,
       "lon": -111.6664123,
       "city": "Flagstaff",
       "state": "Arizona"
   },
   "temperature": [
      28.533, 27.6, 26.72, 25.9, 25.3, 24.7,
      24.3, 24.0, 27.1, 34.0, 38.6, 41.3,
      43.2, 44.4, 45.0, 45.3, 45.1, 44.2,
      41.9, 38, 35.0, 33.0, 31.1, 29.9
   ]
}';
$payload = json_encode($data);

//attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute the POST request
$result = curl_exec($ch);
print_r($result);
//print_r(get_headers($url));

//close cURL resource
curl_close($ch);
