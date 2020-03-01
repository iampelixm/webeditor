<?php
require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PelixMs ONLINE code editor</title>
    <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ext-modelist.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ext-beautify.js" type="text/javascript" charset="utf-8"></script>
    
</head>
<body>
    <div class="container-fluid pt-2 pb-2">
        <?php
        if (isset($_GET['file'])) {
            echo $_GET['file'];
        }
        ?>
        <button class="btn btn-primary btn-sm" onclick="$('#files').show(200)">choose file</button>

        <select name="path" id="path" style="width: 100px;">
            <option value="/">/</option>
            <?PHP
            $listed = [];
            $file_arr = explode('/', $_GET['file']);
            array_pop($file_arr);
            $c_dir = implode('/', $file_arr);
            $listed[] = $c_dir;
            echo '<option value="'.$c_dir.'" selected>'.$c_dir.'</option>';
            listdir($_SERVER['DOCUMENT_ROOT'].$c_dir, $_SERVER['DOCUMENT_ROOT'], '', $listed);
            listdir($_SERVER['DOCUMENT_ROOT'].'', $_SERVER['DOCUMENT_ROOT'], '', $listed);
            ?>
        </select>
        <button class="btn btn-success btn-sm" onclick="tarDir()">BACKUP DIR</button>
        <button class="btn btn-warning btn-sm" onclick="moveFile()">-> MOVE</button>
        <input type="text" name="filename" id="filename">
        <button class="btn btn-primary btn-sm" onclick="addDir()">+dir</button>
        <button class="btn btn-primary btn-sm" onclick="addFile()">+file</button>

        <button class="btn btn-primary btn-sm" onclick="$('#dropBox').show(200)">DropBox</button>
        <button class="btn btn-info btn-sm" onclick="updateEditor()">UPDATE</button>
        <button class="btn btn-success btn-sm" onclick="beautify.beautify(editor.session);">BEAUTY</button>
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
            <div id="dropBoxPath">
                Куда загружаем:
            </div>
            <div id="dropZone">
                бросай файлы сюда
            </div>
            <div id="dropBoxLog"></div>
        </div>
    </div>

    <script>
        var current_file = '<?php echo $_GET['file']; ?>';
    </script>
    
    <script>
            
    function updateEditor() {
        if (confirm('Обновляемся?')) {
            $.get('update.php')
            .fail(function(data) {
                alert('oops. falied:'+data); console.log(data);
            })
            .done(function(data) {
                alert('Кажись обновились. Перезагрузи страницу'); console.log(data)});
        }
    }
    
    function addDir() {
        var path = $('#path').val();
        var filename = $('#filename').val();
    
        if (filename === '') {
            alert('no filename');
            return 0;
        }
        var fullname = path+'/'+filename;
        $.post('mkdir.php', {
            file: fullname
        })
        .fail(function() {
            alert('saving falied');
        })
        .done(function(response) {
            if (response !== '') {
                alert(response);
            }
        }
        );
    }
    
    function addFile() {
        var path = $('#path').val();
        var filename = $('#filename').val();
        if (filename === '') {
            alert('no filename');
            return 0;
        }
        var fullname = path+'/'+filename;
        $.post('save.php', {
            file: fullname, data: ' '
        })
        .fail(function() {
            alert('saving falied');
        })
        .done(function(response) {
            if (response !== '') {
                alert(response);
            }
        }
        );
    }
    
    $(document).ready(function() {
    
    
        $('#path').clone().attr('id', 'dropPath').appendTo('#dropBoxPath');
        var dropZone = $('#dropZone');
        var maxFileSize = 90000000; // максимальный размер фалйа - 1 мб.
    
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
            
            var files=event.dataTransfer.files;
            
            $.each(files, function(i,v)
                {
                    file=v;
                    $('<div id="dropBoxFile_'+i+'">'+file.name+'</div>').appendTo('#dropBoxLog');
                    fd = new FormData;
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
                });
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
    
    function moveFile() {
        var path = $('#path').val();
    
        $.post('move.php', {
            file: '', target: path
        })
        .fail(function() {
            alert('saving falied');
        })
        .done(function(response) {
            if (response !== '') {
                alert(response);
            }
        }
        );
    }
    
    function tarDir() {
        var path = $('#path').val();
        var tar = 'backup.tar';
        $.post('tar.php', {
            'dir': path, 'tar': tar
        })
        .fail(function() {
            alert('Баскуринг провален');
        })
        .done(function(response) {
            if (response !== '') {
                alert(response);
            } else
            {
                downloadFile(tar);
            }
        }
        );
    }        
    </script>

    <script src="script.js">
    </script>

    <script>
        var editor = ace.edit("editor");
        var file_content = '';

        $.get('get.php', {
            file: current_file
        })
        .done(function(data) {
            editor.setValue(data);
        }
        );

        var modelist = ace.require("ace/ext/modelist");
        var beautify = ace.require("ace/ext/beautify");
        var mode = modelist.getModeForPath(current_file).mode;
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode(mode);
        editor.commands.addCommand({
            name: "save",
            bindKey: {
                win: "Ctrl-s", mac: "Command-s"
            },
            exec: function(editor) {
                $.post('save.php', {
                    file: current_file, data: editor.getValue()})
                .fail(function() {
                    alert('saving falied');
                })
                .done(function(response) {
                    if (response !== '') {
                        alert('RESP:'+response+'|');
                    }
                }
                );
            }
        })

        editor.commands.addCommand({
            name: "showKeyboardShortcuts",
            bindKey: {
                win: "Ctrl-Alt-h", mac: "Command-Alt-h"
            },
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