<?php
//flpoise
//API in old POISE
//under poise/admin/API_ReconcileSKU_flpoise.php

function mysql_call($query){
    //echo "<BR>" . $query;
    include_once 'db_connect.php';
    
	$db_conn = db_connection('flpoise');
    
    $sql = $db_conn->prepare($query); 
    $sql->execute();

	if(strtoupper(substr($query,0,6)) == "SELECT"){
	
		// set the resulting array to associative
		$result = $sql->setFetchMode(PDO::FETCH_ASSOC); 
		//return($result);
		
		$data_array = array();
		$i = 0;

		foreach(new RecursiveArrayIterator($sql->fetchAll()) as $k=>$row) { 
			//$data_array[$i] = array_change_key_case($row, CASE_UPPER); //DO NOT WANT TO MODIFY CASE
			$data_array[$i] = $row;
			$i++;
		}
	
		//print_r($data_array);
		return($data_array);
	}
	elseif(strtoupper(substr($query,0,6)) == "INSERT"){
		return $db_conn->lastInsertId();
	}
	
	elseif(strtoupper(substr($query,0,6)) == "UPDATE"){
		return (0);
	}
	else{
    	return (1);
	}
}


function get_suppliers_list($param){

	$param_json = json_decode($param);
	
	$sql_where = "ID > 0 "; 
	
	if(isset($param_json->INFO)&& $param_json->INFO != ''){
		$sql_where = "NAME LIKE '%" . $param_json->INFO . "%' ";
	}	
	
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->NAME) && $param_json->NAME != ''){
			$sql_where .= "AND NAME LIKE '%" . $param_json->NAME . "%' ";
		}
		if(isset($param_json->STATE) && $param_json->STATE != ''){
			$sql_where .= "AND STATE = '" . $param_json->STATE . "' ";
		}
		if(isset($param_json->STATUS)){
			$sql_where .= "AND STATUS = '" . $param_json->STATUS . "' ";
		}	
	}

    $sql  = "SELECT id AS SUPPLIER_ID, name AS SUPPLIER_NAME, id, name AS supplier ";
    $sql .= "FROM SUPPLIERS ";
	$sql .= "WHERE ";
	$sql .= $sql_where;
	$sql .= "ORDER BY name";
 	//echo $sql;
    
    $suppliers_list = mysql_call($sql);
       
    return($suppliers_list);
}

function get_suppliers_info($id){
    
    $sql  = "SELECT * ";
    $sql .= "FROM SUPPLIERS ";
	$sql .= "WHERE id = '$id' ";
	//$sql .= "ORDER BY name";
    
    //echo $sql;

    $suppliers_info = mysql_call($sql);
       
    return($suppliers_info);
}

function get_suppliers_list_bo_OLD($param){

	$param_json = json_decode($param);
	
	$sql_where = "INVENTORY.QTYBO > 0 "; 
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->NAME) && $param_json->NAME != ''){
			$sql_where .= "AND NAME LIKE '%" . $param_json->NAME . "%' ";
		}
	}

$sql = "SELECT 
SUPPLIERID AS id, 
NAME AS supplier, 
ADDRESS1 AS address1, 
ADDRESS2 AS address2, 
CITY AS city, 
STATE AS state, 
ZIP AS zipcode, 
COUNT(QTYBO) AS items 
FROM 
INVENTORY 
INNER JOIN SUPPLIERS ON SUPPLIERS.ID = INVENTORY.SUPPLIERID 
WHERE " . $sql_where .
"GROUP BY SUPPLIERID 
ORDER BY NAME";
 	
	//echo $sql;
    
    $suppliers_list = mysql_call_api($sql);
       
    return($suppliers_list);
}

function save_suppliers_info($data){
    
	$supplier = json_decode($data);
	
	if($supplier->id == '0')
	{
		$sql  = "INSERT INTO SUPPLIERS ";
		$sql .= "VALUES(";
		$sql .= "0, ";
		$sql .= "'$supplier->name', "; 
		$sql .= "'$supplier->address', ";
		$sql .= "'$supplier->address1', ";
		$sql .= "'$supplier->city', ";
		$sql .= "'$supplier->state', ";
		$sql .= "'$supplier->zipcode', ";
		$sql .= "'$supplier->country', ";
		$sql .= "'$supplier->contact', ";
		$sql .= "'$supplier->email', ";
		$sql .= "'$supplier->phone', ";
		$sql .= "'$supplier->notes', ";
		$sql .= "0, ";
		$sql .= "0, ";
		$sql .= "0, ";
		$sql .= "NULL, ";	
		$sql .= "'$supplier->status', ";
		$sql .= "'$supplier->employee', ";
		$sql .= "'$supplier->onhold', ";
		$sql .= "'$supplier->fax', ";
		$sql .= "'$supplier->transit' ";
		$sql .= ") ";			
				 
	}
	else{
		
		$sql  = "UPDATE SUPPLIERS ";
		$sql .= "SET ";
		$sql .= "NAME = '$supplier->name', ";
		$sql .= "ADDRESS1 = '$supplier->address', ";
		$sql .= "ADDRESS2 = '$supplier->address1', ";
		$sql .= "CITY = '$supplier->city', ";
		$sql .= "STATE = '$supplier->state', ";
		$sql .= "ZIP = '$supplier->zipcode', ";
		$sql .= "COUNTRY = '$supplier->country', ";
		$sql .= "CONTACTPERSON = '$supplier->contact', ";
		$sql .= "EMAIL = '$supplier->email', ";
		$sql .= "PHONE = '$supplier->phone', ";
		$sql .= "FAX = '$supplier->fax', ";
		$sql .= "STATUS = '$supplier->status', ";
		$sql .= "EMPLOYEEID = '$supplier->employee', ";
		$sql .= "ONHOLD = '$supplier->onhold', ";
		$sql .= "TRANSITTIME = '$supplier->transit', ";
		$sql .= "DESCRIPTION = '$supplier->notes' ";
		$sql .= "WHERE ID = '$supplier->id' ";
		//echo $sql;
	}

    $succsess = mysql_call($sql);
       
    return($succsess);
}

function delete_suppliers_info($supid){
   
	$sql  = "DELETE FROM SUPPLIERS ";
	$sql .= "WHERE ID = '$supid' ";
	//echo $sql;

    $succsess = mysql_call($sql);
       
    return($succsess);
}

function get_states_list($param){
    
    $sql  = "SELECT country_id, code AS region_code, default_name AS region_name ";
    $sql .= "FROM country_region ";
	$sql .= "WHERE enable = 1 ";
	$sql .= "ORDER BY code";
    
    //echo $sql;

    $states_list = mysql_call($sql);
       
    return($states_list);
}

function get_employees_list($param){
    
    $sql  = "SELECT * ";//ID AS EMPLOYEE_ID, CONCAT(FIRSTNAME, ' ', LASTNAME) AS EMPLOYEE_FULLNAME ";
    $sql .= "FROM EMPLOYEE ";
	$sql .= "WHERE STATUS = 'ACTIVE' ";
	$sql .= "AND CONCAT(FIRSTNAME, LASTNAME) <> '' ";
	$sql .= "ORDER BY FIRSTNAME, LASTNAME";
    
    //echo $sql;

    $employees_list = mysql_call($sql);
       
    return($employees_list);
}

function get_po_list_OLD($param){

	$param_json = json_decode($param);
	
	$sql_where = "PURCHASEORDERS.ID > 0 ";
	
	if(isset($param_json->INFO)&& $param_json->INFO != ''){
		//$name = $param_json->INFO;
		$sql_where = "CONCAT(TRIM(LEADING '0' FROM PURCHASEORDERS.ID), ' ', SUBSTR(CREATIONDATE, 1, 10), ' ', SUPPLIERS.NAME) LIKE '%" . $param_json->INFO . "%' ";
	}
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->POID)&& $param_json->POID != ''){
			$sql_where .= "AND PURCHASEORDERS.ID = " . $param_json->POID . " ";
		}
		if(isset($param_json->SUPPLIERID)&& $param_json->SUPPLIERID != ''){
			$sql_where .= "AND SUPPLIERID = " . $param_json->SUPPLIERID . " ";
		}
		if(isset($param_json->STATUS)){
			$sql_where .= "AND PURCHASEORDERS.STATUS LIKE '%" . $param_json->STATUS . "%' ";
		}		
	}
	else{
		$sql_where .= "AND PURCHASEORDERS.STATUS IN ('OPEN', 'PARTIAL', 'RECEIVED') ";
	}
	

    $sql  = "SELECT ";
	$sql .= "TRIM(LEADING '0' FROM PURCHASEORDERS.ID) AS PO_ID, ";
	$sql .= "CONCAT(TRIM(LEADING '0' FROM PURCHASEORDERS.ID), ' | ', SUBSTR(CREATIONDATE, 1, 10), ' | ', SUPPLIERS.NAME) AS PO_INFO ";
    $sql .= "FROM PURCHASEORDERS JOIN SUPPLIERS ON PURCHASEORDERS.SUPPLIERID = SUPPLIERS.ID ";
	$sql .= "WHERE ";
	$sql .= $sql_where;
	$sql .= "ORDER BY SUPPLIERS.NAME, PURCHASEORDERS.ID ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $po_list = mysql_call($sql);
       
    return($po_list);
}

