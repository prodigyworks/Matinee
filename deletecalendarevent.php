<?php
	require_once("system-db.php");
	
	start_db();
	
	$json = array();
	$id = $_POST['id'];
	
	$qry = "SELECT A.*, B.bookingstart, B.bookingend " .
			"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
			"ON B.id = A.bookingid " .
			"WHERE B.id = $id";
	$result = mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$origmemberid = $member['memberid'];
			$origstudioid = $member['studioid'];
			$origstart = $member['bookingstart'];
			$origend = $member['bookingend'];
			$createdby = getLoggedOnMemberID();
			
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}auditlog " .
					"(createdby, createddate, modifiedmemberid, modifiedstudioid, modifiedstartdate, modifiedenddate, originalmemberid, originalstudioid, originalstartdate, originalenddate, mode) " .
					"VALUES " .
					"($createdby, NOW(), $createdby, $origstudioid, '$origstart', '$origstart', $origmemberid, $origstudioid, '$origstart', '$origend', 'R')";
			$itemresult = mysql_query($sql);
			
			if (! $itemresult) {
				logError($sql . " - " . mysql_error(), false);
			}
		}
		
	} else {
		logError($qry . " - " . mysql_error(), false);
	}
	
	$sql = "DELETE FROM {$_SESSION['DB_PREFIX']}booking " .
			"WHERE id= $id";
	$result = mysql_query($sql);
	
	if (! $result) {
		logError($sql . " - " . mysql_error(), false);
	}
	
	$line = array(
		"ok" => 1
	); 
	
	array_push($json, $line);

logError(json_encode($json), false);
	echo json_encode($json); 
?>