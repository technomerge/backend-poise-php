
Main();

function Main()
{
	AjaxPopulatePoList('search', '');


	url = new URL(window.location.href);
	param = new URLSearchParams(url.search.slice(1));

	if(param.has('poid') === true){
		AjaxPopulatePoInfo();
	}
	
	
}

function AjaxPopulatePoList(action, filter_param)
{
	
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			//document.getElementById("sel-po").innerHTML=httpxml.responseText;
			
			//Clear the list
			document.getElementById("sel-po").innerHTML='';
			
			po = JSON.parse(httpxml.responseText);
			
			var $dropdown = $("#sel-po");
			
			$.each(po, function() {
				$dropdown.append($("<option style='font-size:14px' />").val(parseInt(this.PO_ID)).text((this.PO_INFO).replaceAll('|',String.fromCharCode(9675))));
			});
			
			totalRecords = Object.keys(po).length
			document.getElementById("sup-total").innerHTML = '<i><small>Total records: ' + totalRecords + '</small></i>';
		}
	}
	
	//alert(document.getElementById("inp-search").value);	

	search_param = {};
	if(action == 'clear'){
		document.getElementById("inp-search").value = '';
		search_param.INFO = document.getElementById("inp-search").value;
		search_param_str = JSON.stringify(search_param);
	}
	else if(action == 'filter'){
		search_param_str = filter_param;
	}
	else{
		search_param.INFO = document.getElementById("inp-search").value;
		search_param_str = JSON.stringify(search_param);
	}

	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=search_po" + "&param=" + encodeURIComponent(search_param_str);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}	

