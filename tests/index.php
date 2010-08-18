<?php
	ob_start();
	system('phpunit');
	$output = ob_get_clean();
	$output = nl2br($output);
?>
<html>
	<head>
		<style type="text/css">
			body {
				font: verdana 10px;
			}
		</style>
	</head>
	<body>
		<p>
			<a href="log/coverage">coverage log</a>
		</p>
		<div>
			<?php echo $output; ?>
		</div>
	</body>
</html>