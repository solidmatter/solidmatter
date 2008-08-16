<?php
include("../Validator.class.php");
$v = new Validator();
$v->execute();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>Validator Test page</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<body>

<p>
Welcome to my 
<? print("dynamic"); ?>
 HTML page that would 
<? print("not"); ?> 
<?= "validate in source form!" ?>

<br />
</p>
</body>
</html>

