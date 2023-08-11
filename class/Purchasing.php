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

	public function getPoList() {	
		$getQuery="
		SELECT 
		TRIM(LEADING '0' FROM PURCHASEORDERS.ID) AS poid, 
		SUBSTR(CREATIONDATE, 1, 10) AS creationdate,  
		SUBSTR(DUEDATE, 1, 10) AS duedate, 
		SUPPLIERS.ID AS supplierid, 
		SUPPLIERS.NAME AS supplier, 
		SUPPLIERS.ADDRESS1 AS address1, 
		SUPPLIERS.ADDRESS2 AS address2, 	
		SUPPLIERS.CITY AS city, 
		SUPPLIERS.STATE AS state, 
		SUPPLIERS.ZIP AS zipcod, 
		PURCHASEORDERS.STATUS AS status 
		FROM PURCHASEORDERS JOIN SUPPLIERS ON PURCHASEORDERS.SUPPLIERID = SUPPLIERS.ID 
		ORDER BY PURCHASEORDERS.CREATIONDATE DESC, PURCHASEORDERS.ID DESC
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

	public function getPoListSearch($data) {	
		$getQuery="
		SELECT 
		TRIM(LEADING '0' FROM PURCHASEORDERS.ID) AS poid, 
		SUBSTR(CREATIONDATE, 1, 10) AS creationdate,  
		SUBSTR(DUEDATE, 1, 10) AS duedate, 
		SUPPLIERS.ID AS supplierid, 
		SUPPLIERS.NAME AS supplier, 
		SUPPLIERS.ADDRESS1 AS address1, 
		SUPPLIERS.ADDRESS2 AS address2, 	
		SUPPLIERS.CITY AS city, 
		SUPPLIERS.STATE AS state, 
		SUPPLIERS.ZIP AS zipcod, 
		PURCHASEORDERS.STATUS AS status 
		FROM PURCHASEORDERS JOIN SUPPLIERS ON PURCHASEORDERS.SUPPLIERID = SUPPLIERS.ID 
		WHERE
		1= case 
		when '" . $data["status"] . "' !='' and PURCHASEORDERS.STATUS = '" . $data["status"] . "' then 1 
		when '" . $data["status"] . "' ='' then 1 
		else 0 
		end
		
		and
		
		(
		1= case 
			when '" . $data["supplierNameMode"] . "' ='starts' and '" . $data["supplierName"] . "' !='' and SUPPLIERS.NAME like '" . $data["supplierName"] . "%' then 1 
			when '" . $data["supplierNameMode"] . "' ='' then 1 
			when '" . $data["supplierName"] . "' ='' then 1 
			else 0 
		end
		
		or
		
		1= case 
			when '" . $data["supplierNameMode"] . "' ='contains' and '" . $data["supplierName"] . "' !='' and SUPPLIERS.NAME like '%" . $data["supplierName"] . "%' then 1 
			when '" . $data["supplierNameMode"] . "' ='' then 1 
			when '" . $data["supplierName"] . "' ='' then 1 
			else 0 
		end	
		)

		ORDER BY PURCHASEORDERS.CREATIONDATE DESC, PURCHASEORDERS.ID DESC
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