function AjaxPopulatePoInfo()
{

	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			po = JSON.parse(httpxml.responseText);
			
			document.getElementById("save_record").style.display = 'block';
			document.getElementById("print_record").style.display = 'block';
			document.getElementById("undo_record").style.display = 'block';
			document.getElementById("delete_record").style.display = 'block';
			document.getElementById("close_record").style.display = 'block';
			
			document.getElementById("div_shipfrom").style.display = 'block';
			document.getElementById("div_shipto").style.display = 'block';
			document.getElementById("div_items").style.display = 'block';
			document.getElementById("div_details").style.display = 'block';
			document.getElementById("div_notes").style.display = 'block';
			
			
			document.getElementById("po-id").value = parseInt(po.ID);
			document.getElementById("po-id-static").innerHTML = 'PO #' + parseInt(po.ID);
			
			document.getElementById("div_select_supplier").style.display = 'none';
			document.getElementById("div_newitem").style.display = 'none';
			
			AjaxPopulatePoItems();
			
			document.getElementById("poitems_tax").innerHTML = parseFloat(po.TAX).toFixed(2);
			document.getElementById("poitems_shipping").innerHTML = parseFloat(po.SHIPCOST).toFixed(2);
			
			
			AjaxPopulateSupplierInfo(parseInt(po.SUPPLIERID));
			
			AjaxPopulateBumInfo();
			
			document.getElementById("po-status").value = po.STATUS;
			if(po.STATUS == 'CLOSED'){
				document.getElementById("po-status-sw-closed").checked = true;
			}
			else if(po.STATUS == 'RECEIVED'){
				document.getElementById("po-status-sw-received").checked = true;
			}
			else{
				document.getElementById("po-status-sw-open").checked = true;
			}			

			AjaxPopulatePoDuedate(parseInt(po.ID));

			
			document.getElementById("po-employee").value = parseInt(po.EMPLOYEEID);
			AjaxPopulateEmployeesList();
			
			document.getElementById("po-ship-via").value = po.SHIPVIA;
			PopulatePoShipViaList();

			document.getElementById("po-payment-type").value = po.PAYMENTTYPE;
			PopulatePoPaymentTypeList();			
			
			document.getElementById("po-notes").value = po.NOTES;
			
		
			window.history.replaceState(null ,null, "?poid=" + poid);
			
		}
	}
	
	//alert(document.getElementById("sel-po").value);
	
	
	url = new URL(window.location.href);
	curparam = new URLSearchParams(url.search.slice(1));
	
	if(document.getElementById("sel-po").value){
		poid = document.getElementById("sel-po").value;
	}
	else if(curparam.has('poid') === true){
		poid = curparam.get('poid');
	}

	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=po_info" + "&param=" + encodeURIComponent(poid);	
	
	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AjaxPopulatePoItems()
{

	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			poitems = JSON.parse(httpxml.responseText);
			
			tbody = document.getElementById("tbody_poitems");
			tbody.innerHTML="";
			poitems_subtotal = 0;
			poitems_total = 0;
			
			for(i=0; i<poitems.length; i++){
				row = tbody.insertRow();
				
				received = row.insertCell(0);
				received.style.textAlign = "center";
				received.innerHTML = poitems[i].QTYRECEIVED;
				
				qty = row.insertCell(1);
				qty.style.textAlign = "center";
				qty.innerHTML = poitems[i].QTY;
				
				sku = row.insertCell(2);
				sku.innerHTML = poitems[i].VENDORSKU;
				
				description = row.insertCell(3);
				description.innerHTML = poitems[i].DESCRIPTION;
				
				price = row.insertCell(4);
				price.style.textAlign = "right";
				price.innerHTML = parseFloat(poitems[i].PRICE).toFixed(2);
				
				amt = parseFloat(poitems[i].AMOUNT);
				amount = row.insertCell(5);
				amount.style.textAlign = "right";
				amount.innerHTML = amt.toFixed(2);
				
				duedate = row.insertCell(6);
				duedate.innerHTML = poitems[i].DUEDATE;
				duedate.style = "white-space: nowrap";

				poitems_subtotal = poitems_subtotal + amt;
			}
			
			document.getElementById("poitems_subtotal").innerHTML = poitems_subtotal.toFixed(2);

			poitems_total = parseFloat(document.getElementById("poitems_subtotal").innerHTML) +
							parseFloat(document.getElementById("poitems_tax").innerHTML) +
							parseFloat(document.getElementById("poitems_shipping").innerHTML);
			
			document.getElementById("poitems_total").innerHTML = poitems_total.toFixed(2);
		}
	}
	
	
	url = new URL(window.location.href);
	curparam = new URLSearchParams(url.search.slice(1));
	
	if(document.getElementById("sel-po").value){
		poid = document.getElementById("sel-po").value;
	}
	else if(curparam.has('poid') === true){
		poid = curparam.get('poid');
	}

	//alert(poid);
	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=po_get_items" + "&param=" + encodeURIComponent(poid);	
	

	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AddPoInfo()
{
	ClosePoInfo();

	document.getElementById("save_record").style.display = 'block';
	document.getElementById("print_record").style.display = 'block';
	document.getElementById("undo_record").style.display = 'block';
	document.getElementById("delete_record").style.display = 'block';
	document.getElementById("close_record").style.display = 'block';
	
	document.getElementById("div_shipfrom").style.display = 'block';
	document.getElementById("div_shipto").style.display = 'block';
	document.getElementById("div_items").style.display = 'block';
	document.getElementById("div_details").style.display = 'block';
	document.getElementById("div_notes").style.display = 'block';
	
	document.getElementById("div_select_supplier").style.display = 'block';

	//document.getElementById("sel-supplier").value = '';

	document.getElementById("div_newitem").style.display = 'block';
	$('.input-group.date').datepicker({format: "yyyy-mm-dd"});
	
	
	window.history.replaceState(null ,null, "?poid=" + 0);
}

function ResetTextFields()
{

	//something to try instead of listing each field
	//document.getElementById("myform").reset();

	document.getElementById("po-id").value = '0';
	document.getElementById("po-id-static").innerHTML = '';

	document.getElementById("sel-supplier").value = '';
	
	document.getElementById("poitem-qty-add").value = '';
	document.getElementById("poitem-sku-add").value = '';
	document.getElementById("poitem-description-add").value = '';
	document.getElementById("poitem-price-add").value = '';
	document.getElementById("poitem-duedate-add").value = '';	
	
	document.getElementById("tbody_poitems").innerHTML="";
	document.getElementById("poitems_subtotal").innerHTML = '0.00';
	document.getElementById("poitems_tax").innerHTML = '0.00';
	document.getElementById("poitems_shipping").innerHTML = '0.00';	
	document.getElementById("poitems_total").innerHTML = '0.00';	

	document.getElementById("po-status").value = '';
	document.getElementById("po-status-sw-open").checked = true;
	
	document.getElementById("po-duedate").value = '';
	document.getElementById("po-duedate-sel").value = '';
	
	document.getElementById("po-employee").value = '';
	document.getElementById("po-employee-sel").value = '';
	
	document.getElementById("po-ship-via").value = '';
	document.getElementById("po-ship-via-sel").value = '';
	
	document.getElementById("po-payment-type").value = '';
	document.getElementById("po-payment-type-sel").value = '';

	document.getElementById("po-notes").value = '';
	
	document.getElementById("po-sup-id").value = '';
	document.getElementById("po-shipfrom-name").value = '';
	document.getElementById("po-shipfrom-name-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-address").value = '';
	document.getElementById("po-shipfrom-address-static").innerHTML = '';

	document.getElementById("po-shipfrom-address1").value = '';
	document.getElementById("po-shipfrom-address1-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-city").value = '';
	document.getElementById("po-shipfrom-city-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-state").value = '';
	document.getElementById("po-shipfrom-state-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-zipcode").value = '';
	document.getElementById("po-shipfrom-zipcode-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-country").value = '';
	document.getElementById("po-shipfrom-country-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-phone").value = '';
	document.getElementById("po-shipfrom-phone-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-fax").value = '';
	document.getElementById("po-shipfrom-fax-static").innerHTML = '';
	
	document.getElementById("po-shipfrom-email").value = '';
	document.getElementById("po-shipfrom-email-static").innerHTML = '';	
}

