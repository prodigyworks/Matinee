<style>
.checkboxlabel  {
  display: inline ! important;
}

</style>
<table border="0" cellspacing="4px" width='350px' style='table-layout: fixed;'>

	<tr>
		<td width='150px'>Engineer</td>
		<td colspan="2">
			<?php createUserCombo("memberid", "WHERE A.member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = 'ENGINEER')"); ?>
		</td>
	</tr>
	<tr>
		<td>Start Date</td>
		<td>
			<input type="text" class="datepicker" id="startdate" name="startdate" onchange="calculateDuration()" />
		</td>
		<td width='200px'>
			<span class='checkboxlabel'>Full Day</<span>
			<input class='checkboxtype' type="checkbox" id="startdate_half" name="startdate_half" onchange="calculateDuration()" />
		</td>
	</tr>
	<tr>
		<td>End Date</td>
		<td>
			<input type="text" class="datepicker" id="enddate" name="enddate" onchange="calculateDuration()" />
		</td>
		<td width='200px'>
			<span class='checkboxlabel'>Full Day</span>
			<input class='checkboxtype'  type="checkbox" id="enddate_half" name="enddate_half" onchange="calculateDuration()" />
		</td>
	</tr>
	<tr>
		<td>Duration</td>
		<td colspan="2">
			<input type="text" id="daystaken" name="daystaken" size=3 />
		</td>
	</tr>
</table>