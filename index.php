<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<meta charset="utf-8" />
	</head>

	<body>
		<h1 id="index_h1">Daily News</h1>

		<p id="login">
			<a href="login.php">Login</a>
		</p>
			
		<p id="register">
			<a href="register.php">Register</a>
		</p>

		<?php
			echo "<h2>News List:</h2>";

			require 'database.php';

			$stmt = $mysqli->prepare("select username, storyName from links order by storyName");
			if(!$stmt) {
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

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
	</body>
</html>