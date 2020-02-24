<?PHP

$dir=$_POST['dir'];
$tar=$_POST['tar'];

if(!$tar)
{
    $tar='backup.tar';
}
$dir=$_SERVER['DOCUMENT_ROOT'].'/'.$dir;
if(is_file($tar))
{
    unlink($tar);
}
$res=exec('tar cfz '.$tar.' '.$dir.' -C '.$_SERVER['DOCUMENT_ROOT']);

?>