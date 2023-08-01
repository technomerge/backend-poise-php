<?php
////////////////////////////////////////////////////
//
// Name:			PDFPackingSlip.php
// Date: 			10/10/03
//  By:			 	Kevin Williams
//	Delk Technologies  									  
//
// 
//
//
//
////////////////////////////////////////////////////

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


date_default_timezone_set('America/New_York');
////////////////////////////////////////////////////
// Standard Header and include files for POISE system...
////////////////////////////////////////////////////

include_once '../../../models/mysql/db_functions.php';

/*
include_once '../lib/config.inc.angular.php';
include_once '../lib/mysql.inc.php';
include_once '../lib/customer.db.inc.php';

	$db = db_connect($host, $user, $pass);
	select_db($db_name);
*/
	
include ('../../../../assets/pdf/class.ezpdf.php');
$pdf = new Cezpdf('LETTER','PORTRAIT');
$pdf->selectFont('../../../../assets/pdf/fonts/Helvetica.afm');
//$pdf->ezSetMargins(30,110,40,40);
$pdf->ezSetMargins(30,30,40,40);

$clientname = "Test";
$shipmentid = $_GET['shipmentid'];

$pdf->addInfo('Title',$clientname.' Packing Slip #'.$shipmentid);
$pdf->addInfo('Author','POISE');
	
//this is where we build and display the Packing Slip as a PDF file...

//setup formatting and options
$shipmentid = substr($shipmentid,-8);

$numOrders = 0;




//find highest inventory ID
//$r=db_query("SELECT MAX(ID) AS ID FROM INVENTORY",$db);
$maxrows = mysql_call("SELECT MAX(ID) AS ID FROM INVENTORY");

echo $maxrows[0]['ID'];
exit();

$maxdim=$maxrows[0]['ID'];
$maxdim = $maxdim + 1;
$maxdim = sprintf("%24d",$maxdim);
//force array size
//$summaryInvQty[$maxdim] = 1;	 // = range(0, (int) $maxdim);
//$summaryInvSKU = "x";	  		 //range(0, (int) $maxdim);			  
//$summaryInvDesc = "x";			 //range(0, (int) $maxdim);
//$summaryOrderIDs = "xx";			 //range(0, (int) $maxdim);


//find orderitems for this shipment, group each order separately
//$oiresult = db_query("SELECT ORDERID FROM ORDERITEMS WHERE SHIPMENTID='$shipmentid' GROUP BY ORDERID",$db);
$oiresult = mysql_call("SELECT ORDERID FROM ORDERITEMS WHERE SHIPMENTID='$shipmentid' GROUP BY ORDERID");

while ($oirow=mysql_fetch_array($oiresult))
{
 $numOrders++;
 if ($numOrders > 1)
   {
   //make a new page since this is a multiple order shipment
   $pdf->ezNewPage();
   }
   
//find shipment info for this shipment
$shipmentresult = db_query("SELECT * FROM SHIPMENT WHERE ID='$shipmentid'",$db);
$shipmentrow=mysql_fetch_array($shipmentresult);

//find order for these orderitems
$orderresult = db_query("SELECT * FROM ORDERS WHERE ID='$oirow[ORDERID]'",$db);
$orderrow=mysql_fetch_array($orderresult);


//find bill to and ship to customers for this order
$shiptoresult = db_query("SELECT * FROM CUSTOMERS WHERE ID='$orderrow[SHIPTOID]'",$db);
$shiptorow=mysql_fetch_array($shiptoresult);

$custresult = db_query("SELECT * FROM CUSTOMERS WHERE ID='$orderrow[CUSTOMERID]'",$db);
$custrow=mysql_fetch_array($custresult);

//find account from bill to 
$orderresult = db_query("SELECT * FROM ACCOUNTS WHERE ID='$custrow[ACCOUNTID]'",$db);
$accountrow=mysql_fetch_array($orderresult);
$showprices = $accountrow[SHOWPRICESONPACKINGSLIP];

//FOR TESTING ONLY...
//$showprices = TRUE;
if ($paymenttypes)
   $paymenttypes = $paymenttypes.','.$orderrow[PAYMENTTYPE];
else
   $paymenttypes = $orderrow[PAYMENTTYPE];

//draw logo and business info centered at top
if($custrow[ACCOUNTID] == 261 or $custrow[ACCOUNTID] == 264 or $custrow[ACCOUNTID] == 277 or $custrow[ACCOUNTID] == 281){ // Only for Pizza Pan, Hurricane and Verizon (Synergy)
	$acc_result = db_query("SELECT * FROM ACCOUNTS WHERE ID IN ('261', '264', '277', '281')",$db);
	$acc_row=mysql_fetch_array($acc_result);
	
	$clientname = $acc_row[ADDRESS1];
	$clientaddress = $acc_row[ADDRESS2];
	$clientcitystatezip = $acc_row[CITY] . ", " . $acc_row[STATE] . " " . $acc_row[ZIP];
	$clientphone = "Phone: " . $acc_row[PHONE];
	$clientfax = "Fax: " . $acc_row[FAX];
}
$pdf->ezText($clientname,16,array('justification'=>'center'));
$pdf->ezText($clientaddress . ', ' . $clientcitystatezip . '    |    ' . $clientphone . ' ' . $clientfax,11,array('justification'=>'center'));
//$pdf->ezText($clientcitystatezip,14,array('justification'=>'center'));
//$pdf->ezText($clientphone . ' ' . $clientfax,12,array('justification'=>'center'));
//$pdf->ezText($clientfax,12,array('justification'=>'center'));
$pdf->ezText('',10,array('justification'=>'center'));
$inv_order = '<b>ORDER  #' . substr($orderrow[ID],-8) . '</b>';

if($custrow[ACCOUNTID] == 252) // Only Thorntons uses Order ID as Invoice # in Packing Slip
	$pdf->ezText($inv_order,22,array('justification'=>'center'));
else
	$pdf->ezText('<b>PACKING SLIP</b>',22,array('justification'=>'center'));
$pdf->ezText('',10,array('justification'=>'center'));

//ship to has 1st line only if employee order, only show SS if PD order
if ($orderrow[CUSTOMEREMPLOYEEID])
{
   $ssr=db_query("SELECT * FROM CUSTOMEREMPLOYEE WHERE ID='$orderrow[CUSTOMEREMPLOYEEID]'",$db);
   $ssrow=mysql_fetch_array($ssr);
   if ($summaryS1)
   	  $summaryS1 .= ", ";
   $s1 = " ".$ssrow[FIRSTNAME]." ".$ssrow[LASTNAME];
   if ($orderrow[PAYMENTTYPE] == 'PD')
   	  $s1 .= " (".substr($ssrow[SSNUM],-4).")";
   $summaryS1 .= $s1;	     
   $s2=ucwords(strtolower(stripslashes($shiptorow[NAME])));
   $s3=ucwords(strtolower($shiptorow[ADDRESS1]));
   if ($shiptorow[ADDRESS2])
   	  {
	  $s4 = ucwords(strtolower($shiptorow[ADDRESS2]));
   	  $s5 = ucwords(strtolower($shiptorow[CITY])).','.$shiptorow[STATE].' '.$shiptorow[ZIP];
   	  }
   else
   	  {
      $s4 = ucwords(strtolower($shiptorow[CITY])).','.$shiptorow[STATE].' '.$shiptorow[ZIP];
      $s5 = ''; 
      }    

}
else
{
   $s1=ucwords(strtolower(stripslashes($shiptorow[NAME])));
   $s2=ucwords(strtolower($shiptorow[ADDRESS1]));
   if ($shiptorow[ADDRESS2])
   	  {
	  $s3 = ucwords(strtolower($shiptorow[ADDRESS2]));
   	  $s4 = ucwords(strtolower($shiptorow[CITY])).','.$shiptorow[STATE].' '.$shiptorow[ZIP];
   	  }
   else
   	  {
      $s3 = ucwords(strtolower($shiptorow[CITY])).','.$shiptorow[STATE].' '.$shiptorow[ZIP];
      $s4 = ''; 
      }    
}

$b1=ucwords(strtolower($custrow[NAME]));
$b2=ucwords(strtolower($custrow[ADDRESS1]));

if ($custrow[ADDRESS2])
   {
   $b3 = ucwords(strtolower($custrow[ADDRESS2]));
   $b4 = ucwords(strtolower($custrow[CITY])).','.$custrow[STATE].' '.$custrow[ZIP];
   }
else
   {
   $b3 = ucwords(strtolower($custrow[CITY])).','.$custrow[STATE].' '.$custrow[ZIP];
   $b4 = ''; 
   }  

 

//draw bill to/ship to table with border
$arrayBillShipTitleData = array(
 array('BILL'=>$b1,'SHIP'=>$s1)
,array('BILL'=>$b2,'SHIP'=>$s2)
,array('BILL'=>$b3,'SHIP'=>$s3)
,array('BILL'=>$b4,'SHIP'=>$s4)
,array('BILL'=>$b5,'SHIP'=>$s5)  
);
$cols = array('BILL'=>'<b>BILL TO:</b>','SHIP'=>'<b>SHIP TO:</b>');
$pdf->ezTable($arrayBillShipTitleData, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'cols'=>array(
									    'BILL'=>array('width'=>250)									   
									   ,'SHIP'=>array('width'=>250)
									   				 )
									   		 )
										);
										
