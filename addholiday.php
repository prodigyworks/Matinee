<?php
	require_once("system-db.php");
	
	start_db();
	
	$json = array();
	$createdby = getLoggedOnMemberID();
	
	$bookingstartdate = $_POST['bookingstartdate'];		
	$bookingenddate = $_POST['bookingenddate'];		
	$startdate_half = $_POST['startdate_half'];
	$enddate_half = $_POST['enddate_half'];
	$daystaken = $_POST['daystaken']; 
	
	if ($startdate_half == "N") {
		$bookingstarttime = "09:30";

	} else {
		$bookingstarttime = "13:30";
	}
	
	if ($enddate_half == "N") {
		$bookingendtime = "18:30";

	} else {
		$bookingendtime = "13:30";
	}
	
	$engineerid = $_POST['engineerid'];
	$holidayid = $_POST['holidayid'];
	$bookingstartdate = convertStringToDate($_POST['bookingstartdate']) . " " . $bookingstarttime . ":00";
	$bookingenddate = convertStringToDate($_POST['bookingenddate']) . " " . $bookingendtime . ":00";
	$error = "";
	
	if (strtotime($bookingenddate) < strtotime($bookingstartdate)) {
		$line = array(
				"bookingid" => -1,
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
			"OR   (bookingend > '$bookingstartdate' AND bookingend < '$bookingenddate')) ";
	
	$found = false;
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
		logError("XX ERROR 1:  " . mysql_error());
	}
	
	if (! $found) {
		if ($holidayid == 0) {
			$qry = "SELECT B.id, " .
					"DATE_FORMAT(B.startdate, '%e/%c/%Y %H:%i') AS bookingstart, " .
					"DATE_FORMAT(B.enddate, '%e/%c/%Y %H:%i') AS bookingend, " .
					"D.firstname, D.lastname " .
					"FROM {$_SESSION['DB_PREFIX']}holiday B " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
					"ON D.member_id = B.memberid " .
					"WHERE  B.memberid = $engineerid " .
					"AND ((startdate >= '$bookingstartdate' AND startdate < '$bookingenddate') " .
					"OR   (enddate > '$bookingstartdate' AND enddate < '$bookingenddate') " .
					"OR   (startdate <= '$bookingstartdate' AND enddate >= '$bookingenddate')) ";
			
			$result = mysql_query($qry);
			$found = false;
		
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
				logError("ERROR 2: " . $qry . " - " . mysql_error());
			}	
			
		} else {
			$qry = "SELECT B.id, " .
					"DATE_FORMAT(B.startdate, '%e/%c/%Y %H:%i') AS bookingstart, " .
					"DATE_FORMAT(B.enddate, '%e/%c/%Y %H:%i') AS bookingend, " .
					"D.firstname, D.lastname " .
					"FROM {$_SESSION['DB_PREFIX']}holiday B " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
					"ON D.member_id = B.memberid " .
					"WHERE B.id != $holidayid " .
					"AND B.memberid = $engineerid " .
					"AND ((B.startdate >= '$bookingstartdate' AND B.startdate < '$bookingenddate') " .
					"OR   (B.enddate > '$bookingstartdate' AND B.enddate < '$bookingenddate') " .
					"OR   (B.startdate <= '$bookingstartdate' AND B.enddate >= '$bookingenddate')) ";
			
			$result = mysql_query($qry);
			$found = false;
		
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
				logError("ERROR 4	: " . $qry . " - " . mysql_error());
			}
		}
	}
		
	if ($found) {
		$line = array(
			"bookingid" => -1,
			"error" => $error
		);  
		
	} else {
		if ($startdate_half == "Y") {
			$startdate_half = 1;
		} else {
			$startdate_half = 0;
		}
		
		if ($enddate_half == "Y") {
			$enddate_half = 1;
		} else {
			$enddate_half = 0;
		}
		
		if ($holidayid == 0) {
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}holiday " .
					"(startdate, memberid, enddate, startdate_half, enddate_half, daystaken) " .
					"VALUES " .
					"('$bookingstartdate', $engineerid, '$bookingenddate', '$startdate_half', '$enddate_half', $daystaken)";
				
		} else {
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}holiday SET " .
					"startdate = '$bookingstartdate', " .
					"enddate = '$bookingenddate', " .
					"startdate_half = '$startdate_half', " .
					"enddate_half = '$enddate_half', " .
					"memberid = '$engineerid', " .
					"daystaken = $daystaken " .
					"WHERE id = $holidayid";
			
			logError($sql, false);
		}
		
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error(), false);
		
			throw new Exception(mysql_error());
		}
		
		$line = array(
			"bookingid" => $holidayid, 
			"engineercalendarid" => $engineercalendarid,
			"engineerid" => $engineerid,
			"memberid" => $engineerid,
			"origstudioid" => $origstudioid,
			"origmemberid" => $origmemberid,
			"studioid" => $studioid,
			"studioname" => $studioname,
			"engineername" => $engineername,
			"calendarid" => $engineercalendarid,
			"alldayevent" => $alldayevent,
			"calendarname" => $calendarname,
			"title" => "<i>$engineername<i><hr><h3>$summary</h3><a href='javascript: navigate(\"$unclink\", true)'>$unclink</a><br>" . str_replace("\n", "<br>", $notes),
			"bookingstartdate" => date('D M d Y H:i:s', strtotime($bookingstartdate)),
			"bookingenddate" => date('D M d Y H:i:s', strtotime($bookingenddate)),
			"notes" => $notes,
			"unclink" => $unclink,
			"summary" => $summary
		);  
	}
	array_push($json, $line);

	echo json_encode($json); 
?>