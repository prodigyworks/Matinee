<?php
	require_once("system-db.php");
	
	start_db();
	
	$bookingid = $_POST['bookingid'];
	$difference = $_POST['difference'];
	$json = array();
	$error = "";
	
	$qry = "SELECT AA.memberid, AA.studioid, " .
			"DATE_FORMAT(B.bookingstart, '%e/%c/%Y %H:%i') AS bookingstart, " .
			"DATE_FORMAT(B.bookingend, '%e/%c/%Y %H:%i') AS bookingend, " .
			"C.name, D.firstname, D.lastname " .
			"FROM {$_SESSION['DB_PREFIX']}booking A " .
			"INNER JOIN  {$_SESSION['DB_PREFIX']}engineercalendar AA " .
			"ON AA.bookingid = A.id " .
			"INNER JOIN  {$_SESSION['DB_PREFIX']}engineercalendar BA " .
			"ON BA.memberid = AA.memberid " .
			"INNER JOIN  {$_SESSION['DB_PREFIX']}booking B " .
			"ON B.id = BA.bookingid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}studio C " .
			"ON C.id = BA.studioid " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
			"ON D.member_id = BA.memberid " .
			"WHERE A.id = $bookingid " .
			"AND B.id != $bookingid " .
			"AND ((B.bookingstart >= A.bookingstart AND B.bookingstart < DATE_ADD(A.bookingend, INTERVAL $difference MINUTE)) " .
			"OR   (B.bookingend > A.bookingstart AND B.bookingend < DATE_ADD(A.bookingend, INTERVAL $difference MINUTE))  " .
			"OR   (B.bookingstart <= A.bookingstart AND B.bookingend >= DATE_ADD(A.bookingend, INTERVAL $difference MINUTE))) ";
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
		$qry = "SELECT AA.memberid, AA.studioid, " .
				"DATE_FORMAT(B.bookingstart, '%e/%c/%Y %H:%i') AS bookingstart, " .
				"DATE_FORMAT(B.bookingend, '%e/%c/%Y %H:%i') AS bookingend, " .
				"C.name, D.firstname, D.lastname " .
				"FROM {$_SESSION['DB_PREFIX']}booking A " .
				"INNER JOIN  {$_SESSION['DB_PREFIX']}engineercalendar AA " .
				"ON AA.bookingid = A.id " .
				"INNER JOIN  {$_SESSION['DB_PREFIX']}engineercalendar BA " .
				"ON BA.studioid = AA.studioid " .
				"INNER JOIN  {$_SESSION['DB_PREFIX']}booking B " .
				"ON B.id = BA.bookingid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}studio C " .
				"ON C.id = BA.studioid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
				"ON D.member_id = BA.memberid " .
				"WHERE A.id = $bookingid " .
				"AND B.id != $bookingid " .
				"AND ((B.bookingstart >= A.bookingstart AND B.bookingstart < DATE_ADD(A.bookingend, INTERVAL $difference MINUTE)) " .
				"OR   (B.bookingend > A.bookingstart AND B.bookingend < DATE_ADD(A.bookingend, INTERVAL $difference MINUTE))  " .
				"OR   (B.bookingstart <= A.bookingstart AND B.bookingend > DATE_ADD(A.bookingend, INTERVAL $difference MINUTE))) ";
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
		$qry = "SELECT B.id, " .
				"DATE_FORMAT(B.startdate, '%e/%c/%Y %H:%i') AS bookingstart, " .
				"DATE_FORMAT(B.enddate, '%e/%c/%Y %H:%i') AS bookingend, " .
				"D.firstname, D.lastname " .
				"FROM {$_SESSION['DB_PREFIX']}booking A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}engineercalendar C " .
				"ON C.bookingid = A.id " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}holiday B " .
				"ON C.memberid = B.memberid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members D " .
				"ON D.member_id = B.memberid " .
				"WHERE A.id = $bookingid " .
				"AND ((B.startdate >= A.bookingstart AND B.startdate < DATE_ADD(A.bookingend, INTERVAL $difference MINUTE)) " .
				"OR   (B.enddate > A.bookingstart AND B.enddate < DATE_ADD(A.bookingend, INTERVAL $difference MINUTE))  " .
				"OR   (B.startdate <= A.bookingstart AND B.enddate > DATE_ADD(A.bookingend, INTERVAL $difference MINUTE))) ";
		$result = mysql_query($qry);
	
		logError($qry, false);
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
		$qry = "SELECT A.*, B.bookingstart, B.bookingend, " .
				"DATE_FORMAT(DATE_ADD(B.bookingend, INTERVAL $difference MINUTE), '%Y-%c-%e %H:%i') AS origbookingend " .
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
				$end = $member['origbookingend'];
				$createdby = getLoggedOnMemberID();
				
				$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}auditlog " .
						"(createdby, createddate, modifiedmemberid, modifiedstudioid, modifiedstartdate, modifiedenddate, originalmemberid, originalstudioid, originalstartdate, originalenddate, mode) " .
						"VALUES " .
						"($createdby, NOW(), $origmemberid, $origstudioid, '$origstart', '$end', $origmemberid, $origstudioid, '$origstart', '$origend', 'U')";
				$itemresult = mysql_query($sql);
				
				if (! $itemresult) {
					logError($sql . " - " . mysql_error(), false);
				}
			}
			
		} else {
			logError($qry . " - " . mysql_error(), false);
		}
		
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking " .
				"SET bookingend = DATE_ADD(bookingend, INTERVAL $difference MINUTE) " .
				"WHERE id = $bookingid";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error(), false);
		
			throw new Exception(mysql_error());
		}
		$line = array(
			"bookingid" => $bookingid
		);  
	}	
	
	
	array_push($json, $line);

	echo json_encode($json); 
?>