//small gap before next section									   
$pdf->ezText("",8,array('justification'=>'center'));

//draw info block
if ($orderrow[PAYMENTTYPE] == 'OP' or $orderrow[PAYMENTTYPE] == 'OPEN')
 	$paytype = 'NET';
else if ($orderrow[PAYMENTTYPE] == 'PD')
	$paytype = $orderrow[PAYMENTTYPE]." (".$orderrow[NUMPD]." deductions)";  
else
	$paytype = $orderrow[PAYMENTTYPE];
if (!$paytype)
 	$paytype = 'UNKNOWN';		


$arrayPayableAccountData = array(
 array('C1'=>'<b>ACCOUNT NUMBER:</b>','C2'=>$accountrow[PREFIX],'C3'=>'<b>PACKING SLIP NO:</b>','C4'=>'<b>'.$shipmentid.'</b>')
,array('C1'=>'<b>P.O. Number:</b>','C2'=>$orderrow[PONUMBER],'C3'=>'<b>STORE NUMBER:</b>','C4'=>'<b>'.$shiptorow[STORENUMBER].'</b>')
,array('C1'=>'<b>Shipment Method:</b>','C2'=>$orderrow[SHIPVIA],'C3'=>'<b>SHIP DATE:</b>','C4'=>substr($shipmentrow[DATETIME],5,5).'-'.substr($shipmentrow[DATETIME],0,4))
,array('C1'=>'<b>Payment Type:</b>','C2'=>$paytype,'C3'=>'<b>ORDER DATE:</b>','C4'=>substr($orderrow[CREATIONDATE],5,5).'-'.substr($orderrow[CREATIONDATE],0,4))
,array('C1'=>'','C2'=>'','C3'=>'<b>DELIVERY DUEDATE:</b>','C4'=>substr($orderrow[DELIVERYDUEDATE],5,5).'-'.substr($orderrow[DELIVERYDUEDATE],0,4))
//,array('C1'=>'<b>Payment Type:</b>','C2'=>$paytype,'C3'=>'','C4'=>'')
);


$cols = array('C1'=>'c1','C2'=>'c2','C3'=>'c3','C4'=>'c4');
$BoxTop = $pdf->ezTable($arrayPayableAccountData, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>0
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'C1'=>array('justification'=>'right')
									   ,'C2'=>array('justification'=>'left')																			   
									   ,'C3'=>array('justification'=>'right')
									   ,'C4'=>array('justification'=>'left')
									   				)									   
									   		 )
									   );

//small gap before next section
$TopGreyBar=$pdf->ezText("",12,array('justification'=>'center'));

unset ($tdata);
unset ($cols);
if($custrow[ACCOUNTID] == 252 AND $orderrow[NUMPD]==1)
	$showprices = 'NO';