function get_bum_info(){
    
    $sql  = "SELECT * ";
    $sql .= "FROM config ";
	$sql .= "WHERE ID = 1 ";
	//$sql .= "ORDER BY name";
    
    //echo $sql;

    $bum_info = mysql_call($sql);
       
    return($bum_info);
}

function get_po_shipvia_list($param){
    
    $sql  = "SELECT DISTINCT SHIPVIA ";
    $sql .= "FROM PURCHASEORDERS ";
	$sql .= "WHERE SHIPVIA IS NOT NULL AND SHIPVIA <> '' ";
	$sql .= "ORDER BY SHIPVIA";
    
    //echo $sql;

    $po_shipvia_list = mysql_call($sql);
       
    return($po_shipvia_list);
}

function get_po_payment_type_list($param){
    
    $sql  = "SELECT DISTINCT PAYMENTTYPE ";
    $sql .= "FROM PURCHASEORDERS ";
	$sql .= "WHERE PAYMENTTYPE IS NOT NULL AND PAYMENTTYPE <> '' ";
	$sql .= "ORDER BY PAYMENTTYPE";
    
    //echo $sql;

    $po_payment_type_list = mysql_call($sql);
       
    return($po_payment_type_list);
}

function get_po_duedate($param){
    
    $sql  = "SELECT DUEDATE ";
    $sql .= "FROM POITEMS ";
	$sql .= "WHERE PURCHASEORDERSID = $param ";
	$sql .= "ORDER BY ID ";
	$sql .= "LIMIT 1";
    
    //echo $sql;

    $po_items = mysql_call($sql);
       
    return($po_items);
}

function get_po_items_OLD($param){

    $sql  = "SELECT ";
	$sql .= "POITEMS.QTYRECEIVED, POITEMS.QTY, POITEMS.VENDORSKU, INVENTORY.DESCRIPTION, POITEMS.PRICE, (POITEMS.QTY * POITEMS.PRICE) AS AMOUNT, POITEMS.DUEDATE ";
    $sql .= "FROM POITEMS JOIN INVENTORY ";
	$sql .= "WHERE ";
	$sql .= "POITEMS.INVENTORYID = INVENTORY.ID ";
	$sql .= "AND POITEMS.PURCHASEORDERSID = $param ";
	$sql .= "ORDER BY POITEMS.ID ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $po_items = mysql_call($sql);
       
    return($po_items);
}

function get_po_items_bo_OLD($param){

	$sql  = "SELECT ";
	$sql .= "0 AS qtyreceived, SUM(INVENTORY.QTYBO) AS qty, INVENTORY.SKU AS bumSku, INVENTORY.VENDORSKU AS vendorSku, INVENTORY.DESCRIPTION AS description, INVENTORY.COST AS price, (SUM(INVENTORY.QTYBO) * INVENTORY.COST) AS amount, DATE_FORMAT(NOW(), '%Y-%m-%d') AS dueDate ";
	$sql .= "FROM ";
	$sql .= "INVENTORY ";
	$sql .= "INNER JOIN SUPPLIERS ON SUPPLIERS.ID = INVENTORY.SUPPLIERID ";
	$sql .= "WHERE ";
	$sql .= "SUPPLIERS.ID = $param ";
	$sql .= "AND INVENTORY.QTYBO > 0 ";
	$sql .= "GROUP BY ";
	$sql .= "INVENTORY.SKU ";
	$sql .= "ORDER BY ";
	$sql .= "INVENTORY.SKU  ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $po_items = mysql_call($sql);
       
    return($po_items);
}


//////////
//////Functions for API
function mysql_call_api($query){
    //echo "<BR>" . $query;
    include_once 'db_connect.php';
    
	$db_conn = db_connection('flpoise');
    
    $sql = $db_conn->prepare($query); 
    $sql->execute();

	if(strtoupper(substr($query,0,6)) == "SELECT"){
	
		// set the resulting array to associative
		$result = $sql->setFetchMode(PDO::FETCH_ASSOC); 
		//return($result);
		
		$data_array = array();
		$i = 0;
		foreach(new RecursiveArrayIterator($sql->fetchAll()) as $k=>$row) { 
			$data_array[$i] = array_change_key_case($row, CASE_LOWER);
			$i++;
		}
	
		//print_r($data_array);
		return($data_array);
	}
	elseif(strtoupper(substr($query,0,6)) == "INSERT"){
		return $db_conn->lastInsertId();
	}
	
	elseif(strtoupper(substr($query,0,6)) == "UPDATE"){
		return (0);
	}
	elseif(strtoupper(substr($query,0,6)) == "LOCK"){
		return (0);
	}
	elseif(strtoupper(substr($query,0,6)) == "UNLOCK"){
		return (0);
	}	
	else{
    	return (1);
	}
}

function get_users_list($param){
    
    $sql  = "SELECT ID AS userid, username, password, CONCAT(FIRSTNAME, ' ', LASTNAME) AS user_name ";
    $sql .= "FROM EMPLOYEE ";
	$sql .= "WHERE STATUS = 'ACTIVE' ";
	$sql .= "AND CONCAT(FIRSTNAME, LASTNAME) <> '' ";
	$sql .= "ORDER BY FIRSTNAME, LASTNAME";
    
    //echo $sql;

    $users_list = mysql_call_api($sql);
       
    return($users_list);
}

function verify_login($param){
	//{"account":{"username":"alex","password":"1234"}}
	
	$credentials = json_decode($param);
	$username = $credentials->account->username;
	$password = $credentials->account->password;
    
    $sql  = "SELECT ID AS userid, FIRSTNAME, EMAIL, ALLOWADMIN ";
    $sql .= "FROM EMPLOYEE ";
	$sql .= "WHERE ";
	$sql .= "STATUS = 'ACTIVE' ";
	$sql .= "AND ONHOLD = 'NO' ";
	$sql .= "AND USERNAME = '$username' ";
    $sql .= "AND PASSWORD = '$password' ";
	
    //echo $sql;

    $user = mysql_call_api($sql);
       
    return($user);
}

function stats_new_customers($param){
	$sql  = "SELECT ";
	$sql .= "DATE_FORMAT(SUBSTRING(CREATEDON,1,10), '%b %d %Y') AS createddate, ";
	$sql .= "COUNT(DISTINCT(CONCAT(NAME,ADDRESS1,CITY,STATE,ZIP))) AS qty ";
	$sql .= "FROM ";
	$sql .= "CUSTOMERS ";
	$sql .= "WHERE ";
	$sql .= "SUBSTRING(CREATEDON,1,10) > (SUBSTRING(NOW(),1,10) - interval 30 day) ";
	$sql .= "GROUP BY ";
	$sql .= "SUBSTRING(CREATEDON,1,10) ";
	$sql .= "ORDER BY ";
	$sql .= "CREATEDON ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $data = mysql_call($sql);
       
    return($data);
}

function stats_new_orders($param){
	$sql  = "SELECT ";
	$sql .= "DATE_FORMAT(SUBSTRING(CREATIONDATE,1,10), '%b %d %Y') AS createddate, ";
	$sql .= "COUNT(ID) AS qty ";
	$sql .= "FROM ";
	$sql .= "ORDERS ";
	$sql .= "WHERE ";
	$sql .= "SUBSTRING(CREATIONDATE,1,10) > (SUBSTRING(NOW(),1,10) - interval 30 day) ";
	$sql .= "AND STATUS NOT IN ('DELETED') ";
	$sql .= "GROUP BY ";
	$sql .= "SUBSTRING(CREATIONDATE,1,10) ";
	$sql .= "ORDER BY ";
	$sql .= "CREATIONDATE ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $data = mysql_call($sql);
       
    return($data);
}

function stats_revenue($param){
	$sql  = "SELECT ";
	$sql .= "DATE_FORMAT(SUBSTRING(`DATE`,1,10), '%b %d %Y') AS createddate, ";
	$sql .= "SUM(TOTAL/1000) AS qty ";
	$sql .= "FROM ";
	$sql .= "BATCHES ";
	$sql .= "WHERE ";
	$sql .= "SUBSTRING(`DATE`,1,10) > (SUBSTRING(NOW(),1,10) - interval 360 day) ";
	$sql .= "GROUP BY ";
	$sql .= "SUBSTRING(`DATE`,1,10) ";
	$sql .= "ORDER BY ";
	$sql .= "`DATE` ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $data = mysql_call($sql);
       
    return($data);
}

function stats_pending_orders($param){
	$sql  = "SELECT ";
	$sql .= "DATE_FORMAT(SUBSTRING(CREATIONDATE,1,10), '%b %d %Y') AS createddate, ";
	$sql .= "ID AS orderid "; 
	$sql .= "FROM "; 
	$sql .= "ORDERS "; 
	$sql .= "WHERE "; 
	$sql .= "STATUS IN ('PENDING','PROCESSING') "; 
	$sql .= "GROUP BY "; 
	$sql .= "ORDERID ";
	$sql .= "ORDER BY "; 
	$sql .= "SHIPDATE ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $data = mysql_call($sql);
       
    return($data);
}

