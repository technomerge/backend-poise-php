<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Employee.php');
$api = new Employee();

$api->cors();

switch($requestMethod) {	
	case 'POST':
		//print_r($_POST);
		$empId = '';
		if(isset($_GET['id'])) {
			$empId = $_GET['id'];
		}		
		$data = json_decode(file_get_contents('php://input'), true);
		$api->updateEmployee($data);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>