if ($showprices == 'YES')
{
	  		  if($custrow[ACCOUNTID] == 252){ // Only Thorntons
			  	$tdata[] = array('QTYSHIPPED'=>'<b>QTY SHIP</b>','SKU'=>'<b>SKU</b>','CUSTOMERSKU'=>'<b>CODE</b>','DESCRIPTION'=>'<b>DESCRIPTION</b>','PRICE'=>'<b>PRICE</b>','AMOUNT'=>'<b>AMOUNT</b>');	
				
				$cols = array('QTYSHIPPED'=>1,
	  		  			'SKU'=>2,
						'CUSTOMERSKU'=>3,
						'DESCRIPTION'=>4,
	  		  			'PRICE'=>5,
	  		  			'AMOUNT'=>6,										
						);
					
	  		  		$pdf->ezTable($tdata, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'QTYSHIPPED'=>array('justification'=>'center'
													,'width'=>40)									   
									   ,'SKU'=>array('justification'=>'center'
													,'width'=>90)
									   ,'CUSTOMERSKU'=>array('justification'=>'center'
													,'width'=>40)													   
									   ,'DESCRIPTION'=>array('justification'=>'center'
									   				,'width'=>222)
									   ,'PRICE'=>array('justification'=>'center'
													,'width'=>70)									   
									   ,'AMOUNT'=>array('justification'=>'center'
													,'width'=>70)									   
									   								   									   
									   		 )
									   	 )
									   );
			  }
			  else{
			  		$tdata[] = array('QTYSHIPPED'=>'<b>QTY SHIP</b>','SKU'=>'<b>SKU</b>','DESCRIPTION'=>'<b>DESCRIPTION</b>','PRICE'=>'<b>PRICE</b>','AMOUNT'=>'<b>AMOUNT</b>');
			  
				  	$cols = array('QTYSHIPPED'=>1,
	  		  			'SKU'=>2,
						'DESCRIPTION'=>3,
	  		  			'PRICE'=>4,
	  		  			'AMOUNT'=>5,										
						);
					
	  		  		$pdf->ezTable($tdata, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'QTYSHIPPED'=>array('justification'=>'center'
													,'width'=>40)									   
									   ,'SKU'=>array('justification'=>'center'
													,'width'=>90)									   
									   ,'DESCRIPTION'=>array('justification'=>'center'
									   				,'width'=>250)
									   ,'PRICE'=>array('justification'=>'center'
													,'width'=>70)									   
									   ,'AMOUNT'=>array('justification'=>'center'
													,'width'=>70)									   
									   								   									   
									   		 )
									   	 )
									   );
				}
}
else
{
	  		  $tdata[] = array('QTYSOLD'=>'<b>QTY ORD</b>','QTYSHIPPED'=>'<b>QTY SHIP</b>','SKU'=>'<b>SKU</b>','DESCRIPTION'=>'<b>DESCRIPTION</b>');

	  		  $cols = array('QTYSOLD'=>1,
			  		'QTYSHIPPED'=>2,
	  		  		'SKU'=>3,
					'DESCRIPTION'=>4);
					
	  		  $pdf->ezTable($tdata, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'QTYSOLD'=>array('justification'=>'center'
													,'width'=>70)									   
									   ,'QTYSHIPPED'=>array('justification'=>'center'
													,'width'=>70)									   
									   ,'SKU'=>array('justification'=>'center'
													,'width'=>110)									   
									   ,'DESCRIPTION'=>array('justification'=>'center'
 								    			    )									   									   
									   		 )
									   	 )
									   );

}

//draw order items table
	      $pdfoiarraydata = array();
		  $firstrow = 1;
//////// Alex Proano begin
		  //if($shipmentid)			  
		  	//$pdfoiconsolidated = "SELECT SUM(QTYSOLD) AS QTYSOLD,SUM(QTYSHIPPED) AS QTYSHIPPED,SKU,CUSTOMERSKU,DESCRIPTION,SHIPMENTID,SUM(ORDERITEMS.QTYBO) AS QTYBO,INVENTORY.ID,ORDERITEMS.PRICE,SUM(ORDERITEMS.AMOUNT) AS AMOUNT,SHIPMENTID, ORDERITEMS.ITEMDESCRIPTION FROM ORDERITEMS,INVENTORY WHERE ORDERITEMS.ORDERID='$orderrow[ID]' AND (ORDERITEMS.SHIPMENTID='$shipmentid' OR ORDERITEMS.SHIPMENTID=0) AND INVENTORY.ID=ORDERITEMS.INVENTORYID GROUP BY ORDERITEMS.SHIPMENTID, ORDERITEMS.INVENTORYID ORDER BY INVENTORY.SKU,BINNUMBER,DATE";
			$pdfoiconsolidated = "SELECT SUM(QTYSOLD) AS QTYSOLD,SUM(QTYSHIPPED) AS QTYSHIPPED,SKU,CUSTOMERSKU,DESCRIPTION,SHIPMENTID,SUM(ORDERITEMS.QTYBO) AS QTYBO,INVENTORY.ID,ORDERITEMS.PRICE,SUM(ORDERITEMS.AMOUNT) AS AMOUNT,SHIPMENTID, ORDERITEMS.ITEMDESCRIPTION FROM ORDERITEMS,INVENTORY WHERE ORDERITEMS.ORDERID='$orderrow[ID]' AND (ORDERITEMS.SHIPMENTID='$shipmentid' OR ORDERITEMS.SHIPMENTID=0) AND INVENTORY.ID=ORDERITEMS.INVENTORYID GROUP BY ORDERITEMS.SHIPMENTID, ORDERITEMS.INVENTORYID, ORDERITEMS.ITEMDESCRIPTION ORDER BY INVENTORY.SKU,BINNUMBER,DATE";
		  //else
		  	//$pdfoiconsolidated = "SELECT SUM(QTYSOLD) AS QTYSOLD,SUM(QTYSHIPPED) AS QTYSHIPPED,SKU,DESCRIPTION,SHIPMENTID,SUM(ORDERITEMS.QTYBO) AS QTYBO,INVENTORY.ID,ORDERITEMS.PRICE,SUM(ORDERITEMS.AMOUNT) AS AMOUNT,SHIPMENTID FROM ORDERITEMS,INVENTORY WHERE ORDERITEMS.ORDERID='$orderrow[ID]' AND INVENTORY.ID=ORDERITEMS.INVENTORYID GROUP BY ORDERITEMS.SHIPMENTID, ORDERITEMS.INVENTORYID ORDER BY INVENTORY.SKU,BINNUMBER,DATE";
		  
		  
		  $pdfoiresultconsolidated = db_query($pdfoiconsolidated, $db);	
/////// Alex Proano finish
          //$pdfoiquery = "SELECT ORDERITEMS.ID AS IDoi, QTYSOLD,QTYSHIPPED,SKU,DESCRIPTION,SHIPMENTID,ORDERITEMS.QTYBO,INVENTORY.ID,ORDERITEMS.PRICE,ORDERITEMS.AMOUNT,SHIPMENTID FROM ORDERITEMS,INVENTORY WHERE ORDERITEMS.ORDERID='$orderrow[ID]' AND INVENTORY.ID=ORDERITEMS.INVENTORYID ORDER BY BINNUMBER,DATE";			  
		  $pdfoiquery = "SELECT ORDERITEMS.ID AS IDoi, QTYSOLD,QTYSHIPPED,SKU,DESCRIPTION,SHIPMENTID,ORDERITEMS.QTYBO,INVENTORY.ID,ORDERITEMS.PRICE,ORDERITEMS.AMOUNT,SHIPMENTID, ORDERITEMS.ITEMDESCRIPTION FROM ORDERITEMS,INVENTORY WHERE ORDERITEMS.ORDERID='$orderrow[ID]' AND (ORDERITEMS.SHIPMENTID='$shipmentid' OR ORDERITEMS.SHIPMENTID=0) AND INVENTORY.ID=ORDERITEMS.INVENTORYID ORDER BY BINNUMBER,DATE";			  
		  $pdfoiresult = db_query($pdfoiquery, $db);
          //$rowdata = mysql_fetch_assoc($pdfoiresult);	
