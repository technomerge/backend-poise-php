<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Purchasing.php');
$api = new Purchasing();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$poId = '';
		if(isset($_GET['id'])) {
			$poId = $_GET['id'];
		}
		$api->getPoReceiptDates($poId);
		break;	
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>