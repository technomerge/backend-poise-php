<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Region.php');
$api = new Region();

$api->cors();

switch($requestMethod) {
	case 'GET':
		$countryId = '';
		if(isset($_GET['id'])) {
			$countryId = $_GET['id'];
		}	
		$api->getCountryStateList($countryId);
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

?>