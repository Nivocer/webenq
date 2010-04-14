<?php
	ob_start();
	system('phpunit');
//	system('phpunit --testdox');
	$output = ob_get_clean();
?>

<a href="log/coverage">coverage log</a>

<pre>
	<?php echo htmlspecialchars($output); ?>	
</pre>