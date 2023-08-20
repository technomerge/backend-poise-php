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
	
	public function getPoListSearchAdv($data) {
		$fromDate = '';
		$toDate = '';

		if(isset($data["fromDate"]["year"])){
			$fromYear = $data["fromDate"]["year"];
			$fromMonth = $data["fromDate"]["month"];
			$fromDay = $data["fromDate"]["day"];
			if($fromMonth < 10){
				$fromMonth = '0' . $fromMonth;
			}
			if($fromDay < 10){
				$fromDay = '0' . $fromDay;
			}
			$fromDate = $fromYear . '-' . $fromMonth . '-' . $fromDay . ' 00:00:00';
		}

		if(isset($data["toDate"]["year"])){
			$toYear = $data["toDate"]["year"];
			$toMonth = $data["toDate"]["month"];
			$toDay = $data["toDate"]["day"];
			if($toMonth < 10){
				$toMonth = '0' . $toMonth;
			}
			if($toDay < 10){
				$toDay = '0' . $toDay;
			}
			$toDate = $toYear . '-' . $toMonth . '-' . $toDay . ' 23:59:59';
		}

		$getQuery="
		SELECT
		DISTINCT 
		TRIM(LEADING '0' FROM PURCHASEORDERS.ID) AS poid, 
		SUBSTR(PURCHASEORDERS.CREATIONDATE, 1, 10) AS creationdate,  
		SUBSTR(PURCHASEORDERS.DUEDATE, 1, 10) AS duedate, 
		SUPPLIERS.ID AS supplierid, 
		SUPPLIERS.NAME AS supplier, 
		SUPPLIERS.ADDRESS1 AS address1, 
		SUPPLIERS.ADDRESS2 AS address2, 	
		SUPPLIERS.CITY AS city, 
		SUPPLIERS.STATE AS state, 
		SUPPLIERS.ZIP AS zipcod, 
		PURCHASEORDERS.STATUS AS status 
		FROM 
    	PURCHASEORDERS 
    	JOIN SUPPLIERS ON PURCHASEORDERS.SUPPLIERID = SUPPLIERS.ID 
    	JOIN POITEMS ON PURCHASEORDERS.ID = POITEMS.PURCHASEORDERSID
    	JOIN INVENTORY ON POITEMS.INVENTORYID = INVENTORY.ID
		WHERE
		1= case 
		when '" . $data["poNum"] . "' !='' and PURCHASEORDERS.ID = '" . $data["poNum"] . "' then 1 
		when '" . $data["poNum"] . "' ='' then 1 
		else 0 
		end
		
		and

		1= case 
		when '" . $data["status"] . "' !='' and PURCHASEORDERS.STATUS = '" . $data["status"] . "' then 1 
		when '" . $data["status"] . "' ='' then 1 
		else 0 
		end
		
		and

		1= case 
			when '" . $data["supplierListForm"] . "' !='' and SUPPLIERS.ID = '" . $data["supplierListForm"] . "' then 1 
			when '" . $data["supplierListForm"] . "' ='' then 1 
			else 0 
		end
		
		and

		1= case 
		when '" . $data["bennettSku"] . "' !='' and INVENTORY.SKU like '%" . $data["bennettSku"] . "%' then 1 
		when '" . $data["bennettSku"] . "' ='' then 1 
		else 0 
		end

		and

		1= case 
		when '" . $data["supplierSku"] . "' !='' and INVENTORY.VENDORSKU like '%" . $data["supplierSku"] . "%' then 1 
		when '" . $data["supplierSku"] . "' ='' then 1 
		else 0 
		end

		and

		1= case 
		when '" . $fromDate . "' !='' and PURCHASEORDERS.CREATIONDATE >= '" . $fromDate . "' then 1 
		when '" . $fromDate . "' ='' then 1 
		else 0 
		end

		and

		1= case 
		when '" . $toDate . "' !='' and PURCHASEORDERS.CREATIONDATE <= '" . $toDate . "' then 1 
		when '" . $toDate . "' ='' then 1 
		else 0 
		end		

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

	public function closePo($poId, $data) {	
		//var_dump($data);
		$notes = '';
		if($data['updates'][0]['param'] == "NOTES"){
			$notes = $data['updates'][0]['value'];
		}
		
		
		$query="
		UPDATE 
		PURCHASEORDERS
		SET 
		STATUS = 'CLOSED',
		NOTES = \"$notes\" 
		WHERE ID = '$poId'
		";
		
		if( mysqli_query($this->dbConnect, $query)) {
			$message = "PO closed";
			$this->closePoItems($poId);
			$success = 1;			
		} else {
			$message = "PO close failed.";
			$success = 0;			
		}
		
		header('Content-Type: application/json');
		echo json_encode($message);		
		
	}	

	public function closePoItems($poId) {	
		$query="
		UPDATE 
		POITEMS
		SET STATUS = 'CLOSED' 
		WHERE PURCHASEORDERSID = '$poId'
		";
		
		if( mysqli_query($this->dbConnect, $query)) {
			$message = "POITEMS closed";
			$success = 1;			
		} else {
			$message = "POITEMS close failed.";
			$success = 0;			
		}
		
		//header('Content-Type: application/json');
		//echo json_encode($message);	
		return $success;	
		
	}	
}
?>