<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Employee.php');
$api = new Employee();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$api->getEmployeeList();
		break;
	case 'POST':
		$data = json_decode(file_get_contents('php://input'), true);
		$api->getEmployeeListFiltered($data);
		break;		
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>