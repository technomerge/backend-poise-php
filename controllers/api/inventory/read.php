<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Inventory.php');
$api = new Inventory();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$invId = '';
		if(isset($_GET['id'])) {
			$invId = $_GET['id'];
		}
		$api->getInventoryInfo($invId);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>