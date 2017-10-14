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
			echo "<link media = 'only screen and (max-device-width: 500px)' id = 'style' rel = 'stylesheet' type = 'text/css' href = 'mobile_style_".$theme.".css'>";
		?>
		<?php
			if (isset($_GET['upload']) || isset($_GET['newFolder']))
				echo "<style> .failRegisterLogin{display : initial;} </style>";
		?>
		<script type = "text/javascript" src = "jquery.js"></script>
	</head>
	<body>
		<div class = "overlay" id = "overlay" onclick = "popup()"></div>

		<div class = "header" data-style = "header2">

			<a href = "home.php"><img data-style = "logo2" class = "logo" src = "logo.png"></a>
				<form name = "search" action = "home.php" method = "GET">
					<input data-style = 'searchBar' type = "text" class = "search" name = "query" placeholder = "Search Storage">
					<button name = "search" type = "submit" value = "true" class = "login" data-style = "searchButton">Search</button>
				</form>
				
			<div class = "profileLogo" onclick = "popup('accountSidedown', 'X')" id = "off">
				<center> 
					<?php
						echo $_SESSION['user_fname'][0];
					?>
				</center>
			</div>
			<div class = "directoryTree">
				<img data-style = 'menuIcon' src = "homeIcon.png">&nbsp&nbsp<a href = "folder.php?next=0"><div class = "login" data-style = "dBtn">Home</div></a>

				<?php
					include_once 'db_conn.php';
					$folderid = $_SESSION['folder_id'];
					$tree = array();
					while(true)
					{
						if ($folderid == 0)
							 break;
						$sql = "SELECT * FROM files_table WHERE file_id = '$folderid'";
						$result = mysqli_query($conn, $sql);
						if (mysqli_num_rows($result) > 0)
						{
							$file = mysqli_fetch_assoc($result);
							array_push($tree, "<a href = 'folder.php?next=".$file['file_id']."'><div class = 'login' data-style = 'dBtn'>".$file['user_filename']."</div></a>");
							$folderid = $file['folder_id'];
						}
					}

					for ($i = count($tree)-1; $i >= 0; $i--)
					{
						echo "<div class = 'gtn'> > </div>".$tree[$i];
					}
				?>

			</div>
		</div>

		<br>

		<div class = "wrapper">

			<div class = "sideMenu">
				<center>
					<?php
						if ($_SESSION['folder_id'] == 0)
							echo "<div class = 'login' data-style = 'backBdeactive'>BACK</div>";
						else
							echo "<a href = 'folder.php?back=true'><div class = 'login' data-style = 'backBactive'>BACK</div></a>";
					?>
					<div class = "option" onclick = "popup('uploadPage', '')">Upload</div>
					<div class = "option" onclick = "popup('newFolder', '')">New Folder</div>
					<?php
						if (isset($_SESSION['cut']) && $_SESSION['cut'] != null)
							echo "<div onclick = \"window.location.href = 'folder.php?paste=".$_SESSION['cut']."'\" class = 'option'>Paste</div><br>";
						else
							echo "<div style = 'opacity: 0.2' class = 'option'>Paste</div><br>";
						$percent = ($_SESSION['user_totalUsed'] / (500*1024*1024)) * 100;
						echo "<div style = 'font-size:10px;color:lightgrey;font-weight:bold;position:absolute;left:50;bottom:50'>".round($percent, 2)."% Used Of 500MB</div>";
					?>
				</center>
			</div>

			<div class = "files">
				<?php
					include_once 'db_conn.php';
					$userid = $_SESSION['user_id'];
					$folderid = $_SESSION['folder_id'];

					if (isset($_GET['search']) && isset($_GET['query']) && $_GET['query'] != null)
					{
						$q = mysqli_real_escape_string($conn, $_GET['query']);
						$sql = "SELECT * FROM files_table WHERE user_id = '$userid' AND trash != 1 AND (user_filename REGEXP '$q')";
						echo "<div class = 'filesAreaText'> Search results for '".$q."'</div>";
						$rows = mysqli_query($conn, $sql);
						$num = mysqli_num_rows($rows);
					}
					else
					{
						$sql = "SELECT * FROM files_table WHERE user_id = '$userid' AND trash != 1 AND folder_id = '$folderid'";
						$rows = mysqli_query($conn, $sql);
						$num = mysqli_num_rows($rows);
						if ($num == 0)
							echo "<div class = 'filesAreaText'> No Files Here </div>";
					}

					for ($i = 0; $i < $num; $i++){
						$file = mysqli_fetch_assoc($rows);
						if (isset($_SESSION['cut']) && $file['file_id'] == $_SESSION['cut'])
							$mod = "'opacity: 0.5'";
						else
							$mod = "'opacity: 1'";
						if (!strcmp($file['file_type'], 'file'))
							echo "<a href = 'home.php?file=".$file['file_id']."'><div style = ".$mod." oncontextmenu = \"window.location.href = 'home.php?file=".$file['file_id']."'; return false;\" class = 'icon' id = '".$file['file_id']."'><img data-style = 'filesAreaIcon' src = 'fileIcon.png'>".$file['user_filename']."</div></a>";
						else
							echo "<a href = 'folder.php?next=".$file['file_id']."'><div style = ".$mod." oncontextmenu = \"window.location.href = 'home.php?folder=".$file['file_id']."'; return false;\" class = 'icon' id = '".$file['file_id']."'><img data-style = 'filesAreaIcon' src = 'folderIcon.png'>".$file['user_filename']."</div></a>";
					}

					mysqli_close($conn);
				?>
			</div>

		</div>

		<div id = "accountSidedown" data-status = "false" class = "dropSide">
			<center>
				<a href = "home.php"><img height = "100" width = "100" src = "logo.png"></a> <br> <br> <hr> <br> <br>
				<div class = "option" onclick = "window.location.href = 'account.php'"><img data-style = 'menuIcon' src = "settingsIcon.png">Settings</div> <br>
				<div class = "option" onclick = "window.location.href = 'trash.php'"><img data-style = 'menuIcon' src = "trashIcon.png">Trash</div> <br>
				<div class = "option" onclick = "window.location.href = 'logout.php'"><img data-style = 'menuIcon' src = "logoutIcon.png">Logout</div> <br>
			</center>
		</div>

		<div class = "loginPage" data-status = "false" id = "uploadPage">
			<h2 class = "loginH">UPLOAD</h2> <hr> <br>
			<div class = "failRegisterLogin">
				<?php
					if (isset($_GET['upload']) && !strcmp($_GET['upload'], 'invalid'))
						echo "This file type is not allowed!";
					if (isset($_GET['upload']) && !strcmp($_GET['upload'], 'limitExceeded'))
						echo "Max limit exceeded!";
				?>
			</div> <br> <br>

			<form name = "uploadForm" action = "upload.php?upload=file" method = "POST" enctype = "multipart/form-data" onsubmit = "return validateFile()">
				<input name = "user_file" id = "file" class = "inputfile" type = "file" required>
				<center><label for = "file"><div class = "loginButton"><div id = "uploaded" style = "padding-top: 6px">Choose file</div></div></label></center> <br> <br>
				<button type = "submit" name = "fileUp" class = "loginButton">Upload</button>
			</form>

		</div>

		<div class = "loginPage" data-status = "false" id = "fileOptions">
			<h2 class = "loginH">File Options</h2> <hr> <br>
			<?php
				echo "<a href = 'folder.php?trashid=".$_GET['file']."'><button class = 'loginButton'>Delete</button></a> <br> <br> <br>";
				echo "<a href = 'download.php?id=".$_GET['file']."'><button class = 'loginButton' style = 'margin-left: -5px'>Download</button></a><br><br><br>";
				echo "<a href = 'folder.php?cut=".$_GET['file']."'><button class = 'loginButton'>Cut</button></a> <br> <br> <br>";
			?>
		</div>

		<div class = "loginPage" data-status = "false" id = "folderOptions">
			<h2 class = "loginH">File Options</h2> <hr> <br>
			<?php
				echo "<a href = 'folder.php?trashid=".$_GET['folder']."'><button class = 'loginButton'>Delete</button></a> <br> <br> <br>";
				echo "<a href = 'folder.php?cut=".$_GET['folder']."'><button class = 'loginButton'>Cut</button></a> <br> <br> <br>";
			?>
		</div>
		<div class = "loginPage" data-status = "false" id = "newFolder">
			<h2 class = "loginH">New Folder</h2> <hr> <br>

			<div class = "failRegisterLogin">
				<?php
					if (isset($_GET['newFolder']) && $_GET['newFolder'] == 'fail')
						echo "Name already exists!";
					else if (isset($_GET['newFolder']) && $_GET['newFolder'] == 'failTrash')
						echo "A file of the same name already exists in this directory (in the trash)";
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