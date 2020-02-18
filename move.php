<?php
header('Cache-Control: no-cache');

$file=$_POST['file'];
$target=$_POST['target'];

if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$file))
{
    echo 'FILE NOT FOUND';
    return '';
}
$filename=basename($file);
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$target.'/'.$filename))
{
    echo 'TARGET ALREADY EXISTS. FILE WAS NOT MOVED';
    return '';
}


rename($_SERVER['DOCUMENT_ROOT'].'/'.$file, $_SERVER['DOCUMENT_ROOT'].'/'.$target.'/'.$filename);
?>
