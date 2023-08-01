<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Inventory.php');
$api = new Inventory();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$rootSku = '';
		if(isset($_GET['id'])) {
			$rootSku = $_GET['id'];
		}
		$api->getProduct($rootSku);
		break;	
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>