<?php

	# -------------------- HEADER / ERROR CATCH -------------------- #
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	ini_set('display_errors', 1);

	include("sqlaccount.php");

	$db = mysqli_connect($hostname, $username, $password, $project);
	if (mysqli_connect_errno()){
		echo "Failed to connect: " . mysqli_connect_error();
		exit();
	}

	mysqli_select_db( $db, $project );

	# -------------------- FUNCTION DECLARATION -------------------- #

	//safely capture url paramters
	function safe ( $name ) {
		global $db;
		global $flag;
		
		//sanatize
		$value = $_GET[$name];
		$value = trim($value);
		if($value!='') {  $value = mysqli_real_escape_string($db, $value);  }
		
		//regex
		if ($name == "ucid") {
			$count = preg_match ('/^[a-z]{2,4}[0-9]{0,3}$/', $value, $matches);
		}
		else if ($name == "pass") {
			$count = preg_match ('/^([a-z]|[A-Z]|[0-9]|[?*]){3,5}$/', $value, $matches);
		}
		else if ($name == "amount") {
			$count = preg_match ('/^(([\+-]?([1-9]\d{0,3}|0{1,2})(\.(\d{2})?)?)|([\+-]?(\.(\d{2})?)))$/', $value, $matches);
			//Break down
			/*^(
					([\+-]?([1-9]\d{0,3}|0{1,2}) (\.(\d{2})?)) //starting with numbers before decimal
					| //OR
					([\+-]?(\.(\d{2})?)) //starting with a decimal
				)$
				//had to add the a somewhat redundant '|' to account that the plus/minus can 
				//be before the decimal or number but not both
			*/
		}
		else{ $count = -1; } //no regex
		
		//handle regex failure
		if ($count == 0) {
			$flag = false;
			echo "Regex violated in $name, please type in valid $name.<br>";
			return "invalid";
		}
		return $value;
	}

	//verify username exists in db
	function verifyUsername($ucid) {
		
		global $db;

		$sql = "SELECT * 
					FROM users 
					WHERE ucid='$ucid'";

		( $t = mysqli_query($db, $sql) ) or die( mysqli_error($db) );
		
		$num = mysqli_num_rows($t);

		if ($num==0){ return false; }
		else { return true; }
	}

	//authenticate the pass and ucid in db
	function authenticate($ucid, $pass) {
		global $db;

		$sql = "SELECT * 
					FROM users 
					WHERE ucid='$ucid'";	
		( $t = mysqli_query($db, $sql) ) or die( mysqli_error($db) );
		
		$r = mysqli_fetch_array($t, MYSQLI_ASSOC);
		$hash = $r['hash'];
		
		if (password_verify($pass, $hash)){
			$_SESSION["logged"] = true;
			$_SESSION["ucid"] = $ucid;
			return true;
		} else { return false; }
	}

	//retrieve user's email
	function retrieveUserEmail($ucid) {
		global $db;

		$sql = "SELECT email 
					FROM users 
					WHERE ucid='$ucid'";
		( $emailrow = mysqli_query($db, $sql) ) or die( mysqli_error($db) );
		
		$num = mysqli_num_rows($emailrow);

		if ($num==0){ return "Error: email cannot be retrieved"; }
		else { 
			$email = mysqli_fetch_row ($emailrow)[0];
			return $email; 
		}
	}

	//retrieve all the accounts a user has
	function retrieveAccounts ($ucid) {
		global $db;

		$sql = "SELECT  ucid, account, balance 
					FROM accounts
					WHERE ucid='$ucid'";
		( $accounts = mysqli_query($db, $sql) ) or die( mysqli_error($db) );

		return $accounts;
	}
	
	//retrieve all the transactions in a user's account
	function retrieveTransactions ($ucid,$account) {
		global $db;
		
		$sql = "SELECT amount, timestamp, mail  
					FROM transactions 
					WHERE ucid = '$ucid' and account = '$account' 
					ORDER BY timestamp DESC";
		( $transactions = mysqli_query($db, $sql) ) or die( mysqli_error($db) );

		return $transactions;
	}
	
	//display a number of transactions in each of users accounts in a table
	function displayUserTransactions ($ucid, $num) {
		global $db;
		$accounts = retrieveAccounts($ucid);

		//Loop thru accounts
		while (   $acc = mysqli_fetch_row ($accounts)) {
			echo "<h3><b>Account #: " . $acc [1] . " Balance: $" . $acc [2] . "</b></h3><p>Ordered by <i>most recent</i></p>";

			//get transactions for account
			$transactions = retrieveTransactions ($ucid,$acc [1]);

			echo "<br><table>";
			echo "<th>Amount</th><th>Timestamp</th><th>Email Reciept</th>";

			//loop thru transactions $num times or when there are no more rows
			$count=0;
			while ($count < $num AND $transaction = mysqli_fetch_row ($transactions)) {
				echo "<tr><td><i>    $" . $transaction [0] . "</i></td>";
				echo "<td><i>      " . $transaction [1] . "</i></td>";
				echo "<td><i> mail copy: " . $transaction [ 2] . "</i></td></tr>";
				$count+=1;
			}
			echo "</table>";
		}
		echo "<hr>";
	}
	
	//clear all transactions in particular account
	function clearAccount ($ucid, $account) {
		global $db;

		//update account
		$sql = "UPDATE accounts 
					SET balance = 0.00, recent = '0000-01-01 00:00:01'
					WHERE ucid = '$ucid' and account = '$account'";
		mysqli_query($db, $sql) or die( mysqli_error($db));

		//delete transaction	
		$sql = "DELETE FROM transactions
					WHERE ucid = '$ucid' and account = '$account'";
		($d=mysqli_query($db, $sql)) or die( mysqli_error($db));

		return mysqli_affected_rows($db);
	}
	
	//add a transaction to a specific account
	function transact ($ucid, $account, $amount, $mail){
		global $db;
		
		//check accounts exists
		$sql = "SELECT *
					FROM accounts 
					WHERE ucid = '$ucid' and account = '$account'";
		(mysqli_query($db, $sql)) or die( mysqli_error($db));
		
		if(mysqli_affected_rows($db) > 0) {
			//update account balance
			$sql = "UPDATE accounts 
						SET balance = balance + $amount, recent = NOW()
						WHERE ucid = '$ucid' and account = '$account' and (balance + '$amount'>= 0.0)";
			(mysqli_query($db, $sql)) or die( mysqli_error($db));
			
			//ensure no overdraft and insert new transaction
			if(mysqli_affected_rows($db) > 0) {
				$sql = "INSERT INTO transactions (ucid, account, amount, timestamp, mail) 
							VALUES ('$ucid','$account','$amount',NOW(),'$mail')";
				(mysqli_query($db, $sql)) or die( mysqli_error($db));

				return "success"; //flag successful transact
			}
			else{return "overdraft";} //flag unsuccessful transact
		}
		else {return "notaccount";}
	}

	# ------------------------- BODY STARTS ------------------------- #

	#----------------------------  FOOTER  ---------------------------- #
	mysqli_close($db);
?>