function ClosePoInfo()
{
	document.getElementById("save_record").style.display = 'none';
	document.getElementById("print_record").style.display = 'none';
	document.getElementById("undo_record").style.display = 'none';
	document.getElementById("delete_record").style.display = 'none';
	document.getElementById("close_record").style.display = 'none';
	
	document.getElementById("div_items").style.display = 'none';
	document.getElementById("div_shipfrom").style.display = 'none';
	document.getElementById("div_shipto").style.display = 'none';
	document.getElementById("div_details").style.display = 'none';
	document.getElementById("div_notes").style.display = 'none';
	
	document.getElementById("div_select_supplier").style.display = 'none';
	document.getElementById("div_newitem").style.display = 'none';
	
	ResetTextFields();
	
	window.history.replaceState(null ,null, "?poid=" + 0);
}

function AjaxSaveSupplierInfo()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			$supplier_id = parseInt(httpxml.responseText);
			if($supplier_id > 0){
				//FIXME: It would be nice to implement a modal window for the message
				alert("Record was saved succesfully!");
				document.getElementById("sup-id").value = $supplier_id;
				AjaxPopulatePoList('');
			}
			else{
				alert("There was a problem saving this record!");
			}
		}
	}

	SetTextFields();
	
	const supplier = {};
	supplier.id = document.getElementById("sup-id").value;
	supplier.name = document.getElementById("po-shipfrom-name").value;
	supplier.address = document.getElementById("po-shipfrom-address").value;
	supplier.address1 = document.getElementById("po-shipfrom-address1").value;
	supplier.city = document.getElementById("po-shipfrom-city").value;
	supplier.state = document.getElementById("po-shipfrom-state").value;
	supplier.zipcode = document.getElementById("po-shipfrom-zipcode").value;
	supplier.country = document.getElementById("po-shipfrom-country").value;
	supplier.contact = document.getElementById("sup-contact").value;
	supplier.email = document.getElementById("sup-email").value;
	supplier.phone = document.getElementById("sup-phone").value;
	supplier.fax = document.getElementById("sup-fax").value;
	supplier.status = document.getElementById("po-status").value;
	supplier.employee = document.getElementById("po-employee").value;
	supplier.transit = document.getElementById("po-ship-via").value;
	supplier.notes = document.getElementById("po-notes").value;
	
	var json_str = JSON.stringify(supplier);
	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=save_supplier_info" + "&param=" + encodeURIComponent(json_str);

	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AjaxDeleteSupplierInfo()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			$success = parseInt(httpxml.responseText);
			if($success > 0){
				//FIXME: It would be nice to implement a modal window for the message
				alert("Record was deleted succesfully!");
				document.getElementById("save_record").style.display = 'none';
				document.getElementById("print_record").style.display = 'none';
				document.getElementById("undo_record").style.display = 'none';
				document.getElementById("delete_record").style.display = 'none';
				document.getElementById("close_record").style.display = 'none';
				
				document.getElementById("div_shipfrom").style.display = 'none';
				document.getElementById("div_shipto").style.display = 'none';
				document.getElementById("div_items").style.display = 'none';
				document.getElementById("div_details").style.display = 'none';
				document.getElementById("div_notes").style.display = 'none';
				
				AjaxPopulatePoList('');
			}
			else{
				alert("There was a problem saving this record!");
			}
		}
	}

	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=delete_supplier_info" + "&param=" + document.getElementById("sup-id").value;
	
	if(confirm("You are about to delete this Vendor, are you sure?")){
		httpxml.onreadystatechange=stateck;
		httpxml.open("GET",url,true);
		httpxml.send(null);	
	}
	else{
		//Do nothing
	}
}

