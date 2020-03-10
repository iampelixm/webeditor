<?php
require_once('functions.php');
?>

<?php


$dir=$_POST['dir'];

if($dir)
{
    echo "<div>$dir</div>";
    showdir($dir, $_SERVER['DOCUMENT_ROOT'].'/', 1,1,0);
}
?>