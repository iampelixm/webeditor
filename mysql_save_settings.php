<?php
require_once('functions.php');
?>
<?php

$str=$_POST['host']."\n".$_POST['database']."\n".$_POST['user']."\n".$_POST['password'];

file_put_contents('.mysql_settings', $str);

?>