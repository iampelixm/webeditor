<?php 
header('Content-type: text/plain');
header('Cache-Control: no-cache');
if (file_exists($_SERVER['DOCUMENT_ROOT'].$_GET['file'])) 
{
    if(!is_dir($_SERVER['DOCUMENT_ROOT'].$_GET['file']))
    {
        readfile($_SERVER['DOCUMENT_ROOT'].$_GET['file']);
    }
} else 
{
    echo 'not found';
}
?>