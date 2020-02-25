<?php
require_once('functions.php');
?>
<?php
header('Cache-Control: no-cache');
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['file']))
{
	$fh = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['file'], 'w');
	if($fh)
	{
    	if(! fwrite($fh, $_POST['data']))
    	{
    		echo 'WRITE ERROR';
    	}
	}
	else
	{
		echo 'cant open for write '.$_SERVER['DOCUMENT_ROOT'].$_POST['file'];
	}
}
else
{
    $fh = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$_POST['file'], 'w');
    fclose($fh);
}
#WRITE FILE $FILE WIHTH DATA $DATA
?>
