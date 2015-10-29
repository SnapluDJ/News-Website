<!DOCTYPE html>
<html lang="en">
	<head>
		<title>News Web Site</title>
		<meta charset="utf-8" />
	</head>

	<body>
		<?php
			session_start();
			session_destroy();
			clearstatcache();
			header("Location: index.php");
			exit;
		?>
	</body>
</html>