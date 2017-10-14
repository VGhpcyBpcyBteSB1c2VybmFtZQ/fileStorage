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
		<title>Settings</title>
		<?php
			if ($_SESSION['user_theme'] == 'dark' || $_SESSION['user_theme'] == 'space')
				$theme = "dark";
			else
				$theme = "light";
			echo "<link id = 'style' rel = 'stylesheet' type = 'text/css' href = 'style_".$theme.".css'>";
		?>
		<?php
			if (isset($_GET['accountUpdate']))
				echo "<style> .failRegisterLogin{display : initial;} </style>";
		?>
		<script type = "text/javascript" src = "jquery.js"></script>
	</head>
	<body>
		<div class = "overlay" id = "overlay" onclick = "popup()"></div>

		<div class = "header" style = "height: 120px">

			<a href = "home.php"><img class = "logo" src = "logo.png"></a>
				<form action = "home.php" method = "GET">
					<input type = "text" class = "search" name = "query" placeholder = "Search Storage">
					<button name = "search" value = "true" type = "submit" class = "login" style = "margin-left: 15px; font-weight: bold; font-size: 15; width: 100px">Search</button>
				</form>
			<div class = "profileLogo" onclick = "popup('accountSidedown', 'X')" id = "off">
				<center> 
					<?php
						echo $_SESSION['user_fname'][0];
					?>
				</center>
			</div>

			<div class = "directoryTree">
				<img data-style = 'menuIcon' width = "20" height = "20" src = "settingsIcon.png">&nbsp&nbsp<a href = 'account.php'><div class = "login" data-style = "dBtn">Settings</div></a>
				<?php
					if(isset($_GET['settings']) && !strcmp($_GET['settings'], 'account'))
						$tagID = "Account";
					else if(isset($_GET['settings']) && !strcmp($_GET['settings'], 'theme'))
						$tagID = "Appearance";
					else if(isset($_GET['settings']) && !strcmp($_GET['settings'], 'storage'))
						$tagID = "Storage";
					else
						$tagID = "Account";
					echo "<div class = 'gtn'> > </div><div class = 'login' data-style = 'dBtn'>".$tagID."</div>";
				?>
			</div>
		</div>

		<br>

		<div class = "wrapper">

			<div class = "sideMenu">
				<center>
					<br><br>
					<div class = "option" onclick = "window.location.href = 'account.php?settings=account'">Account</div> 
					<div class = "option" onclick = "window.location.href = 'account.php?settings=theme'">Appearance</div>
					<div class = "option" onclick = "window.location.href = 'account.php?settings=storage'">Storage</div>
				</center>
			</div>

			<div class = "files">

				<center><div id = "accountSettings" style = "margin-top: 40px; display:none">
					<?php
						if (isset($_GET['accountUpdate']) && !strcmp($_GET['accountUpdate'], 'fail'))
							$msg = "<div class = 'failRegisterLogin'> Invalid Password </div> <br> <br>";
						else
							$msg = null;
						 echo "<form name ='registerForm' method = 'POST' action = 'accountUpdate.php' onsubmit = 'return validateRegister()'>
									<input type = 'text' name = 'fname' placeholder = 'First Name' value='".$_SESSION['user_fname']."' required><br> <br>
									<input type = 'text' name = 'lname' placeholder = 'Last Name' value='".$_SESSION['user_lname']."' required><br> <br>
									<input type = 'text' name = 'email' placeholder = 'Email' value='".$_SESSION['user_email']."' required><br> <br>
									<input type = 'password' name = 'oldPass' placeholder='Old Password' required> <br> <br>
									<input type = 'password' name = 'newPass' placeholder='New Password' required> <br> <br>".$msg."
									<button class = 'signup' type = 'submit'> Save </button>
								</form>";
					?>
				</div></center>

				<center><div id = "themeSettings" style = "margin-top: 50px; display:none">
					
					<h1 class = "themeText"> Choose a theme </h1> <br> <br>
					<button class = "login" style = "font-weight:bold;width:300px" onclick = "window.location.href = 'accountUpdate.php?theme=light'"> Light </button><br> <br>
					<button class = "login" style = "font-weight:bold;width:300px" onclick = "window.location.href = 'accountUpdate.php?theme=dark'"> Dark </button><br> <br>
					<button class = "login" style = "font-weight:bold;width:300px" onclick = "window.location.href = 'accountUpdate.php?theme=space'"> Space </button><br> <br>
					
				</div></center>

				<center><div id = "storageSettings" style = "margin-top: 50px; display:none">
					<?php
						$length = ($_SESSION['user_totalUsed'] / (500*1024*1024)) * 350;
						echo "<div class = 'progressWindow'><div style = 'width: ".$length."px' class = 'progressBar'></div></div> <br> <br>
							<div class = 'pallette' style = 'background-color: #4286f4'></div><div class = 'themeText'> Used Space: <b>".round(($_SESSION['user_totalUsed']/1024/1024), 2)."MB</b></div><br> <br>
							<div class = 'pallette' style = 'background-color: white'></div><div class = 'themeText'> Free Space: <b>".round(500 - ($_SESSION['user_totalUsed']/1024/1024), 2)."MB</b></div><br> <br>
							<div class = 'themeText'> Total Space: <b>500MB</b></div><br> <br>
							<div class = 'themeText'> Max Upload Size: <b>20MB</b></div><br> <br>";
					?>
				</div></center>
				
			</div>

		</div>

		<div id = "accountSidedown" data-status = "false" class = "dropSide">
			<center>
				<a href = "home.php"><img height = "100" width = "100" src = "logo.png"></a> <br> <br> <hr> <br> <br>
				<div class = "option" onclick = "window.location.href = 'home.php'"><img data-style = 'menuIcon' width = "20" height = "20" src = "HomeIcon.png">Home</div> <br>
				<div class = "option" onclick = "window.location.href = 'trash.php'"><img data-style = 'menuIcon' width = "20" height = "20" src = "trashIcon.png">Trash</div> <br>
				<div class = "option" onclick = "window.location.href = 'logout.php'"><img data-style = 'menuIcon' width = "20" height = "20" src = "logoutIcon.png">Logout</div> <br>
			</center>
		</div>
		<?php
			if($_SESSION['user_theme'] == 'space')
				echo "<canvas id='can'></canvas>";
		?>
	</body>
	<script type = "text/javascript" src = "script.js"></script>
	<?php
		if(isset($_GET['settings']) && !strcmp($_GET['settings'], 'account'))
			$tagID = "\"#accountSettings\"";
		else if(isset($_GET['settings']) && !strcmp($_GET['settings'], 'theme'))
			$tagID = "\"#themeSettings\"";
		else if(isset($_GET['settings']) && !strcmp($_GET['settings'], 'storage'))
			$tagID = "\"#storageSettings\"";
		else
			$tagID = "\"#accountSettings\"";
		echo "<script type = 'text/javascript'> $(".$tagID.").show();</script>";
	?>
</html>