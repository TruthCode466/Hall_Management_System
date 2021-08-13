<?php

include('vms.php');

$visitor = new vms();

if($visitor->is_login())
{
	header("location:".$visitor->base_url."dashboard.php");
}

include('header.php');

?>
		<br />
		<br />
		<br />
		<br />
		<div class="container">
			<h3 align="center">Visitor Management System</h3>
			<br />
			
			<div class="row">
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-6">
					<span id="error"></span>
					<div class="card">
						<div class="card-header">Login</div>
						<div class="card-body">
							<form method="post" id="login_form">
								<div class="form-group">
									<label>Enter Email Address</label>
									<input type="text" name="user_email" id="user_email" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
								</div>
								<div class="form-group">
									<label>Enter password</label>
									<input type="password" name="user_password" id="user_password" class="form-control" required  data-parsley-trigger="keyup" />
								</div>
								<div class="form-group text-center">
									<input type="submit" name="login" id="login_button" class="btn btn-primary" value="Login" />
								</div>
							</form>
						</div>
					</div>
					<br />
					<br />
				</div>
			</div>
		</div>
		<br />
		<br />
	</body>
</html>

<script>

$(document).ready(function(){

	$('#login_form').parsley();

	$('#login_form').on('submit', function(event){
		event.preventDefault();
		if($('#login_form').parsley().isValid())
		{		
			$.ajax({
				url:"login_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function()
				{
					$('#login_button').attr('disabled', 'disabled');
					$('#login_button').val('wait...');
				},
				success:function(data)
				{
					$('#login_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#error').html(data.error);
						$('#login_button').val('Login');
					}
					else
					{
						window.location.href = "<?php echo $visitor->base_url; ?>dashboard.php";
					}
				}
			})
		}
	});

});

</script>