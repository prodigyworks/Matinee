<?php 
	include("system-db.php");
	
	start_db();
	
	header('Content-Type: application/json');
	
	if (! isset($_POST['memberid'])) {
		$memberid = 220;
		$studioid = 0;
		$name = "Test";
		$startdate = "2014-11-01";
		$enddate = "2014-12-01";
				
	} else {
		$memberid = $_POST['memberid'];
		$studioid = $_POST['studioid'];
		$name = $_POST['name'];
		$startdate = $_POST['start'];
		$enddate = $_POST['end'];
	}
	
	
	if ($memberid != 0) {
		$qry = "SELECT A.id, A.memberid, A.studioid, B.id AS bookingid, B.bookingstart, B.bookingend, " .
				"B.summary, B.allday, B.unclink, B.notes, C.firstname, C.lastname, D.name " .
				"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
				"ON B.id = A.bookingid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
				"ON C.member_id = A.memberid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}studio D " .
				"ON D.id = A.studioid " .
				"WHERE A.memberid = $memberid " .
				"AND (B.bookingstart <= '$enddate' AND B.bookingend >= '$startdate') ";
						
	} else if ($studioid != 0) {
		$qry = "SELECT A.id, A.memberid, A.studioid, B.id AS bookingid, B.bookingstart, B.bookingend, " .
				"B.summary, B.allday, B.unclink, B.notes, C.firstname, C.lastname, D.name " .
				"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
				"ON B.id = A.bookingid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
				"ON C.member_id = A.memberid " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}studio D " .
				"ON D.id = A.studioid " .
				"WHERE A.studioid = $studioid " .
				"AND (B.bookingstart <= '$enddate' AND B.bookingend >= '$startdate') ";
	} else {
		$qry = "";
	}
	
	$json = array();

	if ($qry != "") {
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$line = array(
						"id"				=> $member['bookingid'], 
						"calendarid"		=> $member['id'],
						"calendarname"		=> $name,
						"studioname"		=> $member['name'],
						"engineername"		=> $member['firstname'] . " " . $member['lastname'],
						"studioid"			=> $member['studioid'],
						"memberid"			=> $member['memberid'],
						"bookingid"			=> $member['bookingid'],
						"alldayevent"		=> $member['allday'],
						"allDay"			=> "false",
						"editable"			=> "true",
						"unclink"			=> $member['unclink'],
						"notes"				=> $member['notes'],
						"summary"			=> $member['summary'],
						"className"			=> ($memberid == 0 ? "eventColor1" : "eventColor3"),
						"start"				=> $member['bookingstart'],
						"end"				=> $member['bookingend'],
						"startdate_half"	=> "N",
						"enddate_half"		=> "N",
						"title"				=> "<h3>" . $member['summary'] . "</h3><a onclick='enavigate(this, \\\"file:///" . str_replace("\\", "/", $member['unclink']) . "\\\", true)'>" . $member['unclink'] . "</a><br>" . $member['notes'] . "<hr><h3>Studio : " . $member['name'] . "</h3><i>" . $member['firstname'] . " " . $member['lastname']. "<i>"
				);
					
				array_push($json, $line);
			}
			
		} else {
			logError($qry . " - " . mysql_error());
		}
	}
	
	if ($memberid != 0) {
		$qry = "SELECT A.*, C.firstname, C.lastname  " .
				"FROM {$_SESSION['DB_PREFIX']}holiday A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
				"ON C.member_id = A.memberid " .
				"WHERE (A.startdate <= '$enddate' AND A.enddate >= '$startdate') " . 
				"AND A.memberid =  $memberid";

	} else if ($memberid == 0 && $studioid == 0) {
		$qry = "SELECT A.*, " .
				"C.firstname, C.lastname  " .
				"FROM {$_SESSION['DB_PREFIX']}holiday A " .
				"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
				"ON C.member_id = A.memberid " .
				"WHERE (A.startdate <= '$enddate' AND A.enddate >= '$startdate') ";

	} else {
		$qry = "";
	}
	
	if ($qry != "") {
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$line = array(
						"id"				=> "H" . $member['id'], 
						"holidayid"			=> $member['id'],
						"calendarid"		=> "0",
						"calendarname"		=> $name,
						"studioname"		=> "Holiday",
						"engineername"		=> $member['firstname'] . " " . $member['lastname'],
						"studioid"			=> "0",
						"memberid"			=> $member['memberid'],
						"bookingid"			=> "0",
						"alldayevent"		=> "N",
						"allDay"			=> "false",
						"editable"			=> "false",
						"unclink"			=> "",
						"notes"				=> "",
						"summary"			=> "Holiday",
						"className"			=> "eventColor2",
						"start"				=> $member['startdate'],
						"end"				=> $member['enddate'],
						"startdate_half"	=> ($member['startdate_half'] == 1) ? "Y" : "N",
						"enddate_half"		=> ($member['enddate_half'] == 1) ? "Y" : "N",
						"title"				=> "Holiday - " . $member['firstname'] . " " . $member['lastname']
				);
					
				array_push($json, $line);
			}
			
		} else {
			logError($qry . " - " . mysql_error());
		}
	}
	
	
	echo json_encode($json);
?>
