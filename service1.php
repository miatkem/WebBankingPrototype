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
		header ("refresh: 2 ; url = auth.php " );
		exit();
	}
	
	

	# ------------------------- BODY STARTS ------------------------- #
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
		<div class = \"main\" id=\"wrapper\" name=\"wrapper\">
			<div class = \"form1\">
				<p class=\"sign\" >Welcome To NJIT's Web Banking</p>
				<form action=\"service2.php\">
					<input type=\"radio\" id=\"list\" name=\"choice\" value=\"list\" onclick=\"F()\">
					<label for=\"list\">List Accounts and Transacations</label><br>
					
					<input type=\"radio\" id=\"perform\" name=\"choice\" value=\"perform\" onclick=\"F()\">
					<label for=\"perform\">Perform Transaction</label><br>
					
					<input type=\"radio\" id=\"clear\" name=\"choice\" value=\"clear\" onclick=\"F()\">
					<label for=\"clear\">Clear Account</label><br><br>
					
					<div id = \"number\">Enter # of rows: <input type=text autocomplete=\"off\" name=\"number\"><br><br></div>
					<div id = \"account\">Enter Account: <input type=text autocomplete=\"off\" name=\"account\"><br><br></div>
					<div id = \"amount\">Enter Amount: <input type=text autocomplete=\"off\" name=\"amount\"><br><br></div>

					<div id = \"mail\">
						Would you like a reciept mailed?
						<input type=\"radio\" id=\"mail_yes\" name=\"mail\" value=\"Y\" onclick=\"F()\">
						<label for=\"mail_yes\">Yes</label>
						<input type=\"radio\" id=\"mail_no\" name=\"mail\" value=\"N\" onclick=\"F()\">
						<label for=\"mail_no\">No</label> <br><br>
					</div>

					<div id = \"submit\"> <input type=\"submit\" id=\"submit-button\" value=\"Execute Process\"></div>
				</form>
			</div>
			<button id=\"logout\" class=\"button\" onclick=\"window.location.href = 'logout.php';\">Logout</button>
		</div>
	</body>

	<script>
		var number = document.getElementById(\"number\");
		var account = document.getElementById(\"account\");
		var amount = document.getElementById(\"amount\");
		var mail = document.getElementById(\"mail\");
		var submit = document.getElementById(\"submit\");


		function F() {

			number.style.display = \"none\";
			account.style.display = \"none\";
			amount.style.display = \"none\";
			mail.style.display = \"none\";
			submit.style.display = \"none\";


			if (document.getElementById(\"list\").checked){
				number.style.display=\"inline-block\";
				submit.style.display = \"inline-block\";
			}

			if (document.getElementById(\"perform\").checked){
				account.style.display = \"inline-block\";
				amount.style.display = \"inline-block\";
				mail.style.display = \"inline-block\";
				submit.style.display = \"inline-block\";
			}

			if (document.getElementById(\"clear\").checked){
				account.style.display = \"inline-block\";
				submit.style.display = \"inline-block\";
			}
		}  
	</script>"
		
	#----------------------------  FOOTER  ---------------------------- #
?>