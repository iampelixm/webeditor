<?php
require_once('functions.php');
?>
<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function rrmdir($src) {
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                rrmdir($full);
            }
            else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}


#$update_uri="https://github.com/iampelixm/webeditor/archive/master.zip";
$ch = null;
if (!($ch = curl_init()) ){
	throw new Exception('err_curlinit');
}

if ( curl_errno($ch) != 0 ){
	throw new Exception('err_curlinit'.curl_errno($ch).' '.curl_error($ch));
}


$latest_json_uri="https://api.github.com/repos/iampelixm/webeditor/releases/latest";

$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept-language: en\r\n" .
              "User-Agent: Updater\r\n"
  )
);

$context = stream_context_create($opts);
$json=file_get_contents($latest_json_uri, false, $context);
$array=json_decode($json, true);
#var_dump($array);
$update_uri=$array['tarball_url'];
echo $update_uri;


//$ufh = fopen('update.tar.gz', 'wb');


$curl_options = array(
CURLOPT_HEADER				=> 0,
CURLOPT_RETURNTRANSFER		=> true,
CURLOPT_FOLLOWLOCATION      => true,
CURLOPT_USERAGENT           => 'PHPUPDATER',
/*
CURLOPT_TIMEOUT				=> self::TIMEOUT_SOCKET*60,
CURLOPT_CONNECTTIMEOUT		=> self::TIMEOUT_SOCKET,
CURLE_OPERATION_TIMEOUTED	=> self::TIMEOUT_SOCKET*60,
*/
CURLOPT_BINARYTRANSFER		=> true,
CURLOPT_URL					=> $update_uri,
//TODO на ряде хостингов curl работает только через прокси, который необходимо указать в настройках
);
foreach($curl_options as $param=>$option){
	curl_setopt($ch, $param, $option);
}
$res = curl_exec($ch);
file_put_contents('update.tar.gz', $res);
//fclose($ufh);

if ($errno =  curl_errno($ch)) {
	$message = "Curl error: {$errno}# ".curl_error($ch)."";
	throw new Exception($message);
}
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($status != 200){
	throw new Exception("Неверный ответ сервера $update_uri $status",$status);
}
curl_close($ch);

if (is_dir('update'))
{
    rrmdir('update');
}

mkdir('update');
rename('update.tar.gz', 'update/update.tar.gz');
$ret=exec('tar xvfz update/update.tar.gz -C update/');
unlink('update/update.tar.gz');
#хз какое будет названиие папки, но мы точно знаем что там будет всего один каталог и внутри него лежит все что нам нужно
$dir = opendir('update');
$update_dir='';
while(false !== ( $file = readdir($dir)) ) {
    if (( $file != '.' ) && ( $file != '..' )) {
        if ( is_dir('update/'.$file) ) {
            #убедимся что внутри лежит какой-то файл который есть у нас точно, например такой
            if(file_exists('update/'.$file.'/edit.php'))
            {
                $update_dir='update/'.$file;
                break;
            }
        }
    }
}
closedir($dir);

if(!$update_dir)
{
    return 'Опаньки, херня, не смог найти файлы после распаковки архива';
}

$dir=opendir($update_dir);
while(false !== ( $file = readdir($dir)) ) {
    if (( $file != '.' ) && ( $file != '..' )) {
        rename($update_dir.'/'.$file, $file);
    }
}
closedir($dir);

rrmdir('update');

//$ret=exec('unzip -o update.zip');
//echo $ret;
echo 'все зашибись';

?>