<?php

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
	            				<h2>Room Area</h2>
	            			</div>
	            			<div class="col text-right">
	            				<button type="button" name="add_room" id="add_room" class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button>
	            			</div>
	            		</div>
	            	</div>
	            	<div class="card-body">
	            		<div class="table-responsive">
	            			<table class="table table-striped table-bordered" id="room_table">
	            				<thead>
	            					<tr>
                                        <th>Room Name</th>
                                        <th>Block ID</th>
                                        <th>Active</th>
                                        <th>Capacity</th>
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

<div id="roomModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="room_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Data</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
        		   	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Block Name</label>
			            	<div class="col-md-8">
			            		<select name="block_id" id="block_id" class="form-control" required data-parsley-trigger="keyup">
			            			<option value="">Select Block</option>
			            			<?php echo $visitor->load_blocks(); ?>
			            		</select>
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Room Name</label>
			            	<div class="col-md-6">
			            		<input type="text" name="name" id="name" class="form-control" required data-parsley-pattern="/^(.+)$/" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Room Capacity</label>
			            	<div class="col-md-6">
			            		<input type="number" name="capacity" id="capacity" class="form-control" required data-parsley-pattern="/^[\d+\s]+$/" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-4 text-right">Room Active</label>
			            	<div class="col-md-6">
			            		<input type="checkbox" name="active" id="active" class="form-control" checked/>
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

<script>

$(document).ready(function(){

    console.log("loading data...");

	var dataTable = $('#room_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"room_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[4],
				"orderable":false,
			},
		],
	});

    console.log("on add room click start...");

	$('#add_room').click(function(){
		
		$('#room_form')[0].reset();

		$('#room_form').parsley().reset();

    	$('#modal_title').text('Add Data');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#roomModal').modal('show');

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
				<input type="text" name="room_contact_person[]" class="form-control room_contact_person" required data-parsley-pattern="/^[a-zA-Z ]+$/"  data-parsley-trigger="keyup" />
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

	$('#room_form').parsley();

	$('#room_form').on('submit', function(event){
		event.preventDefault();
		console.log($(this).serialize());
		if($('#room_form').parsley().isValid())
		{		
			$.ajax({
				url:"room_action.php",
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
						$('#roomModal').modal('hide');
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

		var room_id = $(this).data('id');

		$('#room_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"room_action.php",

	      	method:"POST",

	      	data:{id:room_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	      	    console.log(data);

	        	$('#name').val(data.name);

	        	$('#block_id').val(data.block_id);

	        	$('#capacity').val(data.capacity);

	        	$('#active').prop('checked', data.active != 0);

	        	$('#modal_title').text('Edit Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#roomModal').modal('show');

	        	$('#hidden_id').val(room_id);

	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"room_action.php",

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