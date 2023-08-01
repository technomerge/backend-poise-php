<?php
class Region{
	public $dbConnect;
	public $table	= "COUNTRIES";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
	
	public function getCountryList() {		

		$getQuery = "SELECT countries_iso_code_2 AS COUNTRYCODE,countries_name AS COUNTRY FROM COUNTRIES WHERE enabled = 'YES'";	
	
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $getRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $getRecord;
		}

		header('Content-Type: application/json');
		echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
	}	
	
	public function getCountryStateList($countryId) {		

		$getQuery = "SELECT code as STATECODE, default_name as STATE from country_region WHERE country_id='$countryId'";	
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