<?php 
	include("system-header.php"); 
?>
<script>
var pauseEvent = false;
</script>
<?php 
	include("calendar.php"); 
?>
<link rel="stylesheet" href="css/fullcalendar.css" type="text/css" media="all" />
<script src='js/fullcalendar.js'></script>
<script src='js/jquery.ui.timepicker.js'></script>

<script>
	var currentEventID = 0;
	var currentMode = "agendaDay";
	
	function deleteEvent(id) {
		currentEventID = id;
		
		$("#confirmdialog").dialog("open");
	}
	
	function confirmDeleteEvent() {
		callAjax(
				"deletecalendarevent.php", 
				{ 
					id: currentEventID
				},
				function(data) {
					$('#calendar_member_' + $("#engineerid").val()).fullCalendar('removeEvents', currentEventID, true);
					$('#calendar_s_' + $("#studioid").val()).fullCalendar('removeEvents', currentEventID, true);
				},
				false,
				function(error) {
					alert(error);
				}
			);
			
		$("#confirmdialog").dialog("close");
		$("#studiobookingdialog").dialog("close");
		
		pauseEvent = false;
	}
	
	function convertTime(tm) {
		if (tm.indexOf(":") != -1) {
			var hour = parseInt(tm.substring(0, tm.indexOf(":")), 10);
			var min = tm.substring(tm.indexOf(":") + 1, tm.indexOf(":") + 3);
			
		} else {
			var hour = parseInt(tm.substring(0, tm.length - 2), 10);
			var min = "00";
		}
		
		if (tm.endsWith("pm") && hour < 12) {
			hour += 12;
		}
		
		return padZero(hour) + ":" + min;
	}


	function calculateDuration() {
		var startDateStr = $("#hol_bookingdate").val();
		var endDateStr = $("#hol_bookingenddate").val();
		
		var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
		var endDate = new Date(endDateStr.substring(6, 10), (parseFloat(endDateStr.substring(3, 5)) - 1), endDateStr.substring(0, 2));
		var days = workingDaysBetweenDates(startDate, endDate);
		
		if (days > 0) {
			if ($("#hol_startdate_half").attr("checked") == false) {
				if (startDate.getDay() > 0 && startDate.getDay() < 6) {
					days -= 0.5;
				}
			}
			
			if ($("#hol_enddate_half").attr("checked") == false) {
				if (endDate.getDay() > 0 && endDate.getDay() < 6) {
					days -= 0.5;
				}
			}
		}
		
		$("#daystaken").val(days);
	}
	
	function convertTimePlus1Hour(tm) {
		if (tm.indexOf(":") != -1) {
			var hour = parseInt(tm.substring(0, tm.indexOf(":")), 10);
			var min = tm.substring(tm.indexOf(":") + 1, tm.indexOf(":") + 3);
			
		} else {
			var hour = parseInt(tm.substring(0, tm.length - 2), 10);
			var min = "00";
		}
		
		if (tm.endsWith("pm") && hour < 12) {
			hour += 12;
		}
		
		if (hour < 23) {
			hour++;
		}
		
		return padZero(hour) + ":" + min;
	}
</script>
<ul id="contextmenu" class="modal">
	<li><a href="javascript: ">Remove</a></li>
	<li><a href="javascript: ">Edit</a></li>
</ul>
<div class="modal" id="generaldialog">
	<p>Test</p>
