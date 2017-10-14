<?php
	session_start();

	function delFile($id, $conn)
	{
		$userid = $_SESSION['user_id'];
		$sql = "SELECT file_size, user_filepath FROM files_table WHERE user_id = '$userid' AND file_id = '$id' AND file_type = 'file'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0)
		{
			$file = mysqli_fetch_assoc($rows);
			$filesize = $file['file_size'];
			$newTotalUsed = $_SESSION['user_totalUsed'] - $filesize;
			unlink($file['user_filepath']);
			$sql = "UPDATE user_table SET user_totalUsed = '$newTotalUsed' WHERE user_id = '$userid'";
			mysqli_query($conn, $sql);
			reloadSession($conn);
			$sql = "DELETE FROM files_table WHERE user_id = '$userid' AND file_id = '$id' AND file_type = 'file'";
			mysqli_query($conn, $sql);
		}
	}

	function delFolder($id, $conn)
	{
		$userid = $_SESSION['user_id'];
		$sql = "SELECT file_id FROM files_table WHERE user_id = '$userid' AND folder_id = '$id' AND file_type = 'file'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0)
		{
			for ($i = 0; $i < mysqli_num_rows($rows); $i++)
			{
				$file = mysqli_fetch_assoc($rows);
				delFile($file['file_id'], $conn);
			}
		}
		$sql = "SELECT file_id FROM files_table WHERE user_id = '$userid' AND folder_id = '$id' AND file_type = 'folder'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0)
		{
			for ($i = 0; $i < mysqli_num_rows($rows); $i++)
			{
				$folder = mysqli_fetch_assoc($rows);
				delFolder($folder['file_id'], $conn);
			}
		}
		$sql = "DELETE FROM files_table WHERE user_id = '$userid' AND file_id = '$id'";
		mysqli_query($conn, $sql);
		reloadSession($conn);
		return;
	}

	if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_fname']))
	{
		header("Location: index.php");
		exit();
	}
	
	if (isset($_GET['back']) && !strcmp($_GET['back'], 'true'))
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$folderid = $_SESSION['folder_id'];
		$sql = "SELECT * FROM files_table WHERE user_id = '$userid' AND file_id = '$folderid'";
		$rows = mysqli_query($conn, $sql);
		if (mysqli_num_rows($rows) > 0)
		{
			$file = mysqli_fetch_assoc($rows);
			$_SESSION['folder_id'] = $file['folder_id'];
			mysqli_close($conn);
		}
		header("Location: home.php");
	}
	else if (isset($_GET['next']) && $_GET['next'] != null)
	{
		$_SESSION['folder_id'] = $_GET['next'];
		header("Location: home.php");
	}
	else if (isset($_GET['delid']) && $_GET['delid'] != null)
	{
		include_once 'db_conn.php';
		delFile($_GET['delid'], $conn);
		mysqli_close($conn);
		header("Location: trash.php");
	}
	else if (isset($_GET['fdelid']) && $_GET['fdelid'] != null)
	{
		include_once 'db_conn.php';
		delFolder($_GET['fdelid'], $conn);
		mysqli_close($conn);
		header("Location: trash.php");
	}
	else if (isset($_GET['cut']) && $_GET['cut'] != null)
	{
		$_SESSION['cut'] = $_GET['cut'];
		header("Location: home.php");
	}
	else if (isset($_GET['paste']) && $_GET['paste'] != null)
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$fileid = $_GET['paste'];
		$folderid = $_SESSION['folder_id'];
		$sql = "UPDATE files_table SET folder_id = '$folderid' WHERE user_id = '$userid' AND file_id = '$fileid'";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		$_SESSION['cut'] = null;
		header("Location: home.php");
	}
	else if (isset($_GET['trashid']) && $_GET['trashid'] != null)
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$fileid = $_GET['trashid'];
		$sql = "UPDATE files_table SET trash = 1 WHERE user_id = '$userid' AND file_id = '$fileid'";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		header("Location: home.php");
	}
	else if (isset($_GET['restoreid']) && $_GET['restoreid'] != null)
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$fileid = $_GET['restoreid'];
		
		$sql = "SELECT folder_id FROM files_table WHERE user_id = '$userid' AND file_id = '$fileid'";
		$result = mysqli_query($conn, $sql);
		$folderid = mysqli_fetch_assoc($result)['folder_id'];
		$sql = "SELECT file_id FROM files_table WHERE user_id = '$userid' AND file_id = '$folderid' AND trash = 0";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) == 0)
		{
			$sql = "UPDATE files_table SET folder_id = 0 WHERE user_id = '$userid' AND file_id = '$fileid'";
			mysqli_query($conn, $sql);
		}


		$sql = "UPDATE files_table SET trash = 0 WHERE user_id = '$userid' AND file_id = '$fileid'";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		header("Location: trash.php");
	}
	else if (isset($_GET['emptyTrash']) && $_GET['emptyTrash'] == 'true')
	{
		include_once 'db_conn.php';
		$userid = $_SESSION['user_id'];
		$sql = "SELECT file_id, file_type FROM files_table WHERE user_id = '$userid' AND trash = 1";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0)
		{
			for ($i = 0; $i < mysqli_num_rows($result); $i++)
			{
				$file = mysqli_fetch_assoc($result);
				if ($file['file_type'] == 'folder')
					delFolder($file['file_id'], $conn);
				else
					delFile($file['file_id'], $conn);
			}
		}

		mysqli_close($conn);
		header("Location: trash.php");
	}
	else
		header("Location: index.php");