function stats_shipped_orders($param){
	$sql  = "SELECT ";
	$sql .= "DATE_FORMAT(SUBSTRING(SHIPDATE,1,10), '%b %d %Y') AS shipdate, ";
	$sql .= "ORDERID AS orderid "; 
	$sql .= "FROM "; 
	$sql .= "ORDERITEMS "; 
	$sql .= "WHERE "; 
	$sql .= "SUBSTRING(SHIPDATE,1,10) = SUBSTRING(NOW(),1,10) "; 
	$sql .= "AND STATUS IN ('SHIPPED','INVOICED') "; 
	$sql .= "GROUP BY "; 
	$sql .= "SUBSTRING(SHIPDATE,1,10), ORDERID ";
	$sql .= "ORDER BY "; 
	$sql .= "SHIPDATE ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $data = mysql_call($sql);
       
    return($data);
}

function add_po_to_db($supplierid, $employeeid, $status, $poTotal)
{
	lock_table('PURCHASEORDERS');
	$sql  = "INSERT INTO PURCHASEORDERS SET ";
	$sql .= "CREATIONDATE=NOW(), SUPPLIERID='$supplierid', EMPLOYEEID='$employeeid', ";
	$sql .= "STATUS='$status', ";
	$sql .= "TOTAL='$poTotal' ";
	$data = mysql_call_api($sql);
    unlock_tables();
	
    return($data);
}

function get_po_items_bo($supplierid){
	date_default_timezone_set("America/New_York");

	// Get backordered items matching the given the supplier
	// Union this with low stock items for the given supplier
	$bobysupp_query  = "SELECT ";
	$bobysupp_query .= "SUM(QTYBO) AS QTYBO, ";
	$bobysupp_query .= "ID, ";
	$bobysupp_query .= "BUSKU, ";
	$bobysupp_query .= "VENDORSKU, ";
	$bobysupp_query .= "DESCRIPTION, ";
	$bobysupp_query .= "PRICE, ";
	$bobysupp_query .= "TRANSITTIME ";
	$bobysupp_query .= "FROM ";
	$bobysupp_query .= "(";
	$bobysupp_query .= "(";
	$bobysupp_query .= "SELECT ";
	$bobysupp_query .= "SUM(oi.QTYBO) AS QTYBO, ";
	$bobysupp_query .= "inv.ID AS ID, ";
	$bobysupp_query .= "inv.SKU AS BUSKU, ";
	$bobysupp_query .= "inv.VENDORSKU AS VENDORSKU, ";
	$bobysupp_query .= "inv.DESCRIPTION AS DESCRIPTION, ";
	$bobysupp_query .= "inv.COST AS PRICE, ";
	$bobysupp_query .= "sup.TRANSITTIME AS TRANSITTIME ";
	$bobysupp_query .= "FROM ";
	$bobysupp_query .= "ORDERS o, ORDERITEMS oi, INVENTORY inv, SUPPLIERS sup ";
	$bobysupp_query .= "WHERE ";
	$bobysupp_query .= "oi.ORDERID = o.ID ";
	$bobysupp_query .= "AND sup.ID='$supplierid' ";
	$bobysupp_query .= "AND sup.ID=inv.SUPPLIERID ";
	$bobysupp_query .= "AND inv.ID=oi.INVENTORYID ";
	$bobysupp_query .= "AND oi.STATUS='BACKORDERED' ";
	$bobysupp_query .= "AND o.STATUS NOT IN ('OPEN') ";
	$bobysupp_query .= "GROUP BY inv.SKU ";
	$bobysupp_query .= ") ";
	$bobysupp_query .= "UNION ";
	$bobysupp_query .= "(";
	$bobysupp_query .= "SELECT ";
	$bobysupp_query .= "SUM(i.QTYREORDER - i.QTYSTOCK) AS QTYBO, ";
	$bobysupp_query .= "i.ID, ";
	$bobysupp_query .= "i.SKU AS BUSKU, "; 
	$bobysupp_query .= "i.VENDORSKU, "; 
	$bobysupp_query .= "i.DESCRIPTION, "; 
	$bobysupp_query .= "i.COST AS PRICE, "; 
	$bobysupp_query .= "s.TRANSITTIME "; 
	$bobysupp_query .= "FROM INVENTORY i, SUPPLIERS s "; 
	$bobysupp_query .= "WHERE "; 
	$bobysupp_query .= "s.ID=i.SUPPLIERID "; 
	$bobysupp_query .= "AND s.ID='$supplierid' ";
	$bobysupp_query .= "AND i.QTYSTOCK < i.QTYREORDER ";
	$bobysupp_query .= "GROUP BY i.SKU ";
	$bobysupp_query .= ")";
	$bobysupp_query .= ") ";
	$bobysupp_query .= "AS X ";
	$bobysupp_query .= "GROUP BY BUSKU";
	
	//echo $bobysupp_query;
	$bobysupp_result = mysql_call_api($bobysupp_query);
	
	if ($bobysupp_result)
	{
		$data = array();
		$j = 0;
		for($i=0; $i<sizeof($bobysupp_result); $i++){
			$subtotal = 0;
			$borowID = $bobysupp_result[$i]['id'];
			$borowQTYBO = $bobysupp_result[$i]['qtybo'];
			$borowPRICE = $bobysupp_result[$i]['price'];
			$borowTRANSITTIME = $bobysupp_result[$i]['transittime'];
			$borowBUSKU = $bobysupp_result[$i]['busku'];
			$borowVENDORSKU = $bobysupp_result[$i]['vendorsku'];
			$borowDESCRIPTION = $bobysupp_result[$i]['description'];
			
			$qtybo = $borowQTYBO;

			/* Don't need this anymore since doing it in one Union query above
			//Check if this backordered item is also below minimum levels
			$belowsupp_query="SELECT QTYREORDER FROM INVENTORY WHERE ID='$borowID' AND QTYSTOCK < QTYREORDER";
			$belowsupp_result = mysql_call_api($belowsupp_query);

			$qtybo=$borowQTYBO;
			if ($belowsupp_result){
				$qtybo+=$belowsupp_result[0]['qtyreorder'];
			}
			*/
			
			// Check how many of these inventoryid are already in a PO
			
			$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowID' AND STATUS IN ('OPEN', 'PARTIAL') ";
			$poinv_result = mysql_call_api($poinv_query);

			$porowQTY = 0;
			$porowQTY = $poinv_result[0]['qty'];
	
			if( $porowQTY > 0 ) {
				$qtybo -= $porowQTY;
			}
			if($qtybo < 0){
				$qtybo = 0;
			}
				
			$price = $borowPRICE;
			$subtotal += $price * $qtybo;
			$duedate  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d") + $borowTRANSITTIME, date("Y")));
			
			//Populate final array here
			if($qtybo > 0){
				$data[$j]['qtyreceived'] = 0;
				$data[$j]['qty'] = $qtybo;
				$data[$j]['bumSku'] = $borowBUSKU;
				$data[$j]['vendorSku'] = $borowVENDORSKU;
				$data[$j]['description'] = $borowDESCRIPTION;
				$data[$j]['price'] = $borowPRICE;
				$data[$j]['amount'] = $subtotal;
				$data[$j]['dueDate'] = $duedate;
				$j++;
			}			
		} //for
		return ($data);
	} //if ($bobysupp_result)
	else
		return null;

}

