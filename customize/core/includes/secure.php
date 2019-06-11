<?php
/**
*	
*	(p) package: lumise
*	(c) author:	King-Theme
*	(i) website: https://www.lumise.com
*
*/

if (!defined('LUMISE')) {
	return header('HTTP/1.0 403 Forbidden');
}

class lumise_secure{
	
	static function create_nonce($name) {
		
		global $lumise;
		$nonce = $lumise->connector->get_session($name);
		
		if ($nonce !== null && $nonce['expires'] > time()+(60*60*24)-600) {
			$lumise->connector->set_session($name, array("value" => $nonce["value"], "expires" => time()+(60*60*24)));
			return $nonce["value"];
		}
		
		$val = strtoupper($lumise->generate_id());
		$lumise->connector->set_session($name, array("value" => $val, "expires" => time()+(60*60*24)));
		
		return $val;
		
	}
	
	static function check_nonce($name, $value) {
		
		global $lumise;
		$nonce = $lumise->connector->get_session($name);
		
		if ($nonce !== null && $nonce["value"] == $value && $nonce["expires"] > time()) {
			$nonce["expires"] = time()+(60*60*24);
			$lumise->connector->set_session($name, $nonce);
			return true;
		}else return false;
		
	}
	
	static function nonce_exist($name, $value) {

		global $lumise;
		$nonce = $lumise->connector->get_session($name);
		
		if ($nonce !== null && $nonce["value"] == $value) {
			return true;
		}else return false;
		
	}
	
}
