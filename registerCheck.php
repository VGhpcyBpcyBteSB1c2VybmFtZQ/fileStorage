<?php
	include_once 'db_conn.php';
	session_start();

	if (!isset($_POST['email']) || !isset($_POST['pass']) || !isset($_POST['fname']) || !isset($_POST['lname']))
	{
		header("Location: index.php");
		exit();
	}

	$fname = mysqli_real_escape_string($conn, ucwords(strtolower($_POST['fname'])));
	$lname = mysqli_real_escape_string($conn, ucwords(strtolower($_POST['lname'])));
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

	$sql = "SELECT * FROM user_table WHERE user_email = '$email'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0)
	{
		header("Location: index.php?register=fail");
		exit();
	}

	$sql = "INSERT INTO user_table (user_fname, user_lname, user_email, user_pwd, user_theme, user_totalUsed) VALUES ('$fname', '$lname', '$email', '$pass', 'light', 0)";

	if (mysqli_query($conn, $sql)){
	    header("Location: home.php");
	    $_SESSION['user_id'] = mysqli_insert_id($conn);
		$_SESSION['user_fname'] = $fname;
		$_SESSION['user_lname'] = $lname;
		$_SESSION['user_theme'] = 'light';
		$_SESSION['user_email'] = $email;
		$_SESSION['user_totalUsed'] = 0;
		$_SESSION['folder_id'] = 0;
	}
	else
		header("Location: index.php");

	mysqli_close($conn);