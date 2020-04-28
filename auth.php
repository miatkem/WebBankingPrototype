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
	
	if (isset ($_GET["ucid"]))
	{	
		//regex flag
		$flag = true;
		
		//capture login parameters 
		$ucid = safe("ucid");
		$pass = safe("pass");
		$amount = safe("amount");
		$delay = safe("delay");
		
		//verify user exists
		$verify = verifyUsername($ucid);
		
		//no regex errors
		if ($flag == true) {
			//username exists
			if ($verify == true){
				
				//verify password correct
				$auth = authenticate($ucid, $pass);

				//Correct password and username
				if ($auth == true) {
					echo "Login successful, routing to pin verification...";
					header("refresh: $delay;url=pin1.php");
					exit();
				}

				//correct username incorrect password
				else {
					echo "Incorrect password, routing back to login...";
					header("refresh: $delay;url=auth.php");
					exit();
				}
			}
			//username does not exist
			else{
				echo "User does not exist, routing back to login...";
				header("refresh: $delay;url=auth.php");
				exit();
			}
		}
		
		//regex error takes place
		header("refresh: $delay;url=auth.php");
		exit();
	}
	#----------------------------  FOOTER  ---------------------------- #
	mysqli_close($db);
?>  

<html>
	<head>
		<link rel="stylesheet" href="css/loginstyle.css">
		<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
		<title>Sign in</title>
	</head>

	<body>
		<div class="main">
			<p class="sign" align="center">Sign in</p>
			<form class="form1" action="auth.php">
				<input class="un" type="text" align="center"  autocomplete="off" placeholder="Ucid" name ="ucid">
				<input class="pass" type="password" align="center" autocomplete="off" placeholder="Password" name="pass">
				<p align = "center">Delay:<input type="text" size = 3 name="delay" value="5" autocomplete=off ></p>
				<p align = "center">Amount:<input type="text" size = 3 name="amount" autocomplete=off ></p>				
				<input type ="submit" class="submit" align="center" text="Sign in" value="Log-In">
			</form>
		</div>
	</body>
</html>