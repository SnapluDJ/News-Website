<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<?php
			session_start();
			
			if ($_SESSION['token'] != $_POST['token']) {
					die("Request forgery detected");
			}

			$username = $_SESSION['username'];
			$storyName = urldecode($_POST['storyName']);

			require 'database.php';

			//delete record in links table
			$stmt = $mysqli->prepare("delete from links where username = ? and storyName = ?");
			if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
			}
			$stmt->bind_param('ss', $username, $storyName);
			$stmt->execute();

			//delete record in stories table
			$stmt = $mysqli->prepare("delete from stories where storyName = ?");
			if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
			}
			$stmt->bind_param('s', $storyName);
			$stmt->execute();

			//delete all comment in comments table
			$stmt = $mysqli->prepare("delete from comments where storyName = ?");
			if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
			}
			$stmt->bind_param('s', $storyName);
			$stmt->execute();

			$stmt->close();

			header("Location: homepage.php");
			exit;
		?>
	</body>
</html>