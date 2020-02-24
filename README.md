# webeditor

<h1>Для чего?</h1>

Редактировать текстовые файлы через браузер. Быстро, удобно, с любого ПК или смартфона. Самостоятельно обновляется с гитхаба по кнопке ОБНОВИТЬ.
<br>Расчитано на работу в *nix системе - использует системные вызовы(exec) tar и mysql_dump. На форточном хостинге не тестировалось и не предполагается.
<br>Зачем все это нужно, когда есть другие инструменты? Это не система управления сервером, не ФТП и не редактор БД, а просто редактор кода, незаменимый в некоторых случаях, да и в целом
удобный для использования 

<h1>Из чего состоит</h1>

<ul>
    <li>
        JQUERY
    </li>
    <li>
        Bootstrap 4
    </li>    
    <li>
        ACE EDITOR (AWS Cloud9)
    </li>
    <li>
        PHP
    </li>
</ul>

<h1>Как пользоваться?</h1>

Положить в папку $DOCUMENT_ROOT/editor и открыть в браузере url/editor/edit.php

<p>
<b>ВАЖНО!</b><br>Обеспечь безопасность доступа к каталогу, где размещен редактор. Пока нет встроенной системы авторизации - делает сервер крайне уязвимым. Рекомендуется защитить каталог через 
.haccess с директивой авторизации. 

<ul>
    <li>
        Сохранить - стандартная комбинация клавиш для сохранения
    </li>
    <li>
        По кнопке выбрать файл выбираем файл для редактирования
    </li>
    <li>
        По кнопке Бэкап Каталога создается TAR архив указанного каталога в выпадающем спике.
        Как архивация отработает сразу же начинается скачивание файла. Сам архив создается в каталоге, где находится этот редактор
    </li>
    <li>
        По кнопке ПЕРЕМЕСТИТЬ текущий открытый файл перемещается в каталог, выбранный в выпадающем списке
    </li>
    <li>
        + ПАПКА - создает каталог в выбранном в выпадающем списке каталоге с именем, введенным в поле ввода.
    </li>
    <li>
        + ФАЙЛ создает файл, так же как с папкой
    </li>
    <li>
        ДРОП БОКС - открывает окно с Drag'n'Drop приемником и складывает все файлы в указанный в окне каталог
    </li>
    <li>
        Обновить - качает последний релиз с гитхаба и разворачивает его по текущему расположению этого редактора
    </li>
    <li>
        МуСКУЛЬ - открывает раздел работы с MySQL
    </li>
</ul>

<h1>Что делает?</h1>
<ul>
    <li>
        Составляет дерево каталогов и файлов от $DOCUMENT_ROOT и позволяет открыть для редактирования любой файл
    </li>
    <li>
        Автоматически определяет тип файла и включает соответствующую подсветку синтаксиса
    </li>
    <li>
        Создавать папки
    </li>
    <li>
        Создавать файлы
    </li>
    <li>
        Перемещать файлы
    </li>
    <li>
        Закачивать файлы через браузер
    </li>
    <li>
        Сделать tar архив папки и скачать по одному клику
    </li>
    <li>
        Автоформатирование кода
    </li>
</ul>

<h1>MySQL</h1>

Добавился функционал работы с MySQL. <br>
Настройки доступа хранятся в файле, обычно один сайт не использует более одной БД поэтому пока нет смысла добавлять возможность выбора БД.

<ul>
    <li>
        Составляет список всех таблиц БД
    </li>
    <li>
        Вывести содержимое таблицы
    </li>
    <li>
        Отредактировать запись таблицы
    </li>
    <li>
        Бэкап БД
    </li>
</ul>

<h1>TODO:</h1>
<ul>
    <li>
        Авторизация
    </li>
    <li>
        Минимизация кода
    </li>
    <li>
        Автосохранение по таймеру
    </li>
    <li>
        Сделать индексный файл
    </li>
    <li>
        Настройки безопасности
    </li>    
</ul>