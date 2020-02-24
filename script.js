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

        var file = event.dataTransfer.files[0];
        console.log(file);

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

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function downloadFile(url) {
    var link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', url);
    link.click();
    $(link).remove();
}