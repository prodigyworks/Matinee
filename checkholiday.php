<?php
	require_once("system-db.php");
	
	start_db();
	
	$json = array();
	$createdby = getLoggedOnMemberID();
	$id = $_POST['id'];
	$bookingstarttime = $_POST['bookingstartdate'];
	$bookingendtime = $_POST['bookingenddate'];
	$engineerid = $_POST['memberid'];
	$startfullday = (isset($_POST['startfullday']) && $_POST['startfullday'] == "on" ? "Y" : "N");
	$endfullday = (isset($_POST['endfullday']) && $_POST['endfullday'] == "on" ? "Y" : "N");
	$engineername = GetUserName($engineerid);
	$origmemberid = null;
	$origstudioid = null;
	$origstart = null;
	$origend = null;
	
	if ($startfullday == "Y") {
		$bookingstarttime = "09:30";
		
	} else {
		$bookingstarttime = "13:30";
	}
	
	if ($endfullday == "Y") {
		$bookingendtime = "18:30";
		
	} else {
		$bookingendtime = "13:30";
	}
	
	$bookingstartdate = convertStringToDate($_POST['bookingstartdate']) . " " . $bookingstarttime . ":00";
	$bookingenddate = convertStringToDate($_POST['bookingenddate']) . " " . $bookingendtime . ":00";
	
	if (strtotime($bookingenddate) < strtotime($bookingstartdate)) {
		$line = array(
				"error" => "End date / time cannot precede the start date / time."
			);
		
		array_push($json, $line);
	
		echo json_encode($json); 	
		return;
	}
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
			"WHERE A.memberid = $engineerid " .
			"AND ((bookingstart >= '$bookingstartdate' AND bookingstart < '$bookingenddate') " .
			"OR   (bookingend > '$bookingstartdate' AND bookingend < '$bookingenddate') " .
			"OR   (bookingstart <= '$bookingstartdate' AND bookingend >= '$bookingenddate')) ";
	
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
		if ($id != "") {
			$qry = "SELECT A.id, " .
					"DATE_FORMAT(A.startdate, '%e/%c/%Y %H:%i') AS bookingstart, " .
					"DATE_FORMAT(A.enddate, '%e/%c/%Y %H:%i') AS bookingend, " .
					"D.firstname, D.lastname " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
					"ON D.member_id = A.memberid " .
					"WHERE A.memberid = $engineerid " .
					"And   A.id != $id " .
					"AND ((A.startdate >= '$bookingstartdate' AND A.startdate < '$bookingenddate') " .
					"OR   (A.enddate > '$bookingstartdate' AND A.enddate < '$bookingenddate') " .
					"OR   (A.startdate <= '$bookingstartdate' AND A.enddate >= '$bookingenddate')) ";
			
		} else {
			$qry = "SELECT A.id, " .
					"DATE_FORMAT(A.startdate, '%e/%c/%Y %H:%i') AS bookingstart, " .
					"DATE_FORMAT(A.enddate, '%e/%c/%Y %H:%i') AS bookingend, " .
					"D.firstname, D.lastname " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
					"ON D.member_id = A.memberid " .
					"WHERE A.memberid = $engineerid " .
					"AND ((A.startdate >= '$bookingstartdate' AND A.startdate < '$bookingenddate') " .
					"OR   (A.enddate > '$bookingstartdate' AND A.enddate < '$bookingenddate') " .
					"OR   (A.startdate <= '$bookingstartdate' AND A.enddate >= '$bookingenddate')) ";
		}
		
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
		$line = array(
				"bookingstartdate" => $bookingstartdate
			);  
	}

	array_push($json, $line);

	echo json_encode($json); 
?>