<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Colors.php');
$api = new Colors();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$api->getColors();
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>