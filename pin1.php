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

	//create & save pin
	$pin = mt_rand(10000, 999999);
	$_SESSION["pin"] = $pin;
	$ucid = $_SESSION['ucid'];

	//capture user email
	$to = retrieveUserEmail($ucid);

	//code for test
	echo "<p style=\"font-size:24; color: white;\">pin is $pin and would have been sent to $to<p>";
	$to = "mdm56@njit.edu";
	echo "<p style=\"font-size:24; color: white;\">Instead, sent to $to<p>";

	//create & send email
	$subj = "Login PIN";
	$msg = $pin;
	mail($to, $subj, $msg);

	//HTML form for user to enter pin
	echo "
	<html>
		<head>
			<link rel=\"stylesheet\" href=\"css/loginstyle.css\">
			<link href=\"https://fonts.googleapis.com/css?family=Ubuntu\" rel=\"stylesheet\">
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
			<link rel=\"stylesheet\" href=\"path/to/font-awesome/css/font-awesome.min.css\">
			<title>Sign in</title>
		</head>

		<body>
			<div class=\"main\">
				<p class=\"sign\" align=\"center\">Verify Email</p>
				<form class=\"form1\" action=\"pin2.php\">
					<input class=\"un\"  autocomplete=\"off\" type=\"text\" align=\"center\" placeholder=\"Code Sent To Your Email\" name =\"pin\">
					<input type =\"submit\" class=\"submit\" align=\"center\" text=\"Submit\" value=\"Submit\">
					</form>
			</div>
		</body>
	</html>";
	
	#----------------------------  FOOTER  ---------------------------- #
	mysqli_close($db);
?>