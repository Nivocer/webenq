<?php
    $base = $_SERVER['REQUEST_URI'];
    $end  = $base[strlen($base)-1];
    if ($end == '/') {
        $base = substr($base, 0, -1);
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<title>Webenq Test Zone</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<style type="text/css">
	   body {
	       font-size: 16px;
	       font-family: Myriad Pro, Corbel, Arial, sans-serif;
	   }
	   
	   .main a {
	       display: block;
	       padding: 3px;
	   }
	   
	   .main a:hover {
	       background-color: #FFF2BD;
	   }
	   
	   .main {
	       width: 300px;
	       padding: 10px;
	       text-align: center;
	       border: 3px solid #EEE;
	       margin: auto;
	       margin-top: 10%;
	   }
	</style>
</head>
<body>
	<div class="main">
	    <h1>Test Zone</h1>
	    <a href="<?php echo $base ?>/log/testdox.html">Test suite report</a>
	    <a href="<?php echo $base ?>/log/report/">Code coverage report</a>
	    <a href="<?php echo $base ?>/log/pdepend.php">PDepend report</a>
	    <a href="<?php echo $base ?>/log/phpcs.php">Code Sniffer report</a>
	    <a href="<?php echo $base ?>/log/pmd.php">Mess Detector report</a>
	</div>
</body>
</html>