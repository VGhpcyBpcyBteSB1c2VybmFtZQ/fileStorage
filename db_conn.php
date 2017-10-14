<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "db_drp";

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}

	function reloadSession($connection)
	{
		$userid = $_SESSION['user_id'];
		$sql = "SELECT * FROM user_table WHERE user_id = '$userid'";
		$result = mysqli_query($connection, $sql);
		if (mysqli_num_rows($result) == 1)
		{
			$row = mysqli_fetch_assoc($result);
			$_SESSION['user_fname'] = $row['user_fname'];
			$_SESSION['user_lname'] = $row['user_lname'];
			$_SESSION['user_theme'] = $row['user_theme'];
			$_SESSION['user_email'] = $row['user_email'];
			$_SESSION['user_totalUsed'] = $row['user_totalUsed'];
		}
	}