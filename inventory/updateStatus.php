<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Inventory.php');
$api = new Inventory();

$api->cors();

switch($requestMethod) {
	case 'POST':
		$invId = '';
		if(isset($_GET['id'])) {
			$invId = $_GET['id'];
		}	
		$data = json_decode(file_get_contents('php://input'), true);
		$api->updateStatus($invId, $data);
		break;		
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>