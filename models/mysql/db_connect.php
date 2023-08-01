
<?php 

function db_connection ($dbname)
{
//$servername = "dbm1ez";
//$username = "php_app";
//$password = "Pa123456";
//$servername = "46.1.3.6";
//$username = "gone";
//$password = "fishing";

$servername = "192.168.1.195"; // OLD POISE
$username = "bennett";
$password = "bennett";

//$servername = "192.168.1.191";  // NEW SERVER
//$username = "backend";
//$password = "1709171019";

try {
    $conn = new PDO("mysql:host=$servername;port=3306;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "<br>Packing Connected successfully"; 
	return ($conn);
	
    }
catch(PDOException $e)
    {
    echo "<br>Fishbowl Connection failed: " . $e->getMessage();
    }
}
?>