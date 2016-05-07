<?php

//ini_set('display_errors',1);  error_reporting(E_ALL);

$configfile = 'settings.ini.php';
$examplefile = 'example.ini.php';

if(isset($_GET["action"])){$action = $_GET["action"];}

if(!file_exists($filename) && !file_exists($examplefile)){
    die('You are missing the ini configuration file, please download and refresh this page');
}

if(!file_exists($configfile)){
    echo "The file $configfile does not exist, we will make a copy now...<br/><br/>";
    if (!is_writable(dirname($examplefile)))
        die('We don\'t have access to write to the current directory, please change the permissions to this directory.');
    else {
        copy($examplefile, $configfile);
        sleep(2);
        echo "<!DOCTYPE html>";
        echo "<head>";
        echo "<title>Form submitted</title>";
        echo "<script type='text/javascript'>window.parent.location.reload()</script>";
        echo "</head>";
        echo "<body></body></html>";
    }
}

try {
    $config = parse_ini_file('settings.ini.php', true);
} catch(Exception $e) {
    die('<b>Unable to read config.ini.php. Did you rename it from settings.ini.php-example?</b><br><br>Error message: ' .$e->getMessage());
}

foreach ($config as $keyname => $section) {
    
    if(($keyname == "general")) { $hash_pass = $section["password"]; }

}

$pass = isset( $_POST["pass"] ) ? $_POST["pass"] : "none" ;

$parts = explode('$', $hash_pass);
$test_hash = crypt($pass, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3]));

if(($action == "write" && $hash_pass == $test_hash)){ 
    setcookie("logged", $hash_pass, time() + (86400 * 7), "/");
    $error = "You got it dude!";
    echo "<!DOCTYPE html>";
    echo "<head>";
    echo "<title>Form submitted</title>";
    echo "<script type='text/javascript'>window.parent.location.reload()</script>";
    echo "</head>";
    echo "<body></body></html>";
}

if(isset( $_POST["pass"] ) && ($hash_pass !== $test_hash)){
    $error = "Wrong Password!";
}
    
if($_COOKIE["logged"] == $hash_pass){
    
    echo "<!DOCTYPE html>";
    echo "<head>";
    echo "<title>Form submitted</title>";
    echo "<script type='text/javascript'>window.location.replace('settings.php');</script>";
    echo "</head>";
    echo "<body></body></html>";
    
}

if($hash_pass !== $test_hash){

    echo "<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css'>";
    echo "<center><B>Please Login to Contiune<br/><br/>";
    echo $error . "<br/>";
    echo "<form action=\"?action=write\" method='POST'>";
    echo "<div class=\"form-group clearfix well well-sm\" style=\"width: 25%; padding-bottom: 0px; padding-top: 10px; margin-bottom: 5px;\">";
    echo "<div style=\"margin-bottom: 8px\" class=\"input-group\"><div class=\"input-group-addon\">Password</div>";
    echo "<input style=\"margin-bottom: 0px\" type=\"password\" name=\"pass\" class=\"form-control\">";
    echo "<span class=\"input-group-btn\"><button name=\"submit\" class=\"btn btn-success\" type=\"submit\">Go!</button></span></div></div>";
    echo "</form></center>";
    
}
?>