function get_suppliers_list_bo($param){

	// Get backordered items matching the given the supplier
	$bobysupp_query  = "SELECT ";
	$bobysupp_query .= "SUM(QTYBO) AS QTYBO, ";
	$bobysupp_query .= "x.ID AS INVENTORYID, ";
	$bobysupp_query .= "s.id AS SUPPLIERID, ";
	$bobysupp_query .= "s.NAME AS SUPPLIER, ";
	$bobysupp_query .= "s.ADDRESS1, ";
	$bobysupp_query .= "s.ADDRESS2, ";
	$bobysupp_query .= "s.CITY, ";
	$bobysupp_query .= "s.STATE, ";
	$bobysupp_query .= "s.ZIP as ZIPCODE ";
	$bobysupp_query .= "FROM ";
	$bobysupp_query .= "( ";
	$bobysupp_query .= "( ";
	$bobysupp_query .= "SELECT ";
	$bobysupp_query .= "SUM(oi.QTYBO) AS QTYBO, ";
	$bobysupp_query .= "inv.ID AS ID, ";
	$bobysupp_query .= "inv.SKU AS BUSKU, ";
	$bobysupp_query .= "inv.VENDORSKU AS VENDORSKU, ";
	$bobysupp_query .= "inv.DESCRIPTION AS DESCRIPTION, ";
	$bobysupp_query .= "inv.COST AS PRICE, ";
	$bobysupp_query .= "sup.TRANSITTIME AS TRANSITTIME, ";
	$bobysupp_query .= "sup.ID AS SUPPLIERID ";
	$bobysupp_query .= "FROM ORDERS o, ORDERITEMS oi, INVENTORY inv, SUPPLIERS sup "; 
	$bobysupp_query .= "WHERE ";
	$bobysupp_query .= "oi.ORDERID = o.ID "; 
	$bobysupp_query .= "AND sup.ID=inv.SUPPLIERID "; 
	$bobysupp_query .= "AND inv.ID=oi.INVENTORYID "; 
	$bobysupp_query .= "AND oi.STATUS='BACKORDERED' "; 
	$bobysupp_query .= "AND o.STATUS NOT IN ('OPEN') "; 
	$bobysupp_query .= "GROUP BY sup.ID, inv.SKU ";
	$bobysupp_query .= ") ";
	$bobysupp_query .= "UNION ";
	$bobysupp_query .= "( ";
	$bobysupp_query .= "SELECT ";
	$bobysupp_query .= "SUM(i.QTYREORDER - i.QTYSTOCK) AS QTYBO, ";
	$bobysupp_query .= "i.ID, ";
	$bobysupp_query .= "i.SKU AS BUSKU, "; 
	$bobysupp_query .= "i.VENDORSKU, "; 
	$bobysupp_query .= "i.DESCRIPTION, "; 
	$bobysupp_query .= "i.COST AS PRICE, "; 
	$bobysupp_query .= "s.TRANSITTIME, ";
	$bobysupp_query .= "s.ID AS SUPPLIERID ";
	$bobysupp_query .= "FROM INVENTORY i, SUPPLIERS s "; 
	$bobysupp_query .= "WHERE "; 
	$bobysupp_query .= "s.ID=i.SUPPLIERID "; 
	$bobysupp_query .= "AND i.QTYSTOCK < i.QTYREORDER ";
	$bobysupp_query .= "GROUP BY s.ID, i.SKU ";
	$bobysupp_query .= ") ";
	$bobysupp_query .= ") ";
	$bobysupp_query .= "AS x, ";
	$bobysupp_query .= "SUPPLIERS s ";
	$bobysupp_query .= "WHERE x.SUPPLIERID = s.ID ";
	$bobysupp_query .= "GROUP BY SUPPLIER, INVENTORYID ";
	$bobysupp_query .= "ORDER BY SUPPLIER";
	
	$bobysupp_result = mysql_call_api($bobysupp_query);

	if ($bobysupp_result)
	{
		$data = array();
		$j = 0;
		for($i=0; $i<sizeof($bobysupp_result); $i++){
			$borowINVENTORYID = $bobysupp_result[$i]['inventoryid'];
			$borowQTYBO = $bobysupp_result[$i]['qtybo'];
			$borowSUPPLIERID = $bobysupp_result[$i]['supplierid'];
			$borowSUPPLIER = $bobysupp_result[$i]['supplier'];
			$borowADDRESS1 = $bobysupp_result[$i]['address1'];
			$borowADDRESS2 = $bobysupp_result[$i]['address2'];
			$borowCITY = $bobysupp_result[$i]['city'];
			$borowSTATE = $bobysupp_result[$i]['state'];
			$borowZIPCODE = $bobysupp_result[$i]['zipcode'];
			
			$qtybo=$borowQTYBO;

			/*
			//Check if this backordered item is also below minimum levels
			$belowsupp_query="SELECT QTYREORDER FROM INVENTORY WHERE ID='$borowINVENTORYID' AND QTYSTOCK < QTYREORDER";
			$belowsupp_result = mysql_call_api($belowsupp_query);

			$qtybo=$borowQTYBO;
			if ($belowsupp_result){
				$qtybo+=$belowsupp_result[0]['qtyreorder'];
			}
			*/
					
			// Check how many of these inventoryid are already in a PO
			
			$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowINVENTORYID' AND STATUS IN ('OPEN', 'PARTIAL') ";
			$poinv_result = mysql_call_api($poinv_query);

			$porowQTY = $poinv_result[0]['qty'];

			if( $porowQTY > 0 ) {
				$qtybo-=$porowQTY;
			}
			if($qtybo < 0){
				$qtybo = 0;
			}

			//Populate final array here
 			
			if($qtybo > 0){
				$data[$j]['id'] 		= $borowSUPPLIERID;
				$data[$j]['supplier'] 	= $borowSUPPLIER;
				$data[$j]['address1'] 	= $borowADDRESS1;
				$data[$j]['address2'] 	= $borowADDRESS2;
				$data[$j]['city'] 		= $borowCITY;
				$data[$j]['state'] 		= $borowSTATE;
				$data[$j]['zipcode'] 	= $borowZIPCODE;
				$data[$j]['items'] 		= $qtybo;
				$j++;
			}			
			
		} //for
		
		$result = array();
		$j = 0;
		for($i=0; $i<sizeof($data); $i++){
			if($i == 0){
				$result[$j]['id'] 		= $data[$i]['id'];
				$result[$j]['supplier'] = $data[$i]['supplier'];
				$result[$j]['address1'] = $data[$i]['address1'];
				$result[$j]['address2'] = $data[$i]['address2'];
				$result[$j]['city'] 	= $data[$i]['city'];
				$result[$j]['state'] 	= $data[$i]['state'];
				$result[$j]['zipcode'] 	= $data[$i]['zipcode'];
				
				$item_count = 1;
				$result[$j]['items'] = $item_count;
			}
			else {
				if($data[$i]['id'] !== $data[$i-1]['id']){
					$result[$j]['items'] 	= $item_count;
					$j++;
					$result[$j]['id'] 		= $data[$i]['id'];
					$result[$j]['supplier'] = $data[$i]['supplier'];
					$result[$j]['address1'] = $data[$i]['address1'];
					$result[$j]['address2'] = $data[$i]['address2'];
					$result[$j]['city'] 	= $data[$i]['city'];
					$result[$j]['state'] 	= $data[$i]['state'];
					$result[$j]['zipcode'] 	= $data[$i]['zipcode'];					
					$item_count = 1;
					$result[$j]['items'] 	= $item_count;
				}
				else{
					$item_count++;
				}
			}
		}
		$result[$j]['items'] = $item_count;
		
		return ($result);
	} //if ($bobysupp_result)
	else
		return null;


}

