<?php
$employeeid = 1006;
$supplierid = 224;
$CreatePO = true;
$totalpoitems = 3;


$post_invid = array();
$post_invid[] = 109400;
$post_invid[] = 109401;
$post_invid[] = 109402;

$post_boqty = array();
$post_boqty[] = '2';
$post_boqty[] = '3';
$post_boqty[] = '4';

$post_price = array();
$post_price[] = '15';
$post_price[] = '20';
$post_price[] = '25';

$post_dateyear = array();
$post_dateyear[] = '2022';
$post_dateyear[] = '2022';
$post_dateyear[] = '2022';

$post_datemonth = array();
$post_datemonth[] = '05';
$post_datemonth[] = '05';
$post_datemonth[] = '05';

$post_dateday = array();
$post_dateday[] = '09';
$post_dateday[] = '09';
$post_dateday[] = '09';




date_default_timezone_set("America/New_York");

include 'db_functions.php';



	$errormsg = "";
//	$prevsku = $newsku;
//	$venprevsku = $vendornewsku;
//	$prevqty = $newqty;
//	$prevprice = $newprice;

	
	$query = "SELECT * FROM SUPPLIERS WHERE ID='$supplierid'";
	$suppliers_list = mysql_call($query);

	$SupplierName = $suppliers_list[0]['NAME'];


	$emptypo = false;



//load_bo_items($supplierid);

//load_minimum_level_items($supplierid);


	if ( $CreatePO ) {
		// check for an empty PO
		$emptypo = true;
		if( $totalpoitems > 0 ) {
			for($i=1; $i <= $totalpoitems; $i++) {
				if( $post_boqty[$i] > 0 ) {
					$emptypo = false;
					break;
				}
			}
		}

		if( !$emptypo ) {
			$lockquery = "LOCK TABLES PURCHASEORDERS WRITE, POITEMS READ";
			$lockresult = mysql_call($lockquery);

			$status = 'OPEN';

			$PurchaseOrderID=add_po_to_db($supplierid, $employeeid, $status);

			$unlockquery = "UNLOCK TABLES";
			$unlockresult = mysql_call($unlockquery);

			echo "Purchase Order #: $PurchaseOrderID";

		}
	}


//load_bo_items($supplierid);


if( $CreatePO && !$emptypo ) {
	add_po_items($PurchaseOrderID, $totalpoitems, $post_invid, $post_boqty, $post_price, $post_dateyear, $post_datemonth, $post_dateday, $status);
}	

?>




<?php
///////////////////////////////
// FUNCTIONS //////////////////


