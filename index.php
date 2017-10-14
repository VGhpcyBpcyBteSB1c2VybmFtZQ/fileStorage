<?php
	session_start();
	if (isset($_SESSION['user_id']) || isset($_SESSION['user_fname']))
	{
		header("Location: home.php");
		exit();
	}
	$servername = "localhost";
	$username = "root";
	$password = "";

	$connCreate = mysqli_connect($servername, $username, $password);
	$sql = "CREATE DATABASE IF NOT EXISTS db_drp";
	mysqli_query($connCreate, $sql);
	mysqli_close($connCreate);
	include_once 'db_conn.php';

	$sql = 'CREATE TABLE IF NOT EXISTS `files_table` (
			  `user_id` int(11) NOT NULL,
			  `user_filename` varchar(255) NOT NULL,
			  `user_filepath` varchar(255) NOT NULL,
			  `file_id` int(11) NOT NULL AUTO_INCREMENT,
			  `folder_id` int(11) NOT NULL,
			  `file_type` varchar(255) NOT NULL,
			  `file_size` int(11) NOT NULL,
			  `trash` int(11) NOT NULL,
			  	PRIMARY KEY (`file_id`)
			);';
	mysqli_query($conn, $sql);
	$sql = 'CREATE TABLE IF NOT EXISTS `user_table` (
			  `user_id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_fname` varchar(255) NOT NULL,
			  `user_lname` varchar(255) NOT NULL,
			  `user_email` varchar(255) NOT NULL,
			  `user_pwd` varchar(255) NOT NULL,
			  `user_theme` varchar(255) NOT NULL,
			  `user_totalUsed` int(11) NOT NULL,
			  PRIMARY KEY (`user_id`)
			);';
	mysqli_query($conn, $sql);
?>

<html>
	<head>
		<title>File Storage</title>
		<link id = "style" rel = "stylesheet" type = "text/css" href = "style_light.css">
		<link media = "only screen and (max-device-width: 500px)" id = "style" rel = "stylesheet" type = "text/css" href = "mobile_style_light.css">
		<script type = "text/javascript" src = "jquery.js"></script>
		<?php
			if (isset($_GET['login']) || isset($_GET['register']))
				echo "<style> .failRegisterLogin{display : initial;} </style>";
		?>
	</head>
	<body>
		<div class = "overlay" id = "overlay" onclick = "popup()"></div>
		<div class = "header">
			<img class = "logo" src = "logo.png">
			<h1 class = "titleText">FILE STORAGE</h1>
			<div class = "text"> Already have an account? <div onclick = "popup('loginPage', '')" class = "login">Login</div> </div> 
		</div>
		<br>
		<div class = "wrapper">
			<div class = "body">
				&nbspAll Your Files<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp At One Place
			</div>
			<div class = "register">
				<h1 class = "signupH"> Create An Account For Free </h1>
				<div class = "failRegisterLogin">
					<?php
						if (isset($_GET['register']) && !strcmp($_GET['register'], 'fail'))
							echo "Email already registered to an account";
					?>
				</div> <br>
				<form name = "registerForm" method = "POST" action = "registerCheck.php" onsubmit = "return validateRegister()">
					<input type = "text" name = "fname" placeholder="First Name" required><br>
					<input type = "text" name = 'lname' placeholder="Last Name" required><br>
					<input type = "text" name = 'email' placeholder="Email" required><br>
					<input type = "password" name = 'pass' placeholder="Password" required> <br> <br>
					<button class = "signup" type = "submit"> Signup For Free </button>
				</form>
			</div>
		</div> <br>
		<div class = "footer">
			<div class = "copyRight">Copyright © 2017 All Rights Reserved</div>
		</div>
		<div class = "loginPage" id = "loginPage" data-status = "false">
			<h2 class = "loginH">LOGIN</h2> <hr> <br>
			<div class = "failRegisterLogin">
				<?php
					if (isset($_GET['login']))
					{
						if (!strcmp($_GET['login'], 'error'))
							echo "Wrong email/password combination";
						else if (!strcmp($_GET['login'], 'noUser'))
							echo "No account is registered with this email";
					}
				?>
			</div> <br>
			<form name = "loginForm" action = "loginCheck.php" method = "POST" onsubmit = "return validateLogin()">
				<input name = "email" type = "text" placeholder="Email" required><br> <br>
				<input name = "pass" type = "password" placeholder="Password" required><br> <br> <br>
				<button type = "submit" class = "loginButton">Login</button>
			</form>
		</div>
	</body>
	<script type = "text/javascript" src = "script.js"></script>
	<?php
		if (isset($_GET['login']))
		{
			echo "<script type = 'text/javascript'> popup('loginPage', ''); </script>";
		}
	?>
</html>