function get_suppliers_list_low_stock($param){
	
	// Get bellow minimum levels items matching the given supplier
	$minsupp_query  = "SELECT ";
	$minsupp_query .= "s.ID AS SUPPLIERID, ";
	$minsupp_query .= "s.NAME AS SUPPLIER, ";
	$minsupp_query .= "s.ADDRESS1, ";
	$minsupp_query .= "s.ADDRESS2, ";
	$minsupp_query .= "s.CITY, ";
	$minsupp_query .= "s.STATE, ";
	$minsupp_query .= "s.ZIP AS ZIPCODE, ";
	$minsupp_query .= "i.QTYREORDER AS QTYRO, ";
	$minsupp_query .= "i.QTYSTOCK, ";
	$minsupp_query .= "i.ID, ";
	$minsupp_query .= "i.SKU AS BUSKU, ";
	$minsupp_query .= "i.VENDORSKU, ";
	$minsupp_query .= "i.DESCRIPTION, ";
	$minsupp_query .= "i.COST AS PRICE, ";
	$minsupp_query .= "s.TRANSITTIME ";
	$minsupp_query .= "FROM ";
	$minsupp_query .= "INVENTORY i, SUPPLIERS s ";
	$minsupp_query .= "WHERE ";
	$minsupp_query .= "s.ID=i.SUPPLIERID ";
	$minsupp_query .= "AND i.QTYSTOCK < i.QTYREORDER ";
	$minsupp_query .= "GROUP BY s.ID, i.SKU";
	$minsupp_result = mysql_call_api($minsupp_query);

//echo "<pre>";	print_r($minsupp_result);	

	if ($minsupp_result)
	{
		// perform a query and see how many items where ordered for this inventoryid
		
		$data = array();
		$j = 0;
		for($i=0; $i<sizeof($minsupp_result); $i++){
			$borowID = $minsupp_result[$i]['id'];
			$borowQTYRO = $minsupp_result[$i]['qtyro'];
			$borowQTYSTOCK = $minsupp_result[$i]['qtystock'];
			$borowPRICE = $minsupp_result[$i]['price'];
			$borowTRANSITTIME = $minsupp_result[$i]['transittime'];	
				
			// Check how many of these inventoryid are already in a PO
			$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowID' AND STATUS IN ('OPEN', 'PARTIAL') ";
			$poinv_result = mysql_call_api($poinv_query);

			$porowQTY = $poinv_result[0]['qty'];

			if( $porowQTY < $borowQTYRO ) {
				$qtyRo = $borowQTYRO - $borowQTYSTOCK - $porowQTY;
				if ($qtyRo < 0) $qtyRo = 0;
			}
			if($qtyRo > 0){
				$data[$j]['supplierid'] 		= $minsupp_result[$i]['supplierid'];
				$data[$j]['supplier']	= $minsupp_result[$i]['supplier'];
				$data[$j]['address1']	= $minsupp_result[$i]['address1'];
				$data[$j]['address2']	= $minsupp_result[$i]['address2'];
				$data[$j]['city']		= $minsupp_result[$i]['city'];
				$data[$j]['state']		= $minsupp_result[$i]['state'];
				$data[$j]['zipcode']	= $minsupp_result[$i]['zipcode'];
				$j++;
			}
		} //for
	} //if ($minsupp_result)	

		$result = array();
		$j = 0;
		for($i=0; $i<sizeof($data); $i++){
			if($i == 0){
				$result[$j]['supplierid'] 		= $data[$i]['supplierid'];
				$result[$j]['supplier'] = $data[$i]['supplier'];
				$result[$j]['address1'] = $data[$i]['address1'];
				$result[$j]['address2'] = $data[$i]['address2'];
				$result[$j]['city'] 	= $data[$i]['city'];
				$result[$j]['state'] 	= $data[$i]['state'];
				$result[$j]['zipcode'] 	= $data[$i]['zipcode'];
				
				$item_count = 1;
				$result[$j]['items'] = $item_count;
			}
			else {
				if($data[$i]['supplierid'] !== $data[$i-1]['supplierid']){
					$result[$j]['items'] 	= $item_count;
					$j++;
					$result[$j]['supplierid'] 		= $data[$i]['supplierid'];
					$result[$j]['supplier'] = $data[$i]['supplier'];
					$result[$j]['address1'] = $data[$i]['address1'];
					$result[$j]['address2'] = $data[$i]['address2'];
					$result[$j]['city'] 	= $data[$i]['city'];
					$result[$j]['state'] 	= $data[$i]['state'];
					$result[$j]['zipcode'] 	= $data[$i]['zipcode'];					
					$item_count = 1;
					$result[$j]['items'] 	= $item_count;
				}
				else{
					$item_count++;
				}
			}
		}
		$result[$j]['items'] = $item_count;
		
		return ($result);

/////////////////////////////////////////////////////////////////////////
/*
	// Get backordered items matching the given supplier
	$bobysupp_query="SELECT SUM(oi.QTYBO) AS QTYBO, inv.ID AS INVENTORYID, sup.ID AS SUPPLIERID, sup.NAME AS SUPPLIER, sup.ADDRESS1 AS ADDRESS1, sup.ADDRESS2 AS ADDRESS2, sup.CITY AS CITY, sup.STATE AS STATE, sup.ZIP AS ZIPCODE FROM ORDERS o, ORDERITEMS oi, INVENTORY inv, SUPPLIERS sup WHERE oi.ORDERID = o.ID AND sup.ID=inv.SUPPLIERID AND inv.ID=oi.INVENTORYID AND oi.STATUS='BACKORDERED' AND o.STATUS NOT IN ('OPEN') GROUP BY SUPPLIER, INVENTORYID";
	$bobysupp_result = mysql_call_api($bobysupp_query);

	if ($bobysupp_result)
	{
		$data = array();
		$j = 0;
		for($i=0; $i<sizeof($bobysupp_result); $i++){
			$borowINVENTORYID = $bobysupp_result[$i]['inventoryid'];
			$borowQTYBO = $bobysupp_result[$i]['qtybo'];
			$borowSUPPLIERID = $bobysupp_result[$i]['supplierid'];
			$borowSUPPLIER = $bobysupp_result[$i]['supplier'];
			$borowADDRESS1 = $bobysupp_result[$i]['address1'];
			$borowADDRESS2 = $bobysupp_result[$i]['address2'];
			$borowCITY = $bobysupp_result[$i]['city'];
			$borowSTATE = $bobysupp_result[$i]['state'];
			$borowZIPCODE = $bobysupp_result[$i]['zipcode'];

			//Check if this backordered item is also below minimum levels
			$belowsupp_query="SELECT QTYREORDER FROM INVENTORY WHERE ID='$borowINVENTORYID' AND QTYSTOCK < QTYREORDER";
			$belowsupp_result = mysql_call_api($belowsupp_query);

			$qtybo=$borowQTYBO;
			if ($belowsupp_result){
				$qtybo+=$belowsupp_result[0]['qtyreorder'];
			}
					
			// Check how many of these inventoryid are already in a PO
			
			$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowINVENTORYID' AND STATUS IN ('OPEN', 'PARTIAL') ";
			$poinv_result = mysql_call_api($poinv_query);

			$porowQTY = $poinv_result[0]['qty'];

			if( $porowQTY > 0 ) {
				$qtybo-=$porowQTY;
			}
			if($qtybo < 0){
				$qtybo = 0;
			}

			//Populate final array here
 			
			if($qtybo > 0){
				$data[$j]['id'] 		= $borowSUPPLIERID;
				$data[$j]['supplier'] 	= $borowSUPPLIER;
				$data[$j]['address1'] 	= $borowADDRESS1;
				$data[$j]['address2'] 	= $borowADDRESS2;
				$data[$j]['city'] 		= $borowCITY;
				$data[$j]['state'] 		= $borowSTATE;
				$data[$j]['zipcode'] 	= $borowZIPCODE;
				$data[$j]['items'] 		= $qtybo;
				$j++;
			}			
			
		} //for
		
		$result = array();
		$j = 0;
		for($i=0; $i<sizeof($data); $i++){
			if($i == 0){
				$result[$j]['id'] 		= $data[$i]['id'];
				$result[$j]['supplier'] = $data[$i]['supplier'];
				$result[$j]['address1'] = $data[$i]['address1'];
				$result[$j]['address2'] = $data[$i]['address2'];
				$result[$j]['city'] 	= $data[$i]['city'];
				$result[$j]['state'] 	= $data[$i]['state'];
				$result[$j]['zipcode'] 	= $data[$i]['zipcode'];
				
				$item_count = 1;
				$result[$j]['items'] = $item_count;
			}
			else {
				if($data[$i]['id'] !== $data[$i-1]['id']){
					$result[$j]['items'] 	= $item_count;
					$j++;
					$result[$j]['id'] 		= $data[$i]['id'];
					$result[$j]['supplier'] = $data[$i]['supplier'];
					$result[$j]['address1'] = $data[$i]['address1'];
					$result[$j]['address2'] = $data[$i]['address2'];
					$result[$j]['city'] 	= $data[$i]['city'];
					$result[$j]['state'] 	= $data[$i]['state'];
					$result[$j]['zipcode'] 	= $data[$i]['zipcode'];					
					$item_count = 1;
					$result[$j]['items'] 	= $item_count;
				}
				else{
					$item_count++;
				}
			}
		}
		$result[$j]['items'] = $item_count;
		
		return ($result);
	} //if ($bobysupp_result)
	else
		return null;

*/
}


function add_po_items($PurchaseOrderID, $param, $status){
	$poitems = json_decode($param);

	for($i=0; $i < sizeof($poitems); $i++) {
		if( $poitems[$i]->qty > 0 ) {
			$findinv_query = "SELECT * FROM INVENTORY WHERE SKU='" . $poitems[$i]->bumSku . "'";
			$findinv_result = mysql_call_api($findinv_query);

			$l_invid		= $findinv_result[0]['id'];
			$l_vendorsku	= $findinv_result[0]['vendorsku'];
			$_lprice		= $poitems[$i]->price;
			$_lamount		= $poitems[$i]->price * $poitems[$i]->qty;
			$_lboqty		= $poitems[$i]->qty;
			$_lduedate		= $poitems[$i]->dueDate;

			//insert po item
			lock_table('POITEMS');
			$query  = "INSERT INTO POITEMS SET ";
			$query .= "DUEDATE='$_lduedate', QTY='$_lboqty', PURCHASEORDERSID='$PurchaseOrderID'";
			$query .= ", INVENTORYID='$l_invid', VENDORSKU='$l_vendorsku', ";
			$query .= "AMOUNT='$_lamount', PRICE='$_lprice', STATUS='$status'";
			
			$result = mysql_call_api($query);
			unlock_tables();

			////// Update here Inventory On Order Items
			lock_table('INVENTORY');
			$query = "UPDATE INVENTORY SET ONORDER = ABS(ONORDER + '$_lboqty') WHERE ID = '$l_invid'";
			$addresult = mysql_call_api($query);
			unlock_tables();				

			// Get backordered items matching the given inventoryid
			$orderitems_query="SELECT ID, QTYBO FROM ORDERITEMS WHERE INVENTORYID ='$l_invid' AND STATUS='BACKORDERED' AND PURCHASEORDERID IS NULL ORDER BY DATE";
			$orderitems_result = mysql_call_api($orderitems_query);

			if ($orderitems_result)
			{
				$onorder_residual = $_lboqty;
				for($j=0; $j < sizeof($orderitems_result); $j++){
					$oirowID = $orderitems_result[$j]['id'];
					$oirowQTYBO = $orderitems_result[$j]['qtybo'];
					
					$onorder_residual -= $oirowQTYBO;
					
					if($onorder_residual > 0){
						////// Update here OrderItems OnOrder Items
						lock_table('ORDERITEMS');
						$query = "UPDATE ORDERITEMS SET QTYONORDER = '$oirowQTYBO' , PURCHASEORDERID = '$PurchaseOrderID', ESTIMATEDRXDATE = '$_lduedate' WHERE ID = '$oirowID'";
						$query_result = mysql_call_api($query);
						unlock_tables();							
					}		
				}
			}
		}
	} // for

	$data = array();
	return($data);
}	

function get_employeeId($user_name){
    $sql  = "SELECT ID ";
    $sql .= "FROM EMPLOYEE ";
	$sql .= "WHERE USERNAME = '$user_name' ";
    
    //echo $sql;

    $employee = mysql_call_api($sql);
       
    return($employee);
}

