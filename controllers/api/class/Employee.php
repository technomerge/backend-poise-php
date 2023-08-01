<?php
class Employee{
	public $dbConnect;
	public $table	= "EMPLOYEE";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
		
	public function insertEmployee($data){ 		
		$empQuery="
			INSERT INTO ".$this->table." 
			SET 
				FIRSTNAME			=	'" . $data["FIRSTNAME"] . "',
				LASTNAME			=	'" . $data["LASTNAME"] . "',
				ADDRESS1			=	'" . $data["ADDRESS1"] . "',
				CITY				=	'" . $data["CITY"] . "',
				STATE				=	'" . $data["STATE"] . "',
				ZIP					=	'" . $data["ZIP"] . "',
				COUNTRY				=	'" . $data["COUNTRY"] . "',
				PHONE				=	'" . $data["PHONE"] . "',
				EMAIL				=	'" . $data["EMAIL"] . "',
				COMMISSION			=	'" . $data["COMMISSION"] . "',
				ONHOLD				=	'" . $data["ONHOLD"] . "',
				ALLOWADMIN			=	'" . $data["ALLOWADMIN"] . "',
				ALLOWORDERS			=	'" . $data["ALLOWORDERS"] . "',
				ALLOWINVOICES		=	'" . $data["ALLOWINVOICES"] . "',
				ALLOWSHIPPING		=	'" . $data["ALLOWSHIPPING"] . "',
				ALLOWPO				=	'" . $data["ALLOWPO"] . "',
				USERNAME			=	'" . $data["USERNAME"] . "',
				PASSWORD			=	'" . $data["PASSWORD"] . "',
				STATUS				=	'" . $data["STATUS"] . "',
				SHOWINDROPDOWN		=	'" . $data["SHOWINDROPDOWN"] . "',
				PICKER				=	'" . $data["PICKER"] . "',
				PACKER				=	'" . $data["PACKER"] . "',
				WORKORDER			=	'" . $data["WORKORDER"] . "',
				TRUCKDRIVER			=	'" . $data["TRUCKDRIVER"] . "',
				TRUCKNUMBER			=	'" . $data["TRUCKNUMBER"] . "',
				THIRDPARTYSHIPPER	=	'" . $data["THIRDPARTYSHIPPER"] . "'
				";

		if( mysqli_query($this->dbConnect, $empQuery)) {
			$message = "Employee created Successfully.";
			$success = 1;
		} else {
			$message = "Employee creation failed.";
			$success = 0;
		}
		$empResponse = array(
			'success' => $success,
			'success_message' => $message
		);
		
		header('Content-Type: application/json');
		echo json_encode($empResponse);
	}
	
	
	public function getEmployee($empId, $data) {		
		$sqlQuery = '';
		if($empId) {
			$sqlQuery = "WHERE ID = '".$empId."'";
		}
		else{
			$sqlQuery = "
				WHERE 
				FIRSTNAME	LIKE 	'%" . $data['firstName'] . "%' 	AND 
				LASTNAME	LIKE 	'%" . $data['lastName']	. "%' 	AND
				USERNAME	LIKE 	'%" . $data['userName'] . "%'	AND
				PHONE		LIKE 	'%" . $data['phone'] . "%' 		AND
				CITY		LIKE 	'%" . $data['city'] . "%' 		AND
				ADDRESS1	LIKE 	'%" . $data['address'] . "%' 	AND
				STATUS		=	 	'"	. $data['status'] . "'		AND
				EMAIL		LIKE 	'%" . $data['email'] . "%' 		AND
				STATE		LIKE 	'%" . $data['state'] . "%' 		AND
				COUNTRY		LIKE 	'%" . $data['country'] . "%'	AND
				SALESREP	LIKE 	'%" . $data['salesRep'] . "%'
				";
		}	
		$empQuery = "
			SELECT
			 CAST(ID AS UNSIGNED) AS ID, FIRSTNAME, LASTNAME, ADDRESS1, CITY, STATE, PHONE, EMAIL  
			FROM ".$this->table." $sqlQuery
			ORDER BY ID";	
	
		$resultData = mysqli_query($this->dbConnect, $empQuery);

		$empData = array();
		while( $empRecord = mysqli_fetch_assoc($resultData) ) {
			$empData[] = $empRecord;
		}

		header('Content-Type: application/json');
		echo '{"data":' . json_encode($empData). ',"totalRecords":' . sizeof($empData) . '}';
	}
	
	public function getEmployeeInfo($empId) {		
		$sqlQuery = '';
		if($empId) {
			$sqlQuery = "WHERE ID = '".$empId."'";
		}
	
		$empQuery = "
			SELECT * 
			FROM ".$this->table." $sqlQuery
			";	
	
		$resultData = mysqli_query($this->dbConnect, $empQuery);

		$empData = array();
		while( $empRecord = mysqli_fetch_assoc($resultData) ) {
			$empData[] = $empRecord;
		}

		header('Content-Type: application/json');
		//echo '{"data":' . json_encode($empData). ',"totalRecords":' . sizeof($empData) . '}';
		echo rtrim((ltrim(json_encode($empData),"[")), "]");
	}	
	
