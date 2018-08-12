<?php
include 'src/Database/Model.php';
include 'src/API/WeatherAPI.php';
include 'src/API/LocationData.php';
include 'src/Request.php';
include 'src/Controller.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$controller = new Controller();
print_r($controller->returnAction());