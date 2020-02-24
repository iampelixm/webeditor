<?PHP

function listdir($dir, $root, $sep = '', $listed) {
    $rel_dir = substr($dir, strlen($root));

    if (!in_array($dir, $listed)) {
        $listed[] = $dir;
        echo "<option value=\"".$rel_dir."/\">".$rel_dir."/</option>";
    }

    $list = scandir($dir);
    if (is_array($list)) {
        $list = array_diff($list, array('.', '..'));
        if ($list) {
            foreach ($list as $name) {
                $path = $dir . '/' . $name;
                $rel_path = substr($path, strlen($root));
                $is_dir = is_dir($path);
                if ($is_dir) {
                    $listed[] = $path;
                    echo "<option value=\"".$rel_path."/\">".$sep.$rel_path."/</option>";
                }

                if ($is_dir) {
                    listdir($path, $root, $sep.'-', $listed);
                }
            }
        }
    } else
    {
        echo '<option>не могу прочитать</option>';
    }
}

function showdir($dir, $root, $showdir = 1, $showfiles = 1) {
    $list = scandir($dir);
    if (is_array($list)) {
        $list = array_diff($list, array('.', '..'));
        if ($list) {
            echo '<ul style="list-style: none">';
            foreach ($list as $name) {
                $path = $dir . '/' . $name;
                $rel_path = substr($path, strlen($root));
                $is_dir = is_dir($path);
                echo '<li class="', $is_dir ? 'dir' : 'file', '">';
                if ($is_dir) {
                    if ($showdir) {
                        echo "<button class=\"mt-1 btn btn-outline-primary\" onclick=\"$(event.target).next().toggle(300);\">".htmlspecialchars($name)."</button>";
                    }
                } else
                {
                    if ($showfiles) {
                        echo '<div class="mt-1 border-bottom pb-1">
                        
                        <a class="btn btn-warning pt-0 pb-0" href="edit.php?file='.$rel_path.'">'.htmlspecialchars($name).'</a>
                        <a class="btn btn-primary pt-0 pb-0" href="'.$rel_path.'" target="_blank">перейти</a>
                        <a class="btn btn-info pt-0 pb-0" href="'.$rel_path.'" download="'.htmlspecialchars($name).'">скачать</a>
                        
                        </div>';
                    }
                }

                if ($is_dir) {
                    echo '<div class="dircontent">';
                    showdir($path, $root, $showdir, $showfiles);
                    echo '</div>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
    } else
    {
        echo '<i>не могу прочитать</i>';
    }
}

function getMySQLSettings() {
    $mysql_settings = [];
    if (file_exists('.mysql_settings')) {
        $mysqlset = file_get_contents('.mysql_settings');
        $mysql_settings = explode("\n", $mysqlset);
        /*
        структура файла настроек:
        хост
        база данных
        имя пользователя
        пароль
        */
    }
    return $mysql_settings;
}

function connectMySQL() {
    $mysql_settings = getMySQLSettings();
    if (!empty($mysql_settings[0])) {
        return mysqli_connect($mysql_settings[0], $mysql_settings[2], $mysql_settings[3], $mysql_settings[1]);
    }
    return '';
}

?>