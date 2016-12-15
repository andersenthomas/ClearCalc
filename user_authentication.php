<?php

    // checks whether the user has been validated and if activity has occured
    session_start();

	//if($_SESSION['LOGGED_IN'] != True || (time() - $_SESSION['LAST_ACTIVITY'] > 900)){
	if($_SESSION['LOGGED_IN'] != True){
		// last request was more than 30 minutes ago or user is not logged in
		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
		header("Location: login.php");
		exit;
	} else {
		$_SESSION['LAST_ACTIVITY'] = time();
	}
 
?>
