<?php
header('Cache-Control: no-cache');
$dir=$_POST['file'];

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$dir))
{
    echo 'ALREADY EXISTS';
    return '';
}

mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$dir);
?>
