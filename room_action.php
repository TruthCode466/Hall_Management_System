<?php


include('vms.php');

$visitor = new vms();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('id', 'name', 'block_id', 'capacity', 'active');

		$output = array();

		$main_query = "SELECT * FROM room ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR name LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY id DESC ';
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
			$sub_array[] = html_entity_decode($row["name"]);
			$sub_array[] = html_entity_decode($row["block_id"]);
			$sub_array[] = $row["active"] == '0' ? 'no' : 'yes';
			$sub_array[] = html_entity_decode($row["capacity"]);
            $sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-sm edit_button" data-id="'.$row["id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-sm delete_button" data-id="'.$row["id"].'"><i class="fas fa-times"></i></button>
			</div>
			';
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
			':name'	=>	$_POST["name"]
		);

		$visitor->query = "
		SELECT * FROM room
		WHERE name = :name
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Room Already Exists</div>';
		}
		else
		{
			$data = array(
				':name'		=>	$visitor->clean_input($_POST["name"]),
				':block_id'		=>	$visitor->clean_input($_POST["block_id"]),
				':capacity'		=>	$visitor->clean_input($_POST["capacity"]),
				':active'		=>	isset($_POST["active"]) ? true : false
			);

			$visitor->query = "
			INSERT INTO room
			(name, block_id, capacity, active)
			VALUES (:name, :block_id, :capacity, :active)
			";

			$visitor->execute($data);

			$success = '<div class="alert alert-success">Room Added</div>';
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
		SELECT * FROM room
		WHERE id = '".$_POST["id"]."'
		";

		$result = $visitor->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['name'] = $row['name'];
			$data['block_id'] = $row['block_id'];
			$data['capacity'] = $row['capacity'];
			$data['active'] = $row['active'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':id'	=>	$_POST['hidden_id'],
	        ':name'	=>	$_POST["name"]
		);

		$visitor->query = "
		SELECT * FROM room
		WHERE name = :name 
		AND id != :id
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Room Already Exists</div>';
		}
		else
		{
			$data = array(
				':name'		=>	$visitor->clean_input($_POST["name"]),
				':block_id'		=>	$visitor->clean_input($_POST["block_id"]),
				':capacity'		=>	$visitor->clean_input($_POST["capacity"]),
				':active'		=>	isset($_POST["active"]) ? true : false,
			);

			$visitor->query = "
			UPDATE room
			SET name = :name, block_id = :block_id, capacity = :capacity, active = :active
			WHERE id = '".$_POST['hidden_id']."'
			";

			$visitor->execute($data);

			$success = '<div class="alert alert-success">Room Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$visitor->query = "
		DELETE FROM room
		WHERE id = '".$_POST["id"]."'
		";

		$visitor->execute();

		echo '<div class="alert alert-success">Room Deleted</div>';
	}
}

?>