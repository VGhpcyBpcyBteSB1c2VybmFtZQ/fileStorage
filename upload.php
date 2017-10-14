<?php
	session_start();
	if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_fname']))
	{
		header("Location: index.php");
		exit();
	}

	if (isset($_GET['upload']) && !strcmp($_GET['upload'], 'file'))
	{
		$name = explode('.', $_FILES['user_file']['name']);
		$ext = end($name);
		//////////////// CHECK EXTENSION ////////////////
		if ($ext == 'php' || $ext == 'js' || $ext == 'PHP' || $ext == 'JS')
		{
			header('Location: home.php?upload=invalid');
			exit();
		}
		if ($_FILES['user_file']['size'] > (20*1024*1024) || $_FILES['user_file']['size'] == 0)
		{
			header('Location: home.php?upload=limitExceeded');
			exit();
		}

		include_once 'db_conn.php';

		if (!is_dir("../../user_data_files"))
			mkdir("../../user_data_files");

		$path = '../../user_data_files/'.$_SESSION['user_id'];
		$userid = mysqli_real_escape_string($conn, $_SESSION['user_id']);
		$filename = mysqli_real_escape_string($conn, $_FILES['user_file']['name']);
		$folderid = mysqli_real_escape_string($conn, $_SESSION['folder_id']);
		$filesize = mysqli_real_escape_string($conn, $_FILES['user_file']['size']);
		$newTotalUsed = $_SESSION['user_totalUsed'] + $filesize;

		if ($newTotalUsed > (500*1024*1024))
		{
			header('Location: home.php?upload=limitExceeded');
			mysqli_close($conn);
			exit();
		}

		if (!is_dir($path))
			mkdir($path);

		//////////// CHECK IF NAME EXISTS /////////////////////
		$name = (string)'^'.preg_replace('/\.[^.]+$/', "", $filename);
		$sql = "SELECT user_filename FROM files_table WHERE (user_filename REGEXP '$name') AND folder_id = '$folderid'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0){
			$filename = preg_replace('/\.[^.]+$/', '('.mysqli_num_rows($rows).').'.$ext, $filename);
		}
		///////////////////////////////////////////////////////

		$sql = "INSERT INTO files_table (user_id, user_filename, folder_id, file_type, file_size, trash) VALUES ('$userid', '$filename', '$folderid', 'file', '$filesize', 0)";
		mysqli_query($conn, $sql);
		//////////////// ADD PATH /////////////////////////////
		$id = mysqli_insert_id($conn);
		$filepath = mysqli_real_escape_string($conn, $path."/".$id);
		$sql = "UPDATE files_table SET user_filepath = '$filepath' WHERE file_id = '$id'";
		mysqli_query($conn, $sql);
		///////////////////////////////////////////////////////

		///////////////// UPDATE SIZE /////////////////////
		$sql = "UPDATE user_table SET user_totalUsed = '$newTotalUsed' WHERE user_id = '$userid'";
		echo $filesize." ".$newTotalUsed;
		mysqli_query($conn, $sql);

		reloadSession($conn);
		///////////////////////////////////////////////////
		mysqli_close($conn);
		move_uploaded_file($_FILES['user_file']['tmp_name'], $filepath);
		header("Location: home.php");
		exit();
	}
	else if (isset($_GET['upload']) && !strcmp($_GET['upload'], 'folder') && isset($_POST['folderCreate']))
	{
		include_once 'db_conn.php';
		$filename = mysqli_real_escape_string($conn, $_POST['folder_name']);
		$folderid = $_SESSION['folder_id'];
		$userid = $_SESSION['user_id'];
		/// CHECK IF NAME ALREADY EXISTS ///
		$sql = "SELECT trash FROM files_table WHERE user_id = '$userid' AND user_filename = '$filename' AND folder_id = '$folderid'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0)
		{
			$file = mysqli_fetch_assoc($rows);
			mysqli_close($conn);
			if ($file['trash'] == 1)
				header("Location: home.php?newFolder=failTrash");
			else
				header("Location: home.php?newFolder=fail");
			exit();
		}
		//////////////////////////////////

		$sql = "INSERT INTO files_table (user_id, user_filename, folder_id, file_type) VALUES ('$userid', '$filename', '$folderid', 'folder')";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		header("Location: home.php");
	}