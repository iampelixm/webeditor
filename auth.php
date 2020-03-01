<?php

if(!empty($_POST['login']) && !empty($_POST['password']))
{
    if(is_file('.shadow'))
    {
        $shadow=file_get_contents('.shadow');
        $s_pair=explode(':', $shadow);
    }
    else
    {
        die('Авторизация не настроена');
    }
    
    $login=$_POST['login'];
    $pass=hash('sha512',$_POST['password']);

    if($login === $s_pair[0])
    {
        if($pass === trim($s_pair[1]))
        {
            session_destroy();
            session_commit();
            session_start();
            $_SESSION['REMOTE_ADDR']=$_SERVER['REMOTE_ADDR'];
            $_SESSION['AUTHED']=1;
            header('Location: index.php');
        }
        else
        {
            session_destroy();
            session_commit();
            header('Location: login.php');
            die();
        }
    }
    else
    {
        session_destroy();
        session_commit();
        header('Location: login.php');
        die();
    }
}
else
{
    session_destroy();
    session_commit();
    header('Location: login.php');
    die();    
}
?>