function get_inventoryId($sku){
    $sql  = "SELECT ID ";
    $sql .= "FROM INVENTORY ";
	$sql .= "WHERE SKU = '$sku' ";
    
    //echo $sql;

    $inventory = mysql_call_api($sql);
       
    return($inventory);
}

function get_inventoryInfo($sku){
    $sql  = "SELECT * ";
    $sql .= "FROM INVENTORY ";
	$sql .= "WHERE SKU = '$sku' ";
    
    //echo $sql;

    $inventory = mysql_call_api($sql);
       
    return($inventory);
}

function get_inventoryInfoNotPo($PurchaseOrderID, $sku){
    $sql  = "SELECT * FROM INVENTORY ";
    $sql .= "WHERE ";
    $sql .= "ID NOT IN ";
    $sql .= "( ";
    $sql .= "SELECT INVENTORY.ID ";
    $sql .= "FROM "; 
    $sql .= "INVENTORY JOIN POITEMS ON INVENTORY.ID = POITEMS.INVENTORYID ";
    $sql .= "WHERE ";
    $sql .= "POITEMS.PURCHASEORDERSID = '$PurchaseOrderID' ";
    $sql .= ") ";
    $sql .= "AND SKU = '$sku'";
    
    //echo $sql;

    $inventory = mysql_call_api($sql);
       
    return($inventory);
}

function lock_table($table){
	$lockquery = "LOCK TABLES $table WRITE";
	$lockresult = mysql_call_api($lockquery);
	//echo "<pre>" . 	var_dump($lockresult);
}

function unlock_tables(){
	$unlockquery = "UNLOCK TABLES";
	$unlockresult = mysql_call_api($unlockquery);
	//echo "<pre>" . 	var_dump($unlockresult);
}

function get_po_list($param){

	$param_json = json_decode($param);
	
	$sql_where = "PURCHASEORDERS.ID > 0 ";
	
	if(isset($param_json->INFO)&& $param_json->INFO != ''){
		//$name = $param_json->INFO;
		$sql_where = "CONCAT(TRIM(LEADING '0' FROM PURCHASEORDERS.ID), ' ', SUBSTR(CREATIONDATE, 1, 10), ' ', SUPPLIERS.NAME) LIKE '%" . $param_json->INFO . "%' ";
	}
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->POID)&& $param_json->POID != ''){
			$sql_where .= "AND PURCHASEORDERS.ID = " . $param_json->POID . " ";
		}
		if(isset($param_json->SUPPLIERID)&& $param_json->SUPPLIERID != ''){
			$sql_where .= "AND SUPPLIERID = " . $param_json->SUPPLIERID . " ";
		}
		if(isset($param_json->STATUS)){
			$sql_where .= "AND PURCHASEORDERS.STATUS LIKE '%" . $param_json->STATUS . "%' ";
		}		
	}
	else{
		$sql_where .= "AND PURCHASEORDERS.STATUS IN ('OPEN', 'PARTIAL', 'RECEIVED', 'CLOSED') ";
	}
	
    $sql  = "SELECT ";
	$sql .= "TRIM(LEADING '0' FROM PURCHASEORDERS.ID) AS poid, ";
	$sql .= "SUBSTR(CREATIONDATE, 1, 10) AS creationdate, "; 
	$sql .= "SUBSTR(DUEDATE, 1, 10) AS duedate, ";
	$sql .= "SUPPLIERS.ID AS supplierid, ";
	$sql .= "SUPPLIERS.NAME AS supplier, ";
	$sql .= "SUPPLIERS.ADDRESS1 AS address1, ";
	$sql .= "SUPPLIERS.ADDRESS2 AS address2, ";	
	$sql .= "SUPPLIERS.CITY AS city, ";
	$sql .= "SUPPLIERS.STATE AS state, ";
	$sql .= "SUPPLIERS.ZIP AS zipcod, ";
	$sql .= "PURCHASEORDERS.STATUS AS status ";
    $sql .= "FROM PURCHASEORDERS JOIN SUPPLIERS ON PURCHASEORDERS.SUPPLIERID = SUPPLIERS.ID ";
	$sql .= "WHERE ";
	$sql .= $sql_where;
	$sql .= "ORDER BY PURCHASEORDERS.CREATIONDATE DESC, PURCHASEORDERS.ID DESC ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $po_list = mysql_call_api($sql);
       
    return($po_list);
}

function get_po_items($param){

    $sql  = "SELECT ";
	$sql .= "'update' AS ACTION, (POITEMS.QTY - COALESCE(POITEMS.QTYRECEIVED, 0)) AS QTYTORECEIVE, POITEMS.QTYRECEIVED, POITEMS.QTY, INVENTORY.SKU AS BUMSKU, POITEMS.VENDORSKU, INVENTORY.DESCRIPTION, POITEMS.PRICE, (POITEMS.QTY * POITEMS.PRICE) AS AMOUNT, POITEMS.DUEDATE, POITEMS.STATUS ";
    $sql .= "FROM POITEMS JOIN INVENTORY ";
	$sql .= "WHERE ";
	$sql .= "POITEMS.INVENTORYID = INVENTORY.ID ";
	$sql .= "AND POITEMS.PURCHASEORDERSID = '$param' ";
	$sql .= "ORDER BY POITEMS.ID ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $po_items = mysql_call_api($sql);
       
    return($po_items);
}

function get_po_low_stock_items($supplierid){
	date_default_timezone_set("America/New_York");

	// Get backordered items matching the given the supplier
	$bobysupp_query  = "SELECT ";
	$bobysupp_query .= "SUM(i.QTYREORDER) AS QTYREORDER, ";
	$bobysupp_query .= "i.ID,";
	$bobysupp_query .= "i.SKU AS BUSKU, "; 
	$bobysupp_query .= "i.VENDORSKU, "; 
	$bobysupp_query .= "i.DESCRIPTION, "; 
	$bobysupp_query .= "i.COST AS PRICE, "; 
	$bobysupp_query .= "s.TRANSITTIME "; 
	$bobysupp_query .= "FROM INVENTORY i, SUPPLIERS s "; 
	$bobysupp_query .= "WHERE "; 
	$bobysupp_query .= "s.ID=i.SUPPLIERID "; 
	$bobysupp_query .= "AND s.ID='$supplierid' "; 
	$bobysupp_query .= "AND i.QTYSTOCK < i.QTYREORDER ";
	$bobysupp_query .= "GROUP BY i.SKU";

	$bobysupp_result = mysql_call_api($bobysupp_query);
	
	if ($bobysupp_result)
	{
		$data = array();
		$j = 0;
		for($i=0; $i<sizeof($bobysupp_result); $i++){
			$subtotal = 0;
			$borowID = $bobysupp_result[$i]['id'];
			$borowQTYREORDER = $bobysupp_result[$i]['qtyreorder'];
			$borowPRICE = $bobysupp_result[$i]['price'];
			$borowTRANSITTIME = $bobysupp_result[$i]['transittime'];
			$borowBUSKU = $bobysupp_result[$i]['busku'];
			$borowVENDORSKU = $bobysupp_result[$i]['vendorsku'];
			$borowDESCRIPTION = $bobysupp_result[$i]['description'];

			$qtyReorder=$borowQTYREORDER;
					
			// Check how many of these inventoryid are already in a PO
			
			$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowID' AND STATUS IN ('OPEN', 'PARTIAL') ";
			$poinv_result = mysql_call_api($poinv_query);

			$porowQTY = $poinv_result[0]['qty'];
	
			if( $porowQTY > 0 ) {
				$qtyReorder-=$porowQTY;
			}
			if($qtyReorder < 0){
				$qtyReorder = 0;
			}
				
			$price=$borowPRICE;
			$subtotal += $price * $qtyReorder;
			$duedate  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d") + $borowTRANSITTIME, date("Y")));
			
			//Populate final array here
			if($qtyReorder > 0){
				$data[$j]['qtyreceived'] = 0;
				$data[$j]['qty'] = $qtyReorder;
				$data[$j]['bumSku'] = $borowBUSKU;
				$data[$j]['vendorSku'] = $borowVENDORSKU;
				$data[$j]['description'] = $borowDESCRIPTION;
				$data[$j]['price'] = $borowPRICE;
				$data[$j]['amount'] = $subtotal;
				$data[$j]['dueDate'] = $duedate;
				$j++;
			}			
		} //for
		return ($data);
	} //if ($bobysupp_result)
	else
		return null;

}

function update_po($poData, $employeeid)
{
	//$poData_json = json_decode($poData);
	
	$poId = $poData['poId'];
	$dueDate = $poData['poDueDate'];
	$status = $poData['poStatus'];
	$total = $poData['poTotal'];
	$notes = $poData['poNotes'];
	if($notes == "null"){
		$notes = NULL;
	}
	
	lock_table('PURCHASEORDERS');
	$sql  = "UPDATE PURCHASEORDERS SET ";
	$sql .= "EMPLOYEEID='$employeeid', ";
	$sql .= "DUEDATE='$dueDate', ";
	$sql .= "STATUS='$status', ";
	$sql .= "TOTAL='$total', ";
	$sql .= "NOTES='$notes' ";
	$sql .= "WHERE ";
	$sql .= "ID='$poId' ";

	$data = mysql_call_api($sql);
    unlock_tables();
	
    return($data);
}

