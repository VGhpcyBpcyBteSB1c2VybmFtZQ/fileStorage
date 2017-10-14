<?php
	session_start();
	if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_fname']))
	{
		header("Location: index.php");
		exit();
	}
?>

<html>
	<head>
		<title>Home</title>
		<?php
			if ($_SESSION['user_theme'] == 'dark' || $_SESSION['user_theme'] == 'space')
				$theme = "dark";
			else
				$theme = "light";
			echo "<link id = 'style' rel = 'stylesheet' type = 'text/css' href = 'style_".$theme.".css'>";
		?>
		<?php
			if (isset($_GET['upload']) || isset($_GET['newFolder']))
				echo "<style> .failRegisterLogin{display : initial;} </style>";
		?>
		<script type = "text/javascript" src = "jquery.js"></script>
	</head>
	<body>
		<div class = "overlay" id = "overlay" onclick = "popup()"></div>

		<div class = "header" style = "height: 120px">

			<a href = "home.php"><img class = "logo" src = "logo.png"></a>
				<form action = "trash.php" method = "GET">
					<input type = "text" class = "search" name = "query" placeholder = "Search Trash">
					<button name = "search" type = "submit" value = "true" class = "login" style = "margin-left: 15px; font-weight: bold; font-size: 15; width: 100px">Search</button>
				</form>
				
			<div class = "profileLogo" onclick = "popup('accountSidedown', 'X')" id = "off">
				<center> 
					<?php
						echo $_SESSION['user_fname'][0];
					?>
				</center>
			</div>
			<div class = "directoryTree">
				<img data-style = 'menuIcon' width = "20" height = "20" src = "trashIcon.png">&nbsp&nbsp<a href = "folder.php?emptyTrash=true"><div class = "login" data-style = "dBtn">Empty Trash</div></a>
			</div>
		</div>

		<br>

		<div class = "wrapper">

			<div class = "sideMenu"></div>

			<div class = "files">
				<?php
					include_once 'db_conn.php';
					$userid = $_SESSION['user_id'];

					if (isset($_GET['search']) && isset($_GET['query']) && $_GET['query'] != null)
					{
						$q = mysqli_real_escape_string($conn, $_GET['query']);
						$sql = "SELECT * FROM files_table WHERE user_id = '$userid' AND trash = 1 AND (user_filename REGEXP '$q')";
						echo "<div class = 'filesAreaText'> Search results for '".$q."'</div>";
						$rows = mysqli_query($conn, $sql);
						$num = mysqli_num_rows($rows);
					}
					else
					{
						$sql = "SELECT * FROM files_table WHERE user_id = '$userid' AND trash = 1";
						$rows = mysqli_query($conn, $sql);
						$num = mysqli_num_rows($rows);
						if ($num == 0)
							echo "<div class = 'filesAreaText'> Trash is empty </div>";
					}

					for ($i = 0; $i < $num; $i++){
						$file = mysqli_fetch_assoc($rows);
						if (isset($_SESSION['cut']) && $file['file_id'] == $_SESSION['cut'])
							$mod = "'opacity: 0.5'";
						else
							$mod = "'opacity: 1'";
						if (!strcmp($file['file_type'], 'file'))
							echo "<a href = 'trash.php?file=".$file['file_id']."'><div style = ".$mod." oncontextmenu = \"window.location.href = 'trash.php?file=".$file['file_id']."'; return false;\" class = 'icon' id = '".$file['file_id']."'><img src = 'fileIcon.png' wdith = '90' height = '90'>".$file['user_filename']."</div></a>";
						else
							echo "<a href = 'trash.php?folder=".$file['file_id']."'><div style = ".$mod." oncontextmenu = \"window.location.href = 'trash.php?folder=".$file['file_id']."'; return false;\" class = 'icon' id = '".$file['file_id']."'><img src = 'folderIcon.png' width = '90' height = '90'>".$file['user_filename']."</div></a>";
					}

					mysqli_close($conn);
				?>
			</div>

		</div>

		<div id = "accountSidedown" data-status = "false" class = "dropSide">
			<center>
				<a href = "home.php"><img height = "100" width = "100" src = "logo.png"></a> <br> <br> <hr> <br> <br>
				<div class = "option" onclick = "window.location.href = 'home.php'"><img data-style = 'menuIcon' width = "20" height = "20" src = "homeIcon.png">Home</div> <br>
				<div class = "option" onclick = "window.location.href = 'account.php'"><img data-style = 'menuIcon' width = "20" height = "20" src = "settingsIcon.png">Settings</div> <br>
				<div class = "option" onclick = "window.location.href = 'logout.php'"><img data-style = 'menuIcon' width = "20" height = "20" src = "logoutIcon.png">Logout</div> <br>
			</center>
		</div>

		<div class = "loginPage" data-status = "false" id = "fileOptions">
			<h2 class = "loginH">File Options</h2> <hr> <br>
			<?php
				echo "<a href = 'folder.php?delid=".$_GET['file']."'><button class = 'loginButton'>Delete Permanently</button></a> <br> <br> <br>";
				echo "<a href = 'folder.php?restoreid=".$_GET['file']."'><button class = 'loginButton' style = 'margin-left: -5px'>Restore</button></a><br><br><br>";
			?>
		</div>

		<div class = "loginPage" data-status = "false" id = "folderOptions">
			<h2 class = "loginH">File Options</h2> <hr> <br>
			<?php
				echo "<a href = 'folder.php?fdelid=".$_GET['folder']."'><button class = 'loginButton'>Delete Permanently</button></a> <br> <br> <br>";
				echo "<a href = 'folder.php?restoreid=".$_GET['folder']."'><button class = 'loginButton'>Restore</button></a> <br> <br> <br>";
			?>
		</div>
		<div class = "loginPage" data-status = "false" id = "newFolder">
			<h2 class = "loginH">New Folder</h2> <hr> <br>

			<div class = "failRegisterLogin">
				<?php
					if (isset($_GET['newFolder']) && !strcmp($_GET['newFolder'], 'fail'))
						echo "Name already exists!";
				?>
			</div> <br> <br>

			<form name = "folderForm" action = "upload.php?upload=folder" method = "POST">
				<input name = "folder_name" type = "text" required value = "New Folder"> <br> <br>
				<button type = "submit" name = "folderCreate" class = "loginButton">Create</button>
			</form>
		</div>
		<?php
			if($_SESSION['user_theme'] == 'space')
				echo "<canvas id='can'></canvas>";
		?>
	</body>
	<script type = "text/javascript" src = "script.js"></script>
	<?php
		if (isset($_GET['upload']))
			echo "<script type = 'text/javascript'> popup('uploadPage', ''); </script>";
		else if (isset($_GET['file']) && $_GET['file'] != null)
			echo "<script type = 'text/javascript'> popup('fileOptions', ''); </script>";
		else if (isset($_GET['folder']) && $_GET['folder'] != null)
			echo "<script type = 'text/javascript'> popup('folderOptions', '') </script>";
		else if (isset($_GET['newFolder']) && $_GET['newFolder'] != null)
			echo "<script type = 'text/javascript'> popup('newFolder', '') </script>";
	?>
</html>