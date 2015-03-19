<?php
	require_once("crud.php");
	
	$crud = new Crud();
	$crud->title = "Video Studios";
	$crud->table = "{$_SESSION['DB_PREFIX']}studio";
	$crud->dialogwidth = 500;
	$crud->sql = 
			"SELECT A.*  " .
			"FROM {$_SESSION['DB_PREFIX']}studio A " .
			"WHERE A.type = 'V' " .
			"ORDER BY A.name";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'pk'		 => true,
				'showInView' => false,
				'editable'	 => false,
				'bind' 	 	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'type',
				'length' 	 => 1,
				'showInView' => false,
				'default'	 => 'V',
				'editable'	 => false,
				'label' 	 => 'Type'
			),
			array(
				'name'       => 'name',
				'length' 	 => 60,
				'label' 	 => 'Name'
			)
		);

	$crud->run();
?>
