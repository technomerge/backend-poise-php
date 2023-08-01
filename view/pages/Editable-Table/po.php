<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

	<title>Purchase Order</title>
	
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="keywords" content="opensource jquery bootstrap editable table spreadsheet" />
    <meta name="description" content="This tiny jQuery bootstrap plugin turns any table into an editable spreadsheet" />
    <link rel="apple-touch-icon" href="http://static.mindmup.com/img/apple-touch-icon.png" />
    <link rel="shortcut icon" href="http://static.mindmup.com/img/favicon.ico" >
    <link href="external/google-code-prettify/prettify.css" rel="stylesheet">
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
    <script src="external/google-code-prettify/prettify.js"></script>
		<link href="index.css" rel="stylesheet">
    <script src="mindmup-editabletable.js"></script>
    <script src="numeric-input-example.js"></script>
  </head>
  <body>
<div class="container">
  <div class="hero-unit">
  <div class="pull-right">

  </div>
	<h1>Purchase Order<br/> <small>Backorder Items Purchase Order</small></h1>
	<hr/>
		<div class="alert alert-error hide">
			That would cost too much
		</div>
          <table id="mainTable" class="table table-striped">
            <thead><tr><th>QTY</th><th>BU SKU</th><th>Vendor SKU</th><th>Description</th><th>Price</th><th>Amount</th><th>Due Date</th></tr></thead>
            <tbody>
              <tr><td>0</td><td>Car</td><td>100</td><td>200</td><td>0</td><td>0</td><td>todays date</td></tr>
              <tr><td>0</td><td>Bike</td><td>330</td><td>240</td><td>1</td><td>0</td><td>todays date</td></tr>
              <tr><td>0</td><td>Plane</td><td>430</td><td>540</td><td>3</td><td>0</td><td>todays date</td></tr>
              <tr><td>0</td><td>Yacht</td><td>100</td><td>200</td><td>0</td><td>0</td><td>todays date</td></tr>
              <tr><td>0</td><td>Segway</td><td>330</td><td>240</td><td>1</td><td>0</td><td>todays date</td></tr>
            </tbody>
			<!--<tfoot><tr><th><strong>TOTAL</strong></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></thead>-->
          </table>
          <h2><small>just start typing to edit, or move around with arrow keys or mouse clicks!</small></h2>
</div>


<script>
  $('#mainTable').editableTableWidget().numericInputExample().find('td:first').focus();
  $('#textAreaEditor').editableTableWidget({editor: $('<textarea>')});
  window.prettyPrint && prettyPrint();
</script>

    
  </body>
</html>
