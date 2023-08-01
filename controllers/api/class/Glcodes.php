<?php
class Glcodes{
	public $dbConnect;
	public $table	= "GLCODEDETAILS";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
	
	public function getGlCodes() {		

		$getQuery = "SELECT * FROM GLCODEDETAILS WHERE GLCODE like '3%' ORDER BY DESCRIPTION";	

		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $getRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $getRecord;
		}

		header('Content-Type: application/json');
		echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
	}	
}
?>