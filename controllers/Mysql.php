<?php header('Access-Control-Allow-Origin: *'); ?>

<?php
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
//echo $ip;	
	
if($ip == "192.168.169.1" || $ip == "192.168.1.14"){
	//include '../models/mysql/flpoise_db_functions.php';
	include '../models/mysql/db_functions.php';
}
else{
	//include '../models/mysql/flpoise_db_functions.php';
	include '../models/mysql/db_functions.php';
}

?>

<?php

$host 		= $_POST['x_host'];
$user 		= $_POST['x_user'];
$password 	= $_POST['x_password'];
$db 		= $_POST['x_db'];
$query 		= $_POST['x_query'];
$method 	= $_POST['x_method'];

if($method == 'query'){
	$result = get_query($host, $user, $password, $db, $query, $method);
//echo "<pre>";
//print_r($result[0]['CLIENTNAME']);
	//$data = json_encode($result);
	
//echo $data;

//$test = json_decode($data);
//var_dump($test);

//echo $test[0]->ID;
	//$data = $result[0]
	
	$data = serialize($result);
	
	echo $data;
}

/* End of file Mysql.php */
/* Location: ./application/controllers/Mysql.php */

?>