<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Account.php');
$api = new Account();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$api->getAccountList();
		break;
	case 'POST':
		$data = json_decode(file_get_contents('php://input'), true);
		$api->getAccountListFiltered($data);


		//$data = array();
		//$data[] = 1;
		//header('Content-Type: application/json');
		//echo json_encode($data);



		break;		
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>