function AjaxPopulateStatesList()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			states = JSON.parse(httpxml.responseText);
			
			var $dropdown = $("#sup-state-sel");
			
			$dropdown.append($("<option selected />").val('').text('Select...'));
			
			$.each(states, function() {
				dropdown_selected = '';
				
				if(this.REGION_CODE == document.getElementById("po-shipfrom-state").value){
					dropdown_selected = 'selected';
				}	
				$dropdown.append($("<option " + dropdown_selected + " />").val(this.REGION_CODE).text(this.REGION_NAME));
			});
		}
	}
	//alert(document.getElementById("inp-search").value);	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=search_states" + "&param=";

	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function PopulateSupplierCountriesList()
{
	countries_json =	[
						{"CODE":"USA",		"NAME":"USA"},
						{"CODE":"Mexico",	"NAME":"Mexico"},
						{"CODE":"Canada",	"NAME":"Canada"},
						{"CODE":"China",	"NAME":"China"},
						{"CODE":"Pakistan",	"NAME":"Pakistan"}
						];
	
	var $dropdown = $("#sup-country-sel");
	
	$dropdown.append($("<option selected />").val('').text('Select...'));
	
	$.each(countries_json, function() {
		dropdown_selected = '';
		
		if(this.CODE == document.getElementById("po-shipfrom-country").value){
			dropdown_selected = 'selected';
		}	
		$dropdown.append($("<option " + dropdown_selected + " />").val(this.CODE).text(this.NAME));
	});
}

function SetTextFields()
{
	document.getElementById("po-shipfrom-state").value = document.getElementById("sup-state-sel").value;
	document.getElementById("po-shipfrom-country").value = document.getElementById("sup-country-sel").value;
	document.getElementById("po-employee").value = parseInt(document.getElementById("po-employee-sel").value);
	document.getElementById("po-ship-via").value = document.getElementById("po-ship-via-sel").value;
	
	if(document.getElementById("po-status-sw").checked == true){
		document.getElementById("po-status").value = 'ACTIVE';
	}
	else{
		document.getElementById("po-status").value = '';
	}
	
}

function PopulatePoShipViaList()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			shipvia = JSON.parse(httpxml.responseText);
			
			var $dropdown = $("#po-ship-via-sel");
			
			$dropdown.append($("<option selected />").val('').text('Select...'));
			
			$.each(shipvia, function() {
				dropdown_selected = '';
				
				if(this.SHIPVIA == document.getElementById("po-ship-via").value){
					dropdown_selected = 'selected';
				}	
				$dropdown.append($("<option " + dropdown_selected + " />").val(parseInt(this.SHIPVIA)).text(this.SHIPVIA));
			});
		}
	}
	//alert(document.getElementById("inp-search").value);	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=search_po_shipvia" + "&param=";

	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function PopulatePoPaymentTypeList()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			payment_type = JSON.parse(httpxml.responseText);
			
			var $dropdown = $("#po-payment-type-sel");
			
			$dropdown.append($("<option selected />").val('').text('Select...'));
			
			$.each(payment_type, function() {
				dropdown_selected = '';
				
				if(this.PAYMENTTYPE == document.getElementById("po-payment-type").value){
					dropdown_selected = 'selected';
				}	
				$dropdown.append($("<option " + dropdown_selected + " />").val(parseInt(this.PAYMENTTYPE)).text(this.PAYMENTTYPE));
			});
		}
	}
	//alert(document.getElementById("inp-search").value);	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=search_po_payment_type" + "&param=";

	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AjaxPopulateEmployeesList()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			employee = JSON.parse(httpxml.responseText);
			
			var $dropdown = $("#po-employee-sel");
			
			$dropdown.append($("<option selected />").val('').text('Select...'));
			
			$.each(employee, function() {
				dropdown_selected = '';
				
				if(this.EMPLOYEE_ID == parseInt(document.getElementById("po-employee").value)){
					dropdown_selected = 'selected';
				}	
				$dropdown.append($("<option " + dropdown_selected + " />").val(parseInt(this.EMPLOYEE_ID)).text(this.EMPLOYEE_FULLNAME));
			});
		}
	}
	//alert(document.getElementById("inp-search").value);	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=search_employees" + "&param=";

	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function EventHandler(e)
{
	if(e.keyCode === 13){
		e.preventDefault();
		AjaxPopulatePoList('');
	}
}