</div>
<div class="modal" id="studiobookingdialog">
	<table cellpadding=4 cellspacing=4 >
		<tr id="dialogchoice">
			<td>
				<label>Type&nbsp;&nbsp;</label>
				<SELECT id="dialogselect" name="dialogselect" onchange="showdialog()">
					<OPTION value="B">Booking</OPTION>
					<OPTION value="H">Holiday</OPTION>
				</SELECT>
				<br>
				<br>
				<hr />
				<br>
			</td>
		</tr>
		<tr id="dialogholiday">
			<td>
				<table border="0" cellspacing="4px" width='250px' style='table-layout: fixed;'>
				
					<tr>
						<td >Engineer</td>
						<td colspan="2">
							<?php createUserCombo("hol_engineerid", "WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER')"); ?>
						</td>
					</tr>
					<tr>
						<td >Start Date</td>
						<td>
							<input type="text" class="datepicker" id="hol_bookingdate" name="hol_bookingdate" onchange="calculateDuration()" />
						</td>
						<td>
							<span class='checkboxlabel'>Full Day</<span>
							<input class='checkboxtype' type="checkbox" id="hol_startdate_half" name="hol_startdate_half" onchange="calculateDuration()" />
						</td>
					</tr>
					<tr>
						<td>End Date</td>
						<td>
							<input type="text" class="datepicker" id="hol_bookingenddate" name="hol_bookingenddate" onchange="calculateDuration()" />
						</td>
						<td>
							<span class='checkboxlabel'>Full Day</span>
							<input class='checkboxtype'  type="checkbox" id="hol_enddate_half" name="hol_enddate_half" onchange="calculateDuration()" />
						</td>
					</tr>
					<tr>
						<td>Duration</td>
						<td colspan="2">
							<input type="text" id="daystaken" name="daystaken" size=3 />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id="bookingdialog">
			<td>
				<table border="0" cellspacing="4px" width='450px' style='table-layout: fixed;'>
					<tr>
						<td width='100px'><label>Engineer</label></td>
						<td><?php createUserCombo("engineerid", "WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER')"); ?></td>
					</tr>
					<tr>
						<td width='100px'><label>Studio</label></td>
						<td><?php createCombo("studioid", "id", "name", "{$_SESSION['DB_PREFIX']}studio"); ?></td>
					</tr>
					<tr>
						<td width='100px'><label>All Day Event</label></td>
						<td><input id="alldayevent" name="alldayevent" type="checkbox" /></td>
					</tr>
					<tr>
						<td width='100px'><label>Start Date</label></td>
						<td><input id="bookingdate" name="bookingdate" class="datepicker" /></td>
					</tr>
					<tr>
						<td width='100px'><label>Start Time</label></td>
						<td><input id="bookingstarttime" name="bookingstarttime" class="timepicker" /></td>
					</tr>
					<tr>
						<td width='100px'><label>End Date</label></td>
						<td><input id="bookingenddate" name="bookingenddate" class="datepicker" /></td>
					</tr>
					<tr>
						<td width='100px'><label>End Time</label></td>
						<td><input id="bookingendtime" name="bookingendtime" class="timepicker" /></td>
					</tr>
					<tr>
						<td width='100px'><label>Summary</label></td>
						<td><input id="summary" name="summary" class="textfield60" /></td>
					</tr>
					<tr>
						<td width='100px'><label>UNC Link</label></td>
						<td><input id="unclink" name="unclink" class="textfield90" /></td>
					</tr>
					<tr>
						<td width='100px'><label>Notes</label></td>
						<td><textarea id="notes" name="notes" cols=97 rows=6></textarea></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<input type="hidden" id="calendarid" name="calendarid" />
	<input type="hidden" id="calendarname" name="calendarname" />
	<input type="hidden" id="bookingid" name="bookingid" />
	<input type="hidden" id="hol_holidayid" name="bookingid" />
</div>

<div id="rightpanel">
	<h2 class="subtitle"><input type="checkbox" id="allengineers" />&nbsp;Engineers</h2>
<?php
	createConfirmDialog("confirmdialog", "Delete event ?", "confirmDeleteEvent");
	
	$qry = "SELECT DISTINCT A.member_id, A.firstname, A.lastname, B.checked " .
			"FROM {$_SESSION['DB_PREFIX']}members A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}membersession B " .
			"ON B.calendarid = CONCAT('calendar_member_', A.member_id) " .
			"AND B.memberid = " . getLoggedOnMemberID() . " " .
			"WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER') " .
			"ORDER BY A.firstname, A.lastname";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$checked = "checked";
				
			} else {
				$checked = "";
			}
