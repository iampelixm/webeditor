<?php
    require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>PelixMs ONLINE code editor</title>
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ext-modelist.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<div class="container-fluid pt-2 pb-2">
<?php
	if(isset($_GET['file']))
	{
		echo $_GET['file'];
	}
?>
<button class="btn btn-primary btn-sm" onclick="$('#files').show(200)">choose file</button>

<select name="path" id="path" style="width: 100px;">
    <option value="/">/</option>
    <?PHP
        $listed=[];
	    $file_arr=explode('/', $_GET['file']);
	    array_pop($file_arr);
	    $c_dir=implode('/',$file_arr);
	    $listed[]=$c_dir;
	    echo '<option value="'.$c_dir.'" selected>'.$c_dir.'</option>'; 
		listdir($_SERVER['DOCUMENT_ROOT'].$c_dir, $_SERVER['DOCUMENT_ROOT'],'',$listed);
		listdir($_SERVER['DOCUMENT_ROOT'].'', $_SERVER['DOCUMENT_ROOT'],'',$listed);
    ?>
</select>
<button class="btn btn-success btn-sm" onclick="tarDir()">BACKUP DIR</button>
<button class="btn btn-warning btn-sm" onclick="moveFile()">-> MOVE</button>
<input type="text" name="filename" id="filename">
<button class="btn btn-primary btn-sm" onclick="addDir()">+dir</button>
<button class="btn btn-primary btn-sm" onclick="addFile()">+file</button>

<button class="btn btn-primary btn-sm" onclick="$('#dropBox').show(200)">DropBox</button>
<button class="btn btn-info btn-sm" onclick="updateEditor()">UPDATE</button>
<a class="btn btn-primary btn-sm" href="mysql.php">MySQL</a>
</div>
<div id="editor"></div>

<div id="files" class="fileswindow" onclick="$(this).hide(200)">
	<div class="filescontent" onclick="event.stopPropagation();">
	<?php
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
    var current_file='<?php echo $_GET['file']; ?>';
</script>

<script src="script.js">
</script>    

<script>
var editor = ace.edit("editor");
var file_content='';

$.get('get.php', {file: current_file})
    .done(function(data)
    	{
    		editor.setValue(data);
    	}
    );    

var modelist = ace.require("ace/ext/modelist");
var mode = modelist.getModeForPath(current_file).mode;
editor.setTheme("ace/theme/monokai");
editor.session.setMode(mode);
editor.commands.addCommand({
    name: "save",
    bindKey: {win: "Ctrl-s", mac: "Command-s"},
    exec: function(editor) {
		$.post('save.php', {file: current_file, data: editor.getValue()})
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
