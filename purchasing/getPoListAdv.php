<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Purchasing.php');
$api = new Purchasing();

$api->cors();

switch($requestMethod) {
	case 'POST':
		$data = json_decode(file_get_contents('php://input'), true);
		$api->getPoListSearchAdv($data);
		break;				
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>