<?php
	require_once("system-db.php");
	
	start_db();
	
	$json = array();
	$createdby = getLoggedOnMemberID();
	$bookingstarttime = $_POST['bookingstarttime'];
	$bookingendtime = $_POST['bookingendtime'];
	$bookingid = $_POST['bookingid'];
	$studioid = $_POST['studioid'];
	$engineerid = $_POST['engineerid'];
	$summary = ($_POST['summary']);
	$unclink = ($_POST['unclink']);
	$notes = $_POST['notes'];
	$calendarid = $_POST['calendarid'];
	$error = "";
	$calendarname = $_POST['calendarname'];
	$studioname = "";
	$alldayevent = ($_POST['alldayevent'] == "true" ? "Y" : "N");
	$engineername = GetUserName($engineerid);
	$origmemberid = null;
	$origstudioid = null;
	$origstart = null;
	$origend = null;
	
	if ($alldayevent == "Y") {
		$bookingstarttime = "09:30";
		$bookingendtime = "18:30";
	}
	
	$bookingstartdate = convertStringToDate($_POST['bookingstartdate']) . " " . $bookingstarttime . ":00";
	$bookingenddate = convertStringToDate($_POST['bookingenddate']) . " " . $bookingendtime . ":00";
	
	if (strtotime($bookingenddate) < strtotime($bookingstartdate)) {
		$line = array(
				"bookingid" => -1,
				"error" => "End date / time cannot precede the start date / time."
		);
		
		array_push($json, $line);
	
		echo json_encode($json); 	
		return;
	}

	$qry = "SELECT name " .
			"FROM {$_SESSION['DB_PREFIX']}studio A " .
			"WHERE A.id = $studioid";
	$result = mysql_query($qry);
	$found = false;

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$studioname = $member['name'];
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	if ($bookingid == 0) {
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
		
	} else {
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
				"WHERE B.id != $bookingid " .
				"AND A.memberid = $engineerid " .
				"AND ((bookingstart >= '$bookingstartdate' AND bookingstart < '$bookingenddate') " .
				"OR   (bookingend > '$bookingstartdate' AND bookingend < '$bookingenddate') " .
				"OR   (bookingstart <= '$bookingstartdate' AND bookingend >= '$bookingenddate')) ";
	}
	
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
		if ($bookingid == 0) {
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
					"AND ((bookingstart >= '$bookingstartdate' AND bookingstart < '$bookingenddate') " .
					"OR   (bookingend > '$bookingstartdate' AND bookingend < '$bookingenddate') " .
					"OR   (bookingstart <= '$bookingstartdate' AND bookingend >= '$bookingenddate')) ";
			
		} else {
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
					"WHERE B.id != $bookingid " .
					"AND   A.studioid = $studioid " .
					"AND ((bookingstart >= '$bookingstartdate' AND bookingstart < '$bookingenddate') " .
					"OR   (bookingend > '$bookingstartdate' AND bookingend < '$bookingenddate') " .
					"OR   (bookingstart <= '$bookingstartdate' AND bookingend >= '$bookingenddate')) ";
		}
		
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
				"WHERE A.memberid = $engineerid " .
				"AND ((A.startdate >= '$bookingstartdate' AND A.startdate < '$bookingenddate') " .
				"OR   (A.enddate > '$bookingstartdate' AND A.enddate < '$bookingenddate') " .
				"OR   (A.startdate <= '$bookingstartdate' AND A.enddate >= '$bookingenddate')) ";
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
		if ($bookingid == 0) {
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}booking " .
					"(createdby, createddate, bookingstart, bookingend, summary, unclink, notes, allday) " .
					"VALUES " .
					"($createdby, NOW(), '$bookingstartdate', '$bookingenddate', '" . mysql_escape_string($summary) .  "', '" . mysql_escape_string($unclink) .  "', '" . mysql_escape_string($notes) . "', '$alldayevent')";
				
		} else {
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET " .
					"bookingstart = '$bookingstartdate', " .
					"bookingend = '$bookingenddate', " .
					"summary = '" . mysql_escape_string($summary) . "', " .
					"allday = '$alldayevent', " .
					"unclink = '" . mysql_escape_string($unclink) . "', " .
					"notes = '" . mysql_escape_string($notes) . "' " .
					"WHERE id = $bookingid";
		}
		
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error(), false);
		
			throw new Exception(mysql_error());
		}
		
		if ($bookingid == 0) {
			$bookingid = mysql_insert_id();
			
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}engineercalendar " .
					"(memberid, studioid, bookingid) " .
					"VALUES " .
					"($engineerid, $studioid, $bookingid)";
			$result = mysql_query($sql);
			$engineercalendarid = mysql_insert_id();
			
			if (! $result) {
				logError($sql . " - " . mysql_error(), false);
		
				throw new Exception(mysql_error());
			}
		
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}auditlog " .
					"(createdby, createddate, originalmemberid, originalstudioid, originalstartdate, originalenddate, mode) " .
					"VALUES " .
					"($createdby, NOW(), $engineerid, $studioid, '$bookingstartdate', '$bookingenddate', 'I')";
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error(), false);
			
				throw new Exception(mysql_error());
			}
			
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
					$engineercalendarid = $member['id'];
					$origmemberid = $member['memberid'];
					$origstudioid = $member['studioid'];
					$origstart = $member['bookingstart'];
					$origend = $member['bookingend'];
					
					$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}auditlog " .
							"(createdby, createddate, modifiedmemberid, modifiedstudioid, modifiedstartdate, modifiedenddate, originalmemberid, originalstudioid, originalstartdate, originalenddate, mode) " .
							"VALUES " .
							"($createdby, NOW(), $createdby, $studioid, '$bookingstartdate', '$bookingenddate', $origmemberid, $origstudioid, '$origstart', '$origend', 'U')";
					$itemresult = mysql_query($sql);
					
					if (! $itemresult) {
						logError($sql . " - " . mysql_error(), false);
					
						throw new Exception(mysql_error());
					}
				}
				
			} else {
				logError($qry . " - " . mysql_error(), false);
			
				throw new Exception(mysql_error());
			}
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}engineercalendar SET " .
					"memberid = $engineerid, " .
					"studioid = $studioid " .
					"WHERE bookingid = $bookingid";
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error(), false);
			
				throw new Exception(mysql_error());
			}
					
		}
		
		$line = array(
			"bookingid" => $bookingid, 
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