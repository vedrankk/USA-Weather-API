<?php
include 'src/Database/Model.php';
include 'src/API/WeatherAPI.php';
include 'src/API/LocationData.php';
include 'src/Controller.php';
$control = new Controller();

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$controller = new Controller();
print_r($control->returnAction());
//$api = new WeatherAPI();
//print_r($_SERVER['REQUEST_METHOD']);
//$data = json_decode(file_get_contents('php://input'), true);
//print_r($data);
//$code = $api->loadJson($data['weather']['weather']);
//if($code['response_code'] == 400){
//echo header("Status: 400 Not Found");
//}
//print_r($api->getSpecific('35.1234', '-88.5897'));
//print_r($api->returnAllData());
//echo strtotime('1985-01-03'); echo '<br>';
//echo strtotime('1985-01-10'); echo "<br>"; echo date('d-m-Y', 473641200);
//print_r($api->locationObj);
//print_r($api->returnTemperatureRanges());

