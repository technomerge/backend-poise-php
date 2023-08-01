<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Glcodes.php');
$api = new Glcodes();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$api->getGlCodes();
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>