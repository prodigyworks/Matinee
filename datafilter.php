<?php
require_once('php-sql-parser.php');
require_once('php-sql-creator.php');

function getFilteredData($sql) {
	if (! isset($_SESSION['SITE_CONFIG'])) {
		return $sql;
	}
	
	$parser = new PHPSQLParser($sql);
	$tablealias = null;
	$data = getSiteConfigData();
	
	foreach ($parser->parsed['FROM'] as $table) {
		if ($table['table'] == "horizon_members") {
			if ($table['alias'] != "") {
				$tablealias = $table['alias']['name'];
				
			} else {
				$tablealias = $table['table'];
			} 
		}
	}
	
//	echo $sql . "\n";
//	print_r($parser->parsed);
	
	if (! isset($parser->parsed['WHERE'])) {
		/* Create where clause. */
		$parser->parsed['WHERE'] = array();
					
	} else {
		/* Add to the where clause. */
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "operator",
						"base_expr"			=> "AND",
						"sub_tree"			=> ""
					);
	}
	
	if (isUserInRole($data->adminrole) ||
	    isUserInRole($data->managementrole)) {
		/* Do nothing, access rights to all. */
		return $sql;
	} 
	
	if (isUserInRole($data->trainingmanagementrole)) {
		/* Not restricted by anything training related. 
		 * Page roles will prevent access to parts of the system
		 * that are not appropriate to training management.
		 */ 
		return $sql;
	}
					
	if (isUserInRole($data->officeadminrole)) {
		/* Restricted to.
		 * Personal details for APPRAISALS only.
		 */ 
		foreach ($parser->parsed['FROM'] as $table) {
			if ($table['table'] != "horizon_appraisal") {
				$parser->parsed['WHERE'][] = 
						array(
								"expr_type" 		=> "colref",
								"base_expr"			=> $tablealias . ".member_id",
								"sub_tree"			=> ""
							);
							
				$parser->parsed['WHERE'][] = 
						array(
								"expr_type" 		=> "operator",
								"base_expr"			=> "=",
								"sub_tree"			=> ""
							);
							
				$parser->parsed['WHERE'][] = 
						array(
								"expr_type" 		=> "const",
								"base_expr"			=> getLoggedOnMemberID(),
								"sub_tree"			=> ""
							);
			}
		}
	
	} if (isUserInRole($data->compliancerole)) {
		foreach ($parser->parsed['FROM'] as $table) {
			if ($table['table'] == "horizon_holiday") {
				/* Compliance don't restrict holidays */
				return $sql;
			}
		}
		
		/* Restricted to.
		 * All technicians and team leaders.
		 */ 
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "bracket_expression",
						"sub_tree"			=> array(
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".position",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> "'" . $data->technicianposition . "'",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "OR",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".position",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> "'" . $data->teamleaderposition . "'",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "OR",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".member_id",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> getLoggedOnMemberID(),
									"sub_tree"			=> ""
								)
							)
					);
	
	} else if (isUserInRole($data->regionalservicemanagerrole)) {
		/* Restricted to.
		 * All personnel and team leaders.
		 */ 
		$parser->parsed['OPTIONS'][] = "DISTINCT";
		$parser->parsed['FROM'][] = 
				array(
						"expr_type" 		=> "table",
						"table"				=> "horizon_userteams",
						"alias"				=> array(
								"as"					=> "",
								"name"					=> "horizon_userteams",
								"base_expr"				=> "horizon_userteams"
							),
						"join_type"			=> "JOIN",
						"ref_type"			=> "ON",
						"ref_clause"		=> array(
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> "horizon_userteams.memberid",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> getLoggedOnMemberID(),
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "OR",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".member_id",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> getLoggedOnMemberID(),
									"sub_tree"			=> ""
								)
							)
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "bracket_expression",
						"sub_tree"			=> array(
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> "horizon_userteams.teamid",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> $tablealias . ".teamid",
									"sub_tree"			=> ""
								)
							)
					);
					
	} else if (isUserInRole($data->officerole)) {
		$appraisal = false;
		
		foreach ($parser->parsed['FROM'] as $table) {
			if ($table['table'] == "horizon_appraisal") {
				/* Compliance don't restrict holidays */
				$appraisal = true;
			}
		}
		
		if (! $appraisal) {
			return $sql;
		}
		
		/* Restricted to.
		 * All technicians and team leaders.
		 */ 
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "bracket_expression",
						"sub_tree"			=> array(
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".position",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> "'" . $data->technicianposition . "'",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "OR",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".position",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> "'" . $data->teamleaderposition . "'",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "OR",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".member_id",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> getLoggedOnMemberID(),
									"sub_tree"			=> ""
								)
							)
					);

	} else if (isUserInRole($data->officemanagerrole)) {
		/* Restricted to.
		 * All personnel and team leaders.
		 */ 
		$parser->parsed['OPTIONS'][] = "DISTINCT";
		$parser->parsed['FROM'][] = 
				array(
						"expr_type" 		=> "table",
						"table"				=> "horizon_userroles",
						"alias"				=> array(
								"as"					=> "",
								"name"					=> "horizon_userroles",
								"base_expr"				=> "horizon_userroles"
							),
						"join_type"			=> "JOIN",
						"ref_type"			=> "ON",
						"ref_clause"		=> array(
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> "horizon_userroles.memberid",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> $tablealias . ".member_id",
									"sub_tree"			=> ""
								),
							)
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "bracket_expression",
						"sub_tree"			=> array(
								array(
									"expr_type" 		=> "colref",
									"base_expr"			=> "horizon_userroles.roleid",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								),
								array(
									"expr_type" 		=> "const",
									"base_expr"			=> "'" . $data->officepersonnelrole . "'",
									"sub_tree"			=> ""
								)
							)
					);
					
	} else if (isUserInRole($data->teamleaderrole)) {
		/* Restricted to.
		 * Team personnel and themselves.
		 */ 
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "colref",
						"base_expr"			=> $tablealias . ".teamid",
						"sub_tree"			=> ""
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "operator",
						"base_expr"			=> "=",
						"sub_tree"			=> ""
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "const",
						"base_expr"			=> getLoggedOnTeamID(),
						"sub_tree"			=> ""
					);
					
	} else if (isUserInRole($data->areacoordinatorrole)) {
		/* Restricted to.
		 * Team personnel and themselves.
		 */ 
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "colref",
						"base_expr"			=> $tablealias . ".teamid",
						"sub_tree"			=> ""
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "operator",
						"base_expr"			=> "=",
						"sub_tree"			=> ""
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "const",
						"base_expr"			=> getLoggedOnTeamID(),
						"sub_tree"			=> ""
					);
		
	} else {
		/* Restricted to.
		 * Technician Level 1 – Personal details.
		 */ 
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "colref",
						"base_expr"			=> $tablealias . ".member_id",
						"sub_tree"			=> ""
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "operator",
						"base_expr"			=> "=",
						"sub_tree"			=> ""
					);
					
		$parser->parsed['WHERE'][] = 
				array(
						"expr_type" 		=> "const",
						"base_expr"			=> getLoggedOnMemberID(),
						"sub_tree"			=> ""
					);
		
	}
	
	$creator = new PHPSQLCreator($parser->parsed);
	$created = $creator->created;			

	return $created;
}
?>