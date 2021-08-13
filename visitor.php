<?php

//visitor.php

include('vms.php');

$visitor = new vms();

if(!$visitor->is_login())
{
	header("location:".$visitor->base_url."");
}

include('header.php');

include('sidebar.php');



?>

<script>
$(document).ready(function(){
    var date = new Date();
        
    $('.input-daterange').datepicker({
        todayBtn: "linked",
        format: "yyyy-mm-dd",
        autoclose: true
    });
        
});
</script>
	
	
	        <div class="col-sm-10 offset-sm-2 py-4">
	        	<span id="message"></span>
	            <div class="card">
	            	<div class="card-header">
	            		<div class="row">
	            			<div class="col-sm-4">
	            				<h2>Visitor Area</h2>
	            			</div>
	            			<div class="col-sm-4">
	            				<div class="row input-daterange">
	            					<div class="col-md-6">
		            					<input type="text" name="from_date" id="from_date" class="form-control form-control-sm" placeholder="From Date" readonly />
		            				</div>
		            				<div class="col-md-6">
		            					<input type="text" name="to_date" id="to_date" class="form-control form-control-sm" placeholder="To Date" readonly />
		            				</div>
		            			</div>
		            		</div>
		            		<div class="col-md-2">
	            				<button type="button" name="filter" id="filter" class="btn btn-info btn-sm"><i class="fas fa-filter"></i></button>
	            				&nbsp;
	            				<button type="button" name="refresh" id="refresh" class="btn btn-secondary btn-sm"><i class="fas fa-sync-alt"></i></button>
	            			</div>
	            			<div class="col-md-2" align="right">
	            				<a href="#" name="export" id="export" class="text-success"><i class="fas fa-file-csv fa-2x"></i></a>
	            				&nbsp;
	            				<button type="button" name="add_visitor" id="add_visitor" class="btn btn-success btn-sm" style="margin-top: -15px;"><i class="fas fa-user-plus"></i></button>
	            			</div>
	            		</div>
	            	</div>
	            	<div class="card-body">
	            		<div class="table-responsive">
	            			<table class="table table-striped table-bordered" id="visitor_table">
	            				<thead>
	            					<tr>
	            						<th>Visitor Name</th>
										<th>Meet Person Name</th>
										<th>Block</th>
										<th>Room</th>
										<th>In Time</th>
										<th>Out Time</th>
										<th>Status</th>
										<?php
										if($visitor->is_master_user())
										{
											echo '<th>Enter By</th>';
										}
										?>										
										<th>Action</th>
	            					</tr>
	            				</thead>
	            			</table>
	            		</div>  
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>

</body>
</html>

