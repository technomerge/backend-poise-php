<?php
$GLOBALS['servername']	= "192.168.1.195"; // OLD POISE;
$GLOBALS['username']	= "bennett";
$GLOBALS['password']	= "bennett";
$GLOBALS['port']		= "3306";

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
//echo $ip;	
if($ip == "192.168.169.1" || $ip == "192.168.1.14"){
	$GLOBALS['dbname']	= "flpoise";
}
else{
	$GLOBALS['dbname']	= "poise";
}


function db_connection (){

	$servername = $GLOBALS['servername'];
	$username	= $GLOBALS['username'];
	$password	= $GLOBALS['password'];
	$port		= $GLOBALS['port'];
	$dbname		= $GLOBALS['dbname'];
	
	$conn = new mysqli($servername, $username, $password, $dbname, $port);

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit();
	}
	else{
		return ($conn);
	}
}

function db_connection_PDO (){

	$servername = $GLOBALS['servername'];
	$username	= $GLOBALS['username'];
	$password	= $GLOBALS['password'];
	$port		= $GLOBALS['port'];
	$dbname		= $GLOBALS['dbname'];
	
	try {
		$conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo "<br>Packing Connected successfully"; 
		return ($conn);
		
		}
	catch(PDOException $e)
		{
		echo "<br>Connection failed: " . $e->getMessage();
		}
}
?>