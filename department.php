<?php

//department.php

include('vms.php');

$visitor = new vms();

if(!$visitor->is_login())
{
	header("location:".$visitor->base_url."");
}

if(!$visitor->is_master_user())
{
	header("location:".$visitor->base_url."dashboard.php");
}

include('header.php');

include('sidebar.php');
?>
	
	
	        <div class="col-sm-10 offset-sm-2 py-4">
	        	<span id="message"></span>
	            <div class="card">
	            	<div class="card-header">
	            		<div class="row">
	            			<div class="col">
	            				<h2>Department Area</h2>
	            			</div>
	            			<div class="col text-right">
	            				<button type="button" name="add_department" id="add_department" class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button>
	            			</div>
	            		</div>
	            	</div>
	            	<div class="card-body">
	            		<div class="table-responsive">
	            			<table class="table table-striped table-bordered" id="department_table">
	            				<thead>
	            					<tr>
	            						<th>Department Name</th>
										<th>Person List</th>							
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

<div id="departmentModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="deparment_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Data</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Department Name</label>
			            	<div class="col-md-6">
			            		<input type="text" name="department_name" id="department_name" class="form-control" required data-parsley-pattern="/^[a-zA-Z\s]+$/" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Contact Person</label>
			            	<div class="col-md-6">
			            		<input type="text" name="department_contact_person[]" class="form-control department_contact_person" id="department_contact_person" required data-parsley-pattern="/^[a-zA-Z\s]+$/"  data-parsley-trigger="keyup" />
			            	</div>
			            	<div class="col-md-2">
			            		<button type="button" name="add_person" id="add_person" class="btn btn-success btn-sm">+</button>
			            	</div>
			            </div>
		            	<div id="append_person"></div>
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

<script>

$(document).ready(function(){

	var dataTable = $('#department_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"department_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[2],
				"orderable":false,
			},
		],
	});

	$('#add_department').click(function(){
		
		$('#deparment_form')[0].reset();

		$('#deparment_form').parsley().reset();

    	$('#modal_title').text('Add Data');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#departmentModal').modal('show');

    	$('#append_person').html('');

    	$('#form_message').html('');

	});

	var count_person = 0;

	$(document).on('click', '#add_person', function(){

		count_person++;

		var html = `		
		<div class="row mt-2" id="person_`+count_person+`">
			<label class="col-md-4">&nbsp;</label>
			<div class="col-md-6">
				<input type="text" name="department_contact_person[]" class="form-control department_contact_person" required data-parsley-pattern="/^[a-zA-Z ]+$/"  data-parsley-trigger="keyup" />
			</div>
			<div class="col-md-2">
				<button type="button" name="remove_person" class="btn btn-danger btn-sm remove_person" data-id="`+count_person+`">-</button>
			</div>
		</div>
		`;
		$('#append_person').append(html);
	});

	$(document).on('click', '.remove_person', function(){

		var button_id = $(this).data('id');

		$('#person_'+button_id).remove();

	});

	$('#deparment_form').parsley();

	$('#deparment_form').on('submit', function(event){
		event.preventDefault();
		if($('#deparment_form').parsley().isValid())
		{		
			$.ajax({
				url:"department_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#departmentModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

	$(document).on('click', '.edit_button', function(){

		var department_id = $(this).data('id');

		$('#deparment_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"department_action.php",

	      	method:"POST",

	      	data:{department_id:department_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	        	$('#department_name').val(data.department_name);

	        	var person_array = data.department_contact_person.split(', ');

	        	var html = '';

	        	for(var count = 0; count < person_array.length; count++)
	        	{
	        		
	        		if(count == 0)
	        		{
	        			$('#department_contact_person').val(person_array[count]);
	        		}
	        		else
	        		{
	        			html += `
	        			<div class="row mt-2" id="person_`+count+`">
							<label class="col-md-4">&nbsp;</label>
							<div class="col-md-6">
								<input type="text" name="department_contact_person[]" class="form-control department_contact_person" required data-parsley-pattern="/^[a-zA-Z ]+$/"  data-parsley-trigger="keyup" value="`+person_array[count]+`" />
							</div>
							<div class="col-md-2">
								<button type="button" name="remove_person" class="btn btn-danger btn-sm remove_person" data-id="`+count+`">-</button>
							</div>
						</div>
	        			`;
	        		}
	        	}

	        	$('#append_person').html(html);

	        	$('#modal_title').text('Edit Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#departmentModal').modal('show');

	        	$('#hidden_id').val(department_id);

	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"department_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

        		success:function(data)
        		{

          			$('#message').html(data);

          			dataTable.ajax.reload();

          			setTimeout(function(){

            			$('#message').html('');

          			}, 5000);

        		}

      		})

    	}

  });

});

</script>