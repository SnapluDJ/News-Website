<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<h1 id="register_h1">Register</h1>

		<form id="register_form" method="POST", action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
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
				$password = $_POST['password'];

				//check username and make sure it is alphanumeric with limited other characters
				if (!preg_match('/^[\w_\.\-]+$/', $username)) {
					echo "Invalid username";
					exit;
				}

				require 'database.php';

				//check if this username already exits
				$stmt = $mysqli->prepare("select username from userinfo");
				if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->execute();
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					if ($username == $row["username"]) {
						printf('<p id="register_message">
								Existing username, please change!
							</p>'
						);
						$stmt->close();
						exit;
					}
				}
				$stmt->close();

				//create new user
				session_start();
				$_SESSION['username'] = $username;
				$token = substr(md5(rand()), 0, 10);
				$_SESSION['token'] = $token;

				$cryptPassword = crypt($password);

				$stmt = $mysqli->prepare("insert into userinfo (username, password, token) values (?,?,?)");
				if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->bind_param('sss', $username, $cryptPassword, $token);
				$stmt->execute();
				$stmt->close();

				header("Location: homepage.php");
				exit;
			}
		?>
	</body>
</html>