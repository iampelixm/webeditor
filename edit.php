<!DOCTYPE html>
<html lang="en">
<head>
<title>PelixMs ONLINE code editor</title>
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
<style type="text/css" media="screen">
    #editor { 
        position: absolute;
        top: 25px;
        right: 0;
        bottom: 0;
        left: 0;
    }
    .header
    {
    	position: absolute;
    	top: 0px;
    	left: 0px;
    	height: 24px;
    	padding-top: 2px;
    	width: 100%
    }
    .fileswindow
    {
    	position: fixed;
    	left: 0;
    	top: 0;
    	right: 0;
    	bottom: 0;
    	z-index: 2;
    	background: rgba(0,0,0,0.5);
    	display: none;
    }
    .filescontent
    {
    	position: fixed;
    	left: 200px;
    	top: 100px;
    	right: 200px;
    	bottom: 100px;
    	z-index: 3;
    	background: white;
    	overflow: auto;
    }
    
    .dircontent
    {
        display: none;
    }
    
    #dropZone {    
        color: #555;
        font-size: 18px;
        text-align: center;    
        
        width: 90%;
        padding: 50px 0;
        margin: 50px auto;
        
        background: #eee;
        border: 1px solid #ccc;
        
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }
    
    #dropZone.hover {
        background: #ddd;
        border-color: #aaa;
    }
    
    #dropZone.error {
        background: #faa;
        border-color: #f00;
    }
    
    #dropZone.drop {
        background: #afa;
        border-color: #0f0;
    }    
    
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ext-modelist.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<div class="header">
<?php
	if(isset($_GET['file']))
	{
		echo $_GET['file'];
	}
?>
<button onclick="$('#files').show(200)">choose file</button>

<select name="path" id="path" style="width: 100px;">
    <option value="/">/</option>
    <?PHP
        $listed=[];
	    $file_arr=explode('/', $_GET['file']);
	    array_pop($file_arr);
	    $c_dir=implode('/',$file_arr);
	    $listed[]=$c_dir;
	    echo '<option value="'.$c_dir.'" selected>'.$c_dir.'</option>'; 
		function listdir($dir, $root, $sep='',$listed) 
		{
		    $rel_dir=substr($dir, strlen($root));
		    
		    if(!in_array($dir, $listed))
		    {
		        $listed[]=$dir;
		        echo "<option value=\"".$rel_dir."/\">".$rel_dir."/</option>";
		    }
		    
    		$list = scandir($dir);
    		if (is_array($list)) 
    		{
        	    $list = array_diff($list, array('.', '..'));
        		if ($list) 
        		{
					foreach ($list as $name) 
        		    {
						$path = $dir . '/' . $name;
						$rel_path=substr($path, strlen($root));
						$is_dir = is_dir($path);
						if($is_dir)
						{
						    $listed[]=$path;
						    echo "<option value=\"".$rel_path."/\">".$sep.$rel_path."/</option>";
						}

						if ($is_dir)
						{
							listdir($path, $root, $sep.'-',$listed);
						}
					}
				}
			}
			else 
			{
				echo '<option>не могу прочитать</option>';
			}
		}
		listdir($_SERVER['DOCUMENT_ROOT'].$c_dir, $_SERVER['DOCUMENT_ROOT'],'',$listed);
		//listdir($_SERVER['DOCUMENT_ROOT'].'/editor', $_SERVER['DOCUMENT_ROOT'],'',$listed);
		listdir($_SERVER['DOCUMENT_ROOT'].'', $_SERVER['DOCUMENT_ROOT'],'',$listed);
//		listdir($_SERVER['DOCUMENT_ROOT'].'/service.player.bz', $_SERVER['DOCUMENT_ROOT'].'/','',$listed);
    ?>
</select>
<button onclick="moveFile()">-> MOVE</button>
<input type="text" name="filename" id="filename">
<button onclick="addDir()">+dir</button>
<button onclick="addFile()">+file</button>

<button onclick="$('#dropBox').show(200)">DropBox</button>
<button onclick="updateEditor()">UPDATE</button>
</div>
<div id="editor"></div>

<div id="files" class="fileswindow" onclick="$(this).hide(200)">
	<div class="filescontent" onclick="event.stopPropagation();">
	<?php
		function showdir($dir, $root, $showdir=1, $showfiles=1) {
    		$list = scandir($dir);
    		if (is_array($list)) {
        	$list = array_diff($list, array('.', '..'));
        		if ($list) {
					echo '<ul>';
					foreach ($list as $name) 
        		    {
						$path = $dir . '/' . $name;
						$rel_path=substr($path, strlen($root));
						$is_dir = is_dir($path);
						echo '<li class="', $is_dir ? 'dir' : 'file', '">';
						if($is_dir)
						{
						    if($showdir)
						    {
							    echo "<button onclick=$(event.target).next().toggle();>".htmlspecialchars($name)."</button>";
						    }
						}
						else
						{
						    if($showfiles)
						    {
							    echo '<span><a href="edit.php?file='.$rel_path.'">'.htmlspecialchars($name).'</a></span>';
						    }
						}

						if ($is_dir)
						{
						    echo '<div class="dircontent">';
							showdir($path, $root, $showdir, $showfiles);
							echo '</div>';
						}
						echo '</li>';
					}
					echo '</ul>';
				}
			}
			else 
			{
				echo '<i>не могу прочитать</i>';
			}
		}
		showdir($_SERVER['DOCUMENT_ROOT'].'/', $_SERVER['DOCUMENT_ROOT'].'/');	
	?>
	</div>