//////////////// Alex Begin		  
if ($showprices == 'YES'){
		  while ($rowdata = mysql_fetch_assoc($pdfoiresult)){
			if ($rowdata[SHIPMENTID] == $shipmentid AND $rowdata[QTYSHIPPED] > 0){
	   			if ($rowdata[AMOUNT] == 0 OR $rowdata[AMOUNT] == ''){	   
		   			if ($rowdata[QTYSHIPPED] != 0 AND $rowdata[QTYSHIPPED] != '')
		   		   		$tempamount = $rowdata[PRICE] * $rowdata[QTYSHIPPED];

					$updateoiu = "UPDATE ORDERITEMS SET AMOUNT='$tempamount' WHERE ID='$rowdata[IDoi]'";
					//$updateoiu = "UPDATE ORDERITEMS SET AMOUNT='$tempamount' WHERE ID='$rowdata[ID]'";
	   	   			$resultoiu = db_query ($updateoiu, $db);
									
				}
	  		}
	  	  }
}
//////////////// Alex Finish	



if ($showprices == 'YES')
{
  		if($custrow[ACCOUNTID] == 252){ // Only Thorntons
		  $cols = array('QTYSHIPPED'=>'<b><u>QTY SHIP</u></b>',
  		  		'SKU'=>'<b><u>SKU</u></b>',
				'CUSTOMERSKU'=>'<b><u>CODE</u></b>',
				'DESCRIPTION'=>'<b><u>Description</u></b>',
  		  		'PRICE'=>'<b><u>SKU</u></b>',
  		  		'AMOUNT'=>'<b><u>SKU</u></b>',				
				);
				unset($subtotal); 		
		}  
		else{  
		  $cols = array('QTYSHIPPED'=>'<b><u>QTY SHIP</u></b>',
  		  		'SKU'=>'<b><u>SKU</u></b>',
				'DESCRIPTION'=>'<b><u>Description</u></b>',
  		  		'PRICE'=>'<b><u>SKU</u></b>',
  		  		'AMOUNT'=>'<b><u>SKU</u></b>',				
				);
				unset($subtotal); 
		}		

}
else
{
  		  $cols = array('QTYSOLD'=>'<b><u>QTY ORD</u></b>',
		  		'QTYSHIPPED'=>'<b><u>QTY SHIP</u></b>',
  		  		'SKU'=>'<b><u>SKU</u></b>',
				'DESCRIPTION'=>'<b><u>Description</u></b>');
}	  
	  
		  while ($rowdatacons = mysql_fetch_assoc($pdfoiresultconsolidated))
		  {
		   	  /*
			  if ($rowdatacons[SHIPMENTID] == $shipmentid)
			  	 $qtyshipped = $rowdatacons[QTYSHIPPED];
			  else
    	  	     $qtyshipped = 0;
				 
			  if ($rowdatacons[QTYSOLD] <> 0)
			  	 $qtysold = $rowdatacons[QTYSOLD];
			  else	 
				 $qtysold = $rowdatacons[QTYBO];
				*/
			  $qtysold = $rowdatacons[QTYSOLD]+$rowdatacons[QTYBO];
			  $qtyshipped = $rowdatacons[QTYSHIPPED];
			  if ($rowdatacons[QTYSHIPPED] == NULL)
		  		$qtyshipped = 0;
			  //save orderitem data for each item to display a summary page if >1 order
			  if ($qtyshipped > 0)
			  	 {
			  	 $index = (int)$rowdatacons[ID];
			  	 $summaryInvQty[$index] = $summaryInvQty[$index] + $qtyshipped;   //rolling sum as we display other orders
			  	 $summaryInvSKU[$index] = $rowdatacons[SKU];			  
			  	 if ($rowdatacons[ITEMDESCRIPTION])
				 	$summaryInvDesc[$index] = $rowdatacons[ITEMDESCRIPTION];
				 else
				 	$summaryInvDesc[$index] = $rowdatacons[DESCRIPTION];
				 
				 //keep track of which orders each item goes in...
				  	if ($menucookie[THIRDPARTYSHIPPER] == 'NO')
			  		{
			  	 	if ($summaryOrderIDs[$index])
			  	 	$summaryOrderIDs[$index] .= ",   <c:alink:".$root_path."Print/Order.php?orderid=".$orderrow[ID].">".$orderrow[ID]."</c:alink>";
			  	 	else			  		  
			  	 	$summaryOrderIDs[$index] = "<c:alink:".$root_path."Print/Order.php?orderid=".$orderrow[ID].">".$orderrow[ID]."</c:alink>";
					}
				else
					{
					//3rd party shippers do not get links to view orders
			  	 	if ($summaryOrderIDs[$index])
			  	 	$summaryOrderIDs[$index] .= ",".$orderrow[ID];
			  	 	else			  		  
			  	 	$summaryOrderIDs[$index] = $orderrow[ID];
					
					}
				 }
if ($showprices == 'YES')
{
	if ($rowdatacons[SHIPMENTID] == $shipmentid AND $rowdatacons[QTYSHIPPED] > 0)
	   {
	   if ($rowdatacons[AMOUNT]  == 0 OR $rowdatacons[AMOUNT] == '')
		   {	   
		   	if ($rowdatacons[QTYSHIPPED] != 0 AND $rowdatacons[QTYSHIPPED] != '')
		   	   {
		   		   $tempamount = $rowdatacons[PRICE] * $rowdatacons[QTYSHIPPED];
			   }			   			   			   
			//$updateoiu = "UPDATE ORDERITEMS SET AMOUNT='$tempamount' WHERE ID='$rowdata[ID]'";
	   	   	//$resultoiu = db_query ($updateoiu, $db);
		   }
	  else
	  	  $tempamount = $rowdatacons[AMOUNT];	
	  }
	  else
	  	  $tempamount = 0;
		  	  
	  $tempamount = sprintf("%1.2f",$tempamount);	  
	  if($rowdatacons[ITEMDESCRIPTION])	  
	  	$displaydesc = $rowdatacons[ITEMDESCRIPTION];
	  else
	  	$displaydesc = $rowdatacons[DESCRIPTION];
	
	  //'DESCRIPTION'=>ucwords(strtolower($displaydesc)),
	  
	  if($custrow[ACCOUNTID] == 252){ // Only Thorntons
		$pdftempdata[] = array('QTYSHIPPED'=>$qtyshipped,			  
 		  		'SKU'=>$rowdatacons[SKU],
				'CUSTOMERSKU'=>$rowdatacons[CUSTOMERSKU],
				'DESCRIPTION'=>$displaydesc,
  		  		'PRICE'=>$rowdatacons[PRICE],
  		  		'AMOUNT'=>$tempamount,										
				);
								   
	  	$YBottom=$pdf->ezTable($pdftempdata, $cols,''
			   ,array('shaded'=>0
			   ,'width'=>500
			   ,'showLines'=>0
			   ,'showHeadings'=>0
			   ,'cols'=>array(
			    'QTYSHIPPED'=>array('justification'=>'center'
								,'width'=>40)									   
			   ,'SKU'=>array('justification'=>'left'
			   					,'width'=>90)
			   ,'CUSTOMERSKU'=>array('justification'=>'left'
			   					,'width'=>40)																		   
			   ,'DESCRIPTION'=>array('justification'=>'left',
			   						'width'=>222)
			   ,'PRICE'=>array('justification'=>'right'
			   					,'width'=>70)									   
			   ,'AMOUNT'=>array('justification'=>'right'
			   					,'width'=>70)									   
			   				 )
				   		 )
				   );	  
	  
	  }
	  else{	
	    //'DESCRIPTION'=>ucwords(strtolower($displaydesc)),
		$pdftempdata[] = array('QTYSHIPPED'=>$qtyshipped,			  
 		  		'SKU'=>$rowdatacons[SKU],
				'DESCRIPTION'=>$displaydesc,
  		  		'PRICE'=>$rowdatacons[PRICE],
  		  		'AMOUNT'=>$tempamount,										
				);
								   
	  	$YBottom=$pdf->ezTable($pdftempdata, $cols,''
			   ,array('shaded'=>0
			   ,'width'=>500
			   ,'showLines'=>0
			   ,'showHeadings'=>0
			   ,'cols'=>array(
			    'QTYSHIPPED'=>array('justification'=>'center'
								,'width'=>40)									   
			   ,'SKU'=>array('justification'=>'left'
			   					,'width'=>90)									   
			   ,'DESCRIPTION'=>array('justification'=>'left',
			   						'width'=>250)
			   ,'PRICE'=>array('justification'=>'right'
			   					,'width'=>70)									   
			   ,'AMOUNT'=>array('justification'=>'right'
			   					,'width'=>70)									   
			   				 )
				   		 )
				   );
	 }			   
     $subtotal = $subtotal + $tempamount;  					 
}
else
{				 		
		  	if($rowdatacons[ITEMDESCRIPTION])	  
			  	$displaydesc = $rowdatacons[ITEMDESCRIPTION];
	  		else
	  			$displaydesc = $rowdatacons[DESCRIPTION];
			  
			  //'DESCRIPTION'=>ucwords(strtolower($displaydesc)));
			  $pdftempdata[] = array('QTYSOLD'=>$qtysold,
			  		'QTYSHIPPED'=>$qtyshipped,			  
	  		  		'SKU'=>$rowdatacons[SKU],
					'DESCRIPTION'=>$displaydesc);
								   
	  		  $YBottom=$pdf->ezTable($pdftempdata, $cols,''
				   ,array('shaded'=>0
				   ,'width'=>500
				   ,'showLines'=>0
				   ,'showHeadings'=>0
				   ,'cols'=>array(
				    'QTYSOLD'=>array('justification'=>'center'
								,'width'=>70)									   
				   ,'QTYSHIPPED'=>array('justification'=>'center'
									,'width'=>70)									   
				   ,'SKU'=>array('justification'=>'left'
				   					,'width'=>110)									   
				   ,'DESCRIPTION'=>array('justification'=>'left'
 				    			    )									   									   
				   				 )
					   		 )
					   );
}
				$pdf->line(50,$YBottom,550,$YBottom);					   
				unset($pdftempdata);
									   
				}

