<?php
//$url = 'http://localhost/FishingBooker/index.php?lat=35.1234&lon=-88.5897';
$url = 'http://localhost/FishingBooker/weather?a=hello&lat=35.1234&lon=-88.5897';
$ch = curl_init($url);
//setup request to send json via POST
$data = 
        '{
 "id": 32, 
 "date": "1985-01-04",
 "location": {
      "lat": 32.5,
      "lon": -93.6667,
      "city": "Shreveport",
      "state": "Louisiana"
 },
 "temperature": [
      42.3, 54.1, 54.1, 54.1, 54.1, 40.1,
      40.1, 39.4, 40.1, 43.1, 45.8, 48.6,
      50.5, 52.1, 53.4, 54.1, 54.0, 52.2,
      49.2, 47.1, 45.7, 44.7, 44.1, 43.2
 ]
}';
$payload = json_encode($data);
//attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
//return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER  , true);
//execute the POST request
$result = curl_exec($ch);
print_r($result);
//print_r(get_headers($url));
//close cURL resource
curl_close($ch);