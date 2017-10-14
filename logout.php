<?php
	session_start();
	if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_fname']))
	{
		header("Location: index.php");
		exit();
	}
	session_unset();
	header("Location: home.php");
	exit();