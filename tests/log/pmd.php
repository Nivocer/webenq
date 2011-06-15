<?php

$xml = simplexml_load_file("pmd.xml");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>PHP Mess Detector</title>
	<style type="text/css">
	  body {
	    font-family: "Corbel", "Myriad Pro", "Calibri", "Verdana", "Helvetica", "Arial", sans-serif;
	    text-align: center;
	  }
	  
	  table {
	    font-size: 13px;
	    text-align: left;
	    margin: 0 auto;
	  }
	  
	  table th {
	    min-width: 100px;
	    padding: 5px;
	  }
	  
	  table tbody tr:nth-child(2n) td {
	    background-color: #EEE;
	  }
	  
	  table thead th {
	    background-color: #444;
	    color: #FFF;
	    padding: 5px 10px;
	    text-align: center;
	  }
	  
	  table thead th:first-child {
	    -moz-border-radius-topleft: 10px;
	  }
	  
	  table thead th:last-child {
	    -moz-border-radius-topright: 10px;
	  }
	  
	  table td {
	    padding: 3px 10px;
	    border-right: 1px solid #999;
	    border-left: 1px solid #999;
	  }
	  
	  table td.right {
	    text-align: right;
	  }
	  
	  table {
	    border-collapse: collapse;
	    border-bottom: 1px solid #999;
	  }
	  
	  ul, ul li {
	    list-style: none;
	    margin: 0;
	    padding: 0;
	  }
	  
	  .file-name {
	    text-align: center;
	    font-size: 16px;
	    padding: 7px;
	    margin: 0;
	  }
	  
	  .file {
	      margin: 10px 0;
	  }
	  
	  .ok {
	      background-color: #8CCA7C;
	  }
	</style>
</head>

<body>
  <h1>PHP Mess Detector</h1>
  
  <ul id="files">
  <?php foreach($xml->file as $file): ?>
    <li class="file">
      <div class="file-name"><?php echo $file['name']; ?></div>
      <?php if(count($file->violation)): ?>
      <table class="errors">
        <thead>
          <tr>
            <th>Priority</th>
            <th>Line</th>
            <th>To Line</th>
            <th>Class</th>
            <th>Method</th>
            <th>Violation</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($file->violation as $violation): ?>
          <tr class="error">
            <td class="right"><?php echo $violation['priority']; ?></td>
            <td class="right"><?php echo $violation['line']; ?></td>
            <td class="right"><?php echo $violation['to-line']; ?></td>
            <td><?php echo $violation['class']; ?></td>
            <td><?php echo $violation['method']; ?></td>
            <td><?php echo $violation; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  </ul>
</body>
</html>