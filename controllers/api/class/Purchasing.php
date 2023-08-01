<?php
class Purchasing{
	public $dbConnect;
	public $table	= "PURCHASING";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
		
	
	public function getPoInfoReceipt($poId) {	
		$getQuery="
		SELECT 
		s.NAME,
		s.ADDRESS1,
		s.ADDRESS2,
		s.CITY,
		s.STATE,
		s.ZIP,
		s.COUNTRY,
		s.CONTACTPERSON,
		s.EMAIL,
		s.PHONE,
		p.PAYMENTTYPE,
		p.EMPLOYEEID,
		p.ID,
		e.FIRSTNAME,
		e.LASTNAME 
		FROM
		PURCHASEORDERS p 
		left join SUPPLIERS s on s.ID = p.SUPPLIERID 
		left join EMPLOYEE e on e.ID = p.EMPLOYEEID 
		WHERE 
		p.ID = '$poId'
		";
		
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}
		
		header('Content-Type: application/json');
		//echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
		echo json_encode($data);		
		
	}	
		
}
?>