<div class="panel-body">
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Filter</h4>
				</div>
				<div class="modal-body">
					<div class="form-group col-lg-12">
						<label>Purchase Order #</label>
						<input class="form-control" id="filter-poid" name="filter-poid">
						<!-- <p class="help-block">Enter purchase order number.</p> -->																										
					</div>
					<div class="form-group col-lg-12">
						<label>Supplier</label>
						<select class="form-control" id="filter-supplier" name="filter-supplier">
							<option value=''>Select...</option>
							<?php
							$suppliers_list = $myData->load_suppliers('');
							
							for($i=0; $i<sizeof($suppliers_list); $i++){
								echo "<option value='" . $suppliers_list[$i]['SUPPLIER_ID'] . "'>" . $suppliers_list[$i]['SUPPLIER_NAME'] . "</option>";
							}
							?>
						</select>
						<!-- <p class="help-block">Select supplier.</p> -->
					</div>	
					<div class="form-group col-lg-12">
						<label>Status</label>
						<BR>
						<div class="pretty p-switch p-slim">
							<input type="radio" id="filter-po-status-sw-open" name="filter-po-status-sw" />
							<div class="state p-primary">
								<label>Open</label>
							</div>
						</div>
						<div class="pretty p-switch p-slim">
							<input type="radio" id="filter-po-status-sw-received" name="filter-po-status-sw" />
							<div class="state p-primary">
								<label>Received</label>
							</div>
						</div>
						<div class="pretty p-switch p-slim">
							<input type="radio" id="filter-po-status-sw-closed" name="filter-po-status-sw" />
							<div class="state p-primary">
								<label>Closed</label>
							</div>
						</div>													
						<!-- <p class="help-block">Select PO status.</p> -->
					</div>									
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onClick="ApplyFilters()" data-dismiss="modal">Apply</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
</div>
<!-- .panel-body -->


<script>
function ApplyFilters(){

	//document.getElementById("inp-search").value = document.getElementById("filter-name").value;

	filter_param = {};
	filter_param.FILTER = true;
	
	filter_param.POID = document.getElementById("filter-poid").value;
	filter_param.SUPPLIERID = document.getElementById("filter-supplier").value;
	

	
	if(document.getElementById("filter-po-status-sw-open").checked == true){
		filter_param.STATUS = "OPEN";
	}
	else if(document.getElementById("filter-po-status-sw-received").checked == true){
		filter_param.STATUS = "RECEIVED";
	}
	else if(document.getElementById("filter-po-status-sw-closed").checked == true){
		filter_param.STATUS = "CLOSED";
	}
	else{
		filter_param.STATUS = "";
	}
	
	filter_param_str = JSON.stringify(filter_param);

	AjaxPopulatePoList('filter', filter_param_str);
}
</script>