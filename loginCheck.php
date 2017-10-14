<?php
	session_start();

	if (!isset($_POST['email']) || !isset($_POST['pass']))
	{
		header("Location: index.php");
		exit();
	}

	include_once 'db_conn.php';

	$email = $_POST['email'];
	$pass = $_POST['pass'];

	$sql = "SELECT * FROM user_table WHERE user_email = '$email'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1)
	{
		$row = mysqli_fetch_assoc($result);
		if (password_verify($pass, $row['user_pwd']))
		{
			$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['user_fname'] = $row['user_fname'];
			$_SESSION['user_lname'] = $row['user_lname'];
			$_SESSION['user_theme'] = $row['user_theme'];
			$_SESSION['user_email'] = $row['user_email'];
			$_SESSION['user_totalUsed'] = $row['user_totalUsed'];
			$_SESSION['folder_id'] = 0;
			header("Location: home.php");
		}
		else
			header("Location: index.php?login=error");
	}
	else
		header("Location: index.php?login=noUser");

	mysqli_close($conn);