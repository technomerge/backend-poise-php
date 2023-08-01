<?php
include '../header.php';

include '../../../controllers/Data.php';

$myData = new Data();

include 'suppliers_filter.php';

?>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
			<?php
			include '../navtop.php';
			
			include '../navleft.php';
			?>
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- <h1 class="page-header">Suppliers</h1> -->
						<BR>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
				
                <div class="row">
                    <div class="col-lg-3">
						<form role="form">
							
							<div class="form-group input-group">
								<input type="text" class="form-control" id="inp-search" name="inp-search" placeholder="Search..." onKeyPress="EventHandler(event)" >
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" id="btn-sup-search" name="btn-sup-search">
										<i class="fa fa-search" onClick="AjaxPopulateSupplierList('search','')"></i>
									</button>
								</span>
							</div>							
							
							<div class="form-group">
								<select style="height:300px" multiple class="form-control" id="sel-sup" name="sel-sup"></select>
								<p class="text-muted" align="right" id="sup-total" name="sup-total" class="form-control-static"></p>
							</div>
						</form>	

						<p align="right">
							<!-- Button trigger modal -->
							<button class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">Filter</button>
							<button class="btn btn-default btn-sm" onClick="AjaxPopulateSupplierList('clear','')">Clear Filter</button>
							<button type="button" class="btn btn-primary btn-sm" onClick="AjaxPopulateSupplierInfo()">View</button>
						</p>

					</div>
					<!-- /.col-lg-3 -->

					<div class="col-lg-9">
						<div class="panel panel-primary">
							<div class="panel-heading">Supplier</div>
							<!-- /.panel-heading -->
							
							<p></p>
							
							<div class="form-group col-lg-12">
								<div class="btn-group pull-right">
									<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-chevron-down"></i>
									</button>
									<ul class="dropdown-menu slidedown">
										<li>
											<a href="#" onClick="AddSupplierInfo()" id="add_record" name="add_record">
												<i class="fa fa-plus fa-fw"></i> New
											</a>
										</li>
										<li>
											<a href="#" onClick="AjaxSaveSupplierInfo()" id="save_record" name="save_record" style="display:none">
												<i class="fa fa-save fa-fw"></i> Save
											</a>
										</li>
										<li>
											<a href="#" id="undo_record" name="undo_record" style="display:none">
												<i class="fa fa-undo fa-fw"></i> Undo
											</a>
										</li>	
										<li>
											<a href="#" onClick="AjaxDeleteSupplierInfo()" id="delete_record" name="delete_record" style="display:none">
												<i class="fa fa-trash-o fa-fw"></i> Delete
											</a>
										</li>																				
										<li class="divider"></li>
										<li>
											<a href="#" onClick="CloseSupplierInfo()" id="close_record" name="close_record" style="display:none">
												<i class="fa fa-times fa-fw"></i> Close
											</a>
										</li>
									</ul>
								</div>								
							</div>
							
							<div class="panel-body">
								<!-- Nav tabs -->
								<ul class="nav nav-tabs">
									<li class="active"><a href="#info" data-toggle="tab">Info</a>
									</li>
									<li><a href="#contact" data-toggle="tab">Contact</a>
									</li>
									<li><a href="#settings" data-toggle="tab">Settings</a>
									</li>
									<li><a href="#notes" data-toggle="tab">Notes</a>
									</li>
								</ul>

								<p></p>
								
								<!-- Tab panes -->
								<div class="tab-content">
									<div class="tab-pane fade in active" id="info">
										<p></p>

										<!-- <hr class="mb-4"> -->
				                        
										<div class="form-group col-lg-6" id="div_info" name="div_info" style="display:none">
											<div class="row">
												<div class="form-group col-lg-12">
													<label>Name</label>
													<input type='hidden' class="form-control" id="sup-id" name="sup-id" readonly="yes">
													<input class="form-control" id="sup-name" name="sup-name">
													<!-- <p class="help-block">Enter supplier name here.</p> -->
												</div>
											</div>
											<div class="row">							
												<div class="form-group col-lg-12">
													<label>Address</label>
													<input class="form-control" id="sup-address" name="sup-address">
													<!-- <p class="help-block">Enter supplier address here.</p> -->
												</div>
											</div>
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Address1</label>
													<input class="form-control" id="sup-address1" name="sup-address1">
													<!-- <p class="help-block">Enter supplier address1 here.</p> -->
												</div>
											</div>	
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>City</label>
													<input class="form-control" id="sup-city" name="sup-city">
													<!-- <p class="help-block">Enter supplier city here.</p> -->
												</div>	
												<div class="form-group col-lg-12">
													<label>State</label>
													<input type="hidden" class="form-control" id="sup-state" name="sup-state">
													<select class="form-control" id="sup-state-sel" name="sup-state-sel"></select>													
													<!-- <p class="help-block">Enter supplier state here.</p> -->
												</div>
												<div class="form-group col-lg-12">
													<label>Zipcode</label>
													<input class="form-control" id="sup-zipcode" name="sup-zipcode">
													<!-- <p class="help-block">Enter supplier zipcode here.</p> -->																										
												</div>																																		
											</div>	
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Country</label>
													<input type="hidden" class="form-control" id="sup-country" name="sup-country">
													<select class="form-control" id="sup-country-sel" name="sup-country-sel"></select>
													<!-- <p class="help-block">Enter supplier country here.</p> -->
												</div>
											</div>																							
										</div>
									</div>
									
									
									<div class="tab-pane fade" id="contact">
										<!-- <h4>Supplier Contact</h4> -->
										<p></p>
				                        
										<div class="form-group col-lg-6" id="div_contact" name="div_contact" style="display:none">
											<div class="row">
												<div class="form-group col-lg-12">
													<label>Contact Person</label>
													<input class="form-control" id="sup-contact" name="sup-contact">
													<!-- <p class="help-block">Enter supplier contact here.</p> -->
												</div>
											</div>
											<div class="row">							
												<div class="form-group col-lg-12">
													<label>Email</label>
													<input class="form-control" id="sup-email" name="sup-email">
													<!-- <p class="help-block">Enter supplier email here.</p> -->
												</div>
											</div>
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Phone</label>
													<input class="form-control" id="sup-phone" name="sup-phone">
													<!-- <p class="help-block">Enter supplier phone here.</p> -->
												</div>
											</div>	
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Fax</label>
													<input class="form-control" id="sup-fax" name="sup-fax">
													<!-- <p class="help-block">Enter supplier fax here.</p> -->
												</div>
											</div>	
										</div>
									</div>
									<div class="tab-pane fade" id="settings">
										<!-- <h4>Supplier Settings</h4> -->
										<p></p>
				                        
										<div class="form-group col-lg-6" id="div_settings" name="div_settings" style="display:none">
											<div class="row">
												<div class="form-group col-lg-12">
													<label>Active</label>
													<input type="hidden" class="form-control" id="sup-status" name="sup-status">
													<BR>
													<div class="pretty p-switch">
														<input type="checkbox" id="sup-status-sw" name="sup-status-sw" />
														<div class="state p-primary">
															<label></label>
														</div>
													</div>		
													<!-- <p class="help-block">Enter supplier status here.</p> -->
												</div>
											</div>
											<div class="row">							
												<div class="form-group col-lg-12">
													<label>Employee</label>
													<input type="hidden" class="form-control" id="sup-employee" name="sup-employee">
													<select class="form-control" id="sup-employee-sel" name="sup-employee-sel"></select>													
													<!-- <p class="help-block">Enter supplier employee here.</p> -->
												</div>
											</div>
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>On Hold</label>
													<input type="hidden" class="form-control" id="sup-onhold" name="sup-onhold">
													<BR>
													<div class="pretty p-switch">
														<input type="checkbox" id="sup-onhold-sw" name="sup-onhold-sw" />
														<div class="state p-primary">
															<label></label>
														</div>
													</div>													
													<!-- <p class="help-block">Enter supplier on hold here.</p> -->
												</div>
											</div>	
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Transit Time</label>
													<input type="hidden" class="form-control" id="sup-transit" name="sup-transit">
													<select class="form-control" id="sup-transit-sel" name="sup-transit-sel"></select>
													<!-- <p class="help-block">Enter supplier transit time here.</p> -->
												</div>
											</div>	
										</div>
									</div>
									<div class="tab-pane fade" id="notes">
										<!-- <h4>Supplier Settings</h4> -->
										<p></p>
				                        
										<div class="form-group col-lg-12" id="div_notes" name="div_notes" style="display:none">
											<div class="row">
												<div class="form-group col-lg-12">
													<label>Notes</label>
													<textarea class="form-control" rows="5" id="sup-notes" name="sup-notes"></textarea>
													<!-- <p class="help-block">Enter supplier notes here.</p> -->
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /.panel-body -->
						</div>
						<!-- /.panel -->
					</div>
					<!-- /.col-lg-9 -->					
					
				</div>	
				<!-- /.row -->
				
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php
include '../footer.php';
?>

<script src="js/suppliers.js"></script>