//if show prices, then draw bottom subtotal, tax, shipping, total table...
if ($showprices == 'YES')
{
//determine tax, add to subtotal and handling to get total
	$query6 = "SELECT * FROM TAX WHERE FULLCODE='$shiptorow[TAXCODE]'"; 
	$result6 = db_query ($query6, $db);
	$taxrow = mysql_fetch_array($result6);

	if ($accountrow[TAXPOLICY] == 'EVERYWHERE' OR 
   	   ($accountrow[TAXPOLICY] == 'NC' AND $shiptorow[STATE] == 'NC') )
	   {
	   $tax = $subtotal * ($taxrow[STATETAX] + $taxrow[COUNTYTAX] + $taxrow[CITYTAX])*10;
	   $tax = round ($tax,2);
	   }
	else 
 	   $tax = 0;

	if ($orderrow[DROPSHIP] == 'YES')
	   $dropship = $dropshipfee;
	else
	   $dropship = 0;
	   
	   
	if ($orderrow[NOSHIPCOST] == 'YES')
	 $temphandling = 0;
	else
     {
			if ($shipmentrow[MUSTUSE] == 'YES' OR $shipmentrow[COST] > 0 )
				$temphandling = $shipmentrow[COST] + $accountrow[HANDLINGFEE];				
			else if ($orderrow[SHIPCOST])
				$temphandling = $orderrow[SHIPCOST];
			else
				$temphandling = $accountrow[HANDLINGFEE];



	 }
	    
	$total = $subtotal + $tax + $temphandling + $dropship;

    $total = sprintf("%1.2f",$total);
    $tax = sprintf("%1.2f",$tax);
    $dropship = sprintf("%1.2f",$dropship);
    $subtotal = sprintf("%1.2f",$subtotal);
    $handling = sprintf("%1.2f",$temphandling);
	   				
	$SubtotalDataArray = array(
   					     array('C0'=>'','C1'=>'<b>SUBTOTAL</b>','C2'=>$subtotal)
						,array('C0'=>'','C1'=>'<b>TAX</b>','C2'=>$tax)
					    ,array('C0'=>'','C1'=>'<b>SHIPPING & HANDLING</b>','C2'=>$handling)
					    ,array('C0'=>'','C1'=>'<b>DROP SHIP FEE</b>','C2'=>$dropship)						
						,array('C0'=>'','C1'=>'<b>TOTAL</b>','C2'=>'<b>$'.$total.'</b>')
  	);
	
   $cols = array('C1'=>'c1','C2'=>'c2');
   $BoxTop = $pdf->ezTable($SubtotalDataArray, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>0
									   ,'showHeadings'=>0
									   ,'xPos'=>'right'	
									   ,'xOrientation'=>'left'								   
									   ,'cols'=>array(
									   'C0'=>array('width'=>280)
									   ,'C1'=>array('justification'=>'right', 'width'=>150)
									   ,'C2'=>array('justification'=>'right','width'=>70)																			   
									   				)									   
									   		 )
									   );
   unset($total);
   unset($subtotal);
   unset($tax);    

}		

//bottom text string 
$BottomGreyBar=$pdf->ezText("",22,array('justification'=>'center'));


