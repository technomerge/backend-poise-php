AjaxPopulateSupplierList('search', '');


url = new URL(window.location.href);
param = new URLSearchParams(url.search.slice(1));

if(param.has('supid') === true){
	AjaxPopulateSupplierInfo();
}


function AjaxPopulateSupplierList(action, filter_param)
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
			//document.getElementById("sel-sup").innerHTML=httpxml.responseText;
			
			//Clear the list
			document.getElementById("sel-sup").innerHTML='';
			
			supplier = JSON.parse(httpxml.responseText);
			
			var $dropdown = $("#sel-sup");
			
			$.each(supplier, function() {
				$dropdown.append($("<option style='font-size:14px' />").val(parseInt(this.SUPPLIER_ID)).text(this.SUPPLIER_NAME));
			});
			
			totalRecords = Object.keys(supplier).length
			document.getElementById("sup-total").innerHTML = '<i><small>Total records: ' + totalRecords + '</small></i>';
		}
	}
	
	//alert(document.getElementById("inp-search").value);	

	search_param = {};
	if(action == 'clear'){
		document.getElementById("inp-search").value = '';
		search_param.NAME = document.getElementById("inp-search").value;
		search_param_str = JSON.stringify(search_param);
	}
	else if(action == 'filter'){
		search_param_str = filter_param;
	}
	else{
		//search_param.NAME = document.getElementById("inp-search").value;
		//search_param_str = JSON.stringify(search_param);
		
		search_param.INFO = document.getElementById("inp-search").value;
		search_param_str = JSON.stringify(search_param);		
	}


	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=search_supplier" + "&param=" + encodeURIComponent(search_param_str);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}	

function AjaxPopulateSupplierInfo()
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
			
			document.getElementById("save_record").style.display = 'block';
			document.getElementById("undo_record").style.display = 'block';
			document.getElementById("delete_record").style.display = 'block';
			document.getElementById("close_record").style.display = 'block';
			
			document.getElementById("div_info").style.display = 'block';
			document.getElementById("div_contact").style.display = 'block';
			document.getElementById("div_settings").style.display = 'block';
			document.getElementById("div_notes").style.display = 'block';
			
			
			document.getElementById("sup-id").value = parseInt(supplier.ID);
			document.getElementById("sup-name").value = supplier.NAME;
			document.getElementById("sup-address").value = supplier.ADDRESS1;
			document.getElementById("sup-address1").value = supplier.ADDRESS2;
			document.getElementById("sup-city").value = supplier.CITY;
			document.getElementById("sup-state").value = supplier.STATE;
			AjaxPopulateStatesList();
			
			
			document.getElementById("sup-zipcode").value = supplier.ZIP;
			document.getElementById("sup-country").value = supplier.COUNTRY;
			PopulateSupplierCountriesList();
			
			document.getElementById("sup-contact").value = supplier.CONTACTPERSON;
			document.getElementById("sup-email").value = supplier.EMAIL;
			document.getElementById("sup-phone").value = supplier.PHONE;
			document.getElementById("sup-fax").value = supplier.FAX;
			document.getElementById("sup-status").value = supplier.STATUS;
			
			if(supplier.STATUS == 'ACTIVE'){
				document.getElementById("sup-status-sw").checked = true;
			}
			else{
				document.getElementById("sup-status-sw").checked = false;
			}
			
			document.getElementById("sup-employee").value = parseInt(supplier.EMPLOYEEID);
			AjaxPopulateEmployeesList();
			
			document.getElementById("sup-onhold").value = supplier.ONHOLD;
			
			if(supplier.ONHOLD == 'YES'){
				document.getElementById("sup-onhold-sw").checked = true;
			}
			else{
				document.getElementById("sup-onhold-sw").checked = false;
			}			
			
			document.getElementById("sup-transit").value = supplier.TRANSITTIME;
			PopulateSupplierTransitTimeList();
			
			document.getElementById("sup-notes").value = supplier.DESCRIPTION;
			
			
			if(document.getElementById("sel-sup").value){
				supid = document.getElementById("sel-sup").value;
			}
			else{
				supid = document.getElementById("sup-id").value;
			}
			
			window.history.replaceState(null ,null, "?supid=" + supid);
			
		}
	}
	
	//alert(document.getElementById("sel-sup").value);
	
	
	url = new URL(window.location.href);
	curparam = new URLSearchParams(url.search.slice(1));
	
	if(document.getElementById("sel-sup").value){
		supid = document.getElementById("sel-sup").value;
	}
	else if(curparam.has('supid') === true){
		supid = curparam.get('supid');
	}

	
	var url = window.location.origin + "/poise/application/controllers/Ajax.php";
	url=url+"?sid=" + Math.random() + "&fn=supplier_info" + "&param=" + encodeURIComponent(supid);	
	
	//alert(url);
	
	httpxml.onreadystatechange=stateck;
	httpxml.open("GET",url,true);
	httpxml.send(null);
}

