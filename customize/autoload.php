<?php

if (!defined('SITE_URL')) {
		
	$uri = (dirname($_SERVER['SCRIPT_NAME']) == '/')? '/' : dirname($_SERVER['SCRIPT_NAME']).'/';
	$scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
	
	if (!defined('SITE_URL'))
		define('SITE_URL', $scheme."://$_SERVER[HTTP_HOST]" . $uri);
	if (!defined('DS'))
		define('DS', DIRECTORY_SEPARATOR);
	if (!defined('ROOT'))
		define('ROOT', dirname(__FILE__).DS);
	
	if (!class_exists('lumise_connector')) {
		if(file_exists('php_connector.php')) {
		    require('php_connector.php');
		}else{
		    @header("location: ".$scheme."://$_SERVER[HTTP_HOST]" . $uri. 'installer');
		    die();
		}
	}
	
	if (!defined('PAYPAL_RETURN'))
		define('PAYPAL_RETURN', 'success.php');
		
	if (!defined('PAYPAL_CANCEL'))
		define('PAYPAL_CANCEL', 'cancel.php');
	
	function theme($file = '', $http = false) {
		if ($http === false)
			return is_file(ROOT.'theme'.DS.$file) ? ROOT.'theme'.DS.$file : ROOT.$file;	
		else return is_file(ROOT.'theme'.DS.$file) ? SITE_URL.'theme/'.$file : SITE_URL.$file;
	}
		
	require('./inc/helper.php');
	require('./inc/views.php');
	require_once('./core/includes/main.php');
	
	$incf = get_included_files();
	$prin = '';
	
	if (count($incf) > 1) {
		for ($i=0; $i<count($incf); $i++) {
			if ($incf[$i] == __FILE__)
				$prin = isset($incf[$i-1]) ? $incf[$i-1] : '';
		}
		if (!empty($prin)) {
			$prin = str_replace(ROOT, ROOT.'theme'.DS, $prin);
			if (is_file($prin)) {
				include $prin;
				exit;
			}
		}
	}

}
