<?php
	require_once("crud.php");
	require_once("datafilter.php");
	
	class HolidayCrud extends Crud {
		
		public function postAddScriptEvent() {
			?>
			var myDate = new Date(); 
			var prettyDate =
					padZero(myDate.getDate()) + '/' +         
				    padZero((myDate.getMonth() + 1)) + '/' + 
					myDate.getFullYear(); 
					 
			$("#memberid").val("<?php echo getLoggedOnMemberID();?>").trigger("change");
			$("#startdate").val(prettyDate).trigger("change");
			$("#enddate").val(prettyDate).trigger("change");
			$("#startdate_half").attr("checked", true).trigger("change");
			$("#enddate_half").attr("checked", true).trigger("change");
			<?php
		}

		public function getVerifyForm() {
			return "verifyholidayform";
		}
	
		public function editScreenSetup() {
			include("holidayform.php");
		}
		
		/* Pre script event. */
		public function preScriptEvent() {
			?>
			var currentID = 0;
			<?php
		}
		
		public function postEditScriptEvent() {
?>
			if ($("#startdate").val().length > 10) $("#startdate").val($("#startdate").val().substr(0, 11));
			if ($("#enddate").val().length > 10) $("#enddate").val($("#enddate").val().substr(0, 11));
<?php
		}
		
		public function postUpdateEvent($id) {
			$this->updateHoliday($id);
		}
		
		public function postInsertEvent() {
			$this->updateHoliday(mysql_insert_id());
		}
		
		public function updateHoliday($id) {
			if (isset($_POST['startdate_half']) && $_POST['startdate_half'] == "on") {
				$starttime = " 09:30:00";
				
			} else {
				$starttime = " 13:30:00";
			}
			
			if (isset($_POST['enddate_half']) && $_POST['enddate_half'] == "on") {
				$endtime = " 18:30:00";
				
			} else {
				$endtime = " 13:30:00";
			}
			
			$startdate = convertStringToDate($_POST['startdate']) . " $starttime";
			$enddate = convertStringToDate($_POST['enddate']) . " $endtime";
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}holiday SET 
					startdate = '$startdate', 
					enddate = '$enddate' 
					WHERE id = $id";
			
			logError($sql, false);
			
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
		}
		
		public function postHeaderEvent() {
			createConfirmDialog("confirmapprovaldialog", "Confirm approval ?", "approve");
			
			?>
				<div id="reasondialog" class="modal">
					<label>Reason</label>
					<textarea id="reason" name="reason" class="tinyMCE" style='width:770px; height: 300px'></textarea>
				</div>
				<div id="reasondivdialog" class="modal">
					<h5>Reason</h5>
					<br>
					<div id="reasondiv" style='width:770px; height: 290px; border: 1px solid black'></div>
				</div>
			<?php
		}
		
		public function postScriptEvent() {
?>
			$(document).ready(
					function() {
					$("#reasondialog").dialog({
							modal: true,
							autoOpen: false,
							title: "Reason for rejection",
							width: 810,
							height: 420,
							buttons: {
								Ok: function() {
									tinyMCE.triggerSave();
									$(this).dialog("close");
									
									post("editform", "rejectHoliday", "submitframe", 
											{ 
												holidayid: currentID, 
												reasonnotes: $("#reason").val() 
											}
										);
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
						
						$("#reasondivdialog").dialog({
								modal: true,
								autoOpen: false,
								title: "Reason for rejection",
								width: 810,
								height: 420,
								buttons: {
									Ok: function() {
										$(this).dialog("close");
									}
								}
							});
					}
				);
				
			function verifyholidayform(id) {
				return checkBooking(
						$("#startdate").val(),
						$("#startdate_half").val(),
						$("#enddate").val(),
						$("#enddate_half").val(),
						$("#memberid").val(),
						id
					);
			}
				
			function checkBooking(startdate, startfullday, enddate, endfullday, memberid, id) {
				var retVal = true;
				
				callAjax(
						"checkholiday.php", 
						{ 
							bookingstartdate: startdate,
							bookingenddate: enddate,
							startfullday: startfullday,
							endfullday: endfullday,
							memberid: memberid,
							id: id
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								if (node.error) {
									retVal = false;
									alert(node.error);
								}
							}
						},
						false,
						function(jqXHR, textStatus, errorThrown) {
							alert("ERROR:" + errorThrown);
						}
					);
					
				return retVal;
			}
				
			function statusFormatter(el, cval, opts) {
				if (el == "Rejected") {
					return "<a style='color:red' href='javascript: viewReason(" + opts.uniqueid + ")'>" + el + "</a>";
				}
				
				return el;
		    } 	
				
			function viewReason(id) {
				callAjax(
						"findholiday.php", 
						{ 
							id: id
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								$('#reasondiv').html(node.reason); 
								$("#reasondivdialog").dialog("open");
							}
						},
						false
					);
			}
				
				
			/* Full name callback. */
			function fullName(node) {
				return (node.firstname + " " + node.lastname);
			}
			
			function calculateDuration() {
				var startDateStr = $("#startdate").val();
				var endDateStr = $("#enddate").val();
				
				var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
				var endDate = new Date(endDateStr.substring(6, 10), (parseFloat(endDateStr.substring(3, 5)) - 1), endDateStr.substring(0, 2));
				var days = workingDaysBetweenDates(startDate, endDate);
				
				if (days > 0) {
					if ($("#startdate_half").attr("checked") == false) {
						if (startDate.getDay() > 0 && startDate.getDay() < 6) {
							days -= 0.5;
						}
					}
					
					if ($("#enddate_half").attr("checked") == false) {
						if (endDate.getDay() > 0 && endDate.getDay() < 6) {
							days -= 0.5;
						}
					}
				}
				
				$("#daystaken").val(days);
			}
			
			function checkStatus(node) {
				if (node.status != "Pending" && node.status != "Rejected") {
				}
			}
			
			function duration(node) {
				return node.daystaken;
			}
			
			function statusName(node) {
				var startDate = new Date(node.startdate.substring(6, 10), (parseFloat(node.startdate.substring(3, 5)) - 1), node.startdate.substring(0, 2));
				var now = new Date();

				if (node.rejectedby != null) {
					return "Rejected";
					
				} else if (startDate.getTime() <= now.getTime()) {
					if (node.acceptedby != null) {
						return "Taken";
						
					} else {
						return "Pending";
					}
				
				} else if (node.acceptedby != null) {
					return "Accepted";
					
				} else {
					return "Pending";
				}
			}
<?php			
		}
		
		function __construct() {
			parent::__construct();
			
			$this->title = "Holidays";
			$this->table = "{$_SESSION['DB_PREFIX']}holiday";
			$this->dialogwidth = 500;
			$this->onClickCallback = "checkStatus";
	
			if (isset($_GET['id'])) {
				$this->sql = 
					"SELECT A.*, " .
					"B.firstname, B.lastname, " .
					"(SELECT SUM(D.daystaken) FROM {$_SESSION['DB_PREFIX']}holiday D WHERE YEAR(D.startdate) = YEAR(A.startdate) AND D.memberid = A.memberid AND D.acceptedby IS NOT NULL) AS daysremaining " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
					"ON B.member_id = A.memberid " .
					"WHERE B.member_id = " . $_GET['id'];
				
			} else {
				$this->sql = 
					"SELECT A.*, " .
					"B.firstname, B.lastname, " .
					"(SELECT SUM(D.daystaken) FROM {$_SESSION['DB_PREFIX']}holiday D WHERE YEAR(D.startdate) = YEAR(A.startdate) AND D.memberid = A.memberid AND D.acceptedby IS NOT NULL) AS daysremaining " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
					"ON B.member_id = A.memberid";
			}
			
			$this->sql = ($this->sql);
			
			$this->messages = array(
					array('id'		  => 'holidayid'),
					array('id'		  => 'reasonnotes')
				);
				
			$this->columns = array(
					array(
						'name'       => 'id',
						'length' 	 => 6,
						'pk'		 => true,
						'showInView' => false,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'ID'
					),
					array(
						'name'       => 'requestedbyname',
						'type'		 => 'DERIVED',
						'length' 	 => 30,
						'bind'		 => false,
						'editable' 	 => false,
						'filter'	 => false,
						'sortcolumn' => 'B.firstname',
						'function'   => 'fullName',
						'label' 	 => 'Engineer'
					),
					array(
						'name'       => 'memberid',
						'datatype'	 => 'user',
						'length' 	 => 12,
						'showInView' => false,
						'label' 	 => 'Engineer'
					),
					array(
						'name'       => 'startdate',
						'filter'	 => false,
						'datatype'	 => 'datetime',
						'length' 	 => 20,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'Start Date'
					),
					array(
						'name'       => 'startdate_half',
						'type'	 	 => 'CHECKBOX',
						'showInView' => false,
						'filter'	 => false,
						'length' 	 => 15,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'Full Day'
					),
					array(
						'name'       => 'enddate',
						'filter'	 => false,
						'datatype'	 => 'datetime',
						'length' 	 => 20,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'End Date'
					),
					array(
						'name'       => 'enddate_half',
						'filter'	 => false,
						'type'	 	 => 'CHECKBOX',
						'showInView' => false,
						'length' 	 => 15,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'Full Day'
					),
					array(
						'name'       => 'daystaken',
						'filter'	 => false,
						'align'	 	 => 'center',
						'length' 	 => 15,
						'readonly'	 => true,
						'required'	 => false,
						'label' 	 => 'Duration'
					),
					array(
						'name'       => 'status',
						'filter'	 => false,
						'type'		 => 'DERIVED',
						'length' 	 => 30,
						'bind'		 => false,
						'editable' 	 => false,
						'formatter'	 => 'statusFormatter',
						'function'   => 'statusName',
						'label' 	 => 'Status'
					)
				);
		}
	}

	$crud = new HolidayCrud();
	$crud->run();
?>