function load_bo_items($supplierid){
	
	echo "<pre>"; echo "Loading BO items...";

	$skulist = '';
	$rownr = 0;
	$subtotal = 0;
	$notnecessary = 0;

	// Get backordered items matching the given the supplier
	$bobysupp_query="SELECT SUM(oi.QTYBO) QTYBO, inv.ID ID, inv.SKU BUSKU, inv.VENDORSKU VENDORSKU, inv.DESCRIPTION DESCRIPTION, inv.COST PRICE, sup.TRANSITTIME TRANSITTIME FROM ORDERS o, ORDERITEMS oi, INVENTORY inv, SUPPLIERS sup WHERE oi.ORDERID = o.ID AND sup.ID='$supplierid' AND sup.ID=inv.SUPPLIERID AND inv.ID=oi.INVENTORYID AND oi.STATUS='BACKORDERED' AND o.STATUS NOT IN ('OPEN') GROUP BY inv.SKU";
	$bobysupp_result = mysql_call($bobysupp_query);

echo "<pre>"; echo "Loading BO items...";
echo "<pre>"; print_r($bobysupp_result);	
	if ($bobysupp_result)
	{
		
		$bobysupplier = array();
		$j = 0;
		for($i=0; $i<sizeof($bobysupp_result)-1; $i++){
			$borow = array();
			$borow['ID'] = $bobysupp_result[$i]['ID'];
			$borow['QTYBO'] = $bobysupp_result[$i]['QTYBO'];
			$borow['PRICE'] = $bobysupp_result[$i]['PRICE'];
			$borow['TRANSITTIME'] = $bobysupp_result[$i]['TRANSITTIME'];
			$borow['BUSKU'] = $bobysupp_result[$i]['BUSKU'];
			$borow['VENDORSKU'] = $bobysupp_result[$i]['VENDORSKU'];
			$borow['DESCRIPTION'] = $bobysupp_result[$i]['DESCRIPTION'];

			echo "<pre>" . $borow['ID'];
			
		
				//Check if this backordered item is also below minimum levels
				$belowsupp_query="SELECT QTYREORDER FROM INVENTORY WHERE ID='$borow[ID]' AND QTYSTOCK < QTYREORDER";
				$belowsupp_result = mysql_call($belowsupp_query);

echo "<pre>"; echo "Loading Minimum level items...";
//echo "<pre>" . 	var_dump($belowsupp_result);						

			
				$qtybo=$borow['QTYBO'];
				if ($belowsupp_result){
echo "<pre>" . 	"QTY= " . $belowsupp_result[0]['QTYREORDER'];
				
					$qtybo+=$belowsupp_result[0]['QTYREORDER'];
					///// Build here a list of bos items to be used later
					if ($skulist) $skulist .= ", "; 
					$skulist .= "'" . $borow['BUSKU'] . "'";

				}// if $belowsupp_result
				
				
					//////////////////////						
					// Check how many of these inventoryid are already in a PO
					$borowID = $borow['ID'];
					$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowID' AND STATUS='OPEN'";
					$poinv_result = mysql_call($poinv_query);

echo "<pre>"; echo "Have a PO already...";
//echo "<pre>" . 	var_dump($poinv_result);			
					
					$porow = array();
					$porow['QTY'] = $poinv_result[0]['QTY'];

echo "<pre>" . 	"QTY= " . $porow['QTY'];		
	
					if( $porow['QTY'] <= $qtybo ) {
						$notnecessary++;
						$boit = 1;
						$rownr++;
	
						$qtybo-=$porow['QTY'];
						$price=$borow['PRICE'];
				
						$subtotal += $price * $qtybo;
						$duedate  = mktime(0, 0, 0, date("m")  , date("d")+$borow['TRANSITTIME'], date("Y"));
	
	
					} // if qty potems > qty bo				


			//Populate final array here
			
			if($qtybo > 0){
				//$bobysupplier[$j]['ID'] = $borowID;
				$bobysupplier[$j]['qtyreceived'] = 0;
				$bobysupplier[$j]['qty'] = $qtybo;
				$bobysupplier[$j]['bumSku'] = $borow['BUSKU'];
				$bobysupplier[$j]['vendorSku'] = $borow['VENDORSKU'];
				$bobysupplier[$j]['description'] = $borow['DESCRIPTION'];
				$bobysupplier[$j]['price'] = $borow['PRICE'];
				$bobysupplier[$j]['amount'] = $subtotal;
				$bobysupplier[$j]['dueDate'] = $duedate;
				$j++;
			}			
			
		} //for
	} //if ($bobysupp_result)		
	
	echo "<PRE>"; print_r($bobysupplier);
	return ($bobysupplier);

}


function load_minimum_level_items($supplierid){
	
	echo "<pre>"; echo "Loading minimum level items...";
	
	$skulist = '';
	
	// Get bellow minimum levels items matching the given the supplier
	$minsupp_query="SELECT inv.QTYREORDER QTYRO, inv.QTYSTOCK QTYSTOCK, inv.ID ID, inv.SKU BUSKU, inv.VENDORSKU VENDORSKU, inv.DESCRIPTION DESCRIPTION, inv.COST PRICE, sup.TRANSITTIME TRANSITTIME FROM INVENTORY inv, SUPPLIERS sup WHERE sup.ID='$supplierid' AND sup.ID=inv.SUPPLIERID AND inv.QTYSTOCK < inv.QTYREORDER ";
	if($skulist)
		$minsupp_query.="AND inv.SKU NOT IN ($skulist) ";
	$minsupp_query.="GROUP BY inv.SKU";
	$minsupp_result = mysql_call($minsupp_query);

echo "<pre>";	print_r($minsupp_result);	

	if ($minsupp_result)
	{
		$borowID = $minsupp_result[0]['ID'];
		$borowQTYRO = $minsupp_result[0]['QTYRO'];
		$borowQTYSTOCK = $minsupp_result[0]['QTYSTOCK'];
		$borowPRICE = $minsupp_result[0]['PRICE'];
		$borowTRANSITTIME = $minsupp_result[0]['TRANSITTIME'];
		
		// perform a query and see how many items where ordered for this inventoryid
		//$rownr = 0;
		//$subtotal = 0;
		//$notnecessary = 0;

		for($i=0; $i<sizeof($minsupp_result)-1; $i++){
			// Check how many of these inventoryid are already in a PO
			$poinv_query="SELECT SUM(QTY) QTY FROM POITEMS WHERE INVENTORYID='$borowID' AND STATUS='OPEN'";
			$poinv_result = mysql_call($poinv_query);

echo "<pre>"; echo "Minimum have a PO already...";
echo "<pre>" . 	var_dump($poinv_result);					
			
			
			$porow=array();
			$porow['QTY'] = $poinv_result[0]['QTY'];

			if( $porow['QTY'] < $borowQTYRO ) {
				$notnecessary++;
				$minit = 1;

				$rownr++;

				$qtybo=$borowQTYRO - $borowQTYSTOCK - $porow['QTY'];
				if ($qtybo < 0) $qtybo = 0;
				$price=$borowPRICE;
		
				if( $ZeroAll ) {
					$qtybo=0;
					$price=0;
				}

				$subtotal += $price * $qtybo;
				$duedate  = mktime(0, 0, 0, date("m")  , date("d")+$borowTRANSITTIME, date("Y"));

			} // if qty potems > qty bo
		} //While
	} //if ($minsupp_result)	
	
}	

