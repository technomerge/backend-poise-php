<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Inventory.php');
$api = new Inventory();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$invId = '';	
		if($_GET['id']) {
			$invId = $_GET['id'];
		}

		$associations = $api->verifyAssociations($invId);

		if($associations == 0){
			$api->deleteInventory($invId);
		}
		
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>