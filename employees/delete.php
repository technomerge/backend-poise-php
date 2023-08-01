<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Employee.php');
$api = new Employee();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$empId = '';	
		if($_GET['id']) {
			$empId = $_GET['id'];
		}

		$associations = $api->verifyAssociations($empId);

		if($associations == 0){
			$api->deleteEmployee($empId);
		}
		
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>