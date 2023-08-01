<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Sizes.php');
$api = new Sizes();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$api->getSizes();
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>