<div id="visitorModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="visitor_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Visitor</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Visitor Name</label>
			            	<div class="col-md-8">
			            		<input type="text" name="visitor_name" id="visitor_name" class="form-control" required data-parsley-pattern="/^[a-zA-Z\s]+$/" data-parsley-maxlength="150" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Visitor Email</label>
			            	<div class="col-md-8">
			            		<input type="text" name="visitor_email" id="visitor_email" class="form-control" required data-parsley-type="email" data-parsley-maxlength="150" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Visitor Mobile No.</label>
			            	<div class="col-md-8">
			            		<input type="text" name="visitor_mobile_no" id="visitor_mobile_no" class="form-control" required data-parsley-type="integer" data-parsley-minlength="10" data-parsley-maxlength="12" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Visitor Address</label>
			            	<div class="col-md-8">
			            		<textarea name="visitor_address" id="visitor_address" class="form-control" required data-parsley-maxlength="400" data-parsley-trigger="keyup"></textarea>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Block</label>
			            	<div class="col-md-8">
			            		<select name="visitor_block" id="visitor_block" class="form-control" required data-parsley-trigger="keyup">
			            			<option value="">Select Block</option>
			            			<?php echo $visitor->load_blocks(); ?>
			            		</select>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Room</label>
			            	<div class="col-md-8">
			            		<select name="visitor_room" id="visitor_room" class="form-control" required data-parsley-trigger="keyup">
			            			<option value="">Select Room</option>
			            			<?php echo $visitor->load_rooms(); ?>
			            		</select>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Person To Meet</label>
			            	<div class="col-md-8">
			            		<select name="visitor_meet_person_name" id="visitor_meet_person_name" class="form-control" required data-parsley-trigger="keyup">
			            			<option value="">Select Person</option>
			            		</select>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Reason to Visit</label>
			            	<div class="col-md-8">
			            		<textarea name="visitor_reason_to_meet" id="visitor_reason_to_meet" class="form-control" required data-parsley-maxlength="400" data-parsley-trigger="keyup"></textarea>
			            	</div>
			            </div>
		          	</div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<div id="visitordetailModal" class="modal fade">
  	<div class="modal-dialog modal-lg">
    	<form method="post" id="visitor_details_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">View Visitor Details</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">

		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Visitor Name</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_name_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Visitor Email</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_email_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Visitor Mobile No.</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_mobile_no_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Visitor Address</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_address_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Department</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_department_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Person To Meet</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_meet_person_name_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Reason to Visit</b></label>
			            	<div class="col-md-8">
			            		<span id="visitor_reason_to_meet_detail"></span>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right"><b>Visitor Outing Remark</b></label>
			            	<div class="col-md-8">
			            		<textarea name="visitor_outing_remark" id="visitor_outing_remark" class="form-control" required data-parsley-maxlength="400" data-parsley-trigger="keyup"></textarea>
			            	</div>
			            </div>
		          	</div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_visitor_id" id="hidden_visitor_id" />
          			<input type="hidden" name="action" value="update_outing_detail" />
          			<input type="submit" name="submit" id="detail_submit_button" class="btn btn-success" value="Save" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<script>

