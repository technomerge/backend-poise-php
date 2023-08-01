<?php
class Suppliers{
	public $dbConnect;
	public $table	= "SUPPLIERS";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
		
	
	public function getSuppliersListFiltered($data) {	
		$params_status			= $data["status"];
		$params_supplierName	= $data["supplierName"];
		$params_address 		= $data["address"];
		$params_phone			= $data["phone"];
		$params_city			= $data["city"];
		$params_state			= $data["state"];
		$params_email			= $data["email"];
		$params_country			= $data["country"];

		$getQuery="
		SELECT 
		* 
		FROM
		SUPPLIERS 
		WHERE
		1= case 
		   when '$params_status' !='' and STATUS = '$params_status' then 1 
		   when '$params_status' ='' then 1 
		   else 0 
		end
		
		and
		
		1= case 
		   when '$params_supplierName' !='' and NAME like '$params_supplierName%' then 1 
		   when '$params_supplierName' ='' then 1 
		   else 0 
		end
		
		and
		
		1= case 
		   when '$params_address' !='' and ADDRESS1 like '$params_address%' then 1 
		   when '$params_address' ='' then 1 
		   else 0 
		end		
		
		and
		
		1= case 
		   when '$params_phone' !='' and REPLACE(PHONE,'-','') like '%$params_phone%' then 1 
		   when '$params_phone' ='' then 1 
		   else 0 
		end	
		
		and
		
		1= case 
		   when '$params_city' !='' and CITY like '$params_city%' then 1 
		   when '$params_city' ='' then 1 
		   else 0 
		end			
		
		and
		
		1= case 
		   when '$params_state' !='' and STATE like '$params_state%' then 1 
		   when '$params_state' ='' then 1 
		   else 0 
		end			
		
		and
		
		1= case 
		   when '$params_email' !='' and EMAIL like '$params_email%' then 1 
		   when '$params_email' ='' then 1 
		   else 0 
		end	
		
		and
		
		1= case 
		   when '$params_country' !='' and COUNTRY like '$params_country%' then 1 
		   when '$params_country' ='' then 1 
		   else 0 
		end	
						 
		ORDER BY NAME;
		";
		
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