$pdf->ezText('YOUR ORDER HAS BEEN VERIFIED FOR ACCURACY AT THREE INDEPENDENT WORK STATIONS.  ALL CLAIMS FOR DEFICIENCIES AND IMPERFECTIONS MUST BE MADE WITHIN FIVE (5) DAYS OF RECEIPT OF GOODS.',10,array('justification'=>'left'));
$pdf->ezText('',10,array('justification'=>'left'));

$pdf->ezText('<b>NOTE: </b>',10,array('justification'=>'left'));

	$cusResult = db_query("SELECT * FROM CUSTOMERS WHERE ID=$orderrow[SHIPTOID]",$db);
	$cusRow=mysql_fetch_array($cusResult);
	if ($cusRow[INDIVIDUAL] == "YES"){
		$pdf->ezText('',10,array('justification'=>'left'));
		$notes = "<b>RESIDENTIAL SHIPMENT </b>";
		$pdf->ezText($notes,20,array('justification'=>'left'));
		$pdf->ezText('',10,array('justification'=>'left'));
	}

if ($accountrow[UPS_ACCOUNT]){
	$pdf->ezText('',10,array('justification'=>'left'));
	$notes = "<b>USE THIS UPS ACCOUNT: " . $accountrow[UPS_ACCOUNT] . "</b>";
	$pdf->ezText($notes,20,array('justification'=>'left'));
	$pdf->ezText('',10,array('justification'=>'left'));
}
$pdf->ezText($orderrow[SHIPPINGNOTES],10,array('justification'=>'left'));

$r1=db_query("SELECT FIRSTNAME,LASTNAME FROM EMPLOYEE WHERE ID='$orderrow[EMPLOYEEID]'",$db);
$or=mysql_fetch_array($r1);
$ordertaker = substr($or[FIRSTNAME],0,1).substr($or[LASTNAME],0,1);

$r1=db_query("SELECT FIRSTNAME,LASTNAME FROM EMPLOYEE WHERE ID='$shipmentrow[EMPLOYEEID]'",$db);
$or=mysql_fetch_array($r1);
$packer = substr($or[FIRSTNAME],0,1).substr($or[LASTNAME],0,1);


//$pdf->ezSetMargins(30,40,40,40);
$pdf->ezSetMargins(30,30,40,40);
//////////////////////////////
//add bar codes
//////////////////////////////
//add customer ID
//we're C39, alpha-numeric, and mength...
$code=$shiptorow[STORENUMBER];
//if Inter 2 of 5 we must be even length and numbers only, use these...
//$code=intval($shiptorow[STORENUMBER]);
//if (strlen($code)/2 != intval(strlen($code)/2))
//   $code ='0'.$code;


//////////////////////////////   GENERATE BARCODE FOR STORENUMBER     /////////////////////////////////////////////////////
//
// file("http://poise.bennettuniform.com/poise/barcodes/image.php?code=$code&style=164&type=C39&width=250&height=60&xres=1&font=2");
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//ezImage(image,[padding],[width],[resize],[justification],[array border])
//bottom text string 
//$pdf->ezSetY(100);
//$pdf->ezSetX(0);
//$BottomGreyBar=$pdf->ezText("",22,array('justification'=>'center'));
//$pdf->ezImage('../barcodes/'.$code.'.jpg',0,350,'full','left',array('width'=>2) );

//////////////////////////////   PRINT BARCODE FOR STORENUMBER     /////////////////////////////////////////////////////
//
//$pdf->addJpegFromFile('../barcodes/'.$code.'.jpg',0,40,250);
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//add shipment ID
$code=intval($shipmentid);
if (strlen($code)/2 != intval(strlen($code)/2))
   $code ='0'.$code;
file("http://poise.bennettuniform.com/poise/barcodes/image.php?code=$code&style=164&type=C39&width=250&height=60&xres=1&font=2");

//ezImage(image,[padding],[width],[resize],[justification],[array border])
//bottom text string 
//$pdf->ezSetY(150);
//$pdf->ezSetX(400);
//$BottomGreyBar=$pdf->ezText("",22,array('justification'=>'center'));
//$pdf->ezImage('../barcodes/'.$code.'.jpg',0,350,'full','right',array('width'=>2));
//$pdf->addJpegFromFile('../barcodes/'.$code.'.jpg',350,40,250);
$pdf->addJpegFromFile('../barcodes/'.$code.'.jpg',350,10,250);
//bottom text string 

//BU workflow info at bottom left corner
//$pdf->ezSetY(50);
$pdf->ezSetY(40);

if ($menucookie[THIRDPARTYSHIPPER] == 'NO')
	$o="<c:alink:".$root_path."Print/Order.php?orderid=".$orderrow[ID].">".substr($orderrow[ID],-8)."</c:alink>";
else
	$o=substr($orderrow[ID],-8);

$pdf->ezText('<b>O='.$ordertaker.', P='.$packer.', O='.$o.'</b>',8,array('justification'=>'left'));
}








//slip summary page on top if >1 orders for this packing slip and SS Orders are enabled (FL Only...)

if($numOrders > 0 AND $accountrow[ENABLESSORDERS] == 'YES')
{
 while ($numcopies < 2)
	  {
	  $numcopies++;
   		 //insert as first page and draw header
	 $pdf->ezInsertMode(1,1,'before');

 	  if ($numcopies == 2)
	  	  $pdf->ezNewPage();

 
$pdf->ezText('<b>PACKING SLIP SUMMARY</b>',22,array('justification'=>'center'));
//small gap before next section
$TopGreyBar=$pdf->ezText("",12,array('justification'=>'center'));
$TopGreyBar=$pdf->ezText("",12,array('justification'=>'center'));
//draw bill to/ship to table with border
$arrayBillShipTitleData = array(
 array('SHIP'=>$summaryS1)
,array('SHIP'=>$s2)
,array('SHIP'=>$s3)
,array('SHIP'=>$s4)
,array('SHIP'=>$s5) 
);

$cols = array('SHIP'=>'<b>SHIP TO:</b>');
$pdf->ezTable($arrayBillShipTitleData, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'cols'=>array(
									    'BILL'=>array('width'=>250)									   
									   ,'SHIP'=>array('width'=>250)
									  		 )
								 		 )
									   );
										
//small gap before next section									   
$pdf->ezText("",8,array('justification'=>'center'));

$arrayPayableAccountData = array(
 array('C1'=>'<b>ACCOUNT NUMBER:</b>','C2'=>$accountrow[PREFIX],'C3'=>'<b>PACKING SLIP NO:</b>','C4'=>'<b>'.$shipmentid.'</b>')
,array('C1'=>'<b>Shipment Method:</b>','C2'=>$orderrow[SHIPVIA],'C3'=>'<b>STORE NUMBER:</b>','C4'=>'<b>'.$shiptorow[STORENUMBER].'</b>')
,array('C1'=>'<b>Payment Type:</b>','C2'=>$paymenttypes,'C3'=>'<b>SHIP DATE:</b>','C4'=>substr($shipmentrow[DATETIME],5,5).'-'.substr($shipmentrow[DATETIME],0,4))
);

$cols = array('C1'=>'c1','C2'=>'c2','C3'=>'c3','C4'=>'c4');
$BoxTop = $pdf->ezTable($arrayPayableAccountData, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>0
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'C1'=>array('justification'=>'right')
									   ,'C2'=>array('justification'=>'left')																			   
									   ,'C3'=>array('justification'=>'right')
									   ,'C4'=>array('justification'=>'left')
									   				)									   
									   		 )
									   );

