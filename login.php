<?php
include_once ("db.php");

if (!isset($_SESSION['logged'])) {
	$_SESSION['logged'] = "-";
	}

$mail = $_POST['email'];
$pass = $_POST['pass'];	

if ($_POST['LoggingIn'] == "true"){
	$query = "SELECT email,password,fullname,afm FROM pharmacists
		WHERE email="."'".$mail."'"." AND password="."'".$pass."'".";";
	$results = mysql_query($query); 
	$row=mysql_fetch_row($results);
	echo $query;
	if ($row[0] != ''){
			session_start();
			$_SESSION['logged'] = 'in';
			$_SESSION['valid'] = '1';
			$_SESSION['id']=$row[0];
			$_SESSION['name'] = $row[2];	
			$_SESSION['userCode']=$row[3];		
	}
	else {
		$Message = urlencode("Δεν έχετε δικαίωμα πρόσβασης. Παρακαλώ ελέγξτε τα στοιχεία σας και ξαναπροσπαθήστε.");
		header("Location:index.php?Message={$Message}");
		$a=$_POST['LoggingIn'];
		unset($a);
	}
}	
if ($_SESSION['logged'] == "in"){
	header("Location: index.php?option=medicines");
}
	
if ($_POST['logout']=="true"){
	session_destroy();
	unset($_SESSION['logged']);
	unset($_SESSION['valid']);
	unset($_SESSION['id']);
	unset($_SESSION['name']);	
	unset($_SESSION['userCode']);
	header("Location: index.php");
	exit;
}
?>			