	public function updateEmployee($data){ 		
		if($data["ID"]) {
			$empQuery="
				UPDATE ".$this->table." 
				SET 
				FIRSTNAME			=	'" . $data["FIRSTNAME"] . "',
				LASTNAME			=	'" . $data["LASTNAME"] . "',
				ADDRESS1			=	'" . $data["ADDRESS1"] . "',
				CITY				=	'" . $data["CITY"] . "',
				STATE				=	'" . $data["STATE"] . "',
				ZIP					=	'" . $data["ZIP"] . "',
				COUNTRY				=	'" . $data["COUNTRY"] . "',
				PHONE				=	'" . $data["PHONE"] . "',
				EMAIL				=	'" . $data["EMAIL"] . "',
				COMMISSION			=	'" . $data["COMMISSION"] . "',
				ONHOLD				=	'" . $data["ONHOLD"] . "',
				ALLOWADMIN			=	'" . $data["ALLOWADMIN"] . "',
				ALLOWORDERS			=	'" . $data["ALLOWORDERS"] . "',
				ALLOWINVOICES		=	'" . $data["ALLOWINVOICES"] . "',
				ALLOWSHIPPING		=	'" . $data["ALLOWSHIPPING"] . "',
				ALLOWPO				=	'" . $data["ALLOWPO"] . "',
				USERNAME			=	'" . $data["USERNAME"] . "',
				PASSWORD			=	'" . $data["PASSWORD"] . "',
				STATUS				=	'" . $data["STATUS"] . "',
				SHOWINDROPDOWN		=	'" . $data["SHOWINDROPDOWN"] . "',
				PICKER				=	'" . $data["PICKER"] . "',
				PACKER				=	'" . $data["PACKER"] . "',
				WORKORDER			=	'" . $data["WORKORDER"] . "',
				TRUCKDRIVER			=	'" . $data["TRUCKDRIVER"] . "',
				TRUCKNUMBER			=	'" . $data["TRUCKNUMBER"] . "',
				THIRDPARTYSHIPPER	=	'" . $data["THIRDPARTYSHIPPER"] . "'
				WHERE 
				ID = '".$data["ID"]."'";
				//echo $empQuery;
			if( mysqli_query($this->dbConnect, $empQuery)) {
				$message = "Employee updated successfully.";
				$success = 1;			
			} else {
				$message = "Employee update failed.";
				$success = 0;			
			}
		} else {
			$message = "Invalid request.";
			$success = 0;
		}
		$empResponse = array(
			'success' => $success,
			'success_message' => $message
		);
		header('Content-Type: application/json');
		echo json_encode($empResponse);
	}
	
	public function deleteEmployee($empId) {		
		if($empId) {			
			$empQuery = "
				DELETE FROM ".$this->table." 
				WHERE ID = '".$empId."'";	
			if( mysqli_query($this->dbConnect, $empQuery)) {
				$message = "User deleted";
				$success = 1;			
			} else {
				$message = "User delete failed.";
				$success = 0;			
			}		
		} else {
			$message = "Invalid request.";
			$success = 0;
		}
		$empResponse = array(
			'success' => $success,
			'success_message' => $message
		);
		header('Content-Type: application/json');
		//echo json_encode($empResponse);	
		echo json_encode($message);
	}	
	
	public function verifyAssociations($empId){
		if($empId) {			
			$existQuery = "SELECT EXISTS(SELECT ID FROM EMPLOYEE where ID=" . $empId . ") AS exist";
		
			$resultData = mysqli_query($this->dbConnect, $existQuery);
			
			$empExist = mysqli_fetch_assoc($resultData);

			if( $empExist['exist'] ){
				$empQuery = "
					SELECT
						(SELECT count(ID) from CUSTOMERS WHERE EMPLOYEEID = " . $empId . ") +
						(SELECT count(ID) from SUPPLIERS WHERE EMPLOYEEID = " . $empId . ") +
						(SELECT count(ID) from ACCOUNTS  WHERE EMPLOYEEID = " . $empId . ")   
						AS count  
					FROM dual";	
					
				$resultData = mysqli_query($this->dbConnect, $empQuery);
				$empData = mysqli_fetch_assoc($resultData);
	
				if($empData['count'] > 0) {
					$message = "User can not be deleted.";
					$associations = $empData['count'];
					$success = 0;	
				}
				else if($empData['count'] == 0) {
					$message = 'User is not associated ,it can be deleted.';
					$associations = 0;
					$success = 1;	
				}
			}	
			else{
				$message = "User not found";
				$associations = -1;
				$success = 0;				
			}					
		}
		else {
			$message = "Invalid request.";
			$success = 0;
		}
		$empResponse = array(
			'success' => $success,
			'success_message' => $message
		);

		if(!$success){
			header('Content-Type: application/json');
			//echo json_encode($empResponse);
			echo json_encode($message);	
		}
		return($associations);
	}
	
	public function getEmployeeList() {		
		$getQuery = "
			SELECT
			 CAST(ID AS UNSIGNED) AS ID, FIRSTNAME, LASTNAME, ADDRESS1, CITY, STATE, PHONE, EMAIL  
			FROM ".$this->table." 
			WHERE CONCAT(FIRSTNAME, LASTNAME) <> ''
			ORDER BY FIRSTNAME, LASTNAME";		
	
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		header('Content-Type: application/json');
		echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
		//echo json_encode($data);
	}	
}
?>