function AddSupplierInfo()
{
	document.getElementById("save_record").style.display = 'block';
	document.getElementById("undo_record").style.display = 'block';
	document.getElementById("delete_record").style.display = 'block';
	document.getElementById("close_record").style.display = 'block';
	
	document.getElementById("div_info").style.display = 'block';
	document.getElementById("div_contact").style.display = 'block';
	document.getElementById("div_settings").style.display = 'block';
	document.getElementById("div_notes").style.display = 'block';
	
	document.getElementById("sup-id").value = '0';
	document.getElementById("sup-name").value = '';
	document.getElementById("sup-address").value = '';
	document.getElementById("sup-address1").value = '';
	document.getElementById("sup-city").value = '';
	document.getElementById("sup-state").value = '';
	document.getElementById("sup-zipcode").value = '';
	document.getElementById("sup-country").value = '';
	document.getElementById("sup-contact").value = '';
	document.getElementById("sup-email").value = '';
	document.getElementById("sup-phone").value = '';
	document.getElementById("sup-fax").value = '';
	document.getElementById("sup-status").value = '';
	document.getElementById("sup-employee").value = '';
	document.getElementById("sup-onhold").value = '';
	document.getElementById("sup-transit").value = '';
	document.getElementById("sup-notes").value = '';
}

function CloseSupplierInfo()
{
	document.getElementById("save_record").style.display = 'none';
	document.getElementById("undo_record").style.display = 'none';
	document.getElementById("delete_record").style.display = 'none';
	document.getElementById("close_record").style.display = 'none';
	
	document.getElementById("div_info").style.display = 'none';
	document.getElementById("div_contact").style.display = 'none';
	document.getElementById("div_settings").style.display = 'none';
	document.getElementById("div_notes").style.display = 'none';
	
	document.getElementById("sup-id").value = '0';
	document.getElementById("sup-name").value = '';
	document.getElementById("sup-address").value = '';
	document.getElementById("sup-address1").value = '';
	document.getElementById("sup-city").value = '';
	document.getElementById("sup-state").value = '';
	document.getElementById("sup-zipcode").value = '';
	document.getElementById("sup-country").value = '';
	document.getElementById("sup-contact").value = '';
	document.getElementById("sup-email").value = '';
	document.getElementById("sup-phone").value = '';
	document.getElementById("sup-fax").value = '';
	document.getElementById("sup-status").value = '';
	document.getElementById("sup-employee").value = '';
	document.getElementById("sup-onhold").value = '';
	document.getElementById("sup-transit").value = '';
	document.getElementById("sup-notes").value = '';
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
				AjaxPopulateSupplierList('');
			}
			else{
				alert("There was a problem saving this record!");
			}
		}
	}

	SetTextFields();
	
	const supplier = {};
	supplier.id = document.getElementById("sup-id").value;
	supplier.name = document.getElementById("sup-name").value;
	supplier.address = document.getElementById("sup-address").value;
	supplier.address1 = document.getElementById("sup-address1").value;
	supplier.city = document.getElementById("sup-city").value;
	supplier.state = document.getElementById("sup-state").value;
	supplier.zipcode = document.getElementById("sup-zipcode").value;
	supplier.country = document.getElementById("sup-country").value;
	supplier.contact = document.getElementById("sup-contact").value;
	supplier.email = document.getElementById("sup-email").value;
	supplier.phone = document.getElementById("sup-phone").value;
	supplier.fax = document.getElementById("sup-fax").value;
	supplier.status = document.getElementById("sup-status").value;
	supplier.employee = document.getElementById("sup-employee").value;
	supplier.onhold = document.getElementById("sup-onhold").value;
	supplier.transit = document.getElementById("sup-transit").value;
	supplier.notes = document.getElementById("sup-notes").value;
	
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
				document.getElementById("undo_record").style.display = 'none';
				document.getElementById("delete_record").style.display = 'none';
				document.getElementById("close_record").style.display = 'none';
				
				document.getElementById("div_info").style.display = 'none';
				document.getElementById("div_contact").style.display = 'none';
				document.getElementById("div_settings").style.display = 'none';
				document.getElementById("div_notes").style.display = 'none';
				
				AjaxPopulateSupplierList('');
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
				
				if(this.REGION_CODE == document.getElementById("sup-state").value){
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
		
		if(this.CODE == document.getElementById("sup-country").value){
			dropdown_selected = 'selected';
		}	
		$dropdown.append($("<option " + dropdown_selected + " />").val(this.CODE).text(this.NAME));
	});
}

function SetTextFields()
{
	document.getElementById("sup-state").value = document.getElementById("sup-state-sel").value;
	document.getElementById("sup-country").value = document.getElementById("sup-country-sel").value;
	document.getElementById("sup-employee").value = parseInt(document.getElementById("sup-employee-sel").value);
	document.getElementById("sup-transit").value = document.getElementById("sup-transit-sel").value;
	
	if(document.getElementById("sup-status-sw").checked == true){
		document.getElementById("sup-status").value = 'ACTIVE';
	}
	else{
		document.getElementById("sup-status").value = '';
	}
	
	if(document.getElementById("sup-onhold-sw").checked == true){
		document.getElementById("sup-onhold").value = 'YES';
	}
	else{
		document.getElementById("sup-onhold").value = 'NO';
	}	
	
}

function PopulateSupplierTransitTimeList(){
	transit_json =	[
					{"CODE":"Ship Direct",	"NAME":"Ship Direct"},
					{"CODE":"1",			"NAME":"1"},
					{"CODE":"2",			"NAME":"2"},
					{"CODE":"3",			"NAME":"3"},
					{"CODE":"4",			"NAME":"4"},
					{"CODE":"5",			"NAME":"5"},
					{"CODE":"6",			"NAME":"6"},
					{"CODE":"7",			"NAME":"7"},
					{"CODE":"8",			"NAME":"8"},
					{"CODE":"9",			"NAME":"9"},
					{"CODE":"10",			"NAME":"10"}
					];
	
	var $dropdown = $("#sup-transit-sel");
	
	$dropdown.append($("<option selected />").val('').text('Select...'));
	
	$.each(transit_json, function() {
		dropdown_selected = '';
		
		if(this.CODE == document.getElementById("sup-transit").value){
			dropdown_selected = 'selected';
		}	
		$dropdown.append($("<option " + dropdown_selected + " />").val(this.CODE).text(this.NAME));
	});
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
			
			var $dropdown = $("#sup-employee-sel");
			
			$dropdown.append($("<option selected />").val('').text('Select...'));
			
			$.each(employee, function() {
				dropdown_selected = '';
				
				if(this.EMPLOYEE_ID == parseInt(document.getElementById("sup-employee").value)){
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

function EventHandler(e){
	if(e.keyCode === 13){
		e.preventDefault();
		AjaxPopulateSupplierList('');
	}
}