function AjaxPopulateSupplierInfo(supid)
{

	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			supplier = JSON.parse(httpxml.responseText);
			
			document.getElementById("po-sup-id").value = supplier.ID;
			document.getElementById("po-shipfrom-name").value = supplier.NAME;
			document.getElementById("po-shipfrom-name-static").innerHTML = supplier.NAME;
			
			document.getElementById("po-shipfrom-address").value = supplier.ADDRESS1;
			document.getElementById("po-shipfrom-address-static").innerHTML = supplier.ADDRESS1;

			document.getElementById("po-shipfrom-address1").value = supplier.ADDRESS2;
			document.getElementById("po-shipfrom-address1-static").innerHTML = supplier.ADDRESS2;
			
			document.getElementById("po-shipfrom-city").value = supplier.CITY;
			document.getElementById("po-shipfrom-city-static").innerHTML = supplier.CITY + ",";
			
			document.getElementById("po-shipfrom-state").value = supplier.STATE;
			document.getElementById("po-shipfrom-state-static").innerHTML = supplier.STATE;
			
			document.getElementById("po-shipfrom-zipcode").value = supplier.ZIP;
			document.getElementById("po-shipfrom-zipcode-static").innerHTML = supplier.ZIP;
			
			document.getElementById("po-shipfrom-country").value = supplier.COUNTRY;
			document.getElementById("po-shipfrom-country-static").innerHTML = supplier.COUNTRY;
			
			document.getElementById("po-shipfrom-phone").value = supplier.PHONE;
			document.getElementById("po-shipfrom-phone-static").innerHTML = supplier.PHONE;
			
			document.getElementById("po-shipfrom-fax").value = supplier.FAX;
			document.getElementById("po-shipfrom-fax-static").innerHTML = supplier.FAX;
			
			document.getElementById("po-shipfrom-email").value = supplier.EMAIL;
			document.getElementById("po-shipfrom-email-static").innerHTML = supplier.EMAIL;
		}
	}
	
	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=supplier_info" + "&param=" + encodeURIComponent(supid);	
	
	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AjaxPopulateBumInfo()
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			bum = JSON.parse(httpxml.responseText);
			
			document.getElementById("po-shipto-name").value = bum.CLIENTNAME;
			document.getElementById("po-shipto-name-static").innerHTML = bum.CLIENTNAME;
			document.getElementById("po-shipto-address").value = bum.CLIENTADDRESS;
			document.getElementById("po-shipto-address-static").innerHTML = bum.CLIENTADDRESS;
			document.getElementById("po-shipto-city").value = bum.CLIENTCITY;
			document.getElementById("po-shipto-city-static").innerHTML = bum.CLIENTCITY;
			document.getElementById("po-shipto-state").value = bum.CLIENTSTATE;
			document.getElementById("po-shipto-state-static").innerHTML = bum.CLIENTSTATE;
			document.getElementById("po-shipto-zipcode").value = bum.CLIENTZIP;
			document.getElementById("po-shipto-zipcode-static").innerHTML = bum.CLIENTZIP;
			document.getElementById("po-shipto-country").value = bum.CLIENTCOUNTRY;
			document.getElementById("po-shipto-country-static").innerHTML = bum.CLIENTCOUNTRY;
			document.getElementById("po-shipto-phone").value = bum.CLIENTPHONE;
			document.getElementById("po-shipto-phone-static").innerHTML = bum.CLIENTPHONE;
			document.getElementById("po-shipto-fax").value = bum.CLIENTFAX;
			document.getElementById("po-shipto-fax-static").innerHTML = bum.CLIENTFAX;
			document.getElementById("po-shipto-website").value = bum.CLIENTWEBSITE;
			document.getElementById("po-shipto-website-static").innerHTML = bum.CLIENTWEBSITE;
		}
	}
	
	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=bum_info" + "&param=";	
	
	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AjaxPopulatePoDuedate(poid)
{
	var httpxml;
	try
	{
		// Firefox, Opera 8.0+, Safari
		httpxml=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpxml=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	function stateck() 
	{
		if(httpxml.readyState==4)
		{
			//alert(httpxml.responseText);
			po = JSON.parse(httpxml.responseText);

			document.getElementById("po-duedate").value = po.DUEDATE;
			document.getElementById("po-duedate-sel").value = po.DUEDATE;
			$('.input-group.date').datepicker({format: "yyyy-mm-dd"});
		}
	}
	
	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=get_po_duedate" + "&param=" + poid;	
	
	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function PopulatePoHeader()
{
	AjaxPopulateSupplierInfo(document.getElementById("sel-supplier").value);
	
	AjaxPopulateBumInfo();
}

function AjaxAddPoItem()
{
	po_validated = ValidatePo();

	if(po_validated){

		AddPoItems();
	}
}

function ValidatePo()
{
	validation = '';
	if(document.getElementById("sel-supplier").value == ''){
		validation += '<div class="alert alert-danger" id="div-po-sup-validate" name="div-po-sup-validate">Please select Supplier</div>'
	}
	if(document.getElementById("poitem-qty-add").value == ''){
		validation += '<div class="alert alert-danger" id="div-po-qty-validate" name="div-po-qty-validate">Please enter QTY</div>'
	}
	if(document.getElementById("poitem-sku-add").value == ''){
		validation += '<div class="alert alert-danger" id="div-po-sku-validate" name="div-po-sku-validate">Please enter SKU</div>'
	}	
	if(document.getElementById("poitem-price-add").value == ''){
		validation += '<div class="alert alert-danger" id="div-po-price-validate" name="div-po-price-validate">Please enter Price</div>'
	}	
	if(document.getElementById("poitem-description-add").value == ''){
		validation += '<div class="alert alert-danger" id="div-po-desc-validate" name="div-po-desc-validate">Please enter Description</div>'
	}
	
	if(validation != ''){
		$("#modal-validation").modal();
		document.getElementById("div-po-validate").innerHTML = validation;
		return false;
	}
	else{
		return true;
	}
}

function AddPoItems()
{
	//poitems = JSON.parse(httpxml.responseText);
	
	poitems = [];
	poitems[0] = {};
	poitems[0].QTYRECEIVED = '0';
	poitems[0].QTY = document.getElementById("poitem-qty-add").value;
	poitems[0].VENDORSKU = document.getElementById("poitem-sku-add").value;
	poitems[0].DESCRIPTION = document.getElementById("poitem-description-add").value;
	poitems[0].PRICE = document.getElementById("poitem-price-add").value;
	poitems[0].AMOUNT = parseFloat(document.getElementById("poitem-qty-add").value) * parseFloat(document.getElementById("poitem-price-add").value);
	poitems[0].DUEDATE = document.getElementById("poitem-duedate-add").value;
	
	//document.getElementById("poitems_tax").innerHTML = "0.00";
	//document.getElementById("poitems_shipping").innerHTML = "0.00";	
	
	tbody = document.getElementById("tbody_poitems");
//	tbody.innerHTML="";
	poitems_subtotal = 0;
	poitems_total = 0;
	
	for(i=0; i<poitems.length; i++){
		row = tbody.insertRow();
		
		received = row.insertCell(0);
		received.style.textAlign = "center";
		received.innerHTML = poitems[i].QTYRECEIVED;
		
		qty = row.insertCell(1);
		qty.style.textAlign = "center";
		qty.innerHTML = poitems[i].QTY;
		
		sku = row.insertCell(2);
		sku.innerHTML = poitems[i].VENDORSKU;
		
		description = row.insertCell(3);
		description.innerHTML = poitems[i].DESCRIPTION;
		
		price = row.insertCell(4);
		price.style.textAlign = "right";
		price.innerHTML = parseFloat(poitems[i].PRICE).toFixed(2);
		
		amt = parseFloat(poitems[i].AMOUNT);
		amount = row.insertCell(5);
		amount.style.textAlign = "right";
		amount.innerHTML = amt.toFixed(2);
		
		duedate = row.insertCell(6);
		duedate.innerHTML = poitems[i].DUEDATE;
		duedate.style = "white-space: nowrap";

		poitems_subtotal = parseFloat(document.getElementById("poitems_subtotal").innerHTML) + amt;
	}
	
	document.getElementById("poitems_subtotal").innerHTML = poitems_subtotal.toFixed(2);

	poitems_total = parseFloat(document.getElementById("poitems_subtotal").innerHTML) +
					parseFloat(document.getElementById("poitems_tax").innerHTML) +
					parseFloat(document.getElementById("poitems_shipping").innerHTML);
	
	document.getElementById("poitems_total").innerHTML = poitems_total.toFixed(2);	
}

function AjaxPrintPo()
{
	
	//http://poise.bennettuniform.com/poise/Print/PurchaseOrder.php?POid=000000000000000000024345
	
	var url = 'http://poise.bennettuniform.com/poise/Print/PurchaseOrder.php?POid=' + document.getElementById("po-id").value;
	window.open(url, '_blank');
}