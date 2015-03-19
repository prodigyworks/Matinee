<?php
	function showCalendar($name, $memberid, $studioid) {
?>
		$('#<?php echo $name; ?>').fullCalendar({
			<?php
			if (isUserInRole("PM") || isUserInRole("ADMIN")) {
			?>
			editable: true,
			<?php
			} else {
			?>
			editable: false,
			disableResizing: true,
			disableDragging: true,
			<?php
			}
			?>
			aspectRatio: 0.15,
			allDaySlot: false,
			allDayDefault: false, 
			firstHour: 9,
			weekends: false,
			titleFormat: 
			{
				 month: 'MMMM yyyy',                             // September 2009
  				 week: "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}", // Sep 7 - 13 2009
    			 day: 'dddd, MMM d, yyyy'                  // Tuesday, Sep 8, 2009
				
			},
			columnFormat: {
				month: 'ddd',
				week: 'ddd d/M',
				day: 'dddd d/M'
			},
			
			header: {
				left: 'prev,next today',
				center: 'title',
				right: ''
			},

			eventAfterAllRender: function() {
				document.body.style.cursor='default';
			},
 
			eventRender: function(event, element) {
			    element.attr('title', "Click to view");
			    element.find(".fc-event-title").text();
			    element.find(".fc-event-title").html("<h3>Studio : " + event.studioname + "</h3>" + event.title);
			    
			   	element.find("div").data(
			   			'eventObject', 
			   			{
			   				title: event.title, 
			   				id: event.id, 
			   				start: event.start, 
			   				end: event.end, 
			   				alldayevent: event.alldayevent,
			   				className: event.className, 
			   				studioid: event.studioid,
			   				studioname: event.studioname,
			   				engineername: event.engineername,
			   				memberid: event.memberid,
			   				bookingid: event.bookingid,
			   				calendarid: event.calendarid, 
			   				calendar: '<?php echo $name; ?>'
			   			}
			   		);

				<?php 
				if (isUserInRole("PM") || isUserInRole("ADMIN")) {
				?>
				element.find(".fc-event-inner").addClass("draggable2");

	           	element.find(".fc-event-inner").draggable({
	               helper:'clone',
	               appendTo: 'body',
	               containment: 'DOM',
	               zIndex: 1500,
	               addClasses: false,
	          		           	
	        	    start: function(event, ui) {
	                	$(ui.helper).addClass('dragging');
	            	}
	    		});
	    		<?php
				}
				?>
            },
            <?php
			if (isUserInRole("PM") || isUserInRole("ADMIN")) {
	            if ($memberid == 0) {
            ?>
            dropAccept: '.studiocalender div',
            <?php
    	        } else {
            ?>
            dropAccept: '.engineercalender div',
            <?php
	            }
            ?>
            draggable: true,
        	droppable: true, // this allows things to be dropped onto the calendar !!!
            <?php
            } else {
            ?>
            draggable: false,
        	droppable: false, // this allows things to be dropped onto the calendar !!!
            <?php
            }
            ?>
            <?php
			if (isUserInRole("PM") || isUserInRole("ADMIN")) {
            ?>
			drop: function(date, allDay) { // this function is called when something is dropped
		    	var originalEventObject = $(this).data('eventObject');
		    	
				var memberid = originalEventObject.memberid;
				var studioid = originalEventObject.studioid;

				if (originalEventObject.alldayevent == "Y") {
					date.setHours(9);
					date.setMinutes(0);
				}
				
		    	var dateEnd = date.getTime() + (originalEventObject.end.getTime() - originalEventObject.start.getTime());
		    	
		    	if (<?php echo $memberid; ?> != 0) {
		    		memberid = <?php echo $memberid; ?>;
		    	}
		    	
		    	if (<?php echo $studioid; ?> != 0) {
		    		studioid = <?php echo $studioid; ?>;
		    	}
		    	
				callAjax(
						"updatecalendar.php", 
						{ 
							calendarid: originalEventObject.calendarid,
							start: formatDateTime(date),
							end: formatDateTime(new Date(dateEnd)),
							memberid: memberid,
							studioid: studioid,
							alldayevent: originalEventObject.alldayevent,
							bookingid: originalEventObject.bookingid
						},
						function(data) {
							var node = data[0];
							
							if (node.bookingid == -1) {
								$("#generaldialog p").text(node.error);
								$("#generaldialog").dialog("open");

								return;
							}
							
							var myEvent = {
									  id: originalEventObject.id,
									  title: node.title,
									  allDay: false,
									  bookingid: originalEventObject.bookingid,
									  calendarid: originalEventObject.calendarid,
									  calendar: '#<?php echo $name; ?>',
									  studioid: studioid,
									  summary: node.summary,
									  notes: node.notes,
									  unclink: node.unclink,
									  alldayevent: originalEventObject.alldayevent,
									  engineername: node.engineername,
									  studioname: node.studioname,
									  memberid: memberid,
									  start: date,
									  end: new Date(dateEnd),
									  editable: true,
									  className: originalEventObject.className
									};
									
							$('#' + originalEventObject.calendar).fullCalendar('removeEvents', originalEventObject.id, true);
							$('#<?php echo $name; ?>').fullCalendar('renderEvent', myEvent, true);
							
							<?php
								if ($memberid != 0) {
								?>
									event2 = $("#calendar_s_" + myEvent.studioid).fullCalendar('clientEvents', myEvent.id);
								<?php
								} else {
								?>
									event2 = $("#calendar_member_" + myEvent.memberid).fullCalendar('clientEvents', myEvent.id);
								<?php
								}
								?>
								
								event2[0].title = node.title;
								event2[0].summary = node.summary;
								event2[0].unclink = node.unclink;
								event2[0].alldayevent = originalEventObject.alldayevent;
								event2[0].notes = node.notes;
								event2[0].start = myEvent.start;
								event2[0].memberid = memberid;
								event2[0].studioid = studioid;
								event2[0].end = myEvent.end;
								event2[0].engineername = node.engineername;
								event2[0].studioname = node.studioname;
								
								<?php
								if ($memberid != 0) {
								?>
								    $("#calendar_s_" + myEvent.studioid).fullCalendar('updateEvent', event2[0]);
								<?php
								} else {
								?>
								    $("#calendar_member_" + myEvent.memberid).fullCalendar('updateEvent', event2[0]);
								<?php
								}
							?>
						},
						false,
						function(jqXHR, textStatus, errorThrown) {
							alert("ERROR:" + errorThrown);
						}
					);
				
				
			},
            <?php
            }
            ?>
			eventClick: function(calEvent, jsEvent, view) {
				$("#engineerid").val(calEvent.memberid);
				$("#studioid").val(calEvent.studioid);
	        	$("#summary").val(calEvent.summary);
	        	$("#unclink").val(calEvent.unclink);
	        	$("#alldayevent").attr("checked", (calEvent.alldayevent == "Y"));
	        	$("#notes").val(calEvent.notes.replace("\\n", "\n"));
				$("#bookingdate").val(formatDate(calEvent.start));
				$("#bookingenddate").val(formatDate(calEvent.end));
				$("#bookingstarttime").val(formatTime(calEvent.start));
				$("#bookingendtime").val(formatTime(calEvent.end));
				$("#calendarid").val(calEvent.calendarid);
				$("#calendarname").val(calEvent.calendarname);
				$("#bookingid").val(calEvent.bookingid);
				
			<?php
				if (isUserInRole("PM") || isUserInRole("ADMIN")) {
			?>
				$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", false);
			<?php
				} else {
			?>
				$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", true);
			<?php
				}
			?>
				$("#studiobookingdialog").dialog("open");
		    },
			
			<?php
				if (isUserInRole("PM") || isUserInRole("ADMIN")) {
			?>
		    dayRender: function(date, element, view) {
		        element.bind('dblclick', function() {
		        	if ($("#<?php echo $name; ?>").attr("memberid") != null) {
						$("#engineerid").val($("#<?php echo $name; ?>").attr("memberid") );
						
		        	} else {
						$("#engineerid").val(0);
		        	}
		        	
		        	if ($("#<?php echo $name; ?>").attr("studioid") != null) {
						$("#studioid").val($("#<?php echo $name; ?>").attr("studioid") );
						
		        	} else {
						$("#studioid").val(0);
		        	}
		        	
		        	$("#bookingid").val(0);
		        	$("#alldayevent").attr("checked", false).trigger("change");
		        	$("#summary").val("");
		        	$("#unclink").val("");
		        	$("#notes").val("");
					$("#bookingdate").val(formatDate(view.visStart));
					$("#bookingenddate").val(formatDate(view.visStart));
					$("#bookingstarttime").val("09:00");
					$("#bookingendtime").val("10:00");
					$("#calendarname").val("<?php echo $name; ?>");
					
					$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", false);
					$("#studiobookingdialog").dialog("open");
			      });
		    },
			
		    timeRender: function(date, element, view) {
		
		        element.bind('dblclick', function() {
		        	if ($("#<?php echo $name; ?>").attr("memberid") != null) {
						$("#engineerid").val($("#<?php echo $name; ?>").attr("memberid") );
						
		        	} else {
						$("#engineerid").val(0);
		        	}
		        	
		        	if ($("#<?php echo $name; ?>").attr("studioid") != null) {
						$("#studioid").val($("#<?php echo $name; ?>").attr("studioid") );
						
		        	} else {
						$("#studioid").val(0);
		        	}
		        	
		        	$("#bookingid").val(0);
		        	$("#summary").val("");
		        	$("#unclink").val("");
		        	$("#alldayevent").attr("checked", false).trigger("change");
		        	$("#notes").val("");
					$("#bookingdate").val(formatDate(view.visStart));
					$("#bookingenddate").val(formatDate(view.visStart));
					$("#bookingstarttime").val(convertTime(date));
					$("#bookingendtime").val(convertTimePlus1Hour(date));
					$("#calendarid").val("<?php echo $name; ?>");
					$("#calendarname").val("<?php echo $name; ?>");
					
					$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", false);
					$("#studiobookingdialog").dialog("open");
					
			      });
		    },
			
		    weekRender: function(date, element, view) {
		
		        element.bind('dblclick', function() {
			      });
		    },
		    
		    eventDrop: function( event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view ) { 
		    },

		    

		    eventResize: function( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) { 
				callAjax(
						"updatebookingtime.php", 
						{ 
							bookingid: event.bookingid,
							difference: minuteDelta
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								if (node.bookingid == -1) {
									revertFunc();
									
									$("#generaldialog p").text(node.error);
									$("#generaldialog").dialog("open");
									
									return;
								}
								
								<?php
								if ($memberid != 0) {
								?>
									event2 = $("#calendar_s_" + event.studioid).fullCalendar('clientEvents', event.id);
									event2[0].end = event.end;
									
								    $("#calendar_s_" + event.studioid).fullCalendar('updateEvent', event2[0]);
								<?php
								} else {
								?>
									event2 = $("#calendar_member_" + event.memberid).fullCalendar('clientEvents', event.id);
									event2[0].end = event.end;
									
								    $("#calendar_member_" + event.memberid).fullCalendar('updateEvent', event2[0]);
								<?php
								}
								?>
							}
						},
						false,
						function(jqXHR, textStatus, errorThrown) {
							alert("ERROR:" + errorThrown);
						}
					);
		    	
		    },
            <?php
			}
            ?>
			
		    dayClick: function(date, allDay, jsEvent, view) {
		
		        // change the day's background color just for fun
		
		    },
		    
		    defaultView: 'agendaDay',
			
			events: [




<?php
			$first = true;
			
			if ($memberid != 0) {
				$qry = "SELECT A.id, A.memberid, A.studioid, B.id AS bookingid, " .
						"DATE_FORMAT(B.bookingstart, '%Y') AS startyear, " .
						"DATE_FORMAT(B.bookingstart, '%c') AS startmonth, " .
						"DATE_FORMAT(B.bookingstart, '%e') AS startday, " .
						"DATE_FORMAT(B.bookingstart, '%H:%i') AS starttime, " .
						"DATE_FORMAT(B.bookingend, '%Y') AS endyear, " .
						"DATE_FORMAT(B.bookingend, '%c') AS endmonth, " .
						"DATE_FORMAT(B.bookingend, '%e') AS endday, " .
						"DATE_FORMAT(B.bookingend, '%H:%i') AS endtime, " .
						"B.summary, B.allday, B.unclink, B.notes, C.firstname, C.lastname, D.name " .
						"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
						"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
						"ON B.id = A.bookingid " .
						"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
						"ON C.member_id = A.memberid " .
						"INNER JOIN {$_SESSION['DB_PREFIX']}studio D " .
						"ON D.id = A.studioid " .
						"WHERE A.memberid = $memberid";
						
			} else {
				$qry = "SELECT A.id, A.memberid, A.studioid, B.id AS bookingid, " .
						"DATE_FORMAT(B.bookingstart, '%Y') AS startyear, " .
						"DATE_FORMAT(B.bookingstart, '%c') AS startmonth, " .
						"DATE_FORMAT(B.bookingstart, '%e') AS startday, " .
						"DATE_FORMAT(B.bookingstart, '%H:%i') AS starttime, " .
						"DATE_FORMAT(B.bookingend, '%Y') AS endyear, " .
						"DATE_FORMAT(B.bookingend, '%c') AS endmonth, " .
						"DATE_FORMAT(B.bookingend, '%e') AS endday, " .
						"DATE_FORMAT(B.bookingend, '%H:%i') AS endtime, " .
						"B.summary, B.allday, B.unclink, B.notes, C.firstname, C.lastname, D.name " .
						"FROM {$_SESSION['DB_PREFIX']}engineercalendar A " .
						"INNER JOIN {$_SESSION['DB_PREFIX']}booking B " .
						"ON B.id = A.bookingid " .
						"INNER JOIN {$_SESSION['DB_PREFIX']}members C " .
						"ON C.member_id = A.memberid " .
						"INNER JOIN {$_SESSION['DB_PREFIX']}studio D " .
						"ON D.id = A.studioid " .
						"WHERE A.studioid = $studioid";
			}
			
			$result = mysql_query($qry);
		
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
					if (! $first) {
						echo ",\n";
						
					} else {
						$first = false;
					}
					
					$startHour = substr($member['starttime'], 0, 2 );
					$startMinute = substr($member['starttime'], 3, 2 );
					
					$endHour = substr($member['endtime'], 0, 2 );
					$endMinute = substr($member['endtime'], 3, 2 );
?>
				{
					id:<?php echo $member['bookingid']; ?>,
					calendarid: <?php echo $member['id']; ?>,
					calendarname: "<?php echo $name; ?>",
					studioname: "<?php echo $member['name']; ?>",
					engineername: "<?php echo mysql_escape_string($member['firstname'] . " " . $member['lastname']); ?>",
					studioid: <?php echo $member['studioid']; ?>,
					memberid: <?php echo $member['memberid']; ?>,
					bookingid: <?php echo $member['bookingid']; ?>,
					alldayevent: "<?php echo $member['allday']; ?>",
					unclink: "<?php echo mysql_escape_string($member['unclink']); ?>",
					notes: "<?php echo mysql_escape_string($member['notes']); ?>",
					summary: "<?php echo mysql_escape_string($member['summary']); ?>",
					title: "<i><?php echo mysql_escape_string($member['firstname'] . " " . $member['lastname']); ?><i><hr><h3><?php echo mysql_escape_string($member['summary']) . "</h3><a onclick='enavigate(this, \\\"file:///" . str_replace("\\", "/", $member['unclink']) . "\\\", true)'>" . mysql_escape_string($member['unclink']) . "</a><br>" . mysql_escape_string($member['notes']); ?>",
					<?php 
					if ($memberid == 0) {
					?>
					className: 'eventColor1',
					<?php 
					} else {
					?>
					className: 'eventColor3',
					<?php 
					}
					?>
					allDay: false,
					editable: true,
					start: new Date(<?php echo $member['startyear'] . ", " . ($member['startmonth'] - 1) . ", " . $member['startday'] . ", " . $startHour . ", " . $startMinute; ?>),
					end: new Date(<?php echo $member['endyear'] . ", " . ($member['endmonth'] - 1) . ", " . $member['endday'] . ", " . $endHour . ", " . $endMinute; ?>)
				}
<?php
					}
					
				} else {
					logError($qry . " - " . mysql_error());
			}
?>
			]
		});
<?php
}
?>