</div>

<div id="dropBox" class="fileswindow" onclick="$(this).hide(200)">
	<div class="filescontent" onclick="event.stopPropagation();">
	    <div id="dropBoxPath">Куда загружаем: </div>
	    <div id="dropZone">бросай файлы сюда</div>
	</div>
</div>

<script>

    function updateEditor()
    {
        if (confirm('Обновляемся?'))
        {
            $.get('update.php')
            .fail(function(data){alert('oops. falied:'+data); console.log(data);})
            .done(function(data){alert('Кажись обновились. Перезагрузи страницу'); console.log(data)});
        }
    }
    
    function dropBox()
    {
        
        
    }
    
    $(document).ready(function() {
        
        $('#path').clone().attr('id','dropPath').appendTo('#dropBoxPath');
        var dropZone = $('#dropZone'),
            maxFileSize = 90000000; // максимальный размер фалйа - 1 мб.
        
        // Проверка поддержки браузером
        if (typeof(window.FileReader) == 'undefined') {
            dropZone.text('Не поддерживается браузером!');
            dropZone.addClass('error');
        }
        
        // Добавляем класс hover при наведении
        dropZone[0].ondragover = function() {
            dropZone.addClass('hover');
            return false;
        };
        
        // Убираем класс hover
        dropZone[0].ondragleave = function() {
            dropZone.removeClass('hover');
            return false;
        };
        
        // Обрабатываем событие Drop
        dropZone[0].ondrop = function(event) {
            event.preventDefault();
            dropZone.removeClass('hover');
            dropZone.addClass('drop');
            
            var file = event.dataTransfer.files[0];
            console.log(file);
            
            fd=new FormData;
            
            fd.append('file', file);
            fd.append('dir', $('#dropPath').val());
            //file.dir='directorytouploadto';
            // Проверяем размер файла
            if (file.size > maxFileSize) {
                dropZone.text('Файл слишком большой!');
                dropZone.addClass('error');
                return false;
            }
            
            // Создаем запрос
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', uploadProgress, false);
            xhr.onreadystatechange = stateChange;
            xhr.open('POST', 'upload.php');
            xhr.send(fd);
        };
        
        // Показываем процент загрузки
        function uploadProgress(event) {
            var percent = parseInt(event.loaded / event.total * 100);
            dropZone.text('Загрузка: ' + percent + '%');
        }
        
        // Пост обрабочик
        function stateChange(event) {
            if (event.target.readyState == 4) {
                if (event.target.status == 200) {
                    dropZone.text('Загрузка успешно завершена!');
                    dropZone.text(event.target.responseText);
                } else {
                    dropZone.text('Произошла ошибка!');
                    dropZone.addClass('error');
                }
            }
        }
        
    });    
    
    
    function addDir()
    {
        var path=$('#path').val();
        var filename=$('#filename').val();

        if(filename === '' )
        {
            alert('no filename');
            return 0;
        }
        var fullname=path+'/'+filename;
		$.post('mkdir.php', {file: fullname})
		.fail(function(){alert('saving falied');})
		.done(function(response)
			{
				if(response !== '')
				{
					alert(response);
				}
			}
		);
    }
    
    function addFile()
    {
        var path=$('#path').val();
        var filename=$('#filename').val();
        if(filename === '' )
        {
            alert('no filename');
            return 0;
        }
        var fullname=path+'/'+filename;
		$.post('save.php', {file: fullname, data: ' '})
		.fail(function(){alert('saving falied');})
		.done(function(response)
			{
				if(response !== '')
				{
					alert(response);
				}
			}
		);
    }
    
    function moveFile()
    {
        var path=$('#path').val();

		$.post('move.php', {file: '<?php echo $_GET['file'] ?>', target: path})
		.fail(function(){alert('saving falied');})
		.done(function(response)
			{
				if(response !== '')
				{
					alert(response);
				}
			}
		);
    }
    
    
    var editor = ace.edit("editor");
	var file_content='';
	$.get('get.php', {file: '<?php echo $_GET['file']; ?>'})
	.done(function(data)
		{
			editor.setValue(data);
		}
	);    
    
    var modelist = ace.require("ace/ext/modelist");
    var mode = modelist.getModeForPath('<?php echo $_GET['file']; ?>').mode;
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode(mode);
    editor.commands.addCommand({
        name: "save",
        bindKey: {win: "Ctrl-s", mac: "Command-s"},
        exec: function(editor) {
			$.post('save.php', {file: '<?php echo $_GET['file']; ?>', data: editor.getValue()})
			.fail(function(){alert('saving falied');})
			.done(function(response)
				{
					if(response !== '')
					{
						alert(response);
					}
				}
			);
        }
    })
	
	editor.commands.addCommand({
        name: "showKeyboardShortcuts",
        bindKey: {win: "Ctrl-Alt-h", mac: "Command-Alt-h"},
        exec: function(editor) {
            ace.config.loadModule("ace/ext/keybinding_menu", function(module) {
                module.init(editor);
                editor.showKeyboardShortcuts()
            })
        }
    })
</script>

</body>
</html>
