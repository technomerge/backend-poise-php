<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Account.php');
$api = new Account();

$api->cors();

switch($requestMethod) {	
	case 'POST':
		//print_r($_POST);
		$accId = '';
		if(isset($_GET['id'])) {
			$accId = $_GET['id'];
		}		
		$data = json_decode(file_get_contents('php://input'), true);
		$api->updateAccount($accId, $data);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>