function add_po_items($PurchaseOrderID, $totalpoitems, $post_invid, $post_boqty, $post_price, $post_dateyear, $post_datemonth, $post_dateday, $status){
	
	for($i=0; $i <= $totalpoitems-1; $i++) {

		if( $post_boqty[$i] > 0 ) {

			$findinv_query = "SELECT * FROM INVENTORY WHERE ID='" . $post_invid[$i] . "'";
			$findinv_result = mysql_call($findinv_query);
echo "<pre>" . 	var_dump($findinv_result);				
			
			$invrow = array();
			$invrowVendorSku = $findinv_result[0]['VENDORSKU'];
			

			$_lprice		=	$post_price[$i];
			$_lamount		=	$post_price[$i] * $post_boqty[$i];
			$_lboqty		=	$post_boqty[$i];
			$_lduedate		=	$post_dateyear[$i] . $post_datemonth[$i] . $post_dateday[$i];
			$_ldateyear		=	$post_dateyear[$i];
			$_ldatemonth	=	$post_datemonth[$i];
			$_ldateday		=	$post_dateday[$i];

			//insert po item
			$lockquery = "LOCK TABLES POITEMS WRITE";
			$lockresult = mysql_call($lockquery);
echo "<pre>" . 	var_dump($lockresult);					

			$query  = "INSERT INTO POITEMS SET ";
			$query .= "DUEDATE='$_lduedate', QTY='$_lboqty', PURCHASEORDERSID='$PurchaseOrderID'";
			$query .= ", INVENTORYID='" . $post_invid[$i] . "', VENDORSKU='$invrowVendorSku', ";
			$query .= "AMOUNT='$_lamount', PRICE='$_lprice', STATUS='$status'";
			$result = mysql_call($query);
echo "<pre>" . 	var_dump($result);

			$unlockquery = "UNLOCK TABLES";
			$unlockresult = mysql_call($unlockquery);
echo "<pre>" . 	var_dump($unlockresult);					
		
		
			////// Update here Inventory On Order Items
			$lockquery = "LOCK TABLES INVENTORY WRITE";
			$lockresult = mysql_call($lockquery);
echo "<pre>" . 	var_dump($lockresult);
		
			$query = "UPDATE INVENTORY SET ONORDER = ONORDER + '$_lboqty' WHERE ID = '" . $post_invid[$i] . "'";
			$addresult = mysql_call($query);
echo "<pre>" . 	var_dump($addresult);
		
			$unlockquery = "UNLOCK TABLES";
			$unlockresult = mysql_call($unlockquery);
echo "<pre>" . 	var_dump($unlockresult);				
			////////////////////////////////////////////
			
////////////////////
			// Get backordered items matching the given inventoryid
			$orderitems_query="SELECT ID, QTYBO FROM ORDERITEMS WHERE INVENTORYID ='" . $post_invid[$i] . "' AND STATUS='BACKORDERED' AND PURCHASEORDERID IS NULL ORDER BY DATE";
			$orderitems_result = mysql_call($orderitems_query);
echo "<pre>" . 	var_dump($orderitems_result);					

			if ($orderitems_result)
			{
				$onorder_residual = $_lboqty;
				for($i=0; sizeof($orderitems_result)-1; $i++){
					
					$oirowID = $orderitems_result[0]['ID'];
					$oirowQTYBO = $orderitems_result[0]['QTYBO'];
					
					$onorder_residual -= $oirow['QTYBO'];
					
					if($onorder_residual > 0){
						////// Update here OrderItems OnOrder Items
						$lockquery = "LOCK TABLES ORDERITEMS WRITE";
						$lockresult = mysql_call($lockquery);
echo "<pre>" . 	var_dump($lockresult);							
					
						$query = "UPDATE ORDERITEMS SET QTYONORDER = '$oirowQTYBO' , PURCHASEORDERID = '$PurchaseOrderID', ESTIMATEDRXDATE = '$_lduedate' WHERE ID = '$oirowID'";
						$query_result = mysql_call($query);
echo "<pre>" . 	var_dump($query_result);
					
						$unlockquery = "UNLOCK TABLES";
						$unlockresult = mysql_call($unlockquery);
echo "<pre>" . 	var_dump($unlockresult);							

					}		
				}
			}

		} // if( $_POST['boqty_'.$i] > 0 )
	} // for
	
}

?>