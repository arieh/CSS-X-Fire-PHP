<?php
function writeFile($res,$loc){
    $f = fopen($loc,'r+');
    fwrite($f,$res);
    ftruncate($f,strlen($res));
    fclose($f);
}
$on = true;
if (false == $on) die("plugin is not on");

$href = str_replace("http://",'',$_GET['href']);
$href = explode("/",$href);
array_unshift($href,"http:/");
$sites = json_decode(file_get_contents('sites.json'));
$address = '';
$folder = '';

for ($i=0,$l=count($href);$i<$l;$i++){
     $address .= $href[$i] ."/";
     if (isset($sites->{$address})){
         $folder= $sites->{$address};
         break;
     }
}

if (false == $folder) die('not found');

$location = $sites->{$address} .str_replace($address,"",$_GET['href']);

if (false == file_exists($location)) die("file does not exists");

$content = file_get_contents($location);

$selector = $_GET['selector'];
$property = $_GET['property'];
$value = $_GET['value'];

var_dump($value);

$pattern = '/('.preg_quote($selector).'\s*\{[\w\s:\-;\(\)#]*)('.preg_quote($property).'\s*:)([^;\}]+)(;|\})/Ui';

$replace = '$1 $2 '.$_GET['value'] .'; $5';

if ( isset($_GET['deleted']) && true === $_GET['deleted']) $replace = '$1 $5';

$result = preg_replace($pattern,$replace,$content);

if ($result != $content){
    writeFile($result,$location);
    die('success');
}

$pattern = '/('.preg_quote($selector).'\s*\{[\w\s:\-;\(\)#]*)}/Ui';
$result = preg_replace($pattern, '$1 ' . $property . " : " . $value .";\n}",$content);
writeFile($result,$location);
echo "success";