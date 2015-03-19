<?php
	require_once("crud.php");
	
	class AuditCrud extends Crud {
		
		public function postScriptEvent() {
?>
			/* Full name callback. */
			function fullOriginalName(node) {
				return (node.origfirstname + " " + node.origlastname);
			}
			
			function fullModifiedName(node) {
				if (node.modfirstname == null) {
					return "";
				}
				
				return (node.modfirstname + " " + node.modlastname);
			}
			
			function fullCreatedName(node) {
				return (node.cbfirstname + " " + node.cblastname);
			}
<?php			
		}
	}

	$crud = new AuditCrud();
	$crud->title = "Audit Logs";
	$crud->table = "{$_SESSION['DB_PREFIX']}auditlog ";
	$crud->allowEdit = isUserInRole("ADMIN");
	$crud->allowRemove = isUserInRole("ADMIN");
	$crud->allowAdd = isUserInRole("ADMIN");
	$crud->dialogwidth = 600;
	$crud->sql = 
			"SELECT A.*, " .
			"B.name AS originalstudioname, " .
			"D.name AS modifiedstudioname, " .
			"C.firstname AS origfirstname, C.lastname AS origlastname, " .
			"E.firstname AS cbfirstname, E.lastname AS cblastname, " .
			"F.firstname AS modfirstname, F.lastname AS modlastname " .
			"FROM {$_SESSION['DB_PREFIX']}auditlog A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
			"ON C.member_id = A.originalmemberid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}studio B " .
			"ON B.id = A.originalstudioid " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}studio D " .
			"ON D.id = A.modifiedstudioid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members E " .
			"ON E.member_id = A.createdby " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members F " .
			"ON F.member_id = A.modifiedmemberid " .
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
				'name'       => 'createddate',
				'length' 	 => 18,
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
				'name'       => 'mode',
				'length' 	 => 10,
				'label' 	 => 'Type',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'U',
							'text'		=> 'Update'
						),
						array(
							'value'		=> 'R',
							'text'		=> 'Remove'
						),
						array(
							'value'		=> 'I',
							'text'		=> 'Insert'
						)
					)
			),
			array(
				'name'       => 'createdbyname',
				'type'		 => 'DERIVED',
				'length' 	 => 20,
				'sortcolumn' => 'E.firstname',
				'bind'		 => false,
				'function'	 => 'fullCreatedName',
				'label' 	 => 'Created By'
			),
			array(
				'name'       => 'originaluser',
				'type'		 => 'DERIVED',
				'sortcolumn' => 'C.firstname',
				'length' 	 => 20,
				'function'	 => 'fullOriginalName',
				'bind'		 => false,
				'label' 	 => 'Original User'
			),
			array(
				'name'       => 'originalstudioid',
				'type'       => 'DATACOMBO',
				'length' 	 => 15,
				'bind'		 => false,
				'label' 	 => 'Original Studio',
				'table'		 => 'studio',
				'table_id'	 => 'id',
				'alias'		 => 'originalstudioname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'originalstartdate',
				'datatype'	 => 'datetime',
				'length' 	 => 18,
				'label' 	 => 'Original Start Time'
			),
			array(
				'name'       => 'originalenddate',
				'length' 	 => 18,
				'datatype'	 => 'datetime',
				'label' 	 => 'Original End Time'
			),
			array(
				'name'       => 'modifieduser',
				'sortcolumn' => 'F.firstname',
				'type'		 => 'DERIVED',
				'length' 	 => 20,
				'function'	 => 'fullModifiedName',
				'bind'		 => false,
				'label' 	 => 'Modified User'
			),
			array(
				'name'       => 'modifiedstudioid',
				'type'       => 'DATACOMBO',
				'length' 	 => 15,
				'bind'		 => false,
				'label' 	 => 'Modified Studio',
				'table'		 => 'studio',
				'table_id'	 => 'id',
				'alias'		 => 'modifiedstudioname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'modifiedstartdate',
				'length' 	 => 18,
				'datatype'	 => 'datetime',
				'label' 	 => 'Modified Start Time'
			),
			array(
				'name'       => 'modifiedenddate',
				'length' 	 => 18,
				'datatype'	 => 'datetime',
				'label' 	 => 'Modified End Time'
			)
		);
		
	$crud->run();
?>
