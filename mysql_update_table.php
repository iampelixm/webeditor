<?php
require_once('functions.php');
?>
<?php

$table=$_POST['table'];
$data=$_POST['data'];

if(!$table)
{
    return '';
}

if(!$data)
{
    return '';
}

#CONNECTION
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

#PREPARING QUERY

#step 1 определяем ключевые поля
$res=mysqli_query($CONN, 'describe '.$table);

$key_fields=[];
while($line = mysqli_fetch_assoc($res))
{
    if(strtolower($line['Key']) == 'pri')
    {
        $key_fields[]=$line['Field'];
    }
}

#step 2 получем запись по ключевому полю и сверяем что изменилось а что нет
#с парами проще строить запрос
$key_fields_pairs=[];
foreach ($key_fields as $kf)
{
    $key_fields_pairs[]=$kf .'='."'".$data[$kf]."'";
}

$QUERY='
    SELECT 
        * 
    FROM 
        '. $table . ' 
    WHERE 
        '.
        implode(' AND ', $key_fields_pairs)
        .'
    ';
    
if(!$res)
{
    return ' Ну пиздец '.mysqli_error($CONN);
}
$res=mysqli_query($CONN, $QUERY);

if(mysqli_num_rows($res) != 1)
{
    return 'не могу обновить запись, по ключу найдено более одной записи в таблице(или не найдено вообще). Если прям очень нужно - воспользуйся ручным запросом';
}
$current_rec=mysqli_fetch_assoc($res);
if(!$current_rec)
{
    return 'INTERNAL DATA ERROR';
}
#step 3 формируем итоговый запрос и заодно фильтруем данные, мало ли чего левого приперло к нам
#тут по хорошему нужно еще проверять типы данных, но это уже ту мач получается

$update_data=[];

foreach($current_rec as $k=>$v)
{
    if(isset($data[$k]))
    {
        if($data[$k] != $v)
        {
            if(!empty($data[$k.'_function']))
            {
                $update_data[]=$k . ' = ' . $data[$k.'_function']."('" . $data[$k] . "')";
            }
            else
            {
                $update_data[]=$k . ' = ' . "'" . $data[$k] . "'";
            }
        }
    }
}


if(!$update_data)
{
    return '';
}

$QUERY='
    UPDATE '.$table.'
    SET
        '.
        implode(', ', $update_data)
        .'
    WHERE
        '.
        implode(' AND ', $key_fields_pairs)
        .'
    ';

$res=mysqli_query($CONN,$QUERY);

if(!$res)
{
    echo 'ERR: '.mysqli_error($CONN);
    return '';
}

?>