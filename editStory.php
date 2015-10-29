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
			$forPostStoryName = $_POST['storyName'];
			$storyName = urldecode($_POST['storyName']);

			require 'database.php';

			//display original news
			echo "<h2>Original News:</h2>";
			
			$stmt = $mysqli->prepare("select storyName, storyContent from stories where storyName=?");
			$stmt->bind_param('s', $storyName);
			$stmt->execute();
			$stmt->bind_result($varForStory, $varForContent);

			while ($stmt->fetch()) {
				printf('<h1>
							%s
						</h1>

						<p>
							%s
						</p>',
						htmlspecialchars($varForStory),
						htmlspecialchars($varForContent)
				);
			}

			$stmt->close();
		?>

		<h2>Edit this news:</h2>

		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />

			<input type="hidden" name="storyName" value="<?php echo $forPostStoryName;?>">

			<p>
				<label>Title:<textarea cols="37" row="1" name="newStoryName" placeholder="Type news title" required="required"></textarea></label>
			</p>

			<p>
				<label>News:<textarea cols="77" rows="17" name="newStoryContent" placeholder="Write Your news Here" required="required"></textarea></label>
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

				$storyName = urldecode($_POST['storyName']);
				$newStoryName = $_POST['newStoryName'];
				$newStoryContent = $_POST['newStoryContent'];

				require 'database.php';

				//update links table
				$stmt = $mysqli->prepare("update links set storyName = ? where username = ?");
				if (!$stmt) {
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
				}
				$stmt->bind_param('ss', $newStoryName, $username);
				$stmt->execute();

				$stmt->close();

				//update stories table
				$stmt = $mysqli->prepare("update stories set storyName = ?, storyContent = ? where storyName = ?");
				if (!$stmt) {
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
				}
				$stmt->bind_param('sss', $newStoryName, $newStoryContent, $storyName);
				$stmt->execute();

				$stmt->close();

				header("Location: homepage.php");
				exit;
			}
		?>
	</body>
</html>