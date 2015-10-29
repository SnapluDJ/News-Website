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

			$storyName = $_GET['storyName'];
			$forPostStoryName = urlencode($storyName);

			require 'database.php';
			
			//show news content
			$stmt = $mysqli->prepare("select storyName, storyContent from stories where storyName=?");
			if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
			}
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

			if (!empty($_SESSION['username'])){

				$username = $_SESSION['username'];
				$token = $_SESSION['token'];

				//show comments that the user posted
				echo "Your comment:";
				$stmt = $mysqli->prepare("select comment from comments where storyName = ? and username = ?");
				if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->bind_param('ss', $storyName, $username);
				$stmt->execute();
				$stmt->bind_result($varForComment);

				while ($stmt->fetch()) {
					printf('<p>
								%s
							</p>',
							htmlspecialchars($varForComment)
					);

					$comment = urlencode($varForComment);
					
					printf("<form action='" .htmlentities($_SERVER["PHP_SELF"]). " 'method='POST'>
								<input type='hidden' name='token' value=%s />
								<input type='hidden' name='comment' value=%s />
								<input type='hidden' name='storyName' value=%s />

								<label><textarea rows='1' name='newComment' required='required' placeholder='Edit comment'></textarea></label>
								
								<input type='submit' name='edit' value='edit' />
							</form>",
							htmlentities($token),
							htmlentities($comment),
							htmlentities($forPostStoryName)
					);

					printf("<form action='" .htmlentities($_SERVER["PHP_SELF"]). " 'method='POST'>
								<input type='hidden' name='token' value=%s />
								<input type='hidden' name='comment' value=%s />
								<input type='hidden' name='storyName' value=%s />

								<input type='submit' name='delete' value='delete this comment' />
							</form>",
							htmlentities($token),
							htmlentities($comment),
							htmlentities($forPostStoryName)
					);	
				}

				$stmt->close();

				//process edit option
				if (isset($_POST['edit'])) {
					//CSRF check
					if ($_SESSION['token'] != $_POST['token']) {
						die("Request forgery detected");
					}

					$username = $_SESSION['username'];
					$comment = urldecode($_POST['comment']);
					$storyName = urldecode($_POST['storyName']);
					$newComment = $_POST['newComment'];

					$stmt = $mysqli->prepare("update comments set comment = ? where username=? and comment=? and storyName=?");
					if (!$stmt) {
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
					$stmt->bind_param('ssss', $newComment, $username, $comment, $storyName);
					$stmt->execute();

					$stmt->close();
					
					header("Location: homepage.php");
					exit;
				}

				//process delete option
				if (isset($_POST['delete'])) {
					//CSRF check
					if ($_SESSION['token'] != $_POST['token']) {
						die("Request forgery detected");
					}

					$username = $_SESSION['username'];
					$comment = urldecode($_POST['comment']);
					$storyName = urldecode($_POST['storyName']);

					$stmt = $mysqli->prepare("delete from comments where username=? and comment=? and storyName=?");
					if (!$stmt) {
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
					$stmt->bind_param('sss', $username, $comment, $storyName);
					$stmt->execute();

					$stmt->close();

					header("Location: homepage.php");
					exit;
				}

				//show comments that other posted
				echo "<br/>";
				echo "Other comments:";
				$stmt = $mysqli->prepare("select username, comment from comments where storyName = ? and username <> ?");
				if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->bind_param('ss', $storyName, $username);
				$stmt->execute();
				$stmt->bind_result($varForUser, $varForComment);

				while ($stmt->fetch()) {
					printf('<p>
								%s---%s
							</p>',
							htmlspecialchars($varForUser),
							htmlspecialchars($varForComment)
					);
				}

				$stmt->close();

				//comment form for registered user
				printf("<form action='" .htmlentities($_SERVER["PHP_SELF"]). "' method='POST'>
							<input type='hidden' name='storyName' value= %s />
							<input type='hidden' name='token' value=%s />

							<label><textarea rows='3' name='comment' placeholder='Input your comment' required='required' placeholder='Commment'></textarea></label>

							<p>
								<input type='submit' name='submit' value='submit' />
								<input type='reset' />
							</p>
						</form>",
						htmlentities($forPostStoryName),
						htmlentities($token)
				);

				if (isset($_POST['submit'])) {
					//CSRF check
					if ($_SESSION['token'] != $_POST['token']) {
						die("Request forgery detected");
					}

					$storyName = urldecode($_POST['storyName']);
					$username = $_SESSION['username'];
					$comment = $_POST['comment'];

					$stmt = $mysqli->prepare("insert into comments (username, comment, storyName) values (?, ?, ?)");
					if (!$stmt) {
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}

					$stmt->bind_param('sss', $username, $comment, $storyName);
					$stmt->execute();

					$stmt->close();

					header("Location: homepage.php");
					exit;
				}
			} else {
				session_destroy();

				echo "Commment:";
				//show all comments for unregistered user
				$stmt = $mysqli->prepare("select username, comment from comments where storyName = ?");
				if (!$stmt) {
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->bind_param('s', $storyName);
				$stmt->execute();
				$stmt->bind_result($varForUser, $varForComment);

				while ($stmt->fetch()) {
					printf('<p>
								%s---%s
							</p>',
							htmlspecialchars($varForUser),
							htmlspecialchars($varForComment)
					);
				}

				$stmt->close();

				//comment form for unregistered user
				printf("<form action='" .htmlentities($_SERVER["PHP_SELF"]). "' method='POST'>
							<input type='hidden' name='storyName' value= %s />
							<input type='hidden' name='username' value='unRegistered' />

							<label><textarea rows='3' name='comment' placeholder='Input your comment' required='required'></textarea></label>

							<p>
								<input type='submit' name='submit' value='submit' />
								<input type='reset' />
							</p>
						</form>",
						htmlentities($forPostStoryName)
				);

				if (isset($_POST['submit'])) {
					$storyName = urldecode($_POST['storyName']);
					$username = $_POST['username'];
					$comment = $_POST['comment'];

					$stmt = $mysqli->prepare("insert into comments (username, comment, storyName) values (?, ?, ?)");
					if (!$stmt) {
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}

					$stmt->bind_param('sss', $username, $comment, $storyName);
					$stmt->execute();

					$stmt->close();

					header("Location: index.php");
					exit;
				}
			}
		?>
	</body>
</html>