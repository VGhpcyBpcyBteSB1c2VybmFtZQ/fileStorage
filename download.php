<?php
	session_start();
	
	if (isset($_GET['id']) && isset($_SESSION['user_id']) && isset($_SESSION['user_fname']))
	{
		include_once 'db_conn.php';

		$userid = $_SESSION['user_id'];
		$fileid = $_GET['id'];
		$sql = "SELECT * FROM files_table WHERE user_id = '$userid' AND file_id = '$fileid'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0)
		{
			$file = mysqli_fetch_assoc($rows);
			$path = $file['user_filepath'];
			$name = $file['user_filename'];
			mysqli_close($conn);
			header("Content-Type: application/octet-stream");
			header("Content-disposition: attachment; filename=".$name);
			readfile($path);
		}
	}
	else
		header("Location: index.php");
