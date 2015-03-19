<?php
	require_once("system-db.php");
	
	start_db();
	
	$bookingid = $_POST['bookingid'];
	$calendarid = $_POST['calendarid'];
	$start = $_POST['start'];
	$end = $_POST['end'];
	$memberid = $_POST['memberid'];
	$studioid = $_POST['studioid'];
	$alldayevent = $_POST['alldayevent'];
	
	$json = array();
	$error = "";
	
	$qry = "SELECT A.id, " .
			"DATE_FORMAT(B.bookingstart, '%e/%c/%Y %H:%i') AS bookingstart, " .
			"DATE_FORMAT(B.bookingend, '%e/%c/%Y %H:%i') AS bookingend, " .
			"C.name, D.firstname, D.lastname " .
			"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
			"ON B.id = A.bookingid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}studio C " .
			"ON C.id = A.studioid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
			"ON D.member_id = A.memberid " .
			"WHERE A.memberid = $memberid " .
			"AND B.id != $bookingid " .
			"AND ((bookingstart >= '$start' AND bookingstart < '$end') " .
			"OR   (bookingend > '$start' AND bookingend < '$end') " .
			"OR   (bookingstart <= '$start' AND bookingend >= '$end')) ";
	$result = mysql_query($qry);
	$found = false;

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$found = true;
			$membername = $member['firstname'] . " " . $member['lastname'];
			$name = $member['name'];
			$startdate = $member['bookingstart'];
			$enddate = $member['bookingend'];
			$error = "Engineer '$membername' is booked to studio at '$name' between $startdate and $enddate";
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	if (! $found) {
		$qry = "SELECT A.id, " .
				"DATE_FORMAT(B.bookingstart, '%e/%c/%Y %H:%i') AS bookingstart, " .
				"DATE_FORMAT(B.bookingend, '%e/%c/%Y %H:%i') AS bookingend, " .
				"C.name, D.firstname, D.lastname " .
				"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
				"ON B.id = A.bookingid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}studio C " .
				"ON C.id = A.studioid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
				"ON D.member_id = A.memberid " .
				"WHERE A.studioid = $studioid " .
				"AND B.id != $bookingid " .
				"AND ((bookingstart >= '$start' AND bookingstart < '$end') " .
				"OR   (bookingend > '$start' AND bookingend < '$end') " .
				"OR   (bookingstart <= '$start' AND bookingend >= '$end')) ";
		$result = mysql_query($qry);
	
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$found = true;
				$membername = $member['firstname'] . " " . $member['lastname'];
				$name = $member['name'];
				$startdate = $member['bookingstart'];
				$enddate = $member['bookingend'];
				$error = "Engineer '$membername' is booked to studio at '$name' between $startdate and $enddate";
			}
			
		} else {
			logError($qry . " - " . mysql_error());
		}
	}
	
	if (! $found) {
		$qry = "SELECT A.id, " .
				"DATE_FORMAT(A.startdate, '%e/%c/%Y %H:%i') AS bookingstart, " .
				"DATE_FORMAT(A.enddate, '%e/%c/%Y %H:%i') AS bookingend, " .
				"D.firstname, D.lastname " .
				"FROM {$_SESSION['DB_PREFIX']}holiday A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
				"ON D.member_id = A.memberid " .
				"WHERE A.memberid = $memberid " .
				"AND ((A.startdate >= '$start' AND A.startdate < '$end') " .
				"OR   (A.enddate > '$start' AND A.enddate < '$end') " .
				"OR   (A.startdate <= '$start' AND A.enddate >= '$end')) ";
		$result = mysql_query($qry);
	
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$found = true;
				$membername = $member['firstname'] . " " . $member['lastname'];
				$startdate = $member['bookingstart'];
				$enddate = $member['bookingend'];
				$error = "Engineer '$membername' is on holiday between $startdate and $enddate";
			}
			
		} else {
			logError($qry . " - " . mysql_error());
		}
	}
	
	if ($found) {
		$line = array(
			"bookingid" => -1,
			"error" => $error
		);  
		
		
	} else {
		$qry = "SELECT A.*, B.bookingstart, B.bookingend " .
				"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
				"ON B.id = A.bookingid " .
				"WHERE B.id = $bookingid";
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
						"($createdby, NOW(), $memberid, $studioid, '$start', '$end', $origmemberid, $origstudioid, '$origstart', '$origend', 'U')";
				$itemresult = mysql_query($sql);
				
				if (! $itemresult) {
					logError($sql . " - " . mysql_error(), false);
				}
			}
			
		} else {
			logError($qry . " - " . mysql_error(), false);
		}
		
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET " .
				"bookingstart = '$start', " .
				"bookingend = '$end', " .
				"allday = '$alldayevent' " .
				"WHERE id = $bookingid";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error(), false);
		
			throw new Exception(mysql_error());
		}
		
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}engineercalendar SET " .
				"memberid = $memberid, " .
				"studioid = $studioid " .
				"WHERE id = $calendarid";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error(), false);
		
			throw new Exception(mysql_error());
		}
		
		$studioname = "";
		$engineername = "";
		
		$qry = "SELECT name " .
				"FROM {$_SESSION['DB_PREFIX']}studio " .
				"WHERE id = $studioid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$studioname = $member['name'];
			}
		}
		
		$qry = "SELECT firstname, lastname " .
				"FROM {$_SESSION['DB_PREFIX']}members " .
				"WHERE member_id = $memberid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$engineername = $member['firstname'] . " " . $member['lastname'];
			}
			
		} else {
			logError($qry . " - " . mysql_error());
		}
		
		$qry = "SELECT summary, unclink, notes " .
				"FROM {$_SESSION['DB_PREFIX']}booking " .
				"WHERE id = $bookingid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$unclink = $member['unclink'];
				$notes = $member['notes'];
				$summary = $member['summary'];
			}
		}
		
		$title = "<i>$engineername<i><hr><h3>" . ($summary) .  "</h3><a href='javascript: navigate(\\\"" . ($unclink) . "\\\", true)'>" . ($unclink) . "</a><br>" . ($notes);
		
		$line = array(
			"bookingid" => $bookingid,
			"studioname" => $studioname,
			"engineername" => $engineername,
			"summary" => $summary,
			"notes" => $notes,
			"unclink" => $unclink,
			"title" => $title
		);  
	}
	
	array_push($json, $line);
	
	echo json_encode($json); 
?>