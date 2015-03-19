<?php
	require_once("crud.php");
	
	class AuditCrud extends Crud {
		
		public function postScriptEvent() {
?>
			/* Full name callback. */
			function fullName(node) {
				return (node.firstname + " " + node.lastname);
			}
			function fullCreatedName(node) {
				return (node.cbfirstname + " " + node.cblastname);
			}
<?php			
		}
	}

	$crud = new AuditCrud();
	$crud->title = "Audit Logs";
	$crud->table = "{$_SESSION['DB_PREFIX']}booking ";
	$crud->dialogwidth = 400;
	$crud->sql = 
			"SELECT A.*, B.studioid,D.name,  C.firstname, C.lastname, E.firstname AS cbfirstname, E.lastname AS cblastname " .
			"FROM {$_SESSION['DB_PREFIX']}booking A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}engineercalendar B " .
			"ON B.bookingid = A.id " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
			"ON C.member_id = B.memberid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}studio D " .
			"ON D.id = B.studioid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members E " .
			"ON E.member_id = A.createdby " .
			"ORDER BY A.id DESC";
	
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
				'name'       => 'pageid',
				'length' 	 => 6,
				'showInView' => false,
				'editable'	 => false,
				'bind' 	 	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'createddate',
				'length' 	 => 12,
				'bind'		 => false,
				'label' 	 => 'Created Date'
			),
			array(
				'name'       => 'createdby',
				'datatype'	 => 'user',
				'length' 	 => 12,
				'showInView' => false,
				'label' 	 => 'Created By'
			),
			array(
				'name'       => 'createdbyname',
				'type'		 => 'DERIVED',
				'length' 	 => 30,
				'bind'		 => false,
				'function'	 => 'fullCreatedName',
				'label' 	 => 'Created By'
			),
			array(
				'name'       => 'user',
				'type'		 => 'DERIVED',
				'length' 	 => 30,
				'function'	 => 'fullName',
				'bind'		 => false,
				'label' 	 => 'User'
			),
			array(
				'name'       => 'studioid',
				'type'       => 'DATACOMBO',
				'length' 	 => 35,
				'bind'		 => false,
				'label' 	 => 'Studio',
				'table'		 => 'studio',
				'table_id'	 => 'id',
				'alias'		 => 'name',
				'table_name' => 'name'
			),
			array(
				'name'       => 'bookingstart',
				'datatype'	 => 'datetime',
				'label' 	 => 'Start Time'
			),
			array(
				'name'       => 'bookingend',
				'datatype'	 => 'datetime',
				'label' 	 => 'End Time'
			)
		);
		
	$crud->run();
?>
