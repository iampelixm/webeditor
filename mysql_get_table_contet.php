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
else
{
    return '';
}

$table=$_POST['table'];
if(!$table)
{
    return '';
}

$CONN=mysqli_connect($mysql_settings[0],$mysql_settings[2],$mysql_settings[3],$mysql_settings[1]);

$res=mysqli_query($CONN,'select * from '.$table);
echo '
<table class="table table-borderless table-dark">';
$t_has_caption=0;
$keys='';
$all_data=[];
while($line=mysqli_fetch_assoc($res))
{
    $all_data[]=$line;
    if(!$t_has_caption)
    {
        $t_has_caption=1;
        $keys=array_keys($line);
        echo '
        <thead>
            <tr>';
            echo '
                <th scope="col">
                E
                </th>
            ';    
        foreach ($keys as $k)
        {
            echo '
                <th scope="col">
                '.$k.'
                </th>
            ';
        }
        echo '
            </tr>
        </thead>
        <tbody>';
    }
    
    echo '
    <tr>';
    echo '
    <td>
        <button class="btn btn-success" onclick="editRow('.(count($all_data)-1).');">E</button>
    </td>';    
    foreach ($keys as $k)
    {
        echo '
        <td field="'.$k.'">'.$line[$k],'</td>';
    }
    echo '
    </tr>';
}

echo '
</tbody>
</table>
';

echo "
<script>

var table_data=JSON.parse('".json_encode($all_data)."');
</script>
";
?>