<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
	</head>

	<body>
		<?php
			session_start();

			$friend = $_GET['friend'];

			printf('<a href="homepage.php">Back</a>');

			printf('<h1>
						Welcome to %s homepage
					</h1>',
					htmlentities($friend)
			);

			require 'database.php';

			$stmt = $mysqli->prepare("select storyName from links where username=?");
			if(!$stmt) {
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('s', $friend);
			$stmt->execute();
			$stmt->bind_result($storyNameForFriend);

			echo "<ul>\n";
			while ($stmt->fetch()) {
				printf("\t<li>
							<a href='story.php?storyName=%s'>%s</a>
						</li>\n",
						htmlspecialchars($storyNameForFriend),
						htmlspecialchars($storyNameForFriend)
				);
			}	
			echo "</ul>\n";

			$stmt->close();
		?>
	</body>
</html>