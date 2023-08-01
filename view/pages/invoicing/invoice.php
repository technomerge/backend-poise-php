<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

$invoiceid = $_GET['invoiceid'];
$invoiceid = substr($invoiceid,-8);

include ('../../../models/mysql/db_functions.php');
include_once ('../../../../assets/pdf/class.ezpdf.php');

$pdf = new Cezpdf('LETTER','PORTRAIT');


createPDFinvoice($invoiceid);
		
		

function createPDFinvoice($invoiceid){

global $pdf;
///////////////////////////////////////////////////////////////////////////////
//this is where we build and display the Invoice as a PDF file...
///////////////////////////////////////////////////////////////////////////////

//setup formatting and options

	$query = "SELECT * FROM INVOICES WHERE ID = '$invoiceid'";
	$invoice_info = mysql_call($query);
	
	$query = "SELECT SUM(AMOUNT) AS AMOUNT FROM PAYMENTS WHERE INVOICEID = '$invoiceid'";
	$payment_info = mysql_call($query);
	
	$empID = $invoice_info[0]['EMPLOYEEID'];
	$query = "SELECT * FROM EMPLOYEE WHERE ID = '$empID'";
	$employee_info = mysql_call($query);	
		
	$query = "SELECT * FROM ORDERITEMS WHERE INVOICEID = '$invoiceid'";
	$oitems_list = mysql_call($query);
	$shipmentid = substr($oitems_list[0]['SHIPMENTID'],-8);
	
	$query = "SELECT * FROM SHIPMENT WHERE ID = '$shipmentid'";
	$shipment_info = mysql_call($query);

	$orderID = $oitems_list[0]['ORDERID'];	
	
	$query = "SELECT * FROM ORDERS WHERE ID = '$orderID'";
	$order_info = mysql_call($query);
	$orderrowID = substr($order_info[0]['ID'],-8);
	
	$customerID = $order_info[0]['CUSTOMERID'];
	$shipToID = $order_info[0]['SHIPTOID'];
	
	$query = "SELECT * FROM CUSTOMERS WHERE ID = '$customerID'";
	$billto_info = mysql_call($query);	
	$billtoAcctID = $billto_info[0]['ACCOUNTID'];
	
	$query = "SELECT * FROM CUSTOMERS WHERE ID = '$shipToID'";
	$shipto_info = mysql_call($query);	
	
	$query = "SELECT * FROM ACCOUNTS WHERE ID = '$billtoAcctID'";
	$account_info = mysql_call($query);	
	

	$b1 = $billto_info[0]['NAME'];
	$b2 = $billto_info[0]['ADDRESS1'];
	$b3 = $billto_info[0]['ADDRESS2'];
	$b4 = $billto_info[0]['CITY'] . ', ' . $billto_info[0]['STATE'] . ' ' . $billto_info[0]['ZIP'];
	if(!$b3){
		$b3 = $b4;
		$b4 = '';
	}
	
	$s1 = $shipto_info[0]['NAME'];
	$s2 = $shipto_info[0]['ADDRESS1'];
	$s3 = $shipto_info[0]['ADDRESS2'];
	$s4 = $shipto_info[0]['CITY'] . ', ' . $shipto_info[0]['STATE'] . ' ' . $shipto_info[0]['ZIP'];
	if(!$s3){
		$s3 = $s4;
		$s4 = '';
	}	
	
	$bssnum = $order_info[0]['SSNUM'];
	$sssnum = $order_info[0]['SSNUM'];
	$bsn = $billto_info[0]['STORENUMBER'];
	$ssn = $shipto_info[0]['STORENUMBER'];
	
	$orderrowPAYMENTTYPE = "OP";
	$accountrowFACTOROPEN = "NO";
	  
	$RemitToName = "RemitToName not setup";
	$RemitToAddress = "RemitToAddress not setup";
	$RemitToCity = "RemitToCity not setup";
	$RemitToState = "RemitToState not setup";
	$RemitToZip = "RemitToZip not setup";
	
	$ToWhomText1 = '';
	$ToWhomText2 = '';
	$ToWhomText3 = '';
	$ToWhomText4 = '';
	
	$accountrowPREFIX = $account_info[0]['PREFIX'];
	$orderrowPONUMBER = $order_info[0]['PONUMBER'];
	$trackrowTRACKNUM1 = $shipment_info[0]['TRACKINGNUMBER'];
	$trackrowTRACKNUM2 = "";
	$trackrowTRACKNUM3 = "";
	$trackrowTRACKNUM4 = "";

	$invoicedate = date("Y-m-d", strtotime($invoice_info[0]['DATE']));
	$invoicerowTERMS = $invoice_info[0]['TERMS'];
	$invoiceduedate = date("Y-m-d", strtotime($invoice_info[0]['DUEDATE']));

	for($i=0; $i<sizeof($oitems_list); $i++){
		$oibo[$i] = $oitems_list[$i]['QTYBO']; 
		$oishipped[$i] = $oitems_list[$i]['QTYSHIPPED'];
		$oisold[$i] = $oitems_list[$i]['QTYSOLD'];
		$oiamount[$i] = $oitems_list[$i]['AMOUNT'];
		
		$inventoryID = $oitems_list[$i]['INVENTORYID'];
		$query = "SELECT * FROM INVENTORY WHERE ID = '$inventoryID'";
		$inventory_info = mysql_call($query);	
		$oisku[$i] = $inventory_info[0]['SKU'];
		
		$oidescription[$i] = $oitems_list[$i]['INVDESCRIPTION'];
		$oiprice[$i] = $oitems_list[$i]['PRICE'];		
	}


	$invoicediscount = $invoice_info[0]['DISCOUNT'];

	$orderrowRESTOCKINGFEERATE = $order_info[0]['RESTOCKINGFEERATE'];
	$returnfee = $invoice_info[0]['RETURNFEE']; 
	$invoicerowSHIPCOST = $invoice_info[0]['SHIPCOST'];
	$invoicerowHANDLINGFEE = $invoice_info[0]['HANDLINGFEE'];
	$SandH = $invoicerowSHIPCOST + $invoicerowHANDLINGFEE;
	$invoicehandlingfee = $invoice_info[0]['HANDLINGFEE'];
	$HandH = $invoicehandlingfee;
	$FormattedSandH = sprintf("%01.2f",$SandH);
	$FormattedHand = sprintf("%01.2f",$HandH);				  
 		    
	$printcod = $invoice_info[0]['CODFEE'];
	$printcod = sprintf("%1.2f", $printcod);
	
	$dsfee = $invoice_info[0]['DROPSHIPFEE'];
	$printdropship = sprintf("%1.2f", $dsfee);

	$payments = $payment_info[0]['AMOUNT'];
	$printpayments = sprintf("%1.2f", $payments);

	$bal = $invoice_info[0]['BALANCE'];
	$printbalance = sprintf("%1.2f", $bal);

	$printtotal = $invoice_info[0]['TOTAL'];
	$printtotal = sprintf("%1.2f", $printtotal);
	
	$totaltax = $invoice_info[0]['TAX'];
	$totaltax = sprintf("%1.2f", $totaltax);

	$invFirstInitial = strtoupper(substr($employee_info[0]['FIRSTNAME'],0,1));
	$invLastInitial = strtoupper(substr($employee_info[0]['LASTNAME'],0,1));
	$invoicemaker = $invFirstInitial . $invLastInitial;

	$root_path = "";
	$filename = $invoiceid . '.pdf';



//========================================================================//

$header = get_header();

$pdf->selectFont('../../../../assets/pdf/fonts/Helvetica.afm');
$pdf->ezSetMargins(30,30,50,60);

//draw_header($pdf, $header, $doc_type, $doc_id);

$pdf->addInfo('Title',$header['clientname'].' Invoice #'.$invoiceid);
$pdf->addInfo('Author','POISE');
$pdf->addinfo('CreationDate',$invoicedate);

//draw logo and business info centered at top
$pdf->ezText($header['clientname'],16,array('justification'=>'center'));
$pdf->ezText($header['clientaddress'] . ", " . $header['clientcitystatezip'] . "  |  " . $header['clientphone']. " " . $header['clientfax'],10,array('justification'=>'center'));
$pdf->ezText('',10,array('justification'=>'center'));
$pdf->ezText('<b>INVOICE</b>',22,array('justification'=>'center'));
$pdf->ezText('',10,array('justification'=>'center'));


 
if ($bssnum){
    //draw bill to/ship to table with border
    $arrayBillShipTitleData = array(
         array('BILL'=>$b1,'SHIP'=>$s1)
        ,array('BILL'=>$bssnum,'SHIP'=>$sssnum)
        ,array('BILL'=>$bsn,'SHIP'=>$ssn)
        ,array('BILL'=>$b2,'SHIP'=>$s2)
        ,array('BILL'=>$b3,'SHIP'=>$s3)
        ,array('BILL'=>$b4,'SHIP'=>$s4) 
    );
}
else{
    //draw bill to/ship to table with border
    $arrayBillShipTitleData = array(
        array('BILL'=>$b1,'SHIP'=>$s1)
        ,array('BILL'=>$bsn,'SHIP'=>$ssn)
        ,array('BILL'=>$b2,'SHIP'=>$s2)
        ,array('BILL'=>$b3,'SHIP'=>$s3)
        ,array('BILL'=>$b4,'SHIP'=>$s4) 
    );
}

$cols = array('BILL'=>'<b>BILL TO:</b>','SHIP'=>'<b>SHIP TO:</b>');

$pdf->ezTable(
	$arrayBillShipTitleData,
	$cols,
	'',
	array(
		'shaded'=>0,
		'width'=>500,
		'showLines'=>1,
		'cols'=>array(
				'BILL'=>array('width'=>250),
				'SHIP'=>array('width'=>250)
		)
	)
);
						
						
						
										
//small gap before next section									   
$pdf->ezText("",8,array('justification'=>'center'));




if ((	$orderrowPAYMENTTYPE == 'OP' OR 
		$orderrowPAYMENTTYPE == '1TD' OR 
		$orderrowPAYMENTTYPE == 'OPEN') 
		AND
		$accountrowFACTOROPEN == 'YES'
	){
    
	$citText1 = $RemitToName;
    $citText2 = $RemitToAddress;
    $citText3 = $RemitToCity.", ".$RemitToState." ".$RemitToZip;

    $ToWhomText1 = '<b>To Whom This Invoice is Assigned:</b> Remittance';
    $ToWhomText2 = 'is to be made only to Bennett Uniform. Any';
    $ToWhomText3 = 'objections to this invoice or terms must be reported';
    $ToWhomText4 = 'to Bennett Uniform within ten (10) days of receipt.';

    $drawbox = TRUE;
}
else{
    $citText1 = $header['clientname'];
    $citText2 = $header['clientaddress'];
    $citText3 = $header['clientcitystatezip'];
    $drawbox = FALSE;		
}


//draw payable to and account info block, part 1
$arrayPayableAccountData = array(
	array('C1'=>'<b>This Invoice is Payable To:</b>','C2'=>'Account Number:','C3'=>$accountrowPREFIX),
	array('C1'=>'<b>'.$citText1.'</b>','C2'=>'P.O. Number:','C3'=>$orderrowPONUMBER),
	array('C1'=>'<b>'.$citText2.'</b>','C2'=>'Tracking Number:','C3'=>$trackrowTRACKNUM1),
	array('C1'=>'<b>'.$citText3.'</b>','C2'=>'','C3'=>$trackrowTRACKNUM2)
);

array_push($arrayPayableAccountData,
	array(
		'C1'=>'',
		'C2'=>'',
		'C3'=>$trackrowTRACKNUM3)
);
	
array_push($arrayPayableAccountData,
	array(	'C1'=>'',
			'C2'=>'',
			'C3'=>$trackrowTRACKNUM4)
);



$cols = array('C1'=>'c1','C2'=>'c2','C3'=>'c3');
$BoxTop = $pdf->ezTable($arrayPayableAccountData, 
							$cols,
							'',
							array(
								'shaded'=>0,
								'width'=>500,
								'showLines'=>0,
								'showHeadings'=>0,
								'cols'=>array(
										'C2'=>array('justification'=>'right'),
										'C3'=>array('justification'=>'left',
										'width'=>120)
								)
							)
						);



//draw payable to and account info block, part 2
$terms = $invoicerowTERMS;
if($terms == "1TD")
    $printTerms = "1% 30 Days";
else
    $printTerms = $terms;




$arrayPayableAccountData2 = array(
     array('C1'=>$ToWhomText1,'C2'=>'<b>INVOICE NUMBER:</b>','C3'=>'<b>'.ltrim($invoiceid,0).'</b>'),
	 array('C1'=>$ToWhomText2,'C2'=>'Invoice Date:','C3'=>$invoicedate),
	 array('C1'=>$ToWhomText3,'C2'=>'Terms:','C3'=>$printTerms),
	 array('C1'=>$ToWhomText4,'C2'=>'Due Date:','C3'=>$invoiceduedate) 
);

$cols = array('C1'=>'c1','C2'=>'c2','C3'=>'c3');


$BoxBottom = $pdf->ezTable(
		$arrayPayableAccountData2,
		$cols,
		'',
		array(
			'shaded'=>0,
			'width'=>500,
			'showLines'=>0,
			'showHeadings'=>0,
			'cols'=>array(
                       'C2'=>array('justification'=>'right'),
					   'C3'=>array('justification'=>'left',
					   'width'=>120)
						)
			)
        );

//small gap before next section
$pdf->ezText("",6,array('justification'=>'center'));
$TopGreyBar=$pdf->ezText("",8,array('justification'=>'center'));



//draw rectangle around 'To Whom' if it exists
if ($drawbox == TRUE){
   //go back up and draw box around CIT text if it exists								   
   $pdf->setLineStyle(2);
   $pdf->line(50,$BoxTop,285,$BoxTop);
   $pdf->line(50,$BoxTop,50,$BoxBottom-5);
   $pdf->line(285,$BoxTop,285,$BoxBottom-5);
   $pdf->line(50,$BoxBottom-5,285,$BoxBottom-5);
}


						
//draw top grey rectangle BEFORE order items						
$pdf->setLineStyle(8);
$pdf->line(50,$TopGreyBar,550,$TopGreyBar);			

//draw order items table
$pdfoiarraydata = array();




$oiindex = sizeof($oibo);

$firstrow = 1;	//show header before 1st row only
$newoiindex = 0;
$tempsubtotal = 0;
while ($newoiindex < $oiindex){	   
    $tempoibo = $oibo[$newoiindex];	  
    $tempoishipped = $oishipped[$newoiindex];
    if ($tempoishipped == '' OR $tempoishipped == 0)
         $tempoishipped = $tempoibo;

    if ($tempoibo < '1' AND $tempoishipped < '1')
         $tempoibo = $oisold[$newoiindex];		  

    $tempsubtotal = $tempsubtotal + $oiamount[$newoiindex];			  
    $pdfoiarraydata[] = array(
				'QTYSOLD'=>$oisold[$newoiindex],
                'QTYSHIPPED'=>$tempoishipped,
                'SKU'=>$oisku[$newoiindex],
                'DESCRIPTION'=>ucwords(strtolower($oidescription[$newoiindex])),
                'PRICE'=>$oiprice[$newoiindex],
                'AMOUNT'=>$oiamount[$newoiindex]
	);


    //echo " $pdfoiarraydata[] = array('QTYSHIPPED'=>$tempoishipped,'SKU'=>$oisku[$newoiindex],'DESCRIPTION'=>ucwords(strtolower($oidescription[$newoiindex])),'PRICE'=>$oiprice[$newoiindex],'AMOUNT'=>$oiamount[$newoiindex]);";								   
    $newoiindex++;
}


$cols = array(
			'QTYSOLD'=>'<b><u>Shipped</u></b>',
            'QTYSHIPPED'=>'<b><u>Billed</u></b>',
            'SKU'=>'<b><u>SKU</u></b>',
            'DESCRIPTION'=>'<b><u>Description</u></b>',
            'PRICE'=>'<b><u>Price</u></b>',
            'AMOUNT'=>'<b><u>Amount</u></b>'
		);

$pdf->ezTable(
		$pdfoiarraydata,
		$cols,
		'',
		array(
			'shaded'=>0,
			'width'=>500,
			'showLines'=>0,
			'showHeadings'=>$firstrow,
			'cols'=>array(
                    'QTYSOLD'=>array('justification'=>'center', 'width'=>50),
					'QTYSHIPPED'=>array('justification'=>'center', 'width'=>50),
					'SKU'=>array('justification'=>'left', 'width'=>120),
					'DESCRIPTION'=>array('justification'=>'left'),
					'PRICE'=>array('justification'=>'right', 'width'=>60),
					'AMOUNT'=>array('justification'=>'right','width'=>60)
                    )
			)
);


		  
$tempsubtotaldisplay = $tempsubtotal;
$tempsubtotaldisplay = sprintf("%01.2f",$tempsubtotaldisplay);

$tempsubtotal = $tempsubtotal - $invoicediscount;	  
$tempsubtotal = sprintf("%01.2f",$tempsubtotal);

$pdf->ezText("",6,array('justification'=>'center'));


$invoicediscount = sprintf("%01.2f",$invoicediscount);	
///////////////////////////////// Alex
$arraySubtotalData = array(array('C1'=>'Subtotal:','C2'=>$tempsubtotaldisplay));
$cols = array('C1'=>'c1','C2'=>'c2');

$pdf->ezTable(
	$arraySubtotalData,
	$cols,
	'',
	array(
		'shaded'=>0,
		'width'=>500,
		'showLines'=>0,
		'showHeadings'=>0,
		'cols'=>array(
				'C1'=>array('justification'=>'right'),
				'C2'=>array('justification'=>'right','width'=>100)
               )
     )
);



////////////////////////// Alex
// 22/11/04
if ($orderrowRESTOCKINGFEERATE > 0){
    $arraySubtotalData = array(
			array(
				'C1'=>'Restocking Fee ('.($orderrow[RESTOCKINGFEERATE]+0).'%):',
				'C2'=>number_format($restockingfee,2)
			)
    );
    $cols = array('C1'=>'c1','C2'=>'c2');
    
	$pdf->ezTable(
		$arraySubtotalData,
		$cols,
		'',
		array(
			'shaded'=>0,
			'width'=>500,
			'showLines'=>0,
			'showHeadings'=>0,
			'cols'=>array(
				'C1'=>array('justification'=>'right'),
				'C2'=>array('justification'=>'right', 'width'=>100)
            )
		)
    );
}
//***********

// 03/12/04
if ($returnfee > 0){
    $arraySubtotalData = array(array('C1'=>'Return Fee:','C2'=>number_format($returnfee,2))
                                               );
    $cols = array('C1'=>'c1','C2'=>'c2');
    
	$pdf->ezTable(
		$arraySubtotalData,
		$cols,
		'',
		array(
			'shaded'=>0,
			'width'=>500,
			'showLines'=>0,
			'showHeadings'=>0,
			'cols'=>array(
				'C1'=>array('justification'=>'right'),
				'C2'=>array('justification'=>'right', 'width'=>100)
            )
		)
    );
}
//***********
if ($invoicediscount > 0){
    $arraySubtotalData = array(array('C1'=>'Discount:('.$fdiscount.'%)','C2'=>'('.$invoicediscount.')'));	

    $cols = array('C1'=>'c1','C2'=>'c2');
    
	$pdf->ezTable(
		$arraySubtotalData,
		$cols,
		'',
		array(
			'shaded'=>0,
			'width'=>500,
			'showLines'=>0,
			'showHeadings'=>0,
			'cols'=>array(
				'C1'=>array('justification'=>'right'),
				'C2'=>array('justification'=>'right', 'width'=>100)
            )
        )
    );
}

	
//draw subtotal, tax, etc. table
//////////////////////////////////////////alex 
$arraySubtotalData = array(
		//array('C1'=>'Subtotal:','C2'=>$tempsubtotal)
		array('C1'=>'Tax:','C2'=>$totaltax),
		array('C1'=>'Shipping/Handling:','C2'=>$FormattedSandH),
		array('C1'=>'COD Fee:','C2'=>$printcod),
		array('C1'=>'Drop Ship Fee:','C2'=>$printdropship),
		array('C1'=>'<b>Total Invoice:</b>','C2'=>'<b>'.$printtotal.'</b>'),
		array('C1'=>'Amt. Paid:','C2'=>'('.$printpayments.')'),
		array('C1'=>'<b>BALANCE DUE</b>','C2'=>'<b>'.$printbalance.'</b>') 
);

$cols = array('C1'=>'c1','C2'=>'c2');


$pdf->ezTable(
	$arraySubtotalData,
	$cols,
	'',
	array(
		'shaded'=>0,
		'width'=>500,
		'showLines'=>0,
		'showHeadings'=>0,
		'cols'=>array(
			'C1'=>array('justification'=>'right'),
			'C2'=>array('justification'=>'right', 'width'=>100)
        )
    )
);



//draw bottom grey rectangle and credit text string at bottom
$pdf->ezSetY(100);

//$pdf->ezText("",8,array('justification'=>'center'));

$BottomGreyBar=$pdf->ezText("",8,array('justification'=>'center'));
$pdf->setLineStyle(8);

$pdf->line(50,$BottomGreyBar,550,$BottomGreyBar);			
$pdf->ezText(
	'<b>Payments received after 30 days are subject to 1% per month late charge. Credit will be issued for returned goods only with RA# attached to outside of package.  All claims must be made within five (5) days of receipt of goods.</b>',
	10,
	array('justification'=>'left')
);

//BU workflow info at bottom left corner
$pdf->ezSetY(50);


$pdf->ezText(
	'<b>I='.$invoicemaker.', O=<c:alink:'.$root_path.'Print/Order.php?orderid='.$orderrowID.'>'.substr($orderrowID,-8).'</c:alink></b> <b>PS=<c:alink:'.$root_path.'Print/PDFPackingSlip.php?shipmentid='.$shipmentid.'>'.$shipmentid.'</c:alink></b>',
	8,
	array('justification'=>'right')
);

//$pdf->ezText($debug,10,array('justification'=>'left'));





//output pdf to browser
$pdf->ezStream();

//save pdf to file
//$pdf->ezSaveFile($filename);
}


?>


<?php

//////////////////////////FUNCTIONS//////////////////////////

function get_header(){

	$header['clientname'] = "Bennett Uniform Manufacturing Company Inc.";//
	$header['clientaddress'] ="4377 Federal Drive";
	$header['clientcitystatezip'] ="Greensboro NC, 27410";
	$header['clientphone'] ="Phone: 336-232-5772";
	$header['clientfax'] = "Fax: 336-232-4773";
	
	return($header);

}
?>