?>
	<input class="chkbox chkengineers" type="checkbox" id="chk_calendar_member_<?php echo $member['member_id']; ?>" rel="calendar_member_<?php echo $member['member_id']; ?>" <?php echo $checked; ?> />
	<span><?php echo $member['firstname'] . " " . $member['lastname']; ?></span><br>
<?php
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	$qry = "SELECT checked " .
			"FROM {$_SESSION['DB_PREFIX']}membersession " .
			"WHERE calendarid = 'calendar_holiday'";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		$checked = "";
		
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$checked = "checked";
				
			} else {
				$checked = "";
			}
		}
?>
	<br>
	<h2 class="subtitle"><input type="checkbox" id="allaudio" />&nbsp;Audio Studio</h2>
<?php
	$qry = "SELECT DISTINCT A.id, A.name, B.checked " .
			"FROM {$_SESSION['DB_PREFIX']}studio A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}membersession B " .
			"ON B.calendarid = CONCAT('calendar_s_', A.id) " .
			"AND B.memberid = " . getLoggedOnMemberID() . " " .
			"WHERE A.type = 'A' " .
			"ORDER BY A.name";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$checked = "checked";
				
			} else {
				$checked = "";
			}
?>
	<input class="chkbox chkaudio" type="checkbox" id="chk_calendar_s_<?php echo $member['id']; ?>" rel="calendar_s_<?php echo $member['id']; ?>" <?php echo $checked; ?> />
	<span><?php echo $member['name']; ?></span><br>
<?php
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
?>
	<br>
	<h2 class="subtitle"><input type="checkbox" id="allvideo" />&nbsp;Workstations</h2>
<?php
	$qry = "SELECT DISTINCT A.id, A.name, B.checked " .
			"FROM {$_SESSION['DB_PREFIX']}studio A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}membersession B " .
			"ON B.calendarid = CONCAT('calendar_s_', A.id) " .
			"AND B.memberid = " . getLoggedOnMemberID() . " " .
			"WHERE A.type = 'V' " .
			"ORDER BY A.name";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$checked = "checked";
				
			} else {
				$checked = "";
			}
?>
	<input class="chkbox chkvideo" type="checkbox" id="chk_calendar_s_<?php echo $member['id']; ?>" rel="calendar_s_<?php echo $member['id']; ?>" <?php echo $checked; ?> />
	<span><?php echo $member['name']; ?></span><br>
<?php
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
?>
<br>
	<h2 class="subtitle"><input type="checkbox" id="allholidays" />&nbsp;Holidays</h2>
	<input class="chkbox chkholiday" type="checkbox" id="chk_holiday" rel="calendar_holiday" <?php echo $checked; ?> />
	<span>Holidays</span><br>
<?php
		
	} else {
		logError($qry . " - " . mysql_error());
	}
?>
	<br>
<hr />
<div id="globaldate">
	<button id="daymode">Day</button>
	<button id="weekmode">Week</button>
	<button id="monthmode">Month</button>
	
	<div class="datepicker" id="globaldatepicker"></div>
</div>
</div>

<div id="totalcontainer">
	<div id="hilton">
		<div class="studio">
			<div class="calendarcontainer holiday eventcolor2">
				<div class="closeable"><span>Holidays</span></div>
				<div class="closebutton" rel="calendar_holiday"></div>
				<div id='calendar_holiday' class="calendar holidaycalender"></div>
			</div>
<?php
	$qry = "SELECT A.member_id, A.firstname, A.lastname, B.checked " .
			"FROM {$_SESSION['DB_PREFIX']}members A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}membersession B " .
			"ON B.calendarid = CONCAT('calendar_member_', A.member_id) " .
			"AND B.memberid = " . getLoggedOnMemberID() . " " .
			"WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER') " .
			"ORDER BY A.firstname, A.lastname";
	$result = mysql_query($qry);
	$counter = 0;
	$holidaycounter = 1;

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$className = "";
				$counter++;
				
			} else {
				$className = "";
			}
