<?php
include '../header.php';

include '../../../controllers/Data.php';

$myData = new Data();

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
                        <!-- <h1 class="page-header">Purchase Orders</h1> -->
						<BR>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
				
                <div class="row">
                    <div class="col-lg-12">
						<form role="form">
							
							<div class="form-group input-group">
								<input type="text" class="form-control" id="inp-search" name="inp-search" placeholder="Search..." onKeyPress="EventHandler(event)" >
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" id="btn-sup-search" name="btn-sup-search">
										<i class="fa fa-search" onClick="AjaxPopulatePoList('search','')"></i>
									</button>
								</span>
							</div>							
							
							<div class="form-group">
								<select style="height:200px" multiple class="form-control" id="sel-po" name="sel-po"></select>
								<p class="text-muted" align="right" id="sup-total" name="sup-total" class="form-control-static"></p>
							</div>
						</form>	

						<p align="right">
							<!-- Button trigger modal -->
							<button class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">Filter</button>
							<button class="btn btn-default btn-sm" onClick="AjaxPopulatePoList('clear','')">Clear Filter</button>
							<button type="button" class="btn btn-primary btn-sm" onClick="AjaxPopulatePoInfo()">View</button>
						</p>

					</div>
					<!-- /.col-lg-12 -->

					<div class="col-lg-12">
						<div class="panel panel-primary">
							<div class="panel-heading">Purchase Order</div>
							<!-- /.panel-heading -->
							
							<p></p>
							
							<div class="form-group col-lg-12">
								<div class="btn-group pull-right">
									<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-chevron-down"></i>
									</button>
									<ul class="dropdown-menu slidedown">
										<li>
											<a href="#" onClick="AddPoInfo()" id="add_record" name="add_record">
												<i class="fa fa-plus fa-fw"></i> New
											</a>
										</li>
										<li>
											<a href="#" onClick="AjaxSaveSupplierInfo()" id="save_record" name="save_record" style="display:none">
												<i class="fa fa-save fa-fw"></i> Save
											</a>
										</li>
										<li>
											<a href="#" onClick="AjaxPrintPo()" id="print_record" name="print_record" style="display:none">
												<i class="fa fa-print fa-fw"></i> Print
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
											<a href="#" onClick="ClosePoInfo()" id="close_record" name="close_record" style="display:none">
												<i class="fa fa-times fa-fw"></i> Close
											</a>
										</li>
									</ul>
								</div>								
							</div>
							
							<div class="panel-body">
								<!-- Nav tabs -->
								<ul class="nav nav-tabs">
									<li class="active"><a href="#items" data-toggle="tab">Items</a></li>
									<li><a href="#shipfrom" data-toggle="tab">Ship From</a></li>
									<li><a href="#shipto" data-toggle="tab">Ship To</a></li>
									<li><a href="#details" data-toggle="tab">Details</a></li>
									<li><a href="#notes" data-toggle="tab">Notes</a></li>
								</ul>

								<p></p>
								
								<!-- Tab panes -->
								<div class="tab-content">
									<input type='hidden' class="form-control" id="po-id" name="po-id" readonly="yes">
									<font size="+2"><p align="right" class="form-control-static" id="po-id-static"></p></font>
									<div class="tab-pane fade in active" id="items">
										<!-- <h4>Items</h4> -->
										<p></p>
				                        
										<div class="form-group col-lg-12" id="div_items" name="div_items" style="display:none">
	
											<div class="row">
												<div class="form-group col-lg-12" id="div_newitem" name="div_newitem" style="display:none">	
													<div class="form-group col-lg-9" id="div_select_supplier" name="div_select_supplier" style="display:block">
														<label>Supplier</label>
														<select class="form-control" id="sel-supplier" name="sel-supplier" onChange="PopulatePoHeader()">
															<option value=''>Select...</option>
															<?php
															for($i=0; $i<sizeof($suppliers_list); $i++){
																echo "<option value='" . $suppliers_list[$i]['SUPPLIER_ID'] . "'>" . $suppliers_list[$i]['SUPPLIER_NAME'] . "</option>";
															}
															?>
														</select>
														<!-- <p class="help-block">Select supplier.</p> -->
													</div>
													<div class="form-group col-lg-3">						
														<label>Due Date</label>
														<!-- Datepicker as text field -->         
														<div class="input-group date" data-date-format="yyyy-mm-dd">
															<input  type="text" class="form-control" id="poitem-duedate-add" name="poitem-duedate-add" placeholder="yyyy-mm-dd">
															<div class="input-group-addon" >
																<span class="glyphicon glyphicon-th"></span>
															</div>
														</div>												
													</div>												
													<div class="form-group col-lg-2">
														<label>QTY</label>
														<input type='text' class="form-control" id="poitem-qty-add" name="poitem-qty-add">
													</div>
													<div class="form-group col-lg-7">
														<label>SKU</label>
														<input type='text' class="form-control" id="poitem-sku-add" name="poitem-sku-add">
													</div>
													<div class="form-group col-lg-3">
														<label>Price</label>									
														<input type='text' class="form-control" id="poitem-price-add" name="poitem-price-add">
													</div>			
													<div class="form-group col-lg-12">
														<label>Description</label>									
														<textarea class="form-control" id="poitem-description-add" name="poitem-description-add" rows="1"></textarea>
													</div>													
													<div class="form-group col-lg-2">
														<button type="button" class="form-control btn btn-primary btn-sm" id="btn-poitem-add" name="btn-poitem-add" onClick="AjaxAddPoItem()">Add Item</button>
													</div>
												</div>
												
											
												<div class="form-group col-lg-12">						
													<div class="panel-body">
														<div class="table-responsive">
															<table class="table table-hover" style='font-size:12px' >
																<thead id="thead_poitems" name="thead_poitems">
																	<tr>
																		<th>Received</th>
																		<th>Ordered</th>
																		<th>SKU</th>
																		<th>Description</th>
																		<th align="right">Price</th>
																		<th align="right">Amount</th>
																		<th nowrap>Due Date</th>
																	</tr>
																</thead>
																<tbody id="tbody_poitems" name="tbody_poitems" style='font-size:11px'>
																
																</tbody>
																<tfoot id="tfoot_poitmes" name="tfoot_poitems" style='font-size:12px'>
																	<tr>
																		<td colspan="5" align="right">Subtotal:</td>
																		<td align="right" id="poitems_subtotal" name="poitems_subtotal"></td>
																		<td></td>
																		<td></td>
																	</tr>
																	<tr>
																		<td style="border-width:0px" colspan="5" align="right">Tax:</td>
																		<td align="right" id="poitems_tax" name="poitems_tax"></th>
																		<td style="border-width:0px"></td>
																	</tr>
																	<tr>
																		<td style="border-width:0px" colspan="5" align="right">Shipping:</td>
																		<td align="right" id="poitems_shipping" name="poitems_shipping"></td>
																		<td style="border-width:0px"></td>
																	</tr>	
																	<tr>
																		<td style="border-width:0px" colspan="5" align="right">Total</td>
																		<td align="right" id="poitems_total" name="poitems_total"></td>
																		<td style="border-width:0px"></td>
																	</tr>																													
																</tfoot>									
															</table>
														</div>
														<!-- /.table-responsive -->
													</div>
													<!-- /.panel-body -->										
												</div>					
											</div>
										</div>
									</div>
								
									<div class="tab-pane fade" id="shipfrom">
										<p></p>

										<!-- <hr class="mb-4"> -->
				                        
										<div class="form-group col-lg-12" id="div_shipfrom" name="div_shipfrom" style="display:none">
											<font size="+1">
												<div class="row">
													<div class="form-group col-lg-12">
														<input type='hidden' class="form-control" id="po-sup-id" name="po-sup-id" readonly="yes">
														
														<input type='hidden' class="form-control" id="po-shipfrom-name" name="po-shipfrom-name">
														<font class="form-control-static" id="po-shipfrom-name-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipfrom-address" name="po-shipfrom-address">
														<font class="form-control-static" id="po-shipfrom-address-static"></font>	
														<br>
														<input type='hidden' class="form-control" id="po-shipfrom-address1" name="po-shipfrom-address1">
														<font class="form-control-static" id="po-shipfrom-address1-static"></font>
														
														<input type='hidden' class="form-control" id="po-shipfrom-city" name="po-shipfrom-city">
														<font class="form-control-static" id="po-shipfrom-city-static"></font>
														
														<input type='hidden' class="form-control" id="po-shipfrom-state" name="po-shipfrom-state">
														<font class="form-control-static" id="po-shipfrom-state-static"></font>

														<input type='hidden' class="form-control" id="po-shipfrom-zipcode" name="po-shipfrom-zipcode">
														<font class="form-control-static" id="po-shipfrom-zipcode-static"></font>																																																					
														<br>
														<input type='hidden' class="form-control" id="po-shipfrom-country" name="po-shipfrom-country">
														<font class="form-control-static" id="po-shipfrom-country-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipfrom-phone" name="po-shipfrom-phone">
														<font class="form-control-static" id="po-shipfrom-phone-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipfrom-fax" name="po-shipfrom-fax">
														<font class="form-control-static" id="po-shipfrom-fax-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipfrom-email" name="po-shipfrom-email">
														<font class="form-control-static" id="po-shipfrom-email-static"></font>														
													</div>
												</div>
											</font>												
										</div>
									</div>

									<div class="tab-pane fade" id="shipto">
										<p></p>

										<!-- <hr class="mb-4"> -->
				                        
										<div class="form-group col-lg-12" id="div_shipto" name="div_shipto" style="display:none">
											<font size="+1">
												<div class="row">
													<div class="form-group col-lg-12">
														<input type='hidden' class="form-control" id="po-shipto-name" name="po-shipto-name">
														<font class="form-control-static" id="po-shipto-name-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipto-address" name="po-shipto-address">
														<font class="form-control-static" id="po-shipto-address-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipto-city" name="po-shipto-city">
														<font class="form-control-static" id="po-shipto-city-static"></font>
														
														<input type='hidden' type="text" class="form-control" id="po-shipto-state" name="po-shipto-state">
														<font class="form-control-static" id="po-shipto-state-static"></font>
														
														<input type='hidden' class="form-control" id="po-shipto-zipcode" name="po-shipto-zipcode">
														<font class="form-control-static" id="po-shipto-zipcode-static"></font>
														<br>
														<input type='hidden' type="text" class="form-control" id="po-shipto-country" name="po-shipto-country">
														<font class="form-control-static" id="po-shipto-country-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipto-phone" name="po-shipto-phone">
														<font class="form-control-static" id="po-shipto-phone-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipto-fax" name="po-shipto-fax">
														<font class="form-control-static" id="po-shipto-fax-static"></font>
														<br>
														<input type='hidden' class="form-control" id="po-shipto-website" name="po-shipto-website">
														<font class="form-control-static" id="po-shipto-website-static"></font>
													</div>
												</div>
											</font>
										</div>
									</div>

									<div class="tab-pane fade" id="details">
										<!-- <h4>PO Details</h4> -->
										<p></p>
				                        
										<div class="form-group col-lg-6" id="div_details" name="div_details" style="display:none">
											<div class="row">
												<div class="form-group col-lg-12">
													<label>Status</label>
													<input type="hidden" class="form-control" id="po-status" name="po-status">
													<BR>
													<div class="pretty p-switch p-slim p-locked">
														<input type="radio" id="po-status-sw-open" name="po-status-sw" />
														<div class="state p-primary">
															<label>Open</label>
														</div>
													</div>
													<div class="pretty p-switch p-slim p-locked">
														<input type="radio" id="po-status-sw-received" name="po-status-sw" />
														<div class="state p-primary">
															<label>Received</label>
														</div>
													</div>
													<div class="pretty p-switch p-slim p-locked">
														<input type="radio" id="po-status-sw-closed" name="po-status-sw" />
														<div class="state p-primary">
															<label>Closed</label>
														</div>
													</div>													
													<!-- <p class="help-block">Select PO status.</p> -->
												</div>
											</div>
											<div class="row">							
												<div class="form-group col-lg-12">						
													<label>Due Date</label>
													<input type="hidden" class="form-control" id="po-duedate" name="po-duedate">
													<!-- Datepicker as text field -->         
													<div class="input-group date" data-date-format="yyyy-mm-dd">
														<input  type="text" class="form-control" id="po-duedate-sel" name="po-duedate-sel" placeholder="yyyy-mm-dd" disabled>
														<div class="input-group-addon" >
															<span class="glyphicon glyphicon-th"></span>
														</div>
													</div>												
												</div>						
											</div>											
											<div class="row">							
												<div class="form-group col-lg-12">
													<label>Input By</label>
													<input type="hidden" class="form-control" id="po-employee" name="po-employee">
													<select class="form-control" id="po-employee-sel" name="po-employee-sel" disabled></select>													
													<!-- <p class="help-block">Select employee.</p> -->
												</div>
											</div>
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Ship Via</label>
													<input type="hidden" class="form-control" id="po-ship-via" name="po-ship-via">
													<select class="form-control" id="po-ship-via-sel" name="po-ship-via-sel" disabled></select>
													<!-- <p class="help-block">Select PO ship via.</p> -->
												</div>
											</div>
											<div class="row">	
												<div class="form-group col-lg-12">
													<label>Payment Type</label>
													<input type="hidden" class="form-control" id="po-payment-type" name="po-payment-type">
													<select class="form-control" id="po-payment-type-sel" name="po-payment-type-sel" disabled></select>
													<!-- <p class="help-block">Select PO payment type.</p> -->
												</div>
											</div>											
										</div>
									</div>

									<div class="tab-pane fade" id="notes">
										<!-- <h4>PO Notes</h4> -->
										<p></p>
				                        
										<div class="form-group col-lg-12" id="div_notes" name="div_notes" style="display:none">
											<div class="row">
												<div class="form-group col-lg-12">
													<label>Notes</label>
													<textarea class="form-control" rows="5" id="po-notes" name="po-notes"></textarea>
													<!-- <p class="help-block">Enter PO notes here.</p> -->
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
					<!-- /.col-lg-12 -->					
					
				</div>	
				<!-- /.row -->
				
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php	
include 'purchase_orders_filter.php';
include 'purchase_orders_validation.php';
?>	
	
<?php
include '../footer.php';
?>

<script src="js/purchase_orders.js"></script>

