<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Account.php');
$api = new Account();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$accId = '';
		if(isset($_GET['id'])) {
			$accId = $_GET['id'];
		}
		$api->getAccountInfo($accId);
		break;
	case 'POST':
		$accId = '';
	
		$data = json_decode(file_get_contents('php://input'), true);

		if(isset($_GET['id'])) {
			$accId = $_GET['id'];
		}
		$api->getAccount($accId, $data);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>