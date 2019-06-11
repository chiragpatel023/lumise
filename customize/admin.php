<?php 
	
if(file_exists('php_connector.php')) {
    require('php_connector.php');
}else{
    $uri = (dirname($_SERVER['SCRIPT_NAME']) == '/')? '/' : dirname($_SERVER['SCRIPT_NAME']).'/';
    $scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    @header("location: ".$scheme."://$_SERVER[HTTP_HOST]" . $uri. 'installer');
    die();
}
$_REQUEST['lumise-router'] = 'admin';
$_GET['lumise-router'] = 'admin';
include ('core/index.php'); 

?>
