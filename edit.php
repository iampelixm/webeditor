<!DOCTYPE html>
<html lang="en">
<head>
<title>PelixMs ONLINE code editor</title>
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />

<link rel="stylesheet" href="style.css">
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
<a href="mysql.php">MySQL</a>
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
