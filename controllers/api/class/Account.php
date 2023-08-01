<?php
class Account{
	public $dbConnect;
	public $table	= "ACCOUNTS";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
	
	public function getAccountList() {		
		$getQuery = "
			SELECT
			 CAST(ID AS UNSIGNED) AS ID, BENNETTID, PREFIX, NAME, ADDRESS1, CITY, STATE, PHONE  
			FROM ".$this->table."
			ORDER BY NAME";	
	
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		header('Content-Type: application/json');
		//echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
		echo json_encode($data);
	}
	
	public function getAccountListFiltered($data) {	
		$params_id = 			$data["id"];
		$params_status = 		$data["status"];
		$params_accountName = 	$data["accountName"];
		$params_address = 		$data["address"];
		$params_phone = 		$data["phone"];
		$params_city = 			$data["city"];
		$params_state = 		$data["state"];
		$params_country = 		$data["country"];
		$params_email = 		$data["email"];
		$params_employeeId = 	$data["employeeId"];
 
		$getQuery="
		SELECT a.BENNETTID ,a.PREFIX, a.ID, a.NAME, a.ADDRESS1, a.CITY, a.STATE, a.PHONE 
		
		FROM ACCOUNTS AS a   
		
		WHERE   
		1=case 
			when '$params_id' > 0 and a.BENNETTID = '$params_id' then 1 
			when '$params_id' = 0 then 1 
			else 0 
		end  
		
		AND 
		
		1=case 
			when '$params_status' != '' and a.STATUS = '$params_status' then 1 
		  when '$params_status' = '' then 1 
		  else 0 
		end 
		
		AND 
		
		1=case
			when '$params_accountName' != '' and a.NAME like concat('%','$params_accountName','%') then 1 
			when '$params_accountName' = '' then 1 
			else 0 
		end 
		
		AND 
		
		1=case
			when '$params_address' != '' and a.ADDRESS1 like concat('%','$params_address','%') then 1 
			when '$params_address' = '' then 1 
			else 0 
		end  
		
		AND 
		
		1=case 
			when '$params_phone' != '' and REPLACE(a.PHONE,'-','') like concat('%','$params_phone','%') then 1 
			when '$params_phone' = '' then 1 
			else 0 
		end 
		
		AND 
		
		1=case 
			when '$params_city' != '' and a.CITY like concat('%','$params_city','%') then 1 
			when '$params_city' = '' then  1 
			else 0 
		end 
		
		AND 
		
		1=case 
			when '$params_state' != '' and a.STATE like concat('%','$params_state','%') then 1 
			when '$params_state' = '' then 1 
			else 0 
		end 
		
		AND 
		
		1=case
			when '$params_country' != '' and a.COUNTRY like concat('%','$params_country','%') then 1 
			when '$params_country' = '' then 1 
			else 0 
		end  
		
		AND 
		
		1=case 
			when '$params_email' != '' and a.EMAIL like concat('%','$params_email','%') then 1 
			when '$params_email' = '' then 1 
			else 0 
		end
		
		AND 
		
		1=case
			when '$params_employeeId' > 0 and a.EMPLOYEEID = '$params_employeeId' then 1 
			when '$params_employeeId' = 0 then 1 
			else 0 
		end
		
		ORDER BY NAME
		";		
		//echo $getQuery;		
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		header('Content-Type: application/json');
		echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
		//echo json_encode($data);
	}	
	
	public function getAccountInfo($accId) {		
		$sqlQuery = '';
		if($accId) {
			$sqlQuery = "WHERE ID = '".$accId."'";
		}
	
		$getQuery = "
			SELECT * 
			FROM ".$this->table." $sqlQuery
			";	
	
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $accRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $accRecord;
		}

