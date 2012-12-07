<?php

$xml = simplexml_load_file("log/phpcpd.xml");

$duplications = array();
foreach ($xml->duplication as $duplication) {
//  $duplication['name'] = substr($file['name'], strpos($file['name'], 'Joobsbox/'));
  $duplications[] = $duplication;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>PHP Copy paste detector Report</title>
	<style type="text/css">
	  body {
	    font-family: "Corbel", "Myriad Pro", "Calibri", "Verdana", "Helvetica", "Arial", sans-serif;
	  }

	  h1 {
	    text-align: center
	  }
	  .file-name {
	    font-size: 16px;
	    padding: 0px;
	    margin: 0;
	  }

	  .duplication {
	      margin: 10px 0;
	  }
	  .codefragment {
	     background-color: #121212;
	  }
	</style>
</head>

<body>
  <h1>PHP Copy Paste  Report</h1>
  <?php echo "<center>number of duplications: ". count($duplications) ."</center>"?>

  <?php foreach($duplications as $duplication): ?>
    <hr>
    <?php foreach ($duplication->file as $file): ?>
      <div class="file-name"><?php echo $file['path'] ." - line:". $file['line']; ?></div>
    <?php endforeach;?>
  <?php echo "<pre>$duplication->codefragment</pre>" ?>
  <?php endforeach; ?>
</body>
</html>
 