$(document).ready(function(){

	load_data();

	function load_data(from_date = '', to_date = '')
	{
		var dataTable = $('#visitor_table').DataTable({
			"processing" : true,
			"serverSide" : true,
			"order" : [],
			"ajax" : {
				url:"visitor_action.php",
				type:"POST",
				data:{action:'fetch', from_date:from_date, to_date:to_date}
			},
			"columnDefs":[
				{
					<?php
					if($visitor->is_master_user())
					{
					?>
					"targets":[7],
					<?php
					}
					else
					{
					?>
					"targets":[6],
					<?php
					}
					?>
					"orderable":false,
				},
			],
		});
	}

	$('#add_visitor').click(function(){		
		$('#visitor_form')[0].reset();
		$('#visitor_form').parsley().reset();
    	$('#modal_title').text('Add Visitor');
    	$('#action').val('Add');
    	$('#submit_button').val('Add');
    	$('#visitorModal').modal('show');
	});

	$(document).on('change', '#visitor_department', function(){
		var person = $('#visitor_department').find(':selected').data('person');
		var person_array = person.split(", ");
		var html = '<option value="">Select Person</option>';
		for(var count = 0; count < person_array.length; count++)
		{
			html += '<option value="'+person_array[count]+'">'+person_array[count]+'</option>';
		}
		$('#visitor_meet_person_name').html(html);
	});

	$('#visitor_form').parsley();

	$('#visitor_form').on('submit', function(event){
		event.preventDefault();
		if($('#visitor_form').parsley().isValid())
		{		
			$.ajax({
				url:"visitor_action.php",
				method:"POST",
				data:$(this).serialize(),
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					$('#visitorModal').modal('hide');
					$('#message').html(data);
					$('#visitor_table').DataTable().destroy();
					load_data();
					setTimeout(function(){
						$('#message').html('');
					}, 5000);
				}
			})
		}
	});

	$(document).on('click', '.edit_button', function(){
		var visitor_id = $(this).data('id');
		$('#visitor_form').parsley().reset();
		$.ajax({
	      	url:"visitor_action.php",
	      	method:"POST",
	      	data:{visitor_id:visitor_id, action:'fetch_single'},
	      	dataType:'JSON',
	      	success:function(data)
	      	{
	        	$('#visitor_name').val(data.visitor_name);
	        	$('#visitor_email').val(data.visitor_email);
	        	$('#visitor_mobile_no').val(data.visitor_mobile_no);
	        	$('#visitor_address').val(data.visitor_address);
	        	$('#visitor_department').val(data.visitor_department);

	        	var person = $('#visitor_department').find(':selected').data('person');
				var person_array = person.split(", ");
				var html = '<option value="">Select Person</option>';
				for(var count = 0; count < person_array.length; count++)
				{
					html += '<option value="'+person_array[count]+'">'+person_array[count]+'</option>';
				}
				$('#visitor_meet_person_name').html(html);
				$('#visitor_meet_person_name').val(data.visitor_meet_person_name);
				$('#visitor_reason_to_meet').val(data.visitor_reason_to_meet);
	        	
	        	$('#modal_title').text('Edit Data');
	        	$('#action').val('Edit');
	        	$('#submit_button').val('Edit');
	        	$('#visitorModal').modal('show');
	        	$('#hidden_id').val(visitor_id);

	      	}
	    })
	});

	$(document).on('click', '.delete_button', function(){
		var id = $(this).data('id');
		if(confirm("Are you sure you want to remove it?"))
    	{
    		$.ajax({
    			url:"visitor_action.php",
    			method:"POST",
    			data:{id:id, action:'delete'},
    			success:function(data)
        		{
        			$('#message').html(data);
        			$('#visitor_table').DataTable().destroy();
        			load_data();
        			setTimeout(function(){
        				$('#message').html('');
        			}, 5000);
        		}
        	});
    	}
  	});

  	$(document).on('click', '.view_button', function(){
  		var visitor_id = $(this).data('id');
  		$.ajax({
  			url:"visitor_action.php",
	      	method:"POST",
	      	data:{visitor_id:visitor_id, action:'fetch_single'},
	      	dataType:'JSON',
	      	success:function(data)
	      	{
	      		$('#visitor_name_detail').text(data.visitor_name);
	      		$('#visitor_email_detail').text(data.visitor_email);
	      		$('#visitor_mobile_no_detail').text(data.visitor_mobile_no);
	      		$('#visitor_address_detail').text(data.visitor_address);
	      		$('#visitor_department_detail').text(data.visitor_department);
	      		$('#visitor_meet_person_name_detail').text(data.visitor_meet_person_name);
	      		$('#visitor_reason_to_meet_detail').text(data.visitor_reason_to_meet);
	      		$('#visitor_outing_remark').val(data.visitor_outing_remark);
	      		$('#visitordetailModal').modal('show');
	      		$('#hidden_visitor_id').val(visitor_id);
	      	}
  		})
  	});

  	$('#visitor_details_form').parsley();

  	$('#visitor_details_form').on('submit', function(event){
  		event.preventDefault();
  		if($('#visitor_details_form').parsley().isValid())
		{		
			$.ajax({
				url:"visitor_action.php",
				method:"POST",
				data:$(this).serialize(),
				beforeSend:function()
				{
					$('#detail_submit_button').attr('disabled', 'disabled');
					$('#detail_submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#detail_submit_button').attr('disabled', false);
					$('#detail_submit_button').val('Save');
					$('#visitordetailModal').modal('hide');
					$('#message').html(data);
					$('#visitor_table').DataTable().destroy();
					load_data();
					setTimeout(function(){
						$('#message').html('');
					}, 5000);
				}
			});
		}
  	});

  	$('#filter').click(function(){
  		var from_date = $('#from_date').val();
  		var to_date = $('#to_date').val();
  		$('#visitor_table').DataTable().destroy();
  		load_data(from_date, to_date);
  	});

  	$('#refresh').click(function(){
  		$('#from_date').val('');
  		$('#to_date').val('');
  		$('#visitor_table').DataTable().destroy();
  		load_data();
  	});

  	$('#export').click(function(){
  		var from_date = $('#from_date').val();
  		var to_date = $('#to_date').val();

  		if(from_date != '' && to_date != '')
  		{
  			window.location.href="<?php echo $visitor->base_url; ?>export.php?from_date="+from_date+"&to_date="+to_date+"";
  		}
  		else
  		{
  			window.location.href="<?php echo $visitor->base_url; ?>export.php";
  		}
  	});

});

</script>