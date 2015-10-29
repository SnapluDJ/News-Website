<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
	</head>

	<body>
		
		<?php
			session_start();

			printf('<a href="homepage.php">Back</a>');
			
			$token = $_SESSION['token'];
			
			$username = $_SESSION['username'];

			require 'database.php';

			//show all your friends
			echo "<br/>";
			echo "Friend List:";
			$stmt = $mysqli->prepare("select friend from friends where username=?");
			if(!$stmt) {
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('s', $username);
			$stmt->execute();
			$stmt->bind_result($varFriend);

			echo "<ul>\n";
			while ($stmt->fetch()) {
				printf("\t<li>
							<a href='friendhomepage.php?friend=%s'>%s</a>
						</li>\n",
						htmlspecialchars($varFriend),
						htmlspecialchars($varFriend)
				);
			}
			echo "</ul>\n";

			$stmt->close();
		?>

		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
			<input type="hidden" name='token' value="<?php echo $token; ?>" />
			<label><input type='text' name='friend' required='required' /></label>
			<input type='submit' name='submit' value='Send request' />
		</form>

		<?php
			if (isset($_POST['submit'])) {
				//CSRF check
				if ($_SESSION['token'] != $_POST['token']) {
					die("Request forgery detected");
				}

				$username = $_SESSION['username'];
				$friend = $_POST['friend'];
				$friendExist = false;

				//Verify this friend exists
				$stmt = $mysqli->prepare("select username from userinfo");
				if(!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$stmt->execute();
				$stmt->bind_result($userList);

				while ($stmt->fetch()) {
					if ($friend == $userList) {
						$friendExist = true;
						break;
					}
				}

				$stmt->close();

				if ($friendExist == false) {
					echo "This user does not exist";
					exit;
				}

				//put friend name into your friend list
				$stmt = $mysqli->prepare("insert into friends (username, friend) values (?, ?)");
				if(!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$stmt->bind_param('ss', $username, $friend);
				$stmt->execute();

				$stmt->close();

				//put your name into your friend's friend list
				$stmt = $mysqli->prepare("insert into friends (username, friend) values (?, ?)");
				if(!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$stmt->bind_param('ss', $friend, $username);
				$stmt->execute();

				$stmt->close();

				header("Location: friend.php");
			}
		?>
	</body>
</html>