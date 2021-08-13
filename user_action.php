<?php

//user_action.php

include('vms.php');

$visitor = new vms();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('admin_name', 'admin_contact_no', 'admin_email', 'admin_created_on');

		$output = array();

		$main_query = "
		SELECT * FROM admin_table 
		WHERE admin_type = 'User' 
		
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND (admin_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR admin_contact_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR admin_email LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR admin_created_on LIKE "%'.$_POST["search"]["value"].'%") ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY admin_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$visitor->query = $main_query . $search_query . $order_query;

		$visitor->execute();

		$filtered_rows = $visitor->row_count();

		$visitor->query .= $limit_query;

		$result = $visitor->get_result();

		$visitor->query = $main_query;

		$visitor->execute();

		$total_rows = $visitor->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = '<img src="'.$row["admin_profile"].'" class="img-fluid img-thumbnail" width="75" height="75" />';
			$sub_array[] = html_entity_decode($row["admin_name"]);
			$sub_array[] = $row["admin_contact_no"];
			$sub_array[] = $row["admin_email"];
			$sub_array[] = $row["admin_created_on"];
			$delete_button = '';
			if($row["admin_status"] == 'Enable')
			{
				$delete_button = '<button type="button" name="delete_button" class="btn btn-primary btn-sm delete_button" data-id="'.$row["admin_id"].'" data-status="'.$row["admin_status"].'">'.$row["admin_status"].'</button>';
			}
			else
			{
				$delete_button = '<button type="button" name="delete_button" class="btn btn-danger btn-sm delete_button" data-id="'.$row["admin_id"].'" data-status="'.$row["admin_status"].'">'.$row["admin_status"].'</button>';
			}
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-sm edit_button" data-id="'.$row["admin_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			'.$delete_button.'
			</div>';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$data = array(
			':admin_email'	=>	$_POST["admin_email"]
		);

		$visitor->query = "
		SELECT * FROM admin_table 
		WHERE admin_email = :admin_email
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">User Email Already Exists</div>';
		}
		else
		{
			$user_image = '';
			if($_FILES["user_image"]["name"] != '')
			{
				$user_image = upload_image();
			}
			else
			{
				$user_image = make_avatar(strtoupper($_POST["admin_name"][0]));
			}

			$data = array(
				':admin_name'		=>	$visitor->clean_input($_POST["admin_name"]),
				':admin_contact_no'	=>	$_POST["admin_contact_no"],
				':admin_email'		=>	$_POST["admin_email"],
				':admin_password'	=>	password_hash($_POST["admin_password"], PASSWORD_DEFAULT),
				':admin_profile'	=>	$user_image,
				':admin_type'		=>	'User',
				':admin_created_on'	=>	$visitor->get_datetime()
			);

			$visitor->query = "
			INSERT INTO admin_table 
			(admin_name, admin_contact_no, admin_email, admin_password, admin_profile, admin_type, admin_created_on) 
			VALUES (:admin_name, :admin_contact_no, :admin_email, :admin_password, :admin_profile, :admin_type, :admin_created_on)
			";

			$visitor->execute($data);

			$success = '<div class="alert alert-success">User Added</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$visitor->query = "
		SELECT * FROM admin_table 
		WHERE admin_id = '".$_POST["admin_id"]."'
		";

		$result = $visitor->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['admin_name'] = $row['admin_name'];
			$data['admin_contact_no'] = $row['admin_contact_no'];
			$data['admin_email'] = $row['admin_email'];
			$data['admin_profile'] = $row['admin_profile'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':admin_email'	=>	$_POST["admin_email"],
			':admin_id'		=>	$_POST['hidden_id']
		);

		$visitor->query = "
		SELECT * FROM admin_table 
		WHERE admin_email = :admin_email 
		AND admin_id != :admin_id
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">User Email Already Exists</div>';
		}
		else
		{
			$user_image = $_POST["hidden_user_image"];
			if($_FILES["user_image"]["name"] != '')
			{
				$user_image = upload_image();
			}

			$data[':admin_name'] = $visitor->clean_input($_POST["admin_name"]);
			$data[':admin_contact_no'] = $_POST["admin_contact_no"];
			$data[':admin_email'] = $_POST["admin_email"];
			if($_POST["admin_password"] != '')
			{
				$data[':admin_password'] = password_hash($_POST["admin_password"], PASSWORD_DEFAULT);
			}
			$data[':admin_profile'] = $user_image;

			if($_POST["admin_password"] != '')
			{
				$data = array(
					':admin_name'	=>	$visitor->clean_input($_POST["admin_name"]),
					':admin_contact_no'	=>	$_POST["admin_contact_no"],
					':admin_email'	=>	$_POST["admin_email"],
					':admin_password'	=>	password_hash($_POST["admin_password"], PASSWORD_DEFAULT),
					':admin_profile'	=>	$user_image
				);

				$visitor->query = "
				UPDATE admin_table 
				SET admin_name = :admin_name, 
				admin_contact_no = :admin_contact_no, 
				admin_email = :admin_email, 
				admin_password = :admin_password, 
				admin_profile = :admin_profile 
				WHERE admin_id = '".$_POST['hidden_id']."'
				";

				$visitor->execute($data);
			}
			else
			{
				$data = array(
					':admin_name'	=>	$visitor->clean_input($_POST["admin_name"]),
					':admin_contact_no'	=>	$_POST["admin_contact_no"],
					':admin_email'	=>	$_POST["admin_email"],
					':admin_profile'	=>	$user_image
				);

				$visitor->query = "
				UPDATE admin_table 
				SET admin_name = :admin_name, 
				admin_contact_no = :admin_contact_no, 
				admin_email = :admin_email,  
				admin_profile = :admin_profile 
				WHERE admin_id = '".$_POST['hidden_id']."'
				";

				$visitor->execute($data);
			}

			$success = '<div class="alert alert-success">User Details Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$data = array(
			':admin_status'		=>	$_POST['next_status']
		);

		$visitor->query = "
		UPDATE admin_table 
		SET admin_status = :admin_status 
		WHERE admin_id = '".$_POST["id"]."'
		";

		$visitor->execute($data);

		echo '<div class="alert alert-success">User Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'profile')
	{
		sleep(2);

		$error = '';

		$success = '';

		$admin_name = '';

		$admin_contact_no = '';

		$admin_email = '';

		$admin_profile = '';

		$data = array(
			':admin_email'	=>	$_POST["admin_email"],
			':admin_id'		=>	$_POST['hidden_id']
		);

		$visitor->query = "
		SELECT * FROM admin_table 
		WHERE admin_email = :admin_email 
		AND admin_id != :admin_id
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">User Email Already Exists</div>';
		}
		else
		{
			$user_image = $_POST["hidden_user_image"];
			if($_FILES["user_image"]["name"] != '')
			{
				$user_image = upload_image();
			}

			$admin_name = $visitor->clean_input($_POST["admin_name"]);

			$admin_contact_no = $_POST["admin_contact_no"];

			$admin_email = $_POST["admin_email"];

			$admin_profile = $user_image;

			$data = array(
				':admin_name'	=>	$admin_name,
				':admin_contact_no'	=>	$admin_contact_no,
				':admin_email'	=>	$admin_email,
				':admin_profile'	=>	$admin_profile
			);

			$visitor->query = "
			UPDATE admin_table 
			SET admin_name = :admin_name, 
			admin_contact_no = :admin_contact_no, 
			admin_email = :admin_email,  
			admin_profile = :admin_profile 
			WHERE admin_id = '".$_POST['hidden_id']."'
			";

			$visitor->execute($data);

			$success = '<div class="alert alert-success">User Details Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success,
			'admin_name'	=>	$admin_name,
			'admin_contact_no'	=>	$admin_contact_no,
			'admin_email'	=>	$admin_email,
			'admin_profile'	=>	$admin_profile
		);

		echo json_encode($output);
	}

	if($_POST["action"] == 'change_password')
	{
		$error = '';
		$success = '';
		$visitor->query = "
		SELECT admin_password FROM admin_table 
		WHERE admin_id = '".$_SESSION["admin_id"]."'
		";

		$result = $visitor->get_result();

		foreach($result as $row)
		{
			if(password_verify($_POST["current_password"], $row["admin_password"]))
			{
				$data = array(
					':admin_password'	=>	password_hash($_POST["new_password"], PASSWORD_DEFAULT)
				);
				$visitor->query = "
				UPDATE admin_table 
				SET admin_password = :admin_password 
				WHERE admin_id = '".$_SESSION["admin_id"]."'
				";

				$visitor->execute($data);

				$success = '<div class="alert alert-success">Password Change Successfully</div>';
			}
			else
			{
				$error = '<div class="alert alert-danger">You have enter wrong current password</div>';
			}
		}
		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);
		echo json_encode($output);
	}
}

function upload_image()
{
	if(isset($_FILES["user_image"]))
	{
		$extension = explode('.', $_FILES['user_image']['name']);
		$new_name = rand() . '.' . $extension[1];
		$destination = 'images/' . $new_name;
		move_uploaded_file($_FILES['user_image']['tmp_name'], $destination);
		return $destination;
	}
}

function make_avatar($character)
{
    $path = "images/". time() . ".png";
	$image = imagecreate(200, 200);
	$red = rand(0, 255);
	$green = rand(0, 255);
	$blue = rand(0, 255);
    imagecolorallocate($image, $red, $green, $blue);  
    $textcolor = imagecolorallocate($image, 255,255,255);  

    imagettftext($image, 100, 0, 55, 150, $textcolor, 'font/arial.ttf', $character);
    imagepng($image, $path);
    imagedestroy($image);
    return $path;
}

?>