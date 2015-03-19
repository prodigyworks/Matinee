<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
									
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}loginaudit SET " .
			"timeoff = NOW() " .
			"WHERE id = " . $_SESSION['SESS_LOGIN_AUDIT'] . "";
	$result = mysql_query($qry);
	
	unset($_SESSION['SESS_MEMBER_ID']);
	unset($_SESSION['SESS_FIRST_NAME']);
	unset($_SESSION['SESS_LAST_NAME']);
	unset($_SESSION['ROLES']);
	unset($_SESSION['MENU_CACHE']);
	unset($_SESSION['breadcrumb']);
	unset($_SESSION['breadcrumbPage']);
	unset($_SESSION['PRODIGYWORKS.INI']);
	unset($_SESSION['DB_PREFIX']);
	unset($_SESSION['WELCOME_excludeholidays']);
	unset($_SESSION['WELCOME_excludeabsences']);
	unset($_SESSION['WELCOME_excludeappraisals']);
	unset($_SESSION['WELCOME_excludeoncall']);
	unset($_SESSION['WELCOME_filteruserid']);

	header("location: index.php");
?>
