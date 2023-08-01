<?php
include 'header.php';
include '../../controllers/Data.php';


?>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
			<?php
			//include 'navtop.php';
			
			//include 'navleft.php';
			?>
        </nav>

        <div id="page-wrapper">
<?php
$myData = new Data();
$states_list = $myData->load_states('');
print_r($states_list);

?>
		</div>

	</div>

<?php
include 'footer.php';
?>

