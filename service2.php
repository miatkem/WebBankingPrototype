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
	
	if (! isset($_SESSION['logged'])){
		echo "You are not logged in...";
		header ("refresh: 2 ; url = auth.php" );
		exit();
	}
	
	if (! isset($_SESSION['pinpass'])){
		echo "You did noy verify email in...";
		header ("refresh: 2 ; url = pin1.php " );
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
	$choice = safe("choice");
	$ucid = $_SESSION["ucid"];

	echo "
	<head>
		<link rel=\"stylesheet\" href=\"css/loginstyle.css\">
		<link rel=\"stylesheet\" href=\"css/servicestyle.css\">
		<link href=\"https://fonts.googleapis.com/css?family=Ubuntu\" rel=\"stylesheet\">
		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
		<link rel=\"stylesheet\" href=\"path/to/font-awesome/css/font-awesome.min.css\">
	</head>

	<body>
		<br><br><br>
		<div class = \"main\" id=\"wrapper\" name=\"wrapper\">";
		
			//List Service
			if($choice=="list"){
				$num = safe("number");
				echo "
				<div class = \"listdiv\">
					<p class=\"sign\" >Your Transactions
					<div>"; 
						displayUserTransactions($ucid, $num); echo "
					</div>
				</div>";
			}
			
			//Clear Service
			if($choice=="clear"){
				$account = safe("account");
				echo "
				<div class = \"cleardiv\">
					<p class=\"sign\" >Clear Transactions
					<div>"; 
						$amt=clearAccount($ucid, $account); echo "
						<p style=\"padding:10px 10px\">$amt transactions were successfuly deleted from account #: $account!</p>
					</div>
				</div>";
			}
			
			//Perform (transaction) Service
			if($choice== "perform"){
				$flag = true;
				$account = safe("account");
				$mail = safe("mail");
				$amount = safe("amount");
				
				if($flag==false){
					header ("refresh: 5 ; url = service1.php " );
					exit();
				}
				
				echo "
				<div class = \"performdiv\">
					<p class=\"sign\" >Perform Transaction
					<div>
						<p style=\"padding:10px 10px\">"; 
						$flag=transact($ucid, $account, $amount, $mail);
						if($flag == 'overdraft') { echo "$flag Transactions for $amount dollars failed due to overdraft!"; } 
						else if($flag == 'notaccount') { echo "$account is not a valid account!"; } 
						else if ($flag=='success') { echo "Transactions for $amount dollars successfuly performed";}
						else { echo "Error making transaction!"; }

						echo "
						</p>
					</div>
				</div>";
			}

			echo "
			<button id=\"backbtn\" class=\"button\" onclick=\"window.location.href = 'service1.php';\">Back</button>
			<button id=\"logout\" class=\"button\" onclick=\"window.location.href = 'logout.php';\">Logout</button>
		</div>
	</body>";
	
	#----------------------------  FOOTER  ---------------------------- #
	mysqli_close($db);
?>