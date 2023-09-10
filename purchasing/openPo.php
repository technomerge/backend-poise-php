<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Purchasing.php');
$api = new Purchasing();

$api->cors();

switch($requestMethod) {
	case 'POST':
		$poId = '';
		if(isset($_GET['id'])) {
			$poId = $_GET['id'];
		}		
		$data = json_decode(file_get_contents('php://input'), true);
		$api->openPo($poId, $data);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>