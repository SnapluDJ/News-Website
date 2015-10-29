<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<h1 id="login_h1">Login</h1>

		<form id="login_form" method="POST", action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<p>
				<label>Username:<input type="text" name="username" placeholeder="Input your username" /></label>
			</p>

			<p>
				<label>Password:<input type="password" name="password" /></label>
			</p>

			<p>
				<input type="submit" name="submit" value="submit" />
				<input type="reset" />
			</p>	
		</form>

		<?php
			if (isset($_POST['submit'])) {

				$username = $_POST['username'];
				$pwd_guess = $_POST['password'];

				require 'database.php';

				$stmt = $mysqli->prepare("select count(*), password, token from userinfo where username = ?");
				if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$stmt->bind_param('s', $username);
				$stmt->execute();

				//Bind the results
				$stmt->bind_result($cnt, $pwd_hash, $identity);
				$stmt->fetch();
				$stmt->close();

				//check wether the user is valid
				if ($cnt == 1 && crypt($pwd_guess, $pwd_hash) == $pwd_hash) {
					session_start();
					$_SESSION['username'] = $username;
					$_SESSION['token'] = $identity;
					header("Location: homepage.php");
					exit;
				} else {
					printf('<p id="login_message">
								Wrong username or password
				 			</p>'
					);
				}		
			}			
		?>
	</body>
</html>