?>
		<div class="calendarcontainer engineer <?php echo $className; ?>">
			<div class="closeable"><span><?php echo $member['firstname'] . " " . $member['lastname']; ?></span></div>
			<div class="closebutton" rel="calendar_member_<?php echo $member['member_id']; ?>"></div>
			<div id='calendar_member_<?php echo $member['member_id']; ?>' class="calendar engineercalender" memberid='<?php echo $member['member_id']; ?>'></div>
		</div>
<?php
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}

	$qry = "SELECT A.id, A.name, B.checked " .
			"FROM {$_SESSION['DB_PREFIX']}studio A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}membersession B " .
			"ON B.calendarid = CONCAT('calendar_s_', A.id) " .
			"AND B.memberid = " . getLoggedOnMemberID() . " " .
			"WHERE A.type = 'V' " .
			"ORDER BY A.name";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$className = "";
				$counter++;
				
			} else {
				$className = "";
			}
?>
		<div class="calendarcontainer video <?php echo $className; ?>">
			<div class="closeable"><span><?php echo $member['name']; ?></span></div>
			<div class="closebutton" rel="calendar_s_<?php echo $member['id']; ?>"></div>
			<div id='calendar_s_<?php echo $member['id']; ?>' studioid='<?php echo $member['id']; ?>' class="calendar studiocalender"></div>
		</div>
<?php
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	$qry = "SELECT A.id, A.name, B.checked " .
			"FROM {$_SESSION['DB_PREFIX']}studio A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}membersession B " .
			"ON B.calendarid = CONCAT('calendar_s_', A.id) " .
			"AND B.memberid = " . getLoggedOnMemberID() . " " .
			"WHERE A.type = 'A' " .
			"ORDER BY A.name";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($member['checked'] == null || $member['checked'] == 1) {
				$className = "";
				$counter++;
				
			} else {
				$className = "";
			}
?>
		<div class="calendarcontainer audio <?php echo $className; ?>">
			<div class="closeable"><span><?php echo $member['name']; ?></span></div>
			<div class="closebutton" rel="calendar_s_<?php echo $member['id']; ?>"></div>
			<div id='calendar_s_<?php echo $member['id']; ?>' studioid='<?php echo $member['id']; ?>' class="calendar studiocalender"></div>
		</div>
<?php
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}?>
		
		
		
		</div>
	</div>
</div>

