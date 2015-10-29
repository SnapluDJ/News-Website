<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<p id="logout">	
			<a href="logout.php">Logout</a>
		</p>

		<p>
			<a href="friend.php">Friend</a>
		</p>

		<?php
			session_start();
			
			$token = $_SESSION['token'];

			//visit homepage when logout, direct to index.php
			$username = $_SESSION['username'];
			if (!$username) {
				session_destroy();
				header("Location: index.php");
				exit;
			}
			
			echo "<h1>Hello, $username!</h1>";
			echo "<h2>News List</h2>";

			require 'database.php';

			//list stories you posted
			echo "News you posted:";
			$stmt = $mysqli->prepare("select username, storyName from links where username = ?");
			if(!$stmt) {
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

			$stmt->bind_param('s', $username);
			$stmt->execute();
			$stmt->bind_result($nameForUser, $nameForStory);

			echo "<ul>\n";
			while ($stmt->fetch()) {
				printf("\t<li>
							<a href='story.php?storyName=%s'>%s</a>
						</li>\n",
						htmlspecialchars($nameForStory),
						htmlspecialchars($nameForStory)
				);

				$forPostStoryName = urlencode($nameForStory);

				//edit or delete story you posted
				printf('<form action="editStory.php" method="POST">
							<input type="hidden" name="token" value=%s />
							<input type="hidden" name="storyName" value=%s />
							<label><input type="submit" value="Edit" /></label>
						</form>

						<form action="deleteStory.php" method="POST">
							<input type="hidden" name="token" value=%s />
							<input type="hidden" name="storyName" value=%s />
							<label><input type="submit" value="Delete" /></label>
						</form>',
						htmlentities($token),
						htmlspecialchars($forPostStoryName),
						htmlentities($token),
						htmlspecialchars($forPostStoryName)
				);
			}
			echo "</ul>\n";

			$stmt->close();

			//list stories other people posted
			echo "News other posted:";
			$stmt = $mysqli->prepare("select username, storyName from links where username <> ?");
			if(!$stmt) {
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

			$stmt->bind_param('s', $username);
			$stmt->execute();
			$stmt->bind_result($nameForUser, $nameForStory);

			echo "<ul>\n";
			while ($stmt->fetch()) {
				printf("\t<li>
							<a href='story.php?storyName=%s'>%s</a>
							---%s
						</li>\n",
						htmlspecialchars($nameForStory),
						htmlspecialchars($nameForStory),
						htmlspecialchars($nameForUser)
				);
			}
			echo "</ul>\n";

			$stmt->close();
		?>

		<p>Post News:</p>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />

			<p>
				<label>Title:<textarea cols="37" row="1" name="storyName" placeholder="Type news title"></textarea></label>
			</p>

			<p>
				<label>News:<textarea cols="77" rows="17" name="storyContent" placeholder="Write Your news Here"></textarea></label>
			</p>

			<p>
				<input type="submit" name="submit" value="submit" />
				<input type="reset" />
			</p>

		</form>

		<?php
			if (isset($_POST['submit'])) {

				//CSRF detection
				if ($_SESSION['token'] != $_POST['token']) {
					die("Request forgery detected");
				}

				$storyName = $_POST['storyName'];
				$storyContent = $_POST['storyContent'];

				//insert into stories table
				$stmt = $mysqli->prepare("insert into stories (storyName, storyContent) values (?,?)");
				$stmt->bind_param('ss', $storyName, $storyContent);
				$stmt->execute();

				//insert into links table
				$stmt = $mysqli->prepare("insert into links (username, storyName) values (?,?)");
				$stmt->bind_param('ss', $username, $storyName);
				$stmt->execute();

				$stmt->close();

				header("Location: homepage.php");
				exit;
			}
		?>
	</body>
</html>