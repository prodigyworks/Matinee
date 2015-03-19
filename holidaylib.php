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
			$("#requesteddate").val(prettyDate).trigger("change");
			$("#startdate").val(prettyDate).trigger("change");
			$("#enddate").val(prettyDate).trigger("change");
			$("#startdate_half").attr("checked", true).trigger("change");
			$("#enddate_half").attr("checked", true).trigger("change");
			<?php
		}
		
		/* Pre script event. */
		public function preScriptEvent() {
			?>
			var currentID = 0;
			<?php
		}
		
		public function postInsertEvent() {
			$qry = "SELECT A.memberid, A.daystaken, " .
					"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
					"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"WHERE A.id = $id";
			$result = mysql_query($qry);
			
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					sendRoleMessage(
							"MANAGEMENT",
							"Holiday request created", 
							"<h4>A holiday request has been created for " 
							. GetUserName($member['memberid']) 
							. "</h4><p>Date range of holiday " 
							. $member['startdate'] 
							. " - " 
							. $member['enddate']
							. ". This is "
							. $member['daystaken']
							. " days."
							. "</p>"
						);
				}
				
			} else {
				logError($qry . " - " . mysql_error());
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
			
			function approveHoliday(crudID) {
				currentID = crudID;
				
				$("#confirmapprovaldialog .confirmdialogbody").html("You are about to approve this holiday.<br>Are you sure ?");
				$("#confirmapprovaldialog").dialog("open");
			}
			
			function approve() {
				$("#confirmapprovaldialog").dialog("close");
				
				post("editform", "approveHoliday", "submitframe", { holidayid: currentID });
			}
			
			function rejectHoliday(crudID) {
				currentID = crudID;
				
				$("#reasondialog").dialog("open");
			}
			
			function calculateDuration() {
				var startDateStr = $("#startdate").val();
				var endDateStr = $("#enddate").val();
				
				var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
				var endDate = new Date(endDateStr.substring(6, 10), (parseFloat(endDateStr.substring(3, 5)) - 1), endDateStr.substring(0, 2));
				var days = workingDaysBetweenDates(startDate, endDate);
				
				callAjax(
						"findbankholidays.php", 
						{ 
							startdate: startDateStr,
							enddate: endDateStr
						},
						function(data) {
							if (data.length > 0) {
								for (var i = 0; i < data.length; i++) {
									var node = data[i];
									
									var bankStartDate = new Date(node.startdate.substring(6, 10), (parseFloat(node.startdate.substring(3, 5)) - 1), node.startdate.substring(0, 2));
									var bankEndDate = new Date(node.enddate.substring(6, 10), (parseFloat(node.enddate.substring(3, 5)) - 1), node.enddate.substring(0, 2));
									var xdays = workingDaysBetweenDates(bankStartDate, bankEndDate);
									
									days -= xdays;
								}
							}
						},
						false
					);
				
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
					$("#approvebutton").attr("disabled", true);
					$("#rejectbutton").attr("disabled", true);
				}
			}
			
			function daysRemaining(node) {
				return node.prorataholidayentitlement - node.daysremaining;
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
					"B.prorataholidayentitlement," .
					"B.firstname, B.lastname, " .
					"(SELECT SUM(D.daystaken) FROM {$_SESSION['DB_PREFIX']}holiday D WHERE YEAR(D.startdate) = YEAR(A.startdate) AND D.memberid = A.memberid AND D.acceptedby IS NOT NULL) AS daysremaining " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
					"ON B.member_id = A.memberid " .
					"WHERE B.member_id = " . $_GET['id'];
				
			} else {
				$this->sql = 
					"SELECT A.*, " .
					"B.prorataholidayentitlement," .
					"B.firstname, B.lastname, " .
					"(SELECT SUM(D.daystaken) FROM {$_SESSION['DB_PREFIX']}holiday D WHERE YEAR(D.startdate) = YEAR(A.startdate) AND D.memberid = A.memberid AND D.acceptedby IS NOT NULL) AS daysremaining " .
					"FROM {$_SESSION['DB_PREFIX']}holiday A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}members B " .
					"ON B.member_id = A.memberid";
			}
			
			$this->sql = getFilteredData($this->sql);
			
			$this->messages = array(
					array('id'		  => 'holidayid'),
					array('id'		  => 'reasonnotes')
				);
			$this->subapplications = array(
					array(
						'id'		  => 'approvebutton',
						'title'		  => 'Approve',
						'imageurl'	  => 'images/approve.png',
						'script' 	  => 'approveHoliday'
					),
					array(
						'id'		  => 'rejectbutton',
						'title'		  => 'Reject',
						'imageurl'	  => 'images/cancel.png',
						'script' 	  => 'rejectHoliday'
					)
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
						'label' 	 => 'Employee'
					),
					array(
						'name'       => 'memberid',
						'datatype'	 => 'user',
						'length' 	 => 12,
						'showInView' => false,
						'label' 	 => 'Employee'
					),
					array(
						'name'       => 'remaining',
						'type'		 => 'DERIVED',
						'length' 	 => 12,
						'filter'	 => false,
						'bind'		 => false,
						'editable' 	 => false,
						'function'   => 'daysRemaining',
						'align'		 => 'center',
						'label' 	 => 'Days Remainings'
					),
					array(
						'name'       => 'requesteddate',
						'datatype'	 => 'timestamp',
						'length' 	 => 10,
						'filter'	 => false,
						'readonly'	 => true,
						'label' 	 => 'Request Date'
					),
					array(
						'name'       => 'startdate',
						'filter'	 => false,
						'datatype'	 => 'timestamp',
						'associatedcolumns' => 
							array(
								'startdate_half'
							),
						'length' 	 => 15,
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
						'datatype'	 => 'timestamp',
						'associatedcolumns' => 
							array(
								'enddate_half'
							),
						'length' 	 => 15,
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
	
	function approveHoliday() {
		$id = $_POST['holidayid'];
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}holiday SET " .
				"rejectedby = null, " .
				"rejecteddate = null, " .
				"acceptedby = " . getLoggedOnMemberID() . ", " .
				"accepteddate = NOW() " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
		
		$qry = "SELECT A.memberid, " .
				"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
				"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate " .
				"FROM {$_SESSION['DB_PREFIX']}holiday A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage(
						$member['memberid'], 
						"Holiday approved", 
						"Holiday has been approved between " 
						. $member['startdate'] 
						. " and " 
						. $member['enddate'] 
					);
			}
		}
	}
	
	function rejectHoliday() {
		$id = $_POST['holidayid'];
		$reason = $_POST['reasonnotes'];
		
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}holiday SET " .
				"acceptedby = null, " .
				"accepteddate = null, " .
				"reason = '" . mysql_escape_string($reason) . "', " .
				"rejectedby = " . getLoggedOnMemberID() . ", " .
				"rejecteddate = NOW() " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
		
		$qry = "SELECT A.memberid, A.reason, " .
				"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
				"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate " .
				"FROM {$_SESSION['DB_PREFIX']}holiday A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage(
						$member['memberid'], 
						"Holiday rejected", 
						"Holiday has been rejected between " 
						. $member['startdate'] 
						. " and " 
						. $member['enddate'] 
						. ", reason: " . $member['reason']
					);
			}
		}
	}
?>