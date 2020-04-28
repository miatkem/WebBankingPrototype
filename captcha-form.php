<?php
	include("config.php");
	
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

	
	if (isset ($_GET["guess"]))
	{
		//get captcha actual and guess
		$captchaGuess = safe("guess");
		$actual=$_SESSION["captcha"];
		$delay = safe("delay");

		//print captcha actual and guess
		echo "Correct Code = $actual<br>";
		echo "Guessed Code = $captchaGuess<br>";

		//check captcha guess
		if( $captchaGuess == $actual) {
			$_SESSION['captcha-verified']=true;
			echo " <b>Guess is Correct, routing to login form...</b>";
			header("refresh: " . $delay . ";url=auth.php");
			exit();		
		}
		else { 
			echo " <b>Guess is incorrect, routing back to captcha...</b>";
			header("refresh: " . $delay . ";url=captcha-form.php");
			exit();	
		}
	}
?>

<html>
	<head>
			<link rel="stylesheet" href="css/loginstyle.css">
			<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
			<meta name="viewport" content="width=device-width, initial-scale=1" />
			<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
			<title>Captcha Verification</title>
	</head>
	
	<body>
		<div class="maincap">
			<p class="sign" align="center">Captcha</p>
			<form class = "formcap" action="captcha-form.php">
				<div align="center"><img src="captcha.php" width = 275 ></div>
				<p align = "center">What is the captcha text:<input type="text" size = 10 name="guess" autocomplete=off ></p> 
				<p align = "center">Delay:<input type="text" size = 3 name="delay" value="5" autocomplete=off ></p> 
				<input type="submit" class = "submit" align="center" value="Submit">
			</form>
		</div>
	</body>
</html>