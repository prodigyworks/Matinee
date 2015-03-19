<?php
	require_once("system-db.php");
	
	start_db();
	
	$memberid = getLoggedOnMemberID();
	$calendarid = $_POST['calendarid'];
	$checked = $_POST['checked'];
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}membersession " .
			"(memberid, calendarid, checked) " .
			"VALUES " .
			"($memberid, '$calendarid', $checked)";
	$result = mysql_query($sql);
	
	if (! $result) {
		if (mysql_errno() == 1062) {
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}membersession SET " .
					"checked = $checked " .
					"WHERE memberid = $memberid " .
					"AND calendarid = '$calendarid'";
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error(), false);
			}
			
		} else {
			logError($sql . " - " . mysql_error(), false);
	
			throw new Exception(mysql_error());
		}
	}
	
	$line = array(
		"ok" => 1
	); 
	
	array_push($json, $line);

	echo json_encode($json); 
?>