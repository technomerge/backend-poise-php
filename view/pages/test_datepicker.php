<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>POISE 2.0 - Dashboard</title>
<link href="../../../assets/css/bootstrap.min.css" rel="stylesheet">

<link href="../../../assets/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">

<script src="../../../assets/js/jquery.js"></script>

<script src="../../../assets/js/bootstrap.min.js"></script>

<script src="../../../assets/datepicker/js/bootstrap-datepicker.min.js"></script>

</head>

<body>

<div class="container">
  <br />
  <div class="row">
    <div class='col-sm-3'>
      <div class="form-group">
        <div id="filterDate2">
          
          <!-- Datepicker as text field -->         
          <div class="input-group date" data-date-format="dd.mm.yyyy">
            <input  type="text" class="form-control" placeholder="dd.mm.yyyy">
            <div class="input-group-addon" >
              <span class="glyphicon glyphicon-th"></span>
            </div>
          </div>
          
        </div>    
      </div>
    </div>
  </div>
</div>

</body>

</html>



<script type="text/javascript">
$('.input-group.date').datepicker({format: "dd.mm.yyyy"});
</script>