function update_po_items($poData, $param){

	$poId = $poData['poId'];
	$poStatus = $poData['poStatus'];
	
	$poitems = json_decode($param);
	
	for($i=0; $i < sizeof($poitems); $i++) {
		//if( $poitems[$i]->qty > 0 ) {
			$findinv_query = "SELECT * FROM INVENTORY WHERE SKU='" . $poitems[$i]->bumsku . "'";
			
			$findinv_result = mysql_call_api($findinv_query);
			
			$action 		= $poitems[$i]->action;
			
			$oi_invid		= $findinv_result[0]['id'];
			$oi_vendorsku	= $findinv_result[0]['vendorsku'];
			$oi_price		= $poitems[$i]->price;
			$oi_amount		= $poitems[$i]->price * $poitems[$i]->qty;
			$oi_qty			= $poitems[$i]->qty;
			$oi_qtyreceived	= $poitems[$i]->qtyreceived;
			$oi_duedate		= $poitems[$i]->duedate;
			$oi_status		= $poitems[$i]->status;

			//get qty before update
			$findoitems  = "SELECT QTY FROM POITEMS ";
			$findoitems .= "WHERE PURCHASEORDERSID='$poId' ";
			$findoitems .= "AND INVENTORYID='$oi_invid'";
			$resultoitems = mysql_call_api($findoitems);

			//update poitems
			lock_table('POITEMS');
			if($action == "insert"){
				$query  = "INSERT INTO POITEMS SET ";
				$query .= "DUEDATE='$oi_duedate', QTY='$oi_qty', ";
				$query .= "VENDORSKU='$oi_vendorsku', ";
				$query .= "AMOUNT='$oi_amount', PRICE='$oi_price', STATUS='$oi_status', ";
				$query .= "PURCHASEORDERSID='$poId', INVENTORYID='$oi_invid'";
			}
			else{
				$query  = "UPDATE POITEMS SET ";
				$query .= "DUEDATE='$oi_duedate', QTY='$oi_qty', ";
				$query .= "VENDORSKU='$oi_vendorsku', ";
				$query .= "AMOUNT='$oi_amount', PRICE='$oi_price', STATUS='$oi_status' ";
				$query .= "WHERE PURCHASEORDERSID='$poId' AND INVENTORYID='$oi_invid'";
			}
			$result = mysql_call_api($query);
			unlock_tables();
			

			//calculate the difference between before and after update
			$diff = 0;
			if(isset($resultoitems[0]))
				$diff = $resultoitems[0]['qty'] - $oi_qty;

			////// Update here Inventory On Order Items
			lock_table('INVENTORY');
			$query = "UPDATE INVENTORY SET ONORDER = ABS(ONORDER - $diff) WHERE ID = '$oi_invid'";
			$addresult = mysql_call_api($query);
			unlock_tables();				


			/*
			//WAP: Need to review this code
			// Get backordered items matching the given inventoryid
			$orderitems_query="SELECT ID, QTYBO FROM ORDERITEMS WHERE INVENTORYID ='$oi_invid' AND STATUS='BACKORDERED' AND PURCHASEORDERID IS NULL ORDER BY DATE";
			$orderitems_result = mysql_call_api($orderitems_query);

			if ($orderitems_result)
			{
				$onorder_residual = $oi_qty;
				for($i=0; sizeof($orderitems_result)-1; $i++){
					$oirowID = $orderitems_result[0]['ID'];
					$oirowQTYBO = $orderitems_result[0]['qtybo'];
					
					$onorder_residual -= $oirow['qtybo'];
					
					if($onorder_residual > 0){
						////// Update here OrderItems OnOrder Items
						lock_table('ORDERITEMS');
						$query = "UPDATE ORDERITEMS SET QTYONORDER = '$oirowQTYBO' , PURCHASEORDERID = '$PurchaseOrderID', ESTIMATEDRXDATE = '$oi_duedate' WHERE ID = '$oirowID'";
						$query_result = mysql_call_api($query);
						unlock_tables();							
					}		
				}
			}
			*/
		//}
	} // for


///////////////////////////
	$data = array();
	return($data);

	
}

function delete_po_item($poData, $bumsku){

	$poId = $poData['poId'];
	
	$findinv_query = "SELECT * FROM INVENTORY WHERE SKU='" . $bumsku . "'";
	
	$findinv_result = mysql_call_api($findinv_query);

	$oi_invid		= $findinv_result[0]['id'];


	//get qty before delete
	$findoitems  = "SELECT QTY FROM POITEMS ";
	$findoitems .= "WHERE PURCHASEORDERSID='$poId' ";
	$findoitems .= "AND INVENTORYID='$oi_invid'";
	$resultoitems = mysql_call_api($findoitems);

	//update poitems
	lock_table('POITEMS');

	$query  = "DELETE FROM POITEMS ";
	$query .= "WHERE PURCHASEORDERSID='$poId' AND INVENTORYID='$oi_invid'";

	$result = mysql_call_api($query);
	unlock_tables();
	

	//calculate the difference between before and after update
	$diff = 0;
	if(isset($resultoitems[0]))
		$diff = $resultoitems[0]['qty'];

	////// Update here Inventory On Order Items
	lock_table('INVENTORY');
	$query = "UPDATE INVENTORY SET ONORDER = ABS(ONORDER - $diff) WHERE ID = '$oi_invid'";
	$addresult = mysql_call_api($query);
	unlock_tables();				

	$data = array();
	return($data);

}

function delete_po($poData){

	$poId = $poData['poId'];
	
	//delete purchaseorder
	lock_table('PURCHASEORDERS');

	$query  = "DELETE FROM PURCHASEORDERS ";
	$query .= "WHERE ID='$poId'";

	$result = mysql_call_api($query);
	unlock_tables();

	$data = array();
	return($data);
}

function get_po_info($id){
    
    $sql  = "SELECT ";
	$sql .= "p.ID, ";
	$sql .= "p.SUPPLIERID, ";
	$sql .= "p.STATUS, ";
	$sql .= "p.CREATIONDATE, ";
	$sql .= "p.DUEDATE, ";
	$sql .= "p.NOTES, ";
	$sql .= "p.EMPLOYEEID, ";
	$sql .= "p.TOTAL, ";
	$sql .= "s.NAME, ";
	$sql .= "s.ADDRESS1, ";
	$sql .= "s.ADDRESS2, ";
	$sql .= "s.CITY, ";
	$sql .= "s.STATE, ";
	$sql .= "s.ZIP ";
    $sql .= "FROM PURCHASEORDERS p, SUPPLIERS s ";
	$sql .= "WHERE p.SUPPLIERID = s.ID ";
	$sql .= "AND p.ID = '$id' ";
    //echo $sql;

    $po_info = mysql_call_api($sql);
       
    return($po_info);
}

function receive_po_items($poData, $param){
	
	$poId = $poData['poId'];
	$poStatus = $poData['poStatus'];
	
	$poitems = json_decode($param);
//print_r($poitems);	
	for($i=0; $i < sizeof($poitems); $i++) {
		if( $poitems[$i]->qtytoreceive > 0 ) {
			$findinv_query = "SELECT * FROM INVENTORY WHERE SKU='" . $poitems[$i]->bumsku . "'";
			$findinv_result = mysql_call_api($findinv_query);

			$oi_invid		= $findinv_result[0]['id'];
			$oi_vendorsku	= $findinv_result[0]['vendorsku'];
			$oi_price		= $poitems[$i]->price;
			$oi_amount		= $poitems[$i]->price * $poitems[$i]->qty;
			$oi_qty			= $poitems[$i]->qty;
			$oi_qtyreceived	= $poitems[$i]->qtytoreceive;
			$oi_duedate		= $poitems[$i]->duedate;
			$oi_status		= "RECEIVED";

			//get qty before update
			//$findoitems  = "SELECT QTY FROM POITEMS ";
			//$findoitems .= "WHERE PURCHASEORDERSID='$poId' ";
			//$findoitems .= "AND INVENTORYID='$oi_invid'";
			//$resultoitems = mysql_call_api($findoitems);

			//update poitems
			lock_table('POITEMS');
			$query  = "UPDATE POITEMS SET ";
			//$query .= "DUEDATE='$oi_duedate', QTY='$oi_qty', ";
			$query .= "DUEDATE='$oi_duedate', ";
			//$query .= "VENDORSKU='$oi_vendorsku', ";
			//$query .= "AMOUNT='$oi_amount', PRICE='$oi_price', STATUS='$oi_status', ";
			$query .= "QTYRECEIVED = COALESCE(QTYRECEIVED, 0) + $oi_qtyreceived, ";
			$query .= "DATERX=NOW(), LASTRXDATE=NOW() ";
			$query .= "WHERE PURCHASEORDERSID='$poId' AND INVENTORYID='$oi_invid'";
//echo $query;
			$result = mysql_call_api($query);
			unlock_tables();
			

			//calculate the difference between before and after update
			//$diff = $resultoitems[0]['qty'] - $result[0]['qty'];

			////// Update here Inventory On Order Items
			lock_table('INVENTORY');
			$query = "UPDATE INVENTORY SET QTYSTOCK = (QTYSTOCK + $oi_qtyreceived), ONORDER = ABS(ONORDER - $oi_qtyreceived) WHERE ID = '$oi_invid'";
			//echo $query;			
			$addresult = mysql_call_api($query);
			unlock_tables();				


			////Reconcile inventory after receiving.
			reconcile_inventory($oi_invid);
			
			////WAP: Close all POs with no open items
			

			/*
			//WAP: Need to review this code
			// Get backordered items matching the given inventoryid
			$orderitems_query="SELECT ID, QTYBO FROM ORDERITEMS WHERE INVENTORYID ='$oi_invid' AND STATUS='BACKORDERED' AND PURCHASEORDERID IS NULL ORDER BY DATE";
			$orderitems_result = mysql_call_api($orderitems_query);

			if ($orderitems_result)
			{
				$onorder_residual = $oi_qty;
				for($i=0; sizeof($orderitems_result)-1; $i++){
					$oirowID = $orderitems_result[0]['ID'];
					$oirowQTYBO = $orderitems_result[0]['qtybo'];
					
					$onorder_residual -= $oirow['qtybo'];
					
					if($onorder_residual > 0){
						////// Update here OrderItems OnOrder Items
						lock_table('ORDERITEMS');
						$query = "UPDATE ORDERITEMS SET QTYONORDER = '$oirowQTYBO' , PURCHASEORDERID = '$PurchaseOrderID', ESTIMATEDRXDATE = '$oi_duedate' WHERE ID = '$oirowID'";
						$query_result = mysql_call_api($query);
						unlock_tables();							
					}		
				}
			}
			*/
		}
	} // for

	$data = array();
	return($data);
}

function get_open_po_list($param){

	$param_json = json_decode($param);
	
	$sql_where = "PURCHASEORDERS.ID > 0 ";
	
	if(isset($param_json->INFO)&& $param_json->INFO != ''){
		//$name = $param_json->INFO;
		$sql_where = "CONCAT(TRIM(LEADING '0' FROM PURCHASEORDERS.ID), ' ', SUBSTR(CREATIONDATE, 1, 10), ' ', SUPPLIERS.NAME) LIKE '%" . $param_json->INFO . "%' ";
	}
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->POID)&& $param_json->POID != ''){
			$sql_where .= "AND PURCHASEORDERS.ID = " . $param_json->POID . " ";
		}
		if(isset($param_json->SUPPLIERID)&& $param_json->SUPPLIERID != ''){
			$sql_where .= "AND SUPPLIERID = " . $param_json->SUPPLIERID . " ";
		}
		if(isset($param_json->STATUS)){
			$sql_where .= "AND PURCHASEORDERS.STATUS LIKE '%" . $param_json->STATUS . "%' ";
		}		
	}
	else{
		$sql_where .= "AND PURCHASEORDERS.STATUS IN ('OPEN', 'PARTIAL') ";
	}
	
    $sql  = "SELECT ";
	$sql .= "TRIM(LEADING '0' FROM PURCHASEORDERS.ID) AS poid, ";
	$sql .= "SUBSTR(CREATIONDATE, 1, 10) AS creationdate, "; 
	$sql .= "SUBSTR(DUEDATE, 1, 10) AS duedate, ";
	$sql .= "SUPPLIERS.ID AS supplierid, ";
	$sql .= "SUPPLIERS.NAME AS supplier, ";
	$sql .= "SUPPLIERS.ADDRESS1 AS address1, ";
	$sql .= "SUPPLIERS.ADDRESS2 AS address2, ";	
	$sql .= "SUPPLIERS.CITY AS city, ";
	$sql .= "SUPPLIERS.STATE AS state, ";
	$sql .= "SUPPLIERS.ZIP AS zipcod, ";
	$sql .= "PURCHASEORDERS.STATUS AS status ";
    $sql .= "FROM PURCHASEORDERS JOIN SUPPLIERS ON PURCHASEORDERS.SUPPLIERID = SUPPLIERS.ID ";
	$sql .= "WHERE ";
	$sql .= $sql_where;
	$sql .= "ORDER BY PURCHASEORDERS.CREATIONDATE DESC, PURCHASEORDERS.ID DESC ";
	//$sql .= "LIMIT 100";
 	//echo $sql;
    
    $po_list = mysql_call_api($sql);
       
    return($po_list);
}

function update_inventorypo($invId)
{
	//$poData_json = json_decode($poData);
	
	$poId = $poData['poId'];
	$dueDate = $poData['poDueDate'];
	$status = $poData['poStatus'];
	$total = $poData['poTotal'];
	$notes = $poData['poNotes'];
	if($notes == "null"){
		$notes = NULL;
	}
	
	lock_table('PURCHASEORDERS');
	$sql  = "UPDATE PURCHASEORDERS SET ";
	$sql .= "EMPLOYEEID='$employeeid', ";
	$sql .= "DUEDATE='$dueDate', ";
	$sql .= "STATUS='$status', ";
	$sql .= "TOTAL='$total', ";
	$sql .= "NOTES='$notes' ";
	$sql .= "WHERE ";
	$sql .= "ID='$poId' ";

	$data = mysql_call_api($sql);
    unlock_tables();
	
    return($data);
}

function reconcile_inventory($invId){
	$ch = curl_init("http://192.168.1.194/poise/Admin/API_ReconcileSKU_flpoise.php?");
	$curl_param = "&InvID=" . $invId;
	
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_param);
	
	curl_exec($ch);
	curl_close($ch);

}

function get_accounts_list($param){

	$param_json = json_decode($param);
	
	$sql_where = "ID > 0 "; 
	
	if(isset($param_json->INFO)&& $param_json->INFO != ''){
		$sql_where = "NAME LIKE '%" . $param_json->INFO . "%' ";
	}	
	
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->NAME) && $param_json->NAME != ''){
			$sql_where .= "AND NAME LIKE '%" . $param_json->NAME . "%' ";
		}
		if(isset($param_json->STATE) && $param_json->STATE != ''){
			$sql_where .= "AND STATE = '" . $param_json->STATE . "' ";
		}
		if(isset($param_json->STATUS)){
			$sql_where .= "AND STATUS = '" . $param_json->STATUS . "' ";
		}	
	}

    $sql  = "SELECT * ";
    $sql .= "FROM ACCOUNTS ";
	$sql .= "WHERE ";
	$sql .= $sql_where;
	$sql .= "ORDER BY name";
 	//echo $sql;
    
    $accounts_list = mysql_call($sql);
       
    return($accounts_list);
}

function get_accounts_info($id){
    
    $sql  = "SELECT * ";
    $sql .= "FROM ACCOUNTS ";
	$sql .= "WHERE id = '$id' ";
	//$sql .= "ORDER BY name";
    
    //echo $sql;

    $accounts_info = mysql_call($sql);
       
    return($accounts_info);
}

function get_customers_list($param){

	ini_set('memory_limit', '1024M'); // or you could use 1G

	$param_json = json_decode($param);
	
	$sql_where = "ID > 0 "; 
	
	if(isset($param_json->INFO)&& $param_json->INFO != ''){
		$sql_where = "NAME LIKE '%" . $param_json->INFO . "%' ";
	}	
	
	
	if(isset($param_json->FILTER)&& $param_json->FILTER == true){
		if(isset($param_json->NAME) && $param_json->NAME != ''){
			$sql_where .= "AND NAME LIKE '%" . $param_json->NAME . "%' ";
		}
		if(isset($param_json->STATE) && $param_json->STATE != ''){
			$sql_where .= "AND STATE = '" . $param_json->STATE . "' ";
		}
		if(isset($param_json->STATUS)){
			$sql_where .= "AND STATUS = '" . $param_json->STATUS . "' ";
		}	
	}

    $sql  = "SELECT * ";
    $sql .= "FROM CUSTOMERS ";
	$sql .= "WHERE ";
	$sql .= $sql_where;
	$sql .= "ORDER BY name ";
	$sql .= "LIMIT 100";
 	//echo $sql;
    
    $customers_list = mysql_call($sql);
       
    return($customers_list);
}

function get_customers_info($id){
    
    $sql  = "SELECT * ";
    $sql .= "FROM CUSTOMERS ";
	$sql .= "WHERE id = '$id' ";
	//$sql .= "ORDER BY name";
    
    //echo $sql;

    $customers_info = mysql_call($sql);
       
    return($customers_info);
}

function get_users_info($id){
    
    $sql  = "SELECT * ";
    $sql .= "FROM EMPLOYEE ";
	$sql .= "WHERE id = '$id' ";
	//$sql .= "ORDER BY name";
    
    //echo $sql;

    $users_info = mysql_call($sql);
       
    return($users_info);
}

function get_orders_list($param){

	$param_json = json_decode($param);
	
    $sql  = "SELECT ORDERS.*, CUSTOMERS.NAME ";
    $sql .= "FROM ORDERS, CUSTOMERS ";
	$sql .= "WHERE ";
	$sql .= "ORDERS.SHIPTOID = CUSTOMERS.ID ";
	$sql .= "ORDER BY name";
 	//echo $sql;
    
    $orders_list = mysql_call($sql);
       
    return($orders_list);
}

function get_query($host, $user, $password, $db, $query, $method){
	$data = mysql_call($query);
	return($data);
}
?>