<script>
	function showdialog() {
		if ($("#dialogselect").val() == "B") {
			$("#dialogholiday").hide();
			$("#bookingdialog").show();
			
		} else {
			$("#bookingdialog").hide();
			$("#dialogholiday").show();

			calculateDuration();
		}
	}

	function checkState(allTicker, tickerClass) {
		var state = true;
		
		$("." + tickerClass).each(
				function() {
					if (! $(this).attr("checked")) {
						state = false;
					}
					
				}
			);
		
		$("#" + allTicker).attr("checked", state);
	}
	
	function resize() {
		count = 0;
							
		$(".calendarcontainer").each(
				function() {
					if ( $(this).is(":visible") ) {
						count++;
					}
				}
			);
		
		$("#totalcontainer").css("width", ($("#page1").attr("offsetWidth") - 240) + "px");
		$("#hilton").css("width", (285* count) + "px");
	}
	
	function updateSession(calendarid, value) {
		callAjax(
				"updatesession.php", 
				{ 
					calendarid: calendarid,
					checked: value
				},
				function(data) {
//					$('#' + originalEventObject.calendar).fullCalendar('removeEvents', originalEventObject.id, true);
					
				},
				false,
				function(error) {
				}
			);
	}
	
	$(document).ready(
			function() {
				$("#alldayevent").change(
						function() {
							if ($(this).attr("checked")) {
								$("#bookingstarttime").attr("disabled", true);
								$("#bookingendtime").attr("disabled", true);
								$("#bookingstarttime").val("09:30");
								$("#bookingendtime").val("18:30");
								
							} else {
								$("#bookingstarttime").attr("disabled", false);
								$("#bookingendtime").attr("disabled", false);
							}
						}
					);
					
				$("#confirmdialog .confirmdialogbody").html("You are about to remove this event.<br>Are you sure ?");

				checkState("allvideo", "chkvideo");
				checkState("allholidays", "chkholiday");
				checkState("allengineers", "chkengineers");
				checkState("allaudio", "chkaudio");

				$(".chkvideo").change(
						function() {
							checkState("allvideo", "chkvideo");
						}
					);

				$(".chkholiday").change(
						function() {
							checkState("allholidays", "chkholiday");
						}
					);

				$(".chkengineers").change(
						function() {
							checkState("allengineers", "chkengineers");
						}
					);

				$(".chkaudio").change(
						function() {
							checkState("allaudio", "chkaudio");
						}
					);
				
				$("#allaudio").change(
						function() {
							$(".chkaudio").attr("checked", $(this).attr("checked")).trigger("change");
						}
					);
				
				$("#allvideo").change(
						function() {
							$(".chkvideo").attr("checked", $(this).attr("checked")).trigger("change");
						}
					);
				
				$("#allengineers").change(
						function() {
							$(".chkengineers").attr("checked", $(this).attr("checked")).trigger("change");
						}
					);

				
				$("#allholidays").change(
						function() {
							$(".chkholiday").attr("checked", $(this).attr("checked")).trigger("change");
						}
					);
				
				$("#globaldatepicker").change(
						function() {
							document.body.style.cursor='wait';
							
							var dp = $(this);
							
							var day = parseInt(dp.val().substr(0, 2), 10); 
							var month = parseInt(dp.val().substr(3, 2), 10) - 1; 
							var year = parseInt(dp.val().substr(6, 4), 10); 

							$(".calendar").fullCalendar( 'gotoDate', year, padZero(month), padZero(day));
							
						}
					);
					
				$(".chkbox").change(
						function() {
							if ($(this).attr("checked")) {
								var dp = $("#globaldatepicker");
								var day = parseInt(dp.val().substr(0, 2), 10); 
								var month = parseInt(dp.val().substr(3, 2), 10) - 1; 
								var year = parseInt(dp.val().substr(6, 4), 10); 
	
								$("#" + $(this).attr("rel")).parent().show();
								$("#" + $(this).attr("rel")).fullCalendar( 'gotoDate', year, padZero(month), padZero(day));
								$("#" + $(this).attr("rel")).fullCalendar( 'changeView', currentMode );

								updateSession($(this).attr("rel"), 1);
								
							} else {
								$("#" + $(this).attr("rel")).parent().hide();
								updateSession($(this).attr("rel"), 0);
							}
							
							resize();	
						}
					);
					
				$("#generaldialog").dialog({
						modal: true,
						autoOpen: false,
						show:"fade",
						hide:"fade",
						title:"Alert",
						width: 550,
						open: function(event, ui){
							
						},
						buttons: {
							Ok: function() {
								$(this).dialog("close");
							}
						}
					});
					
				$("#studiobookingdialog").dialog({
						modal: true,
						autoOpen: false,
						show:"fade",
						hide:"fade",
						closeOnEscape: false,
						width: 730,
						title:"Studio Booking",
						open: function(event, ui){
							
						},
						buttons: {
							"Save": function() {
								if ($("#dialogselect").val() == "H") {
									callAjax(
											"addholiday.php", 
											{ 
												bookingstartdate: $("#hol_bookingdate").val(),
												enddate_half: ($("#hol_enddate_half").is(':checked') == false) ? "Y" : "N",
												bookingenddate: $("#hol_bookingenddate").val(),
												startdate_half: ($("#hol_startdate_half").is(':checked') == false) ? "Y" : "N",
												engineerid: $("#hol_engineerid").val(),
												daystaken: $("#daystaken").val(),
												holidayid: $("#hol_holidayid").val()
											},
											function(data) {
												var originalEventObject = data[0];
												
												if (originalEventObject.bookingid == -1) {
													$("#generaldialog p").text(originalEventObject.error);
													$("#generaldialog").dialog("open");

													return;
												}
												
												$("#studiobookingdialog").dialog("close");
	 											$('#calendar_holiday').fullCalendar('refetchEvents');
	 											$('#calendar_member_' + originalEventObject.engineerid).fullCalendar('refetchEvents');

	 											pauseEvent = false;
											},
											false,
											function(jqXHR, textStatus, errorThrown) {
												alert("ERROR:" + errorThrown);
											}
										);	
																	
								} else {
									callAjax(
											"addbooking.php", 
											{ 
												bookingstartdate: $("#bookingdate").val(),
												bookingstarttime: $("#bookingstarttime").val(),
												bookingenddate: $("#bookingenddate").val(),
												bookingendtime: $("#bookingendtime").val(),
												studioid: $("#studioid").val(),
												engineerid: $("#engineerid").val(),
												summary: $("#summary").val(),
												unclink: $("#unclink").val(),
												notes: $("#notes").val(),
												calendarname: $("#calendarname").val(),
												calendarid: $("#calendarid").val(),
												bookingid : $("#bookingid").val(),
												alldayevent: $("#alldayevent").attr("checked")
											},
											function(data) {
												var originalEventObject = data[0];
												
												if (originalEventObject.bookingid == -1) {
													$("#generaldialog p").text(originalEventObject.error);
													$("#generaldialog").dialog("open");

													return;
												}
												
												$("#studiobookingdialog").dialog("close");
												
	 											$('#calendar_s_' + originalEventObject.studioid).fullCalendar('refetchEvents');
	 											$('#calendar_member_' + originalEventObject.engineerid).fullCalendar('refetchEvents');

	 											pauseEvent = false;
											},
											false,
											function(jqXHR, textStatus, errorThrown) {
												alert("ERROR:" + errorThrown);
											}
										);
								}
								
							},
							"Remove": function() {
								if ($("#dialogselect").val() == "H") {
									deleteHoliday($("#hol_holidayid").val());
									
								} else {
									deleteEvent($("#bookingid").val());
								}
							},
							Cancel: function() {
								$(this).dialog("close");
								
								pauseEvent = false;
							}
						}
					});
					
				
					
				$("#monthmode").click(
						function() {
							currentMode = "month";
							
							$(".calendar").each(
									function() {
										var chkbox = $("#chk_" + $(this).attr("id"));
										
										if (chkbox.attr("checked")) {
											$(this).fullCalendar( 'changeView', "month" );
										}
									}
								);
						}
					);
					
				$("#weekmode").click(
						function() {
							currentMode = "agendaWeek";
							
							$(".calendar").each(
									function() {
										var chkbox = $("#chk_" + $(this).attr("id"));
										
										if (chkbox.attr("checked")) {
											$(this).fullCalendar( 'changeView', "agendaWeek" );
										}
									}
								);
								
							$(".calendar .fc-view > div > div").scroll(
									function() {
										var thisID = $(this);
										var scrollTop = $(this).scrollTop();
										
										$(".calendar .fc-view > div > div").each(
												function() {
													if ($(this) != thisID) {
														$(this).scrollTop(scrollTop);
													}
												}
											);
									}
								);
						}
					);
					
				$("#daymode").click(
						function() {
							currentMode = "agendaDay";
							
							$(".calendar").each(
									function() {
										var chkbox = $("#chk_" + $(this).attr("id"));
										
										if (chkbox.attr("checked")) {
											$(this).fullCalendar( 'changeView', "agendaDay" );
										}
									}
								);
							
						}
					);
				
				$(".closebutton").click(
						function() {
							$(this).parent().hide();
							
							var chkbox = $("#chk_" + $(this).attr("rel"));
							
							chkbox.attr("checked", false);
							
							updateSession($(this).attr("rel"), 0);
							
							resize();
						}
					);
<?php
						showCalendar("calendar_holiday", 0, 0, 0);

				$qry = "SELECT A.member_id, A.firstname, A.lastname " .
						"FROM {$_SESSION['DB_PREFIX']}members A " .
						"WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER') " .
						"ORDER BY A.firstname, A.lastname";
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						showCalendar("calendar_member_" . $member['member_id'], $member['member_id'], 0);
					}
		
				} else {
					logError($qry . " - " . mysql_error());
				}
				
				$qry = "SELECT id, name " .
						"FROM {$_SESSION['DB_PREFIX']}studio " .
						"WHERE type = 'V' " .
						"ORDER BY name";
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						showCalendar("calendar_s_" . $member['id'], 0, $member['id']);
					}
					
				} else {
					logError($qry . " - " . mysql_error());
				}
				
				$qry = "SELECT id, name " .
						"FROM {$_SESSION['DB_PREFIX']}studio " .
						"WHERE type = 'A' " .
						"ORDER BY name";
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						showCalendar("calendar_s_" . $member['id'], 0, $member['id']);
					}
					
				} else {
					logError($qry . " - " . mysql_error());
				}
			
				$qry = "SELECT B.calendarid, B.checked " .
						"FROM {$_SESSION['DB_PREFIX']}membersession B " .
						"WHERE B.memberid = " . getLoggedOnMemberID();
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($member['checked'] == 0) {
?>
							$("#<?php echo $member['calendarid']; ?>").parent().hide();
<?php
				
						}
					}
				}
?>
				$(".calendar .fc-view > div > div").scroll(
						function() {
							var thisID = $(this);
							var scrollTop = $(this).scrollTop();
							
							$(".calendar .fc-view > div > div").each(
									function() {
										if ($(this) != thisID) {
											$(this).scrollTop(scrollTop);
										}
									}
								);
						}
					);
				$("#hilton").css("width", (285*(<?php echo  $counter + $holidaycounter; ?>)) + "px");
				$("#totalcontainer").css("width", ($("#page1").attr("offsetWidth") - 240) + "px");
				$("#totalcontainer").css("background", "none");
				$("#hilton").css("visibility", "visible");

				$(".fc-button-next, .fc-button-prev, .fc-button-today").click(
						function() {
							var me = $(this).closest(".calendar");
							var moment = me.fullCalendar('getDate');

							$(".calendar").not(me).fullCalendar( 'gotoDate', moment.getFullYear(), padZero(moment.getMonth()), padZero(moment.getDate()));
						}
					);
				
				setTimeout(
						function() {
							$(".calendar .fc-view > div > div").scrollTop(432);
						},
						2000
					);
			}
		);


	function showBookings() {
		if (! pauseEvent) {
			
<?php
				$qry = "SELECT A.member_id, A.firstname, A.lastname " .
						"FROM {$_SESSION['DB_PREFIX']}members A " .
						"WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER') " .
						"ORDER BY A.firstname, A.lastname";
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
?>
						$("#calendar_member_<?php echo $member['member_id']; ?>").fullCalendar("refetchEvents");
<?php
					}
		
				} else {
					logError($qry . " - " . mysql_error());
				}
				
				$qry = "SELECT id, name " .
						"FROM {$_SESSION['DB_PREFIX']}studio " .
						"WHERE type = 'V' " .
						"ORDER BY name";
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
?>
						$("#calendar_s_<?php echo $member['id']; ?>").fullCalendar("refetchEvents");
<?php
					}
					
				} else {
					logError($qry . " - " . mysql_error());
				}
				
				$qry = "SELECT id, name " .
						"FROM {$_SESSION['DB_PREFIX']}studio " .
						"WHERE type = 'A' " .
						"ORDER BY name";
				$result = mysql_query($qry);
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
?>
						$("#calendar_s_<?php echo $member['id']; ?>").fullCalendar("refetchEvents");
<?php
					}
					
				} else {
					logError($qry . " - " . mysql_error());
				}
?>
		}
		
		setTimeout(showBookings, 10000);
	}

	setTimeout(showBookings, 10000);
		
</script>

<?php include("system-footer.php"); ?>
