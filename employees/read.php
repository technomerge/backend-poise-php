<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Employee.php');
$api = new Employee();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$empId = '';
		if(isset($_GET['id'])) {
			$empId = $_GET['id'];
		}
		$api->getEmployeeInfo($empId);
		break;
	case 'POST':
		$empId = '';
	
		$data = json_decode(file_get_contents('php://input'), true);

		if(isset($_GET['id'])) {
			$empId = $_GET['id'];
		}
		$api->getEmployee($empId, $data);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>