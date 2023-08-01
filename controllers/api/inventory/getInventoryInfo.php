<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Inventory.php');
$api = new Inventory();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$id = '';
		if(isset($_GET['id'])) {
			$id = $_GET['id'];
		}
		$api->getInventoryInfo($id);
		break;	
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>