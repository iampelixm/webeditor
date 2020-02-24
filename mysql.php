<?php
    require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>PelixMs ONLINE MESQL editor</title>
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />

<link rel="stylesheet" href="style.css">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body>
<div class="container pt-2 pb-2">
    
    <a class="btn btn-success btn-sm" href="edit.php">EDITOR</a>
    <button class="btn btn-success btn-sm" onclick="$('#tables').show(200)">TABLES</button>
    <button class="btn btn-warning btn-sm" onclick="$('#mysqlsettings').show(200)">MySQL SETTING</button>
    <button class="btn btn-warning btn-sm" onclick="$.get('mysql_dump_db.php','', function(data){downloadFile(data);})">DUMP DB</button>

</div>
<?php
    $CONN=connectMySQL();
    
    if(!$CONN)
    {
        $mysql_settings = getMySQLSettings();
        echo "<br><br><br>БЯДА, НЕ МОГУ ПОДКЛЮЧИТЬСЯ К МУСКУЛЮ<br>
            host: ". $mysql_settings[0]."<br>\n
            DB: ". $mysql_settings[2]."<br>\n
            U: ". $mysql_settings[3]."<br>\n
            P: ". $mysql_settings[1]."<br>\n
            ".mysqli_connect_error();
    }
?>
<div id="mysqlsettings" class="fileswindow" onclick="$(this).hide(200)">
	<div class="filescontent container" onclick="event.stopPropagation();">
        <form id="mysql_settings_form" action="">
          <div class="form-group">
            <label for="mysqlhost">Хост</label>
            <input name="host" value="<?php echo $mysql_settings[0] ?>" type="text" class="form-control" id="mysqlhost" aria-describedby="mysqlhosthelp" placeholder="Укажи хост куда подключаться">
            <small id="mysqlhostрудз" class="form-text text-muted">localhost в большинстве случаев</small>
          </div>
          <div class="form-group">
            <label for="mysqldb">DB</label>
            <input name="database" value="<?php echo $mysql_settings[1] ?>" type="text" class="form-control" id="mysqldb" placeholder="DATABASE NAME" aria-describedby="mysqldbhelp">
            <small id="mysqldbhelp" class="form-text text-muted">localhost в большинстве случаев</small>
          </div>
          <div class="form-group">
            <label for="mysqluser">Username</label>
            <input name="user" value="<?php echo $mysql_settings[2] ?>" type="text" class="form-control" id="mysqluser" placeholder="MySQL USER" aria-describedby="mysqluserhelp">
            <small id="mysqluserhelp" class="form-text text-muted">Имя пользователя для БД</small>
          </div>
          <div class="form-group">
            <label for="mysqlpassword">Password</label>
            <input name="password" value="<?php echo $mysql_settings[3] ?>" type="text" class="form-control" id="mysqlpassword" placeholder="MySQL PASSWORD" aria-describedby="mysqlpasswordhelp">
            <small id="mysqlpasswordhelp" class="form-text text-muted">Как ни странно - пароль!</small>
          </div>
          <button type="submit" class="btn btn-primary" onclick="event.preventDefault(); $.post('mysql_save_settings.php', $('#mysql_settings_form').serializeObject(), function(data){$('#mesqlsettings').hide(200)})">САХРАНИТЬ</button>
        </form>	    
	</div>
</div>


<div id="tables" class="fileswindow" onclick="$(this).hide(200)">
	<div class="filescontent" onclick="event.stopPropagation();">
	    <?php if($CONN): ?>
	        <?php
	            $res=mysqli_query($CONN,'show tables');
	            while($line=mysqli_fetch_array($res))
	            {
	                echo '<button class="btn btn-info m-1" onclick="getTable(\''.$line[0].'\');">'.$line[0].'</button>';
	            }
            ?>
	    <?php else: ?>
	        <h1>Не подключен к БД</h1>
	    <?php endif; ?>
	</div>
</div>

<div id="table_row" class="fileswindow" onclick="$(this).hide(200)">
	<div class="filescontent content" id="table_row_content" onclick="event.stopPropagation();">
	    <div class="p-2">
            <form onsubmit="event.preventDefault()" action="" id="table_row_content_form">
                <div id="table_row_content_form_data"></div>
                <button class="w-100 mt-5 btn btn-warning" onclick="console.log('serizlized',$('#table_row_content_form').serializeObject()); $.post('mysql_update_table.php',{'table': current_table, 'data': $('#table_row_content_form').serializeObject()}, function(data){alert(data);})">ЗАПИСАТЬ</button>    
            </form>
        </div>
	</div>
</div>

<div class="container-fluid" id="content">
    
</div>
<script src="script.js"></script>
<script>
    var current_table='';
    function getTable(table)
    {
        current_table=table;
        $('#content').empty();
        $('#content').html('гружу');
        $('#tables').hide();
        $.post('mysql_get_table_contet.php', {'table': table}, function(data)
            {
                $('#content').hide();
                $('#content').html(data);
                $('#content').show();
            });
    }
    
    
    
    function editRow(row)
    {
        var it=table_data[row];
        $('#table_row_content_form_data').empty();
        $.each(it, function(k,v)
            {
                var field=$('<div class="form-group">\
                <label for="'+k+'">'+k+'</label>\
                <select name="'+k+'_function">\
                    <option value="">AS IS</option>\
                    <option value="MD5">MD5</option>\
                    <option value="PASSWORD">PASSWORD</option>\
                    <option value="SHA1">SHA1</option>\
                </select>\
                <input name="'+k+'" value="'+v+'" type="text" class="form-control" id="'+k+'">\
                </div>');
                $('#table_row_content_form_data').append(field);
            });
        $('#table_row').show(200);
    }
</script>
</body>
</html>