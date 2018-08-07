<?php
include 'src/Database/Model.php';
include 'src/API/WeatherAPI.php';
include 'src/API/LocationData.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$api = new WeatherAPI();

$data = json_decode(file_get_contents('php://input'), true);
//$api->loadJson($data['weather']['weather']);
print_r($api->returnAllData());
//print_r($api->locationObj);