//small gap before next section
$TopGreyBar=$pdf->ezText("",12,array('justification'=>'center'));

unset ($tdata);
unset ($cols);
	  		  $tdata[] = array('QTYSHIPPED'=>'<b>QTY SHIP</b>','SKU'=>'<b>SKU</b>','DESCRIPTION'=>'<b>DESCRIPTION</b>','FORORDER'=>'<b>FOR ORDER</b>');

	  		  $cols = array('QTYSHIPPED'=>1,
	  		  		'SKU'=>2,
					'DESCRIPTION'=>3,
					'FORORDER'=>4);
					
	  		  $pdf->ezTable($tdata, $cols,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'QTYSHIPPED'=>array('justification'=>'center'
													,'width'=>60)									   
									   ,'SKU'=>array('justification'=>'center'
													,'width'=>110)									   
									   ,'DESCRIPTION'=>array('justification'=>'center','width'=>'260')
   									   ,'FORORDER'=>array('justification'=>'right'
													,'width'=>70)									   
 								    			    )									   									   
									   		   	 )
									   );


//draw order items table
	      $pdfoiarraydata = array();
		  $firstrow = 1;

		  $pdfoiquery = "SELECT QTYSOLD,QTYSHIPPED,SKU,DESCRIPTION,SHIPMENTID,ORDERITEMS.QTYBO,INVENTORY.ID FROM ORDERITEMS,INVENTORY WHERE ORDERITEMS.ORDERID='$orderrow[ID]' AND INVENTORY.ID=ORDERITEMS.INVENTORYID ORDER BY BINNUMBER,DATE";
		  
		  $pdfoiresult = db_query($pdfoiquery, $db);

  		  $cols = array('QTYSHIPPED'=>'<b><u>QTY SHIP</u></b>',
  		  		'SKU'=>'<b><u>SKU</u></b>',
				'DESCRIPTION'=>'<b><u>Description</u></b>',
				'FORORDER'=>'<b><u>FOR ORDER</u></b>'
				);
	  
	  	  unset($index);
		  unset($newindex);

		  while ($newindex <= $maxrows[ID])
		  {	  
		  $testSKU = $summaryInvSKU[$newindex];
		  if ($newindex == 2708)
		  {
			  $x=$summaryInvSKU[2708];
			  $z=$summaryInvQty[2708];
//			  $y=$newindex;			  			  
//			  $debug2text .= "qty2=$z,SKU2=$x,ID2=$y,testSKU=$testSKU,";		  
		  }
		  
//		  $debugtext .= "...$newindex=$testSKU...";
		  if ($testSKU != NULL)	//did this SKU exist in any of the packing slips?
		  	 {	
//			 			  $debug2text .= "got here on sku($testSKU)";
			  //populate array with values		
			  //'DESCRIPTION'=>ucwords(strtolower($summaryInvDesc[$newindex])),  
			  $pdftempdata[] = array('QTYSHIPPED'=>$summaryInvQty[$newindex],	  
	  		  		'SKU'=>$summaryInvSKU[$newindex],
					'DESCRIPTION'=>$summaryInvDesc[$newindex],
					'FORORDER'=>$summaryOrderIDs[$newindex]);
					
			  		//show this line of the table								   
	  		  		 $YBottom=$pdf->ezTable($pdftempdata, $cols,''
				   				,array('shaded'=>0
				   				,'width'=>500
				   				,'showLines'=>0
				   				,'showHeadings'=>0
				   				,'cols'=>array(
				    			'QTYSHIPPED'=>array('justification'=>'center'
									,'width'=>60)									   
				   					,'SKU'=>array('justification'=>'left'
				   					,'width'=>110)									   
				   					,'DESCRIPTION'=>array('justification'=>'left','width'=>'260')
				       				,'QTYSOLD'=>array('justification'=>'right'
									,'width'=>70)									   
 				    			    )									   									   
					   		 )
					   );
					   
					//draw a line after each line	   
					$pdf->line(50,$YBottom,540,$YBottom);
					
					//cleae table contents for next time		   
				    unset($pdftempdata);
					}
				$newindex++;		   
				}

				
				
				
				
				//space down 
				$pdf->ezText("",36,array('justification'=>'center'));

				//$oisumresult = db_query("SELECT ORDERID,ORDERITEMS.QTYSHIPPED,SKU,DESCRIPTION,CUSTOMEREMPLOYEE.FIRSTNAME,CUSTOMEREMPLOYEE.LASTNAME FROM ORDERITEMS,INVENTORY,CUSTOMEREMPLOYEE,ORDERS WHERE ORDERS.CUSTOMEREMPLOYEEID=CUSTOMEREMPLOYEE.ID AND ORDERS.ID=ORDERITEMS.ORDERID AND INVENTORY.ID=ORDERITEMS.INVENTORYID AND ORDERITEMS.SHIPMENTID='$shipmentid' ORDER BY ORDERID",$db);				
				//$oisumresult = db_query("SELECT ORDERID, SUM( ORDERITEMS.QTYSHIPPED )  AS QTYSHIPPED, ORDERITEMS.ITEMDESCRIPTION, SKU, DESCRIPTION, CUSTOMEREMPLOYEE.FIRSTNAME, CUSTOMEREMPLOYEE.LASTNAME FROM ORDERS, ORDERITEMS, INVENTORY, CUSTOMEREMPLOYEE WHERE ORDERITEMS.SHIPMENTID = '$shipmentid' AND ORDERS.ID = ORDERITEMS.ORDERID AND INVENTORY.ID = ORDERITEMS.INVENTORYID AND CUSTOMEREMPLOYEE.ID = ORDERS.CUSTOMEREMPLOYEEID GROUP BY ORDERS.CUSTOMEREMPLOYEEID, ORDERITEMS.INVENTORYID ORDER BY ORDERID",$db);				
				$oisumresult = db_query("SELECT ORDERID, SUM( ORDERITEMS.QTYSHIPPED )  AS QTYSHIPPED, ORDERITEMS.ITEMDESCRIPTION, SKU, DESCRIPTION, CUSTOMEREMPLOYEE.FIRSTNAME, CUSTOMEREMPLOYEE.LASTNAME FROM ORDERS, ORDERITEMS, INVENTORY, CUSTOMEREMPLOYEE WHERE ORDERITEMS.SHIPMENTID = '$shipmentid' AND ORDERS.ID = ORDERITEMS.ORDERID AND INVENTORY.ID = ORDERITEMS.INVENTORYID AND CUSTOMEREMPLOYEE.ID = ORDERS.CUSTOMEREMPLOYEEID GROUP BY ORDERS.CUSTOMEREMPLOYEEID, ORDERITEMS.INVENTORYID, ORDERITEMS.ITEMDESCRIPTION ORDER BY ORDERID",$db);				
				//now draw a table with order #, sku, desc, name

			    $tnewdata[] = array('ORDERID'=>'<b>ORDER #</b>','QTYSHIPPED'=>'<b>QTY</b>','SKU'=>'<b>SKU</b>','DESCRIPTION'=>'<b>DESCRIPTION</b>','NAME'=>'<b>EMP NAME</b>');

	  		    $colsnew = array('ORDERID'=>1,
	  		  		'QTYSHIPPED'=>2,
	  		  		'SKU'=>3,
					'DESCRIPTION'=>4,
					'NAME'=>5);
					
	  		    $pdf->ezTable($tnewdata, $colsnew,''
									   ,array('shaded'=>0
									   ,'width'=>500
									   ,'showLines'=>1
									   ,'showHeadings'=>0
									   ,'cols'=>array(
									    'ORDERID'=>array('justification'=>'left'
													,'width'=>60)									   
									    ,'QTYSHIPPED'=>array('justification'=>'center'
													,'width'=>40)									   
									   ,'SKU'=>array('justification'=>'left'
													,'width'=>110)									   
									   ,'DESCRIPTION'=>array('justification'=>'left','width'=>'200')
   									   ,'NAME'=>array('justification'=>'left'
													,'width'=>110)									   
 								    			    )									   									   
									   		   	 )
									   );
				unset($tnewdata);
		
				while ($sumoirrow=mysql_fetch_array($oisumresult))
				{
				//find each order in this packing slip
				//add a line for each order item with order #, sku, desc, and name
							  //populate array with values		  
			    
			  	if($sumoirrow[ITEMDESCRIPTION])	  
				  	$displaydesc = $sumoirrow[ITEMDESCRIPTION];
		  		else
	  				$displaydesc = $sumoirrow[DESCRIPTION];
				
				//'DESCRIPTION'=>ucwords(strtolower($displaydesc)),
				$pdftemp2data[] = array('ORDERID'=>substr($sumoirrow[ORDERID],-8),
			  		'QTYSHIPPED'=>$sumoirrow[QTYSHIPPED],	  
	  		  		'SKU'=>$sumoirrow[SKU],
					'DESCRIPTION'=>$displaydesc,
					'NAME'=>$sumoirrow[LASTNAME].", ".substr($sumoirrow[FIRSTNAME],0,1));
	
		//show this line of the table								   
	  		  		 $YBottom=$pdf->ezTable($pdftemp2data, $colsnew,''
				   				,array('shaded'=>0
				   				,'width'=>500
				   				,'showLines'=>0
				   				,'showHeadings'=>0
				   				,'cols'=>array(
									    'ORDERID'=>array('justification'=>'left'
													,'width'=>60)									   
									    ,'QTYSHIPPED'=>array('justification'=>'center'
													,'width'=>40)									   
									   ,'SKU'=>array('justification'=>'leftr'
													,'width'=>110)									   
									   ,'DESCRIPTION'=>array('justification'=>'left','width'=>'200')
   									   ,'NAME'=>array('justification'=>'leftt'
													,'width'=>110)									   
 								    			    )									   									   
									   		   	 )
					   );
				
		  
					//draw a line after each line	   
					$pdf->line(50,$YBottom,540,$YBottom);
					
					//cleae table contents for next time		   
				    unset($pdftemp2data);
				}
		
