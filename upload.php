<?php

if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/'.$_POST['dir'].'/'.basename($_FILES['file']['name']))) {
    echo 'OK: '.$_SERVER['DOCUMENT_ROOT'].'/'.$_POST['dir'];
} else
{
    echo 'falied: '.$_SERVER['DOCUMENT_ROOT'].$_POST['dir'].basename($_FILES['file']['name']);
}
?>