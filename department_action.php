<?php

//department_action.php

include('vms.php');

$visitor = new vms();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('department_name', 'department_contact_person');

		$output = array();

		$main_query = "
		SELECT * FROM department_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE department_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR department_contact_person LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY department_id DESC ';
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
			$sub_array[] = html_entity_decode($row["department_name"]);
			$sub_array[] = html_entity_decode($row["department_contact_person"]);
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-sm edit_button" data-id="'.$row["department_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-sm delete_button" data-id="'.$row["department_id"].'"><i class="fas fa-times"></i></button>
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
			':department_name'	=>	$_POST["department_name"]
		);

		$visitor->query = "
		SELECT * FROM department_table 
		WHERE department_name = :department_name
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Department Already Exists</div>';
		}
		else
		{
			$department_contact_person = implode(", ", $_POST["department_contact_person"]);

			$data = array(
				':department_name'		=>	$visitor->clean_input($_POST["department_name"]),
				':department_contact_person'	=>	$visitor->clean_input($department_contact_person),
				':department_created_on'	=>	$visitor->get_datetime()
			);

			$visitor->query = "
			INSERT INTO department_table 
			(department_name, department_contact_person, department_created_on) 
			VALUES (:department_name, :department_contact_person, :department_created_on)
			";

			$visitor->execute($data);

			$success = '<div class="alert alert-success">Department Added</div>';
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
		SELECT * FROM department_table 
		WHERE department_id = '".$_POST["department_id"]."'
		";

		$result = $visitor->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['department_name'] = $row['department_name'];
			$data['department_contact_person'] = $row['department_contact_person'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':department_name'	=>	$_POST["department_name"],
			':department_id'	=>	$_POST['hidden_id']
		);

		$visitor->query = "
		SELECT * FROM department_table 
		WHERE department_name = :department_name 
		AND department_id != :department_id
		";

		$visitor->execute($data);

		if($visitor->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Department Already Exists</div>';
		}
		else
		{
			$department_contact_person = implode(", ", $_POST["department_contact_person"]);

			$data = array(
				':department_name'		=>	$visitor->clean_input($_POST["department_name"]),
				':department_contact_person'	=>	$visitor->clean_input($department_contact_person)
			);

			$visitor->query = "
			UPDATE department_table 
			SET department_name = :department_name, 
			department_contact_person = :department_contact_person  
			WHERE department_id = '".$_POST['hidden_id']."'
			";

			$visitor->execute($data);

			$success = '<div class="alert alert-success">Department Updated</div>';
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
		DELETE FROM department_table 
		WHERE department_id = '".$_POST["id"]."'
		";

		$visitor->execute();

		echo '<div class="alert alert-success">Department Deleted</div>';
	}
}

?>