////
$pdf->ezText('',10,array('justification'=>'left'));
$pdf->ezText('',10,array('justification'=>'left'));
$pdf->ezText('<b>NOTE:</b>',10,array('justification'=>'left'));
$pdf->ezText($orderrow[SHIPPINGNOTES],10,array('justification'=>'left'));
//	
$pdf->ezSetMargins(30,40,40,40);

//////////////////////////////
//add bar codes
//////////////////////////////
//add customer ID
//we're C39, alpha-numeric, and mength...
$code=$shiptorow[STORENUMBER];
//if Inter 2 of 5 we must be even length and numbers only, use these...
//$code=intval($shiptorow[STORENUMBER]);
//if (strlen($code)/2 != intval(strlen($code)/2))
//   $code ='0'.$code;

//////////////////////////////   GENERATE BARCODE FOR STORENUMBER     /////////////////////////////////////////////////////
//
//file("http://poise.bennettuniform.com/poise/barcodes/image.php?code=$code&style=164&type=C39&width=400&height=60&xres=1&font=2");
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//ezImage(image,[padding],[width],[resize],[justification],[array border])
//bottom text string 
//$pdf->ezSetY(100);
//$pdf->ezSetX(0);
//$BottomGreyBar=$pdf->ezText("",22,array('justification'=>'center'));
//$pdf->ezImage('../barcodes/'.$code.'.jpg',0,350,'full','left',array('width'=>2) );

//////////////////////////////   PRINT BARCODE FOR STORENUMBER     /////////////////////////////////////////////////////
//
//$pdf->addJpegFromFile('../barcodes/'.$code.'.jpg',0,40,250);
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//add shipment ID
$code=intval($shipmentid);
if (strlen($code)/2 != intval(strlen($code)/2))
   $code ='0'.$code;
file("http://poise.bennettuniform.com/poise/barcodes/image.php?code=$code&style=164&type=C39&width=250&height=60&xres=1&font=2");

//ezImage(image,[padding],[width],[resize],[justification],[array border])
//bottom text string 
//$pdf->ezSetY(150);
//$pdf->ezSetX(400);
//$BottomGreyBar=$pdf->ezText("",22,array('justification'=>'center'));
//$pdf->ezImage('../barcodes/'.$code.'.jpg',0,350,'full','right',array('width'=>2));
$pdf->addJpegFromFile('../barcodes/'.$code.'.jpg',350,40,250);
//bottom text string 

				
//bottom text string 
$BottomGreyBar=$pdf->ezText("",22,array('justification'=>'center'));
		 $pdf->ezInsertMode(0,1,'before');
		
		 
}

}


//output pdf to browser
$pdf->ezStream();
?>