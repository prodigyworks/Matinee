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
			    element.find(".fc-event-title").html(event.title);
			    
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
				if ((isUserInRole("PM") || isUserInRole("ADMIN") && ($memberid != 0 || $studioid != 0))) {
				?>
				element.find(".fc-event-inner").addClass("draggable2");

	           	element.find(".fc-event-inner").draggable({
	               helper:'clone',
	               appendTo: 'body',
	               containment: 'DOM',
	               zIndex: 1500,
	               addClasses: false,
	          		           	
	        	    start: function(event, ui) {
	        	    	pauseEvent = true;
	                	$(ui.helper).addClass('dragging');
	            	}
	    		});
	    		<?php
				}
				?>
            },
            <?php
			if ((isUserInRole("PM") || isUserInRole("ADMIN")) && ($memberid != 0 || $studioid != 0)) {
	            if ($memberid != 0) {
            ?>
            dropAccept: '.engineercalender div',
            <?php
	            } else if ($studioid != 0) {
            ?>
            dropAccept: '.studiocalender div',
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
			if ((isUserInRole("PM") || isUserInRole("ADMIN")) && ($memberid != 0 || $studioid != 0)) {
            ?>
			drop: function(date, allDay) { // this function is called when something is dropped
		    	var originalEventObject = $(this).data('eventObject');
		    	
				var memberid = originalEventObject.memberid;
				var studioid = originalEventObject.studioid;
				
				var origmemberid = originalEventObject.memberid;
				var origstudioid = originalEventObject.studioid;

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
							pauseEvent = false;
							
							var node = data[0];
							
							if (node.bookingid == -1) {
								$("#generaldialog p").text(node.error);
								$("#generaldialog").dialog("open");

								return;
							}

							$("#calendar_s_" + studioid).fullCalendar('refetchEvents');
							$("#calendar_member_" + memberid).fullCalendar('refetchEvents');
							
							if (origmemberid != memberid) {
								$("#calendar_member_" + origmemberid).fullCalendar('refetchEvents');
							}
							
							if (origstudioid != studioid) {
								$("#calendar_s_" + origstudioid).fullCalendar('refetchEvents');
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
			eventClick: function(calEvent, jsEvent, view) {
				if (! calEvent.holidayid) {
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
					$("#dialogselect").val("B").trigger("change");
					$("#dialogchoice").hide();													
					$("#studiobookingdialog").dialog("open");
					
					pauseEvent = true;
					
				} else {
					$("#hol_engineerid").val(calEvent.memberid);
					$("#hol_bookingdate").val(formatDate(calEvent.start));
					$("#hol_bookingenddate").val(formatDate(calEvent.end));
					$("#hol_bookingstarttime").val(formatTime(calEvent.start));
					$("#hol_bookingendtime").val(formatTime(calEvent.end));
					$("#hol_holidayid").val(calEvent.holidayid);
					$("#hol_startdate_half").attr("checked", (calEvent.startdate_half == "N" ? true : false));
					$("#hol_enddate_half").attr("checked", (calEvent.enddate_half == "N" ? true : false));
					
				<?php
					if (isUserInRole("PM") || isUserInRole("ADMIN")) {
				?>
					$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", false);
					<?php
					} else {
				?>
					$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", true);
					
					$("#dialogchoice").hide();
					$("#dialogselect").val("H").trigger("change");
					$("#studiobookingdialog").dialog("open");
				<?php
					}
				?>
					calculateDuration();
					
					$("#dialogchoice").hide();
					$("#dialogselect").val("H").trigger("change");
					$("#studiobookingdialog").dialog("open");
													
					pauseEvent = true;
				}
		    },
			
			<?php
				if (isUserInRole("PM") || isUserInRole("ADMIN")) {
			?>
		    dayRender: function(date, element, view) {
		        element.bind('dblclick', function() {
					$("#hol_engineerid").val(0);
		        	$("#hol_holidayid").val(0);
					$("#hol_bookingdate").val(formatDate(view.visStart));
					$("#hol_bookingenddate").val(formatDate(view.visStart));
					$("#hol_bookingstarttime").val("09:30");
					$("#hol_bookingendtime").val("18:30");
					$("#hol_startdate_half").attr("checked", true);
					$("#hol_enddate_half").attr("checked", true);
		        	if ($("#<?php echo $name; ?>").attr("memberid") != null) {
						$("#hol_engineerid").val($("#<?php echo $name; ?>").attr("memberid") );
						
		        	} else {
						$("#hol_engineerid").val(0);
		        	}
		        
		        	if ("<?php echo $name; ?>" == "calendar_holiday") {
						$("#studiobookingdialog input, #v select, #v textarea").attr("disabled", false);
						
						calculateDuration();
						
						$("#dialogchoice").hide();													
						$("#dialogselect").val("H").trigger("change");
						$("#studiobookingdialog").dialog("open");
		        	
		        	} else {
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
						
						$("#dialogselect").val("B").trigger("change");
						$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", false);
						$("#dialogchoice").show();													
						$("#studiobookingdialog").dialog("open");
					}
																	
					pauseEvent = true;
			      });
		    },
			
		    timeRender: function(date, element, view) {
		
		        element.bind('dblclick', function() {
		        	$("#hol_holidayid").val(0);
					$("#hol_bookingdate").val(formatDate(view.visStart));
					$("#hol_bookingenddate").val(formatDate(view.visStart));
					$("#hol_bookingstarttime").val("09:30");
					$("#hol_bookingendtime").val("18:30");
					$("#hol_startdate_half").attr("checked", true);
					$("#hol_enddate_half").attr("checked", true);
		        	if ($("#<?php echo $name; ?>").attr("memberid") != null) {
						$("#hol_engineerid").val($("#<?php echo $name; ?>").attr("memberid") );
						
		        	} else {
						$("#hol_engineerid").val(0);
		        	}
						
		        	if ("<?php echo $name; ?>" == "calendar_holiday") {
						$("#studiobookingdialog input, #v select, #v textarea").attr("disabled", false);
						
						calculateDuration();
						
						$("#dialogselect").val("H").trigger("change");
						$("#dialogchoice").hide();													
						$("#studiobookingdialog").dialog("open");
		        	
		        	} else {
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
						
						$("#dialogchoice").show();													
						$("#dialogselect").val("B").trigger("change");
						
						$("#studiobookingdialog input, #studiobookingdialog select, #studiobookingdialog textarea").attr("disabled", false);
						$("#studiobookingdialog").dialog("open");
					}
													
					pauseEvent = true;					
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
								
								$("#calendar_member_" + event.memberid).fullCalendar('refetchEvents');
								$("#calendar_s_" + event.studioid).fullCalendar('refetchEvents');
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
		    
		    eventResizeStart: function( event, jsEvent, ui, view ) { 
		    	pauseEvent = true;
		    },
		    
		    eventResizeStop: function( event, jsEvent, ui, view ) { 
		    	pauseEvent = false;
		    },
		    
		    defaultView: 'agendaDay',
			
		    events: function(start, end, callback) {
		    	var startYear = start.getYear();
		    	var endYear = end.getYear();
		    	
		    	if (startYear < 2000) {
		    	    startYear += 1900;
		    	}
		    	
		    	if (endYear < 2000) {
		    	    endYear += 1900;
		    	}
		    	
			    $.ajax({
	                type: 'POST',
	                url: 'calendardata.php',
	                async: false,
	                dataType:'json',
			        data: {
			            studioid: '<?php echo $studioid; ?>',
			            memberid: '<?php echo $memberid; ?>',
			            name: '<?php echo $name; ?>',
			            start: startYear + "-" + padZero(start.getMonth() + 1) + "-" + padZero(start.getDate()),
	                    end: endYear + "-" + padZero(end.getMonth() + 1) + "-" + padZero(end.getDate()),      
			        },
			        error: function(error) {
//			            alert('there was an error while fetching events for <?php echo $name; ?>');
			        },
			        success: function(msg) {
						var events = [];
						 
                        for(var c = 0; c < msg.length; c++){
                        	var item = msg[c];
                        	
                            events.push({
	                                id: item.id,                                
	                                title: item.title,
	                                allDay: item.allDay == "true" ? true : false,
	                                alldayevent: item.allDay == "true" ? true : false,
	                                start: item.start,
	                                end: item.end,
	                                editable: item.editable == "true" ? true : false,
	                                className: item.className,
	                                bookingid: item.bookingid,
	                                holidayid: item.holidayid,
	                                startdate_half: item.startdate_half,
	                                enddate_half: item.enddate_half,
						            calendarid: item.calendarid,
						            studioid: item.studioid,
						            summary: item.summary,
						            notes: item.notes,
						            unclink: item.unclink,
						            engineername: item.engineername,
						            studioname: item.studioname,
						            memberid: item.memberid
						            
	                            });
                        }
                        
                        callback(events);
			        }
			     });
		    }
		});
<?php
}
?>