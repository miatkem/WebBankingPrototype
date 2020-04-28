<?php

	# -------------------- HEADER / ERROR CATCH -------------------- #
	include("config.php");

	//Error Catcher
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	ini_set('display_errors', 1);

	//Gate Keeper
	if (! isset($_SESSION['captcha-verified'])){
		echo "You did not complete the captcha...";
		header ("refresh: 2 ; url = captcha-form.php" );
		exit();
	}
	
	//Gate Keeper
	if (! isset($_SESSION['logged'])){
		echo "You are not logged in...";
		header ("refresh: 2 ; url = auth.php" );
		exit();
	}

	//include files
	include("sqlaccount.php");
	include("myfunctions.php");

	//connect to sql
	$db = mysqli_connect($hostname, $username, $password, $project);
	if (mysqli_connect_errno()){
		echo "Failed to connect: " . mysqli_connect_error();
		exit();
	}

	mysqli_select_db( $db, $project );

	# ------------------------- BODY STARTS ------------------------- #
	
	//declare variable
	$pinEntered = safe("pin");
	$pinActual = $_SESSION["pin"];
	
	//check pin - correct
	if($pinEntered == $pinActual){
		$_SESSION["pinpass"]=true;
		echo "Correct pin, routing to service...";
		header("refresh: 2;url=service1.php");
		exit();
	} 
	
	//check pin - incorrect
	else {
		echo "Incorrect pin, routing back to pin entry...";
		header("refresh: 2;url=pin1.php");
		exit();
	}

	#----------------------------  FOOTER  ---------------------------- #
	mysqli_close($db);
?>  
