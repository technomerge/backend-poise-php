<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Suppliers.php');
$api = new Suppliers();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$api->getSuppliersList();
		break;
	case 'POST':
		$data = json_decode(file_get_contents('php://input'), true);
		$api->getSuppliersListFiltered($data);
		break;		
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>