		header('Content-Type: application/json');
		//echo '{"data":' . json_encode($data). ',"totalRecords":' . sizeof($data) . '}';
		echo rtrim((ltrim(json_encode($data),"[")), "]");
	}	
	
	public function updateAccount($accId, $data){ 		

//print_r($data);
//echo $data['updates'][0]['param'] . ' : ' . $data['updates'][0]['value'];
		if($data['updates'][0]['value']) {
			$updQuery="
				UPDATE ".$this->table." 
				SET ";
				
				for($i=1; $i<sizeof($data['updates'])-1;$i++){
					$updQuery .= $data['updates'][$i]['param'] . " = '" . $data['updates'][$i]['value'] . "', ";
				}
				$updQuery .= $data['updates'][$i]['param'] . " = '" . $data['updates'][$i]['value'] . "' ";
				/*
				"
				NAME							=	'" . $data["NAME"] . "',
				ADDRESS1						=	'" . $data["ADDRESS1"] . "',
				ADDRESS2						=	'" . $data["ADDRESS2"] . "',
				CITY							=	'" . $data["CITY"] . "',
				STATE							=	'" . $data["STATE"] . "',
				ZIP								=	'" . $data["ZIP"] . "',
				COUNTRY							=	'" . $data["COUNTRY"] . "',
				CONTACTPERSON					=	'" . $data["CONTACTPERSON"] . "',
				EMAIL							=	'" . $data["EMAIL"] . "',
				PHONE							=	'" . $data["PHONE"] . "',
				DESCRIPTION						=	'" . $data["DESCRIPTION"] . "',
				COUNTYTAX						=	'" . $data["COUNTYTAX"] . "',
				CITYTAX							=	'" . $data["CITYTAX"] . "',
				STATETAX						=	'" . $data["STATETAX"] . "',
				PREFIX							=	'" . $data["PREFIX"] . "',
				COUNTY							=	'" . $data["COUNTY"] . "',
				STATUS							=	'" . $data["STATUS"] . "',
				PRICEBUCKET						=	'" . $data["PRICEBUCKET"] . "',
				EMPLOYEEID						=	'" . $data["EMPLOYEEID"] . "',
				ONHOLD							=	'" . $data["ONHOLD"] . "',
				ALLOWCC							=	'" . $data["ALLOWCC"] . "',
				ALLOWCHECK						=	'" . $data["ALLOWCHECK"] . "',
				ALLOWCOD						=	'" . $data["ALLOWCOD"] . "',
				ALLOWOPEN						=	'" . $data["ALLOWOPEN"] . "',
				ENABLESSORDERS					=	'" . $data["ENABLESSORDERS"] . "',
				HANDLINGFEE						=	'" . $data["HANDLINGFEE"] . "',
				BOPOLICY						=	'" . $data["BOPOLICY"] . "',
				SHIPPINGCOSTPOLICY				=	'" . $data["SHIPPINGCOSTPOLICY"] . "',
				TAXPOLICY						=	'" . $data["TAXPOLICY"] . "',
				FACTOROPEN						=	'" . $data["FACTOROPEN"] . "',
				INVOICEATSHIPPING				=	'" . $data["INVOICEATSHIPPING"] . "',
				SENDEDIINVOICE					=	'" . $data["SENDEDIINVOICE"] . "',
				EDILOGIN						=	'" . $data["EDILOGIN"] . "',
				EDIPASSWORD						=	'" . $data["EDIPASSWORD"] . "',
				EDIURL							=	'" . $data["EDIURL"] . "',
				EDIID							=	'" . $data["EDIID"] . "',
				EDIPORT							=	'" . $data["EDIPORT"] . "',
				EDICOUNTER						=	'" . $data["EDICOUNTER"] . "',
				SHOWPRICESONPACKINGSLIP			=	'" . $data["SHOWPRICESONPACKINGSLIP"] . "',
				BENNETTID						=	'" . $data["BENNETTID"] . "',
				PDBILLTO						=	'" . $data["PDBILLTO"] . "',
				NONNETUSETABLE					=	'" . $data["NONNETUSETABLE"] . "',
				FAX								=	'" . $data["FAX"] . "',
				ALLOWTHIRDPARTYSHIPPING			=	'" . $data["ALLOWTHIRDPARTYSHIPPING"] . "',
				FREESHIP						=	'" . $data["FREESHIP"] . "',
				FREESHIPAMOUNT					=	'" . $data["FREESHIPAMOUNT"] . "',
				LIMIT_AMOUNT_PD					=	'" . $data["LIMIT_AMOUNT_PD"] . "',
				UPS_ACCOUNT						=	'" . $data["UPS_ACCOUNT"] . "',
				ALLOWESCROW						=	'" . $data["ALLOWESCROW"] . "',
				ESCROWBILLTO					=	'" . $data["ESCROWBILLTO"] . "',
				SENDINVOICEEMAIL				=	'" . $data["SENDINVOICEEMAIL"] . "',
				EMAILINVOICETO					=	'" . $data["EMAILINVOICETO"] . "'
				WHERE 
				ID = '".$accId."'";
				*/
			$updQuery .= "WHERE 
				ID = '".$data['updates'][0]['value']."'";
				
			if( mysqli_query($this->dbConnect, $updQuery)) {
				$message = "Account updated successfully.";
				$success = 1;			
			} else {
				$message = "Account update failed.";
				$success = 0;			
			}
		} else {
			$message = "Invalid request.";
			$success = 0;
		}
		$updResponse = array(
			'success' => $success,
			'success_message' => $message
		);
		header('Content-Type: application/json');
		echo json_encode($updResponse);
	}	
	
}
?>