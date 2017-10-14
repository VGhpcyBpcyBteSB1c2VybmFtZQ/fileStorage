<?php
	session_start();
	if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_fname']))
	{
		header("Location: index.php");
		exit();
	}

	if(isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']))
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$sql = "SELECT user_pwd FROM user_table WHERE user_id = '$userid'";
		$rows = mysqli_query($conn, $sql);
		$pass = mysqli_fetch_assoc($rows)['user_pwd'];
		/////////// Verify Password ///////////////
		if (!password_verify($_POST['oldPass'], $pass))
		{
			mysqli_close($conn);
			header("Location: account.php?accountUpdate=fail");
			exit();
		}
		else
		{
			$fname = mysqli_real_escape_string($conn, $_POST['fname']);
			$lname = mysqli_real_escape_string($conn, $_POST['lname']);
			$email = mysqli_real_escape_string($conn, $_POST['email']);
			$pass = password_hash($_POST['newPass'], PASSWORD_DEFAULT);
			$sql = "UPDATE user_table SET user_fname = '$fname', user_lname = '$lname', user_email = '$email', user_pwd = '$pass' WHERE user_id = '$userid'";
			mysqli_query($conn, $sql);
			reloadSession($conn);
			mysqli_close($conn);
			header("Location: account.php");
		}
	}
	else if (isset($_GET['theme']) && $_GET['theme'] != null)
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$theme = mysqli_real_escape_string($conn, $_GET['theme']);
		$sql = "UPDATE user_table SET user_theme = '$theme' WHERE user_id = '$userid'";
		mysqli_query($conn, $sql);
		reloadSession($conn);
		mysqli_close($conn);
		header("Location: account.php?settings=theme");
	}