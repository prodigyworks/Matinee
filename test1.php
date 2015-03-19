<?php 
	include("system-header.php"); 
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
	}
	
	function convertTime(tm) {
		if (tm.indexOf(":") != -1) {
			var hour = parseInt(tm.substring(0, tm.indexOf(":")));
			var min = tm.substring(tm.indexOf(":") + 1, tm.indexOf(":") + 3);
			
		} else {
			var hour = parseInt(tm.substring(0, tm.length - 2));
			var min = "00";
		}
		
		if (tm.endsWith("pm") && hour < 12) {
			hour += 12;
		}
		
		return padZero(hour) + ":" + min;
	}
	
	function convertTimePlus1Hour(tm) {
		if (tm.indexOf(":") != -1) {
			var hour = parseInt(tm.substring(0, tm.indexOf(":")));
			var min = tm.substring(tm.indexOf(":") + 1, tm.indexOf(":") + 3);
			
		} else {
			var hour = parseInt(tm.substring(0, tm.length - 2));
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
		<tr>
			<td><label>Engineer</label></td>
			<td><?php createUserCombo("engineerid", "WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER')"); ?></td>
		</tr>
		<tr>
			<td><label>Studio</label></td>
			<td><?php createCombo("studioid", "id", "name", "{$_SESSION['DB_PREFIX']}studio"); ?></td>
		</tr>
		<tr>
			<td><label>All Day Event</label></td>
			<td><input id="alldayevent" name="alldayevent" type="checkbox" /></td>
		</tr>
		<tr>
			<td><label>Start Date</label></td>
			<td><input id="bookingdate" name="bookingdate" class="datepicker" /></td>
		</tr>
		<tr>
			<td><label>Start Time</label></td>
			<td><input id="bookingstarttime" name="bookingstarttime" class="timepicker" /></td>
		</tr>
		<tr>
			<td><label>End Date</label></td>
			<td><input id="bookingenddate" name="bookingenddate" class="datepicker" /></td>
		</tr>
		<tr>
			<td><label>End Time</label></td>
			<td><input id="bookingendtime" name="bookingendtime" class="timepicker" /></td>
		</tr>
		<tr>
			<td><label>Summary</label></td>
			<td><input id="summary" name="summary" class="textfield60" /></td>
		</tr>
		<tr>
			<td><label>UNC Link</label></td>
			<td><input id="unclink" name="unclink" class="textfield90" /></td>
		</tr>
		<tr>
			<td><label>Notes</label></td>
			<td><textarea id="notes" name="notes" cols=97 rows=6></textarea></td>
		</tr>
	</table>
	<input type="hidden" id="calendarid" name="calendarid" />
	<input type="hidden" id="calendarname" name="calendarname" />
	<input type="hidden" id="bookingid" name="bookingid" />
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
?>
	<br>
	<br>
	<h2 class="subtitle"><input type="checkbox" id="allaudio" />&nbsp;Studios</h2>
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
								$("#bookingstarttime").val("09:00");
								$("#bookingendtime").val("19:00");
								
							} else {
								$("#bookingstarttime").attr("disabled", false);
								$("#bookingendtime").attr("disabled", false);
							}
						}
					);
					
				var state = true;
				
				$("#confirmdialog .confirmdialogbody").html("You are about to remove this event.<br>Are you sure ?");
				
				$(".chkvideo").each(
						function() {
							if (! $(this).attr("checked")) {
								state = false;
							}
						}
					);
							
				$("#allvideo").attr("checked", state);
				state = true;
				
				$(".chkengineers").each(
						function() {
							if (! $(this).attr("checked")) {
								state = false;
							}
							
						}
					);
					
				$("#allengineers").attr("checked", state);
				state = true;
				
				$(".chkaudio").each(
						function() {
							if (! $(this).attr("checked")) {
								state = false;
							}
						}
					);
				
				$("#allaudio").attr("checked", state);
							
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
				
				$("#globaldatepicker").change(
						function() {
							document.body.style.cursor='wait';
							
							var dp = $(this);
							
							setTimeout(
									function() {
										var day = parseInt(dp.val().substr(0, 2)); 
										var month = parseInt(dp.val().substr(3, 2)) - 1; 
										var year = parseInt(dp.val().substr(6, 4)); 
			
										$(".calendar").fullCalendar( 'gotoDate', year, month, day);
										
//										document.body.style.cursor='default';
									}, 0
								);
							
						}
					);
					
				$(".chkbox").change(
						function() {
							if ($(this).attr("checked")) {
								var dp = $("#globaldatepicker");
								var day = parseInt(dp.val().substr(0, 2)); 
								var month = parseInt(dp.val().substr(3, 2)) - 1; 
								var year = parseInt(dp.val().substr(6, 4)); 
	
								$("#" + $(this).attr("rel")).parent().show();
								$("#" + $(this).attr("rel")).fullCalendar( 'gotoDate', year, month, day);
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
						width: 680,
						title:"Studio Booking",
						open: function(event, ui){
							
						},
						buttons: {
							"Save": function() {
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
											
											if ($("#bookingid").val() != 0) {
												$('#calendar_member_' + originalEventObject.origmemberid).fullCalendar('removeEvents', $("#bookingid").val(), true);
												$('#calendar_s_' + originalEventObject.origstudioid).fullCalendar('removeEvents', $("#bookingid").val(), true);
											}
											
											var myEvent = {
													  id: originalEventObject.bookingid,
													  title: originalEventObject.title,
													  allDay: false,
													  bookingid: originalEventObject.bookingid,
													  calendarid: originalEventObject.calendarid,
													  calendarname: originalEventObject.calendarname,
													  engineername: originalEventObject.engineername,
													  studioname: originalEventObject.studioname,
													  unclink: originalEventObject.unclink,
													  alldayevent: originalEventObject.alldayevent,
													  notes: originalEventObject.notes,
													  summary: originalEventObject.summary,
													  studioid: originalEventObject.studioid,
													  memberid: originalEventObject.engineerid,
													  start: new Date(originalEventObject.bookingstartdate),
													  end: new Date(originalEventObject.bookingenddate),
													  editable: true,
													  className: "eventColor3"
													};
												
											$('#calendar_member_' + originalEventObject.engineerid).fullCalendar('renderEvent', myEvent, true);

											var myEvent = {
													  id: originalEventObject.bookingid,
													  title: originalEventObject.title,
													  allDay: false,
													  bookingid: originalEventObject.bookingid,
													  calendarid: originalEventObject.calendarid,
													  calendarname: originalEventObject.calendarname,
													  engineername: originalEventObject.engineername,
													  studioname: originalEventObject.studioname,
													  unclink: originalEventObject.unclink,
													  notes: originalEventObject.notes,
													  alldayevent: originalEventObject.alldayevent,
													  summary: originalEventObject.summary,
													  studioid: originalEventObject.studioid,
													  memberid: originalEventObject.engineerid,
													  start: new Date(originalEventObject.bookingstartdate),
													  end: new Date(originalEventObject.bookingenddate),
													  editable: true,
													  className: "eventColor1"
													};
											$('#calendar_s_' + originalEventObject.studioid).fullCalendar('renderEvent', myEvent, true);
										},
										false,
										function(jqXHR, textStatus, errorThrown) {
											alert("ERROR:" + errorThrown);
										}
									);
								
								$(this).dialog("close");
							},
							"Remove": function() {
								deleteEvent($("#bookingid").val());
							},
							Cancel: function() {
								$(this).dialog("close");
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
				$("#hilton").css("width", (285*<?php echo  $counter; ?>) + "px");
				$("#totalcontainer").css("width", ($("#page1").attr("offsetWidth") - 240) + "px");
				$("#totalcontainer").css("background", "none");
				$("#hilton").css("visibility", "visible");
			}
		);
</script>

<?php include("system-footer.php"); ?>
