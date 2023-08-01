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
						<label>Supplier Name</label>
						<input class="form-control" id="filter-name" name="filter-name">
						<!-- <p class="help-block">Enter supplier name.</p> -->																										
					</div>
					<div class="form-group col-lg-12">
						<label>State</label>
						<select class="form-control" id="filter-state" name="filter-state">
							<option value=''>Select...</option>
							<?php
							$states_list = $myData->load_states('');
							
							for($i=0; $i<sizeof($states_list); $i++){
								echo "<option value='" . $states_list[$i]['REGION_CODE'] . "'>" . $states_list[$i]['REGION_NAME'] . "</option>";
							}
							?>
						</select>
						<!-- <p class="help-block">Select state.</p> -->
					</div>	
					<div class="form-group col-lg-12">
						<label>Active</label>
						<input type="hidden" class="form-control" id="filter-status" name="filter-status">
						<BR>
						<div class="pretty p-switch">
							<input type="checkbox" id="filter-status-sw" name="filter-status-sw" />
							<div class="state p-primary">
								<label></label>
							</div>
						</div>
						<!-- <p class="help-block">Enter supplier status here.</p> -->
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
	
	filter_param.NAME = document.getElementById("filter-name").value;
	filter_param.STATE = document.getElementById("filter-state").value;
	
	if(document.getElementById("filter-status-sw").checked == true){
		filter_param.STATUS = "ACTIVE";
	}
	else{
		filter_param.STATUS = "INACTIVE";
	}
	
	filter_param_str = JSON.stringify(filter_param);

	AjaxPopulateSupplierList('filter', filter_param_str);
}
</script>