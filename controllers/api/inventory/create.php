<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Inventory.php');
$api = new Inventory();

$api->cors();

switch($requestMethod) {
	case 'POST':
		$data = json_decode(file_get_contents('php://input'), true);
		$api->insertInventory($data);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>