<?php header('Access-Control-Allow-Origin: *'); ?>

<?php
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
//echo $ip;	
	
if($ip == "192.168.169.1" || $ip == "192.168.1.14"){
	//include '../models/mysql/flpoise_db_functions.php';
	include '../models/mysql/db_functions.php';
}
else{
	//include '../models/mysql/flpoise_db_functions.php';
	include '../models/mysql/db_functions.php';
}

?>

<?php
$fn = $_GET['fn'];
$param = $_GET['param'];

if(isset($_GET['supId'])){
	$supId = $_GET['supId'];
}
if(isset($_GET['userName'])){
	$userName = $_GET['userName'];
}
if(isset($_GET['status'])){
	$status = $_GET['status'];
}
if(isset($_GET['bumsku'])){
	$bumsku = $_GET['bumsku'];
}
if(isset($_GET['poId'])){
	$poData = array();
	$poData['poId'] =  $_GET['poId'];
}
if(isset($_GET['poDate'])){
	$poData['poDate'] =  $_GET['poDate'];
}
if(isset($_GET['poDueDate'])){
	$poData['poDueDate'] =  $_GET['poDueDate'];
}
if(isset($_GET['poStatus'])){
	$poData['poStatus'] =  $_GET['poStatus'];
}
if(isset($_GET['poTotal'])){
	$poTotal = $_GET['poTotal'];
	$poData['poTotal'] =  $_GET['poTotal'];
}
if(isset($_GET['poNotes'])){
	$poData['poNotes'] =  $_GET['poNotes'];
}

if($fn == 'search_supplier'){
	$suppliers = get_suppliers_list($param);

	$options = json_encode($suppliers);
	
	echo $options;
}
else if($fn == 'search_supplier_bo'){
	$suppliers = get_suppliers_list_bo($param);

	$options = json_encode($suppliers);
	
	echo $options;
}
else if($fn == 'search_supplier_low_stock'){
	$suppliers = get_suppliers_list_low_stock($param);

	$options = json_encode($suppliers);
	
	echo $options;
}
else if( $fn == 'supplier_info'){
	$suppliers = get_suppliers_info($param);
	
	echo json_encode($suppliers[0]);
}
else if($fn == 'save_supplier_info'){
	$supplier_id = save_suppliers_info($param);
	
	if($supplier_id == 0){
		$supplier = json_decode($param);
		$supplier_id = $supplier->id;
	}
	
	echo $supplier_id;
}
else if($fn == 'delete_supplier_info'){
	$success = delete_suppliers_info($param);
	
	echo $success;
}
else if($fn == 'search_states'){
	$states = get_states_list($param);
	
	$options = json_encode($states);
	
	echo $options;
}
else if($fn == 'search_employees'){
	$employees = get_employees_list($param);
	
	$options = json_encode($employees);
	
	echo $options;
}	
else if($fn == 'search_po'){
	$po = get_po_list($param);

	$options = json_encode($po);
	
	echo $options;
}
else if($fn == 'search_open_po'){
	$po = get_open_po_list($param);

	$options = json_encode($po);
	
	echo $options;
}
else if( $fn == 'po_info'){
	$po = get_po_info($param);
	
	echo json_encode($po[0]);
}
else if( $fn == 'bum_info'){
	$bum = get_bum_info();
	
	echo json_encode($bum[0]);
}
else if($fn == 'search_po_shipvia'){
	$shipvia = get_po_shipvia_list($param);
	
	$options = json_encode($shipvia[0]);
	
	echo $options;
}
else if($fn == 'search_po_payment_type'){
	$payment_type = get_po_payment_type_list($param);
	
	$options = json_encode($payment_type);
	
	echo $options;
}
else if($fn == 'get_po_duedate'){
	$po_duedate = get_po_duedate($param);
	
	$options = json_encode($po_duedate[0]);
	
	echo $options;
}
else if($fn == 'po_get_items'){
	$po_items = get_po_items($param);

	$options = json_encode($po_items);
	
	echo $options;
}
else if($fn == 'po_get_items_low_stock'){
	$po_items = get_po_low_stock_items($param);

	$options = json_encode($po_items);
	
	echo $options;
}
else if($fn == 'po_get_items_bo'){
	$po_items = get_po_items_bo($param);

	$options = json_encode($po_items);
	
	echo $options;
}
else if($fn == 'get_users'){
	$users = get_users_list($param);
	
	echo json_encode($users);
}
else if( $fn == 'users_info'){
	$user = get_users_info($param);
	
	echo json_encode($user[0]);
}
else if($fn == 'verify_login'){
	
	include '../../assets/json-web-token/jwt.php';

	$user = verify_login($param);
	//print_r($user);
	
	$jwt = array();
	
	if($user){
		$secret = '1709171019';
		$payload = array();
		$payload['iat'] = time();
		$payload['exp'] = time() + 30*60; //30min
		$payload['iss'] = "POISE";
		$payload['user_id'] = $user[0]['userid'];
		$payload['user_name'] = $user[0]['firstname'];
		$payload['user_email'] = $user[0]['email'];
		$isadmin = false;
		if($user[0]['allowadmin'] == 'YES')
			$isadmin = true;
		
		$payload['user_isadmin'] = $isadmin;
		
		$jwt['token'] = create_jwt($secret, $payload);
	}
	else
		$jwt['token'] = null;
		
	echo json_encode($jwt);
}
else if($fn == 'stats_new_customers'){
	$data = stats_new_customers($param);

	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'stats_new_orders'){
	$data = stats_new_orders($param);

	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'statistics'){
	$data = array();
	$data[] = stats_new_customers($param);
	$data[] = stats_new_orders($param);
	$data[] = stats_revenue($param);
 	$data[] = stats_pending_orders($param);
	$data[] = stats_shipped_orders($param);
	
	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'create_po'){
	$employee = get_employeeId($userName);

	$PurchaseOrderID = add_po_to_db($supId, $employee[0]['id'], $status, $poTotal);
 	
	//$data = array();
	$data = add_po_items($PurchaseOrderID, $param, $status);

	$json_data = json_encode($data);
	
	echo $PurchaseOrderID;
}
else if($fn == 'create_empty_po'){
	$employee = get_employeeId($userName);

	$PurchaseOrderID = add_po_to_db($supId, $employee[0]['id'], $status, $poTotal);
 	
	//$data = array();
	//$json_data = json_encode($data);
	
	echo $PurchaseOrderID;
}
else if($fn == 'save_po'){
	$employee = get_employeeId($userName);
	
	update_po($poData, $employee[0]['id']);

	update_po_items($poData, $param);

	$data = array();
	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'save_po_items'){
	$employee = get_employeeId($userName);

	update_po_items($poData, $param);

	$data = array();
	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'delete_po_item'){
	$employee = get_employeeId($userName);
	
	delete_po_item($poData, $bumsku);
	
	update_po($poData, $employee[0]['id']);

	//update_po_items($poData, $param);

	$data = array();
	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'delete_po'){
	//$employee = get_employeeId($userName);
	//$bumskulist = array();
	$bumskulist = get_po_items($poData['poId']);
	
	for($i=0; $i<sizeof($bumskulist); $i++){
		delete_po_item($poData, $bumskulist[$i]['bumsku']);
	}
	
	$bumskulistafter = get_po_items($poData['poId']);
	if(sizeof($bumskulistafter) == 0)
		delete_po($poData);


	$data = array();
	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'get_po_info'){
	$po_items = get_po_info($param);

	$options = json_encode($po_items);
	
	echo $options;
}
else if($fn == 'get_po_info_receipt'){
	$po_info = get_po_info_receipt($param);

	$data = json_encode($po_info);
	
	echo $data;
}
else if($fn == 'receive_po'){
	//print_r($param);
	
	$employee = get_employeeId($userName);
	
	//receive_po($poData, $employee[0]['id']);

	receive_po_items($poData, $param);

	$data = array();
	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'get_inventory_info'){
	$data = get_inventoryInfo($param);

	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'get_inventory_info_not_po'){
	$data = get_inventoryInfoNotPo($poData['poId'], $param);

	$json_data = json_encode($data);
	
	echo $json_data;
}
else if($fn == 'get_suppliers_list'){
	$suppliers = get_suppliers_list($param);

	$options = json_encode($suppliers);
	
	echo $options;
}
else if($fn == 'get_accounts_list'){
	$accounts = get_accounts_list($param);

	$options = json_encode($accounts);
	
	echo $options;
}
else if( $fn == 'account_info'){
	$accounts = get_accounts_info($param);
	
	echo json_encode($accounts[0]);
}
else if($fn == 'get_customers_list'){
	$customers = get_customers_list($param);

	$options = json_encode($customers);
	
	echo $options;
}
else if( $fn == 'customer_info'){
	$customers = get_accounts_info($param);
	
	echo json_encode($customers[0]);
}
else if($fn == 'get_orders_list'){
	$orders = get_orders_list($param);

	$options = json_encode($orders);
	
	echo $options;
}
/* End of file Ajax.php */
/* Location: ./application/controllers/Ajax.php */

?>