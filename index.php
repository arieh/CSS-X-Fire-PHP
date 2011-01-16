<?php

$on = true;

/// finds the wanted css file and return it's location
function findFile($href,$sites){
    $href = str_replace("http://",'',$_GET['href']);
    $href = explode("/",$href);
    array_unshift($href,"http:/");
    $address = '';
    $folder = '';

    for ($i=0,$l=count($href);$i<$l;$i++){
         $address .= $href[$i] ."/";
         if (isset($sites->{$address})){
             $folder= $sites->{$address};
             break;
         }
    }
    if (!$folder) return false;
    
    $location = $folder .str_replace($address,"",$_GET['href']);
    
    return $location;
}

// replaces the property in the css file
function replaceValue($cont,$sel,$prop,$val,$del = false){      
    $pattern = '/('.preg_quote($sel).'\s*\{[\w\s:\-;\(\)#]*)(?:'.preg_quote($prop).'\s*:)(?:[^;\}]+)(;|\})/xi';

    $replace = '$1' . $prop . ' : ' . $val .'$2';

    if ( $del ) $replace = '$1 $2';

    return preg_replace($pattern,$replace,$cont);
}

//adds the new prop/value to the selector
function insertValue($cont,$sel,$prop,$val){
    $pattern = '/('.preg_quote($sel).'\s*\{[\w\s:\-;\(\)#]*)}/xi';
    return preg_replace($pattern, '$1 ' . $prop . " : " . $val .";\n}",$cont);
}

//writes the changes to file
function writeFile($res,$loc){
    $f = fopen($loc,'r+');
    fwrite($f,$res);
    ftruncate($f,strlen($res));
    fclose($f);
}



if (!$on) die;
$error = "";
$action ='';
    
$sites = json_decode(file_get_contents('sites.json'));


$location = findFile($_GET['href'],$sites);
if (false == $location) $error = "file not found";
else{
    if (false == file_exists($location)) $error ="file does not exists";
    else{        
        $content = file_get_contents($location);
        
        $selector = $_GET['selector'];
        $property = $_GET['property'];
        $value = $_GET['value'];
        $del =  (isset($_GET['deleted']) && true === $_GET['deleted']);

        $result = replaceValue($content,$selector, $property,$value,$del);
        if ($result != $content){
            writeFile($result,$location);
            $action = $del ? 'deleted' : 'modified';
        }else{
            $result = insertValue($content,$selector, $property,$value);
            $action = "added";
            writeFile($result,$location);
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8" />
   <link rel="stylesheet" type="text/css" href="css/reset.css" media = "all" />
   <meta name="description" content ="" />
 	<title>CSS-X-Fire PHP</title>
</head>
<body>
	<h1>CSS-X-Fire Results</h1>
    <?php if ($error):?>
        <h2>An error occoured: <?php echo $error;?></h2>    
    <?php else:?>
        <h2>Operation was successful!</h2>
        <ol>
        	<li>Operation: value <?php echo $action;?></li>
        	<li>File: <?php echo $location;?></li>
        	<li>Selector: <?php echo $selector;?></li>
        	<li>Property: <?php echo $property;?></li>
        	<li>Value: <?php echo $value;?></li>
        </ol>
        <h2>Original</h2>
        <pre><?php echo $content;?></pre>
        <h2>New</h2>
        <pre><?php echo $result;?></pre>
    <?php endif;?>    
</body>
</html>