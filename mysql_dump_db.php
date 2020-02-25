<?php
require_once('functions.php');
?>
<?php

$mysql_settings=[];
if(file_exists('.mysql_settings'))
{
    $mysqlset=file_get_contents('.mysql_settings');
    $mysql_settings=explode("\n",$mysqlset);
    /*
    структура файла настроек:
    хост
    база данных
    имя пользователя
    пароль
    */
}
$CONN='';

if(!empty($mysql_settings[0]))
{
    $CONN=mysqli_connect($mysql_settings[0],$mysql_settings[2],$mysql_settings[3],$mysql_settings[1]);
}
else
{
    return '';
}

if(!$CONN)
{
    return '';
}

exec('mysqldump --user='.$mysql_settings[2].' --password='.$mysql_settings[3].' --host='.$mysql_settings[0].' '.$mysql_settings[1].' > '.$mysql_settings[1].'_dump.sql');
echo $mysql_settings[1].'_dump.sql';
return '';
?>