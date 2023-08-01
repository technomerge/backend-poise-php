<?php
include 'header.php';

?>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
			<?php
			include 'navtop.php';
			
			include 'navleft.php';
			?>
        </nav>

        <div id="page-wrapper">
            <!--<div class="container-fluid">-->
			<form>
				
				<input type="checkbox" checked data-toggle="toggle">
				<input type="button" value="Add" onClick="AddParameter()">
			</form>
			<!--</div>-->
		</div>

	</div>

<?php
include 'footer.php';
?>

<script>

$('input[name="my-checkbox"]').bootstrapSwitch('state',true,false);

function AddParameter(){
	window.history.replaceState(null ,null, "?arg=123");
}

</script>