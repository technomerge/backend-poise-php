<?php
class Inventory{
	public $dbConnect;
	public $table	= "INVENTORY";

	public function __construct(){
		include_once('db_connect.php');
		$this->dbConnect = db_connection();
	}
	
	public function cors(){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
	
	
	public function getInventoryInfo($invId) {		
		$sqlQuery = '';
		if($invId) {
			$sqlQuery = "WHERE ID = '".$invId."'" . " OR SKU = '".$invId."'" ;
		}
	
		$invQuery = "
			SELECT * 
			FROM ".$this->table." $sqlQuery
			";	

		$resultData = mysqli_query($this->dbConnect, $invQuery);

		$invData = array();
		while( $invRecord = mysqli_fetch_assoc($resultData) ) {
			$invData[] = $invRecord;
		}

		header('Content-Type: application/json');
		//echo '{"data":' . json_encode($empData). ',"totalRecords":' . sizeof($empData) . '}';
		echo rtrim((ltrim(json_encode($invData),"[")), "]");
	}
	
	public function getInventoryInfoArray($invId) {		
		$sqlQuery = '';
		if($invId) {
			$sqlQuery = "WHERE ID = '".$invId."'";
		}
	
		$invQuery = "
			SELECT * 
			FROM ".$this->table." $sqlQuery
			";	
	
		$resultData = mysqli_query($this->dbConnect, $invQuery);

		$invData = array();
		while( $invRecord = mysqli_fetch_assoc($resultData) ) {
			$invData[] = $invRecord;
		}

		return $invData[0];
	}	
	
	public function getProduct($rootSku) {	
		$invQuery="
		SELECT
		wi.ACCOUNT_ID,wi.NAME,wi.SKU as ACCOUNTSKU, wi.DESCRIPTION, wi.DETAILS, 
    	i.PODESCRIPTION,i.VENDORSKU,i.SUPPLIERID , i.BINNUMBER, i.GLSALEPREFIX as GLCODE ,i.REBATEAMT, 
    	i.CUSTOMIZESKU,i.NONSTOCK,i.DESCRIPTION as ITEMDESCRIPTION ,i.COLOR, i.SKU,i.COST,i.WEIGHT,
    	i.PRICE1,i.PRICE2,
    	i.PRICE3,i.PRICE4,i.PRICE5,
    	i.PRICE6,i.PRICE7,i.PRICE8,
    	i.PRICE9,i.PRICE10,i.PRICE11,
    	i.PRICE12,i.PRICE13,i.PRICE14,
    	i.PRICE15,i.PRICE16,i.PRICE17,
    	i.PRICE18,i.PRICE19,i.PRICE20,
    	a.PRICEBUCKET,
		a.PREFIX
		FROM
		INVENTORY i
    	INNER JOIN WEBITEMS wi on wi.SKU = i.ROOTSKU
    	INNER JOIN ACCOUNTS a on a.id = wi.ACCOUNT_ID
     	WHERE
		i.ROOTSKU = '$rootSku' ORDER BY i.BINNUMBER
		";
		
		$optQuery="
		SELECT  * FROM SKUOPTIONS WHERE ROOTSKU = '$rootSku'";
		
		
		$resultInv = mysqli_query($this->dbConnect, $invQuery);
		$resultOpt = mysqli_query($this->dbConnect, $optQuery);

		$inv = array();
		while( $invRecord = mysqli_fetch_assoc($resultInv) ) {
			$inv[] = $invRecord;
		}
		
		$opt = array();
		while( $optRecord = mysqli_fetch_assoc($resultOpt) ) {
			$opt[] = $optRecord;
		}		

		header('Content-Type: application/json');
		echo '{"inventoryItems":' . json_encode($inv). ',"skuOption":' . json_encode($opt[0]) . '}';		
	
	}	
	
	public function getInventoryListFiltered($data) {	
		$params_pageSize	= $data["pageSize"];
		$params_pageNumber	= $data["pageNumber"];
		$params_sku 		= $data["sku"];
		$params_accountId	= $data["accountId"];
		$low				= 0;


		if ($params_pageNumber > 0){
			$low = $params_pageSize * ($params_pageNumber - 1);
		}
		
		$high = $params_pageSize * $params_pageNumber;

 
		$getQuery="
		SELECT 
		i.ID,i.ROOTSKU,i.STATUS, i.BASESKU, i.QTYSTOCK,i.QTYBO,i.QTYALLOCATED,i.DESCRIPTION,i.SKU,i.QTYREORDER 
		FROM
		INVENTORY i 
		WHERE
		1= case 
		   when '$params_sku' !='' and i.SKU like '$params_sku%' then 1 
		   when '$params_sku' ='' then 1 
		   else 0 
		end  
		ORDER BY 1 desc 
		LIMIT $low, $high;
		";

	
		$getTotalQuery="
		SELECT 
		COUNT(i.ID) AS totalRecords 
		FROM
		INVENTORY i 
		WHERE
		1= case 
		   when '$params_sku' !='' and i.SKU like '$params_sku%' then 1 
		   when '$params_sku' ='' then 1 
		   else 0 
		end;
		";
			
		$resultData = mysqli_query($this->dbConnect, $getQuery);
		$resultTotal = mysqli_query($this->dbConnect, $getTotalQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}
		
		$total = array();
		while( $totalRecord = mysqli_fetch_assoc($resultTotal) ) {
			$total[] = $totalRecord;
		}		

		header('Content-Type: application/json');
		echo '{"data":' . json_encode($data). ',"totalRecords":' . $total[0]['totalRecords'] . '}';
		//echo json_encode($data);		
	
		
	}		

	public function getAccountId($invId) {	
		$getQuery="
		SELECT 
		i.ACCOUNTID
		FROM
		INVENTORY i 
		WHERE
		ID=$invId;
		";
			
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]['ACCOUNTID']);
	}
	
	public function getBennettId($accId) {	
		$getQuery="
		SELECT 
		BENNETTID
		FROM
		ACCOUNTS 
		WHERE
		ID=$accId;
		";
			
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]['BENNETTID']);
	}	
	
	public function getPriceBucket($accId) {	
		$getQuery="
		SELECT 
		PRICEBUCKET
		FROM
		ACCOUNTS 
		WHERE
		ID=$accId;
		";
		
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]['PRICEBUCKET']);
	}	
	
	public function getInventorySku($invId) {	
		$getQuery="
		SELECT 
		i.SKU
		FROM
		INVENTORY i 
		WHERE
		ID=$invId;
		";
			
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]['SKU']);
	}	
	
	public function getInventoryId($sku) {	
		$getQuery="
		SELECT 
		i.ID
		FROM
		INVENTORY i 
		WHERE
		SKU='$sku';
		";
//echo $getQuery;	
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]['ID']);
	}
	
	public function getSkuOptions($rootSku) {	
		$getQuery="
		SELECT 
		*
		FROM
		SKUOPTIONS 
		WHERE
		ROOTSKU='$rootSku';
		";
			
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]);
	}	
	
	public function updateStockAllocated($invId, $data) {	
		$params_stock		= $data["QTYSTOCK"];
		$params_allocated	= $data["QTYALLOCATED"];

 
		$updateQuery="
		UPDATE INVENTORY
        SET QTYALLOCATED = $params_allocated,
			QTYSTOCK = $params_stock
			WHERE ID = $invId";		
		
		$resultData = mysqli_query($this->dbConnect, $updateQuery);
		
		//Need to reconcile inventory here
		$this->reconcile_inventory($invId);
		
		$sku = $this->getInventorySku($invId);
		
		$data["pageSize"] = 1;
		$data["pageNumber"] = 1;
		$data["sku"] = $sku;
		$data["accountId"] = '';
		
		$this->getInventoryListFiltered($data);

	}	
	
	public function updateInventory($invId, $data){ 		

		if($data["ID"]) {
			$accId		= $this->getAccountId($data["ID"]);
			$bennettId  = $this->getBennettId($accId);
			$bucket		= $this->getPriceBucket($accId);
			if(!$bucket){
				$bucket = 7;
			}
			
			$glsaleprefix		= $data["GLCODE"];
			$glsalesuffix		= $bennettId;
			$glcodesale			= $glsaleprefix . '-' . $glsalesuffix;

			$glpurchaseprefix	= $glsaleprefix + 1000;
			$glpurchasesuffix	= $bennettId;
			$glcodepurchase		= $glpurchaseprefix . '-' . $glpurchasesuffix;
			
			$updQuery="
				UPDATE ".$this->table." 
				SET 
				QTYSTOCK			=	'" . $data["QTYSTOCK"] . "',
				QTYREORDER			=	'" . $data["QTYREORDER"] . "',
				DESCRIPTION			=	'" . $data["DESCRIPTION"] . "',
				VENDORSKU			=	'" . $data["VENDORSKU"] . "',
				SUPPLIERID			=	'" . $data["SUPPLIERID"] . "',
				COST				=	'" . $data["COST"] . "',
				PRICE$bucket		=	'" . $data["PRICE"] . "',
				PRICE7				=	'" . $data["PRICE"] . "',
				WEIGHT				=	'" . $data["WEIGHT"] . "',
				BINNUMBER			=	'" . $data["BINNUMBER"] . "',
				GLSALEPREFIX		=	'" . $glsaleprefix . "',
				GLSALESUFFIX		=	'" . $glsalesuffix . "',
				GLCODESALE			=	'" . $glcodesale . "',
				GLPURCHASEPREFIX	=	'" . $glpurchaseprefix . "',
				GLPURCHASESUFFIX	=	'" . $glpurchasesuffix . "',
				GLCODEPURCHASE		=	'" . $glcodepurchase . "',
				REBATEAMT			=	'" . $data["REBATEAMT"] . "',
				NONSTOCK			=	'" . $data["NONSTOCK"] . "',
				CUSTOMIZESKU		=	'" . $data["CUSTOMIZESKU"] . "',
				STATUS				=	'" . $data["STATUS"] . "'
				WHERE 
				ID = '".$data["ID"]."'";

			if( mysqli_query($this->dbConnect, $updQuery)) {
				$message = "SKU updated successfully.";
				$success = 1;			
			} else {
				$message = "SKU update failed.";
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
	
	
	public function updateStatus($invId, $data) {	
		$params_status	= $data["STATUS"];
		if($params_status == '1'){
			$obsolete = 'NO';
		}
		else if($params_status == '0'){
			$obsolete = 'YES';
		}
 
		$updateQuery="
		UPDATE INVENTORY
        SET STATUS = $params_status,
			OBSOLETE = '$obsolete'
			WHERE ID = $invId";		
	
		$resultData = mysqli_query($this->dbConnect, $updateQuery);
		
	}	
	
	public function deleteInventory($invId) {		
		if($invId) {			
			$delQuery = "
				DELETE FROM ".$this->table." 
				WHERE ID = '".$invId."'";	
		
			if( mysqli_query($this->dbConnect, $delQuery)) {
				$message = "Inventory deleted";
				$success = 1;			
			} else {
				$message = "inventory delete failed.";
				$success = 0;			
			}		
		} else {
			$message = "Invalid request.";
			$success = 0;
		}
		$response = array(
			'success' => $success,
			'success_message' => $message
		);
		header('Content-Type: application/json');
		//echo json_encode($empResponse);	
		echo json_encode($message);
	}	
	
	public function verifyDuplicate($rootSku) {	
		$getQuery="
		select COUNT(*) as Count from INVENTORY where ROOTSKU='$rootSku'
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

	public function getColor($colorId) {	
		$getQuery="
		SELECT 
		COLOR_NAME
		FROM
		RGBCOLORS 
		WHERE
		COLOR_ID='$colorId';
		";
			
		$resultData = mysqli_query($this->dbConnect, $getQuery);

		$data = array();
		while( $dataRecord = mysqli_fetch_assoc($resultData) ) {
			$data[] = $dataRecord;
		}

		return ($data[0]['COLOR_NAME']);
	}

	public function insertInventory($data){ 		
/*
"ID":null,
ACCOUNTSKU":"100",
"NAME":"Test 100 Name",
"DESCRIPTION":"Test 100 Description",
"DETAILS":"Test 100 Details",

"ITEMS":[{"COLOR":["02"],"SIZE":"SML","PRICE":"20","WEIGHT":"16","COST":"10","BINNUMBER":"A-B-1"}],
"OPTIONS":[{"CODE":"M","DESCRIPTION":"Manager"}]}: 
*/

		$accId	= $data["ACCOUNTID"];
		$bucket	= $this->getPriceBucket($accId);
		if(!$bucket){
			$bucket = 7;
		}

		$inv = array();
		$z = 0;
		$colors = '';
		$sizes = '';

		if(sizeof($data["OPTIONS"]) > 0){
			for($i=0; $i<sizeof($data["OPTIONS"]); $i++){
				for($j=0; $j<sizeof($data["ITEMS"]); $j++){
					if(sizeof($data["ITEMS"][$j]["COLOR"])>0){
						for($k=0; $k<sizeof($data["ITEMS"][$j]["COLOR"]); $k++){
							$inv[$z]["SKU"]				= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . $data["OPTIONS"][$i]["CODE"] . $data["ITEMS"][$j]["COLOR"][$k] . '-' . $data["ITEMS"][$j]["SIZE"];
							$inv[$z]["COST"]			= $data["ITEMS"][$j]["COST"];
							$inv[$z]["PRICE$bucket"]	= $data["ITEMS"][$j]["PRICE"];
							$inv[$z]["WEIGHT"]			= $data["ITEMS"][$j]["WEIGHT"];
							$inv[$z]["BINNUMBER"]		= $data["ITEMS"][$j]["BINNUMBER"];
							$inv[$z]["COLOR"]			= $data["ITEMS"][$j]["COLOR"][$k];
							$color						= $this->getColor($data["ITEMS"][$j]["COLOR"][$k]);
							$inv[$z]["DESCRIPTION"]		= $data["DESCRIPTION"] . ' ' . $data["OPTIONS"][$i]["DESCRIPTION"] . ' ' . $color;
							$inv[$z]["VENDORSKU"]		= $data["VENDORSKU"] . ' ' . $color . ' ' . $data["ITEMS"][$j]["SIZE"];
							$inv[$z]["ROOTSKU"]			= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
							$colors					   .= $color . ' (' . $data["ITEMS"][$j]["COLOR"][$k] . ');';
							$z++;
						}
					}
					else{
						$inv[$z]["SKU"]					= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . $data["OPTIONS"][$i]["CODE"] . '-' . $data["ITEMS"][$j]["SIZE"];
						$inv[$z]["COST"]				= $data["ITEMS"][$j]["COST"];
						$inv[$z]["PRICE$bucket"]		= $data["ITEMS"][$j]["PRICE"];
						$inv[$z]["WEIGHT"]				= $data["ITEMS"][$j]["WEIGHT"];
						$inv[$z]["BINNUMBER"]			= $data["ITEMS"][$j]["BINNUMBER"];
						$inv[$z]["COLOR"]				= '';
						$inv[$z]["DESCRIPTION"]			= $data["DESCRIPTION"] . ' ' . $data["OPTIONS"][$i]["DESCRIPTION"];
						$inv[$z]["VENDORSKU"]			= $data["VENDORSKU"] . ' ' . $data["ITEMS"][$j]["SIZE"];
						$inv[$z]["ROOTSKU"]				= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
						$z++;					
					
					}
					$pos = strpos($sizes, $data["ITEMS"][$j]["SIZE"]);
					if($pos === false){
						$sizes .= $data["ITEMS"][$j]["SIZE"] . "-";
					}
				}
				$opt_code = $data["OPTIONS"][$i]["CODE"];
				$options[$opt_code] = $data["OPTIONS"][$i]["DESCRIPTION"];		
			
			}
		}
		else{
			for($j=0; $j<sizeof($data["ITEMS"]); $j++){
				if(sizeof($data["ITEMS"][$j]["COLOR"])>0){
					for($k=0; $k<sizeof($data["ITEMS"][$j]["COLOR"]); $k++){
						$inv[$z]["SKU"]					= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . $data["ITEMS"][$j]["COLOR"][$k] . '-' . $data["ITEMS"][$j]["SIZE"];
						$inv[$z]["COST"]				= $data["ITEMS"][$j]["COST"];
						$inv[$z]["PRICE$bucket"]		= $data["ITEMS"][$j]["PRICE"];
						$inv[$z]["WEIGHT"]				= $data["ITEMS"][$j]["WEIGHT"];
						$inv[$z]["BINNUMBER"]			= $data["ITEMS"][$j]["BINNUMBER"];
						$inv[$z]["COLOR"]				= $data["ITEMS"][$j]["COLOR"][$k];
						$color							= $this->getColor($data["ITEMS"][$j]["COLOR"][$k]);
						$inv[$z]["DESCRIPTION"]			= $data["DESCRIPTION"] . ' ' . $color;
						$inv[$z]["VENDORSKU"]			= $data["VENDORSKU"] . ' ' . $color . ' ' . $data["ITEMS"][$j]["SIZE"];
						$inv[$z]["ROOTSKU"]				= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
						$colors					   	   .= $color . ' (' . $data["ITEMS"][$j]["COLOR"][$k] . ');';
						
						$pos = strpos($sizes, $data["ITEMS"][$j]["SIZE"]);
						if(!$pos){
							$sizes				   	   .= $data["ITEMS"][$j]["SIZE"] . "-";
						}
						$z++;
					}
				}
				else{
					$inv[$z]["SKU"]						= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . '-' . $data["ITEMS"][$j]["SIZE"];
					$inv[$z]["COST"]					= $data["ITEMS"][$j]["COST"];
					$inv[$z]["PRICE$bucket"]			= $data["ITEMS"][$j]["PRICE"];
					$inv[$z]["WEIGHT"]					= $data["ITEMS"][$j]["WEIGHT"];
					$inv[$z]["BINNUMBER"]				= $data["ITEMS"][$j]["BINNUMBER"];
					$inv[$z]["COLOR"]					= '';
					$inv[$z]["DESCRIPTION"]				= $data["DESCRIPTION"];
					$inv[$z]["VENDORSKU"]				= $data["VENDORSKU"] . ' ' . $data["ITEMS"][$j]["SIZE"];
					$inv[$z]["ROOTSKU"]					= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
					$z++;					
				}
				$pos = strpos($sizes, $data["ITEMS"][$j]["SIZE"]);
				if($pos === false){
					$sizes .= $data["ITEMS"][$j]["SIZE"] . "-";
				}
			}		
		}
		
		
		
		$bennettId			= $this->getBennettId($accId);
		
		$glsaleprefix		= $data["GLCODE"];
		$glsalesuffix		= $bennettId;
		$glcodesale			= $glsaleprefix . '-' . $glsalesuffix;

		$glpurchaseprefix	= $glsaleprefix + 1000;
		$glpurchasesuffix	= $bennettId;
		$glcodepurchase		= $glpurchaseprefix . '-' . $glpurchasesuffix;			
		
		for($z=0; $z<sizeof($inv); $z++){
			$inv[$z]["CREATIONDATE"]		=	"NOW()";
			$inv[$z]["QTYSTOCK"]			=	0;
			$inv[$z]["QTYREORDER"]			=	0;
			$inv[$z]["SUPPLIERID"]			=	$data["SUPPLIERID"];
			$inv[$z]["PODESCRIPTION"]		=	$data["PODESCRIPTION"];
			$inv[$z]["GLSALEPREFIX"]		=	$glsaleprefix;
			$inv[$z]["GLSALESUFFIX"]		=	$glsalesuffix;
			$inv[$z]["GLCODESALE"]			=	$glcodesale;
			$inv[$z]["GLPURCHASEPREFIX"]	=	$glpurchaseprefix;
			$inv[$z]["GLPURCHASESUFFIX"]	=	$glpurchasesuffix;
			$inv[$z]["GLCODEPURCHASE"]		=	$glcodepurchase;
			$inv[$z]["REBATEAMT"]			=	$data["REBATEAMT"];
			//$inv[$z]["NONSTOCK"]			=	$data["NONSTOCK"];
			//$inv[$z]["CUSTOMIZESKU"]		=	$data["CUSTOMIZESKU"];
			$inv[$z]["ACCOUNTID"]			=	$data["ACCOUNTID"];
		}
		//var_dump($inv);
			
		$values = '';
		for($i=0; $i<sizeof($inv); $i++){
			$values .= "('" . implode("', '", $inv[$i]) . "'),";
		}	
		
		$values = rtrim($values, ',');
		$values = str_replace("'NOW()'", "NOW()", $values);
		
		$query="
			INSERT INTO ".$this->table." 
				(
				SKU, 
				COST,
				PRICE$bucket, 
				WEIGHT,
				BINNUMBER,
				COLOR,
				DESCRIPTION,
				VENDORSKU,
				ROOTSKU,
				CREATEDON,
				QTYSTOCK,
				QTYREORDER,
				SUPPLIERID,
				PODESCRIPTION,
				GLSALEPREFIX,
				GLSALESUFFIX,
				GLCODESALE,
				GLPURCHASEPREFIX,
				GLPURCHASESUFFIX,
				GLCODEPURCHASE,
				REBATEAMT,
				ACCOUNTID
				)
				VALUES $values				
				";
		//var_dump($query);
		
		$updateQuery="
				UPDATE ".$this->table." 
				SET PRICE7 = PRICE$bucket 
				WHERE ROOTSKU = '" . $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . "'
				";
		//var_dump($updateQuery);

		$colors = rtrim($colors, ';');
		$sizes  = rtrim($sizes, '-');
						
		$webItemsQuery = "
			INSERT INTO WEBITEMS
			(
			ACCOUNT_ID,
			SKU,
			COLOR,
			SIZE,
			NAME,
			DESCRIPTION,
			DETAILS
			)
			VALUES
			(
				'" . $data["ACCOUNTID"] . "',
				'" . $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . "',
				'$colors',
				'$sizes',
				'" . $data["NAME"] . "',
				'" . $data["DESCRIPTION"] . "',
				'" . $data["DETAILS"] . "'
			)";
		//var_dump($webItemsQuery);								


		$skuOptions				= new stdClass();
		$skuOptions->acct 		= strlen($data["ACCOUNTPREFIX"]);
		$skuOptions->sku		= strlen($data["ACCOUNTSKU"]);
		$skuOptions->options	= $options;
		$skuOptions->color		= 2;
		$skuOptions->dash		= 1;
		$skuOptions->size		= 3;
		
		$skuOptionsQuery = "
			INSERT INTO SKUOPTIONS
			(
			ROOTSKU,
			OPTIONS
			)
			VALUES
			(
				'" . $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . "',
				'" . json_encode($skuOptions) . "'
			)";
	
		//var_dump($skuOptionsQuery);


		if(mysqli_query($this->dbConnect, $query)) {
			if(mysqli_query($this->dbConnect, $skuOptionsQuery)) {
				if(mysqli_query($this->dbConnect, $webItemsQuery)) {
					if(mysqli_query($this->dbConnect, $updateQuery)) {
						$message = "SKUs created Successfully.";
						$success = 1;	
					}
					else{
						$message = "Inventory update price7 failed.";
						$success = 0;					
					}
				}
				else{
					$message = "Webitems creation failed.";
					$success = 0;				
				}
			}
			else{
				$message = "SKU Options creation failed.";
				$success = 0;
			}			
		}
		else {
			$message = "SKUs creation failed.";
			$success = 0;
		}
		
		$response = array(
			'success' => $success,
			'success_message' => $message
		);
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function updateProduct($data){ 		
/*
"ID":null,
ACCOUNTSKU":"100",
"NAME":"Test 100 Name",
"DESCRIPTION":"Test 100 Description",
"DETAILS":"Test 100 Details",

"ITEMS":[{"COLOR":["02"],"SIZE":"SML","PRICE":"20","WEIGHT":"16","COST":"10","BINNUMBER":"A-B-1"}],
"OPTIONS":[{"CODE":"M","DESCRIPTION":"Manager"}]}: 
*/

		$accId	= $data["ACCOUNTID"];
		$bucket	= $this->getPriceBucket($accId);
		if(!$bucket){
			$bucket = 7;
		}

		$inv = array();
		$z = 0;
		$colors = '';
		$sizes = '';

		if(sizeof($data["OPTIONS"]) > 0){
			for($i=0; $i<sizeof($data["OPTIONS"]); $i++){
				for($j=0; $j<sizeof($data["ITEMS"]); $j++){
					if(sizeof($data["ITEMS"][$j]["COLOR"])>0){
						for($k=0; $k<sizeof($data["ITEMS"][$j]["COLOR"]); $k++){
							//$rootSku					= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
							$rootSku					= $data["ID"];
							$inventorySku				= $rootSku . $data["OPTIONS"][$i]["CODE"] . $data["ITEMS"][$j]["COLOR"][$k] . '-' . $data["ITEMS"][$j]["SIZE"];
							$inventoryId				= $this->getInventoryId($inventorySku);
//echo "WAP: " . 	$inventorySku . " | " . $inventoryId;
//echo "Test";			
							if(!$inventoryId){
								$inventoryId 			= 0;
								$creationDate			= "NOW()";
								$qtyStock				= 0;
								$qtyReorder				= 0;
								$rootDescription		= $data["DESCRIPTION"];				
							}
							else{
								$invData 				= $this->getInventoryInfo($inventoryId);
				
								$creationDate			= $invData["CREATIONDATE"];
								$qtyStock				= $invData["QTYSTOCK"];
								$qtyReorder				= $invData["QTYREORDER"];
								$skuOptions				= $this->getSkuOptions($rootSku);
								$rootDescription		= $skuOptions["ROOTDESCRIPTION"];		
							}
							$inv[$z]["ID"]				= $inventoryId;
							$inv[$z]["SKU"]				= $inventorySku;
							$inv[$z]["COST"]			= $data["ITEMS"][$j]["COST"];
							$inv[$z]["PRICE$bucket"]	= $data["ITEMS"][$j]["PRICE"];
							$inv[$z]["WEIGHT"]			= $data["ITEMS"][$j]["WEIGHT"];
							$inv[$z]["BINNUMBER"]		= $data["ITEMS"][$j]["BINNUMBER"];
							$inv[$z]["COLOR"]			= $data["ITEMS"][$j]["COLOR"][$k];
							$color						= $this->getColor($data["ITEMS"][$j]["COLOR"][$k]);
							$inv[$z]["DESCRIPTION"]		= $rootDescription . ' ' . $data["OPTIONS"][$i]["DESCRIPTION"] . ' ' . $color;
							$inv[$z]["VENDORSKU"]		= $data["VENDORSKU"] . ' ' . $color . ' ' . $data["ITEMS"][$j]["SIZE"];
							$inv[$z]["ROOTSKU"]			= $rootSku;
							$colors					   .= $color . ' (' . $data["ITEMS"][$j]["COLOR"][$k] . ');';
							$z++;
						}
					}
					else{
						//$rootSku						= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
						$rootSku						= $data["ID"];
						$inventorySku					= $rootSku . $data["OPTIONS"][$i]["CODE"] . '-' . $data["ITEMS"][$j]["SIZE"];
						$inventoryId					= $this->getInventoryId($inventorySku);
						if(!$inventoryId){
							$inventoryId 				= 0;
							$creationDate				= "NOW()";
							$qtyStock					= 0;
							$qtyReorder					= 0;
							$rootDescription			= $data["DESCRIPTION"];				
						}
						else{
							$invData 					= $this->getInventoryInfoArray($inventoryId);
							$creationDate				= $invData["CREATIONDATE"];
							$qtyStock					= $invData["QTYSTOCK"];
							$qtyReorder					= $invData["QTYREORDER"];
							$skuOptions					= $this->getSkuOptions($rootSku);
							$rootDescription			= $skuOptions["ROOTDESCRIPTION"];		
						}
						$inv[$z]["ID"]					= $inventoryId;					
						$inv[$z]["SKU"]					= $inventorySku;
						$inv[$z]["COST"]				= $data["ITEMS"][$j]["COST"];
						$inv[$z]["PRICE$bucket"]		= $data["ITEMS"][$j]["PRICE"];
						$inv[$z]["WEIGHT"]				= $data["ITEMS"][$j]["WEIGHT"];
						$inv[$z]["BINNUMBER"]			= $data["ITEMS"][$j]["BINNUMBER"];
						$inv[$z]["COLOR"]				= '';
						$inv[$z]["DESCRIPTION"]			= $rootDescription . ' ' . $data["OPTIONS"][$i]["DESCRIPTION"];
						$inv[$z]["VENDORSKU"]			= $data["VENDORSKU"] . ' ' . $data["ITEMS"][$j]["SIZE"];
						$inv[$z]["ROOTSKU"]				= $rootSku;
						$z++;					
					
					}
					$pos = strpos($sizes, $data["ITEMS"][$j]["SIZE"]);
					if($pos === false){
						$sizes .= $data["ITEMS"][$j]["SIZE"] . "-";
					}
				}
				$opt_code = $data["OPTIONS"][$i]["CODE"];
				$options[$opt_code] = $data["OPTIONS"][$i]["DESCRIPTION"];		
			
			}
		}
		else{
			for($j=0; $j<sizeof($data["ITEMS"]); $j++){
				if(sizeof($data["ITEMS"][$j]["COLOR"])>0){
					for($k=0; $k<sizeof($data["ITEMS"][$j]["COLOR"]); $k++){
						//$rootSku						= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
						$rootSku						= $data["ID"];
						$inventorySku					= $rootSku . $data["ITEMS"][$j]["COLOR"][$k] . '-' . $data["ITEMS"][$j]["SIZE"];
						$inventoryId					= $this->getInventoryId($inventorySku);
						if(!$inventoryId){
							$inventoryId 				= 0;
							$creationDate				= "NOW()";
							$qtyStock					= 0;
							$qtyReorder					= 0;
							$rootDescription			= $data["DESCRIPTION"];				
						}
						else{
							$invData 					= $this->getInventoryInfoArray($inventoryId);
							$creationDate				= $invData["CREATIONDATE"];
							$qtyStock					= $invData["QTYSTOCK"];
							$qtyReorder					= $invData["QTYREORDER"];
							$skuOptions					= $this->getSkuOptions($rootSku);
							$rootDescription			= $skuOptions["ROOTDESCRIPTION"];		
						}

						$inv[$z]["ID"]					= $inventoryId;					
						$inv[$z]["SKU"]					= $inventorySku;
						$inv[$z]["COST"]				= $data["ITEMS"][$j]["COST"];
						$inv[$z]["PRICE$bucket"]		= $data["ITEMS"][$j]["PRICE"];
						$inv[$z]["WEIGHT"]				= $data["ITEMS"][$j]["WEIGHT"];
						$inv[$z]["BINNUMBER"]			= $data["ITEMS"][$j]["BINNUMBER"];
						$inv[$z]["COLOR"]				= $data["ITEMS"][$j]["COLOR"][$k];
						$color							= $this->getColor($data["ITEMS"][$j]["COLOR"][$k]);
						$inv[$z]["DESCRIPTION"]			= $rootDescription . ' ' . $color;
						$inv[$z]["VENDORSKU"]			= $data["VENDORSKU"] . ' ' . $color . ' ' . $data["ITEMS"][$j]["SIZE"];
						$inv[$z]["ROOTSKU"]				= $rootSku;
						$colors					   	   .= $color . ' (' . $data["ITEMS"][$j]["COLOR"][$k] . ');';
						
						$pos = strpos($sizes, $data["ITEMS"][$j]["SIZE"]);
						if(!$pos){
							$sizes				   	   .= $data["ITEMS"][$j]["SIZE"] . "-";
						}
						$z++;
					}
				}
				else{
					//$rootSku							= $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"];
					$rootSku							= $data["ID"];
					$inventorySku						= $rootSku . '-' . $data["ITEMS"][$j]["SIZE"];
					$inventoryId						= $this->getInventoryId($inventorySku);
					if(!$inventoryId){
						$inventoryId 					= 0;
						$creationDate					= "NOW()";
						$qtyStock						= 0;
						$qtyReorder						= 0;
						$rootDescription				= $data["DESCRIPTION"];				
					}
					else{
						$invData 						= $this->getInventoryInfoArray($inventoryId);
						$creationDate					= $invData["CREATIONDATE"];
						$qtyStock						= $invData["QTYSTOCK"];
						$qtyReorder						= $invData["QTYREORDER"];
						$skuOptions						= $this->getSkuOptions($rootSku);
						$rootDescription				= $skuOptions["ROOTDESCRIPTION"];		
					}
					$inv[$z]["ID"]						= $inventoryId;				
					$inv[$z]["SKU"]						= $inventorySku;
					$inv[$z]["COST"]					= $data["ITEMS"][$j]["COST"];
					$inv[$z]["PRICE$bucket"]			= $data["ITEMS"][$j]["PRICE"];
					$inv[$z]["WEIGHT"]					= $data["ITEMS"][$j]["WEIGHT"];
					$inv[$z]["BINNUMBER"]				= $data["ITEMS"][$j]["BINNUMBER"];
					$inv[$z]["COLOR"]					= '';
					$inv[$z]["DESCRIPTION"]				= rootDescription;
					$inv[$z]["VENDORSKU"]				= $data["VENDORSKU"] . ' ' . $data["ITEMS"][$j]["SIZE"];
					$inv[$z]["ROOTSKU"]					= $rootSku;
					$z++;					
				}
				$pos = strpos($sizes, $data["ITEMS"][$j]["SIZE"]);
				if($pos === false){
					$sizes .= $data["ITEMS"][$j]["SIZE"] . "-";
				}
			}		
		}
	
		
		
		$bennettId			= $this->getBennettId($accId);
		
		$glsaleprefix		= $data["GLCODE"];
		$glsalesuffix		= $bennettId;
		$glcodesale			= $glsaleprefix . '-' . $glsalesuffix;

		$glpurchaseprefix	= $glsaleprefix + 1000;
		$glpurchasesuffix	= $bennettId;
		$glcodepurchase		= $glpurchaseprefix . '-' . $glpurchasesuffix;			
		
		for($z=0; $z<sizeof($inv); $z++){
			$inv[$z]["CREATIONDATE"]		=	$creationDate;
			$inv[$z]["QTYSTOCK"]			=	$qtyStock;
			$inv[$z]["QTYREORDER"]			=	$qtyReorder;
			$inv[$z]["SUPPLIERID"]			=	$data["SUPPLIERID"];
			$inv[$z]["PODESCRIPTION"]		=	$data["PODESCRIPTION"];
			$inv[$z]["GLSALEPREFIX"]		=	$glsaleprefix;
			$inv[$z]["GLSALESUFFIX"]		=	$glsalesuffix;
			$inv[$z]["GLCODESALE"]			=	$glcodesale;
			$inv[$z]["GLPURCHASEPREFIX"]	=	$glpurchaseprefix;
			$inv[$z]["GLPURCHASESUFFIX"]	=	$glpurchasesuffix;
			$inv[$z]["GLCODEPURCHASE"]		=	$glcodepurchase;
			$inv[$z]["REBATEAMT"]			=	$data["REBATEAMT"];
			//$inv[$z]["NONSTOCK"]			=	$data["NONSTOCK"];
			//$inv[$z]["CUSTOMIZESKU"]		=	$data["CUSTOMIZESKU"];
			$inv[$z]["ACCOUNTID"]			=	$data["ACCOUNTID"];
		}
		//var_dump($inv);
			
		$values = '';
		for($i=0; $i<sizeof($inv); $i++){
			$values .= "('" . implode("', '", $inv[$i]) . "'),";
		}	
		
		$values = rtrim($values, ',');
		$values = str_replace("'NOW()'", "NOW()", $values);
		
		$query="
			INSERT INTO ".$this->table." 
				(
				ID,
				SKU, 
				COST,
				PRICE$bucket, 
				WEIGHT,
				BINNUMBER,
				COLOR,
				DESCRIPTION,
				VENDORSKU,
				ROOTSKU,
				CREATEDON,
				QTYSTOCK,
				QTYREORDER,
				SUPPLIERID,
				PODESCRIPTION,
				GLSALEPREFIX,
				GLSALESUFFIX,
				GLCODESALE,
				GLPURCHASEPREFIX,
				GLPURCHASESUFFIX,
				GLCODEPURCHASE,
				REBATEAMT,
				ACCOUNTID
				)
				VALUES $values				
				";
		var_dump($query);
exit();		
		$updateQuery="
				UPDATE ".$this->table." 
				SET PRICE7 = PRICE$bucket 
				WHERE ROOTSKU = '" . $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . "'
				";
		//var_dump($updateQuery);

		$colors = rtrim($colors, ';');
		$sizes  = rtrim($sizes, '-');
						
		$webItemsQuery = "
			INSERT INTO WEBITEMS
			(
			ACCOUNT_ID,
			SKU,
			COLOR,
			SIZE,
			NAME,
			DESCRIPTION,
			DETAILS
			)
			VALUES
			(
				'" . $data["ACCOUNTID"] . "',
				'" . $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . "',
				'$colors',
				'$sizes',
				'" . $data["NAME"] . "',
				'" . $data["DESCRIPTION"] . "',
				'" . $data["DETAILS"] . "'
			)";
		//var_dump($webItemsQuery);								


		$skuOptions				= new stdClass();
		$skuOptions->acct 		= strlen($data["ACCOUNTPREFIX"]);
		$skuOptions->sku		= strlen($data["ACCOUNTSKU"]);
		$skuOptions->options	= $options;
		$skuOptions->color		= 2;
		$skuOptions->dash		= 1;
		$skuOptions->size		= 3;
		
		$skuOptionsQuery = "
			INSERT INTO SKUOPTIONS
			(
			ROOTSKU,
			OPTIONS
			)
			VALUES
			(
				'" . $data["ACCOUNTPREFIX"] . $data["ACCOUNTSKU"] . "',
				'" . json_encode($skuOptions) . "'
			)";
	
		//var_dump($skuOptionsQuery);


		if(mysqli_query($this->dbConnect, $query)) {
			if(mysqli_query($this->dbConnect, $skuOptionsQuery)) {
				if(mysqli_query($this->dbConnect, $webItemsQuery)) {
					if(mysqli_query($this->dbConnect, $updateQuery)) {
						$message = "SKUs created Successfully.";
						$success = 1;	
					}
					else{
						$message = "Inventory update price7 failed.";
						$success = 0;					
					}
				}
				else{
					$message = "Webitems creation failed.";
					$success = 0;				
				}
			}
			else{
				$message = "SKU Options creation failed.";
				$success = 0;
			}			
		}
		else {
			$message = "SKUs creation failed.";
			$success = 0;
		}
		
		$response = array(
			'success' => $success,
			'success_message' => $message
		);
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}	
	
	public function verifyAssociations($invId){
		if($invId) {			
			$existQuery = "SELECT EXISTS(SELECT ID FROM INVENTORY where ID=" . $invId . ") AS exist";
	
			$resultData = mysqli_query($this->dbConnect, $existQuery);
			
			$invExist = mysqli_fetch_assoc($resultData);

			if( $invExist['exist'] ){
				$selQuery = "
					SELECT
						(SELECT count(ID) from ORDERITEMS WHERE INVENTORYID = " . $invId . ") +
						(SELECT count(ID) from POITEMS WHERE INVENTORYID = " . $invId . ") +
						(SELECT count(ID) from WORKORDERS WHERE FROMID = " . $invId . ") +
						(SELECT count(ID) from WORKORDERS WHERE TOID = " . $invId . ") +
						(SELECT count(ID) from INVENTORYLOG  WHERE INVENTORYID = " . $invId . ")   
						AS count  
					FROM dual";	
				
				$resultData = mysqli_query($this->dbConnect, $selQuery);
				$invData = mysqli_fetch_assoc($resultData);
	
				if($invData['count'] > 0) {
					$message = "Inventory can not be deleted.";
					$associations = $invData['count'];
					$success = 0;	
				}
				else if($invData['count'] == 0) {
					$message = 'Inventory is not associated ,it can be deleted.';
					$associations = 0;
					$success = 1;	
				}
			}	
			else{
				$message = "Inventory not found";
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
	
	
	public function reconcile_inventory($invId){
		$ch = curl_init("http://192.168.1.194/poise/Admin/API_ReconcileSKU_flpoise.php?");
		$curl_param = "&InvID=" . $invId;
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_param);
		
		curl_exec($ch);
		curl_close($ch);
	
	}	
	
}
?>