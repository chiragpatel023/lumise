<?php
/**
*
*	(p) package: lumise
*	(c) author:	King-Theme
*	(i) website: https://www.lumise.com
*
*/

if (!defined('LUMISE')) {
	header('HTTP/1.0 403 Forbidden');
	die();
}

class lumise_update extends lumise_lib {
	
	protected $current;
	
	public function __construct() {
		
		global $lumise;
		
		$this->main = $lumise;
		
		$current = $current = $this->main->get_option('current_version');
		
		if ($current != LUMISE) {
			$this->main->set_option('current_version', LUMISE);
			$this->run_updater();
		}

	}
	
	public function check() {
		
		$curDate = date_default_timezone_get();
		date_default_timezone_set("Asia/Bangkok");
		$check = $this->main->lib->remote_connect($this->main->cfg->api_url.'updates/lumise.xml?nonce='.date('dH'));
		date_default_timezone_set($curDate);
		
		$check = @simplexml_load_string($check);
		
		if (!is_object($check) || !isset($check->{$this->main->connector->platform}))
			return null;
			
		$update = $check->{$this->main->connector->platform};
		
		$data = array(
			"time" => time(),
			"version" => (string)$update->version,
			"date" => (string)$update->date,
		);
		
		$this->main->set_option('last_check_update', json_encode($data));
		
		$data['status'] = 1;
		
		return $data;
		
	}
	
	protected function run_updater() {
		
		/*
		*	Call this when a new version is installed	
		*	$this->main = global $lumise
		*/
		
		/*
		* Version 1.6
		* add `active` to table categories
		*/
		
		if (version_compare(LUMISE, '1.4') >=0 ){
			$sql = "SHOW COLUMNS FROM `{$this->main->db->prefix}categories` LIKE 'active';";
			$columns = $this->main->db->rawQuery($sql);
			if(count($columns) == 0){
				$sql_active = "ALTER TABLE `{$this->main->db->prefix}categories` ADD `active` INT(1) NULL DEFAULT '1' AFTER `order`;";
				$this->main->db->rawQuery($sql_active);
			}
		}
		
		if (version_compare(LUMISE, '1.5') >=0 ){
			$sql_active = "ALTER TABLE `{$this->main->db->prefix}products` CHANGE `color` `color` TEXT;";
			$this->main->db->rawQuery($sql_active);
			$sql_active = "ALTER TABLE `{$this->main->db->prefix}products` CHANGE `printings` `printings` TEXT;";
			$this->main->db->rawQuery($sql_active);
		}
		
		if (version_compare(LUMISE, '1.7') >=0 ){
			$sql = "SHOW COLUMNS FROM `{$this->main->db->prefix}fonts` LIKE 'upload_ttf';";
			$columns = $this->main->db->rawQuery($sql);
			if(count($columns) == 0){
				$sql_active = "ALTER TABLE `{$this->main->db->prefix}fonts` ADD `upload_ttf` TEXT NULL DEFAULT '' AFTER `upload`;";
				$this->main->db->rawQuery($sql_active);
			}
		}
		
		if (version_compare(LUMISE, '1.7.1') >=0 ){
			
			$this->upgrade_1_7();
			
			$sql = "SHOW COLUMNS FROM `{$this->main->db->prefix}products` LIKE 'variations';";
			$columns = $this->main->db->rawQuery($sql);
			if(count($columns) == 0){
				$sql_active = "ALTER TABLE `{$this->main->db->prefix}products` ADD `variations` TEXT NULL DEFAULT '' AFTER `stages`;";
				$this->main->db->rawQuery($sql_active);
			}
			
			// do the convert old data
			// 1. convert colors to attribute
			// 2. convert all old attribute structure to new structure
			// 3. convert stages
			
			$sql = "SHOW COLUMNS FROM `{$this->main->db->prefix}products` LIKE 'orientation';";
			$columns = $this->main->db->rawQuery($sql);
			if(count($columns) > 0){
				$this->main->db->rawQuery("ALTER TABLE `{$this->main->db->prefix}products` DROP `orientation`;");
				$this->main->db->rawQuery("ALTER TABLE `{$this->main->db->prefix}products` DROP `min_qty`;");
				$this->main->db->rawQuery("ALTER TABLE `{$this->main->db->prefix}products` DROP `max_qty`;");
				$this->main->db->rawQuery("ALTER TABLE `{$this->main->db->prefix}products` DROP `size`;");
				$this->main->db->rawQuery("ALTER TABLE `{$this->main->db->prefix}products` DROP `change_color`;");
				$this->main->db->rawQuery("ALTER TABLE `{$this->main->db->prefix}products` DROP `color`;");
			}
			
		}
		
		if (version_compare(LUMISE, '1.7.3') >=0 ){
			$sql = "SHOW COLUMNS FROM `{$this->main->db->prefix}order_products` LIKE 'print_files';";
			$columns = $this->main->db->rawQuery($sql);
			if(count($columns) == 0){
				$sql_active = "ALTER TABLE `{$this->main->db->prefix}order_products` ADD `print_files` TEXT NULL DEFAULT '' AFTER `screenshots`;";
				$this->main->db->rawQuery($sql_active);
			}
		}
		
		/*
		*	Create subfolder upload	
		*/
		
		$this->main->check_upload();
		
	}
	
	public function upgrade_1_7() {
		
		$products = $this->main->db->rawQuery("SELECT * FROM `{$this->main->db->prefix}products`");
		
		if (count($products) > 0) {
			
			foreach ($products as $product) {
			
				if (isset($product['color'])) {
					
					$color = explode(':', $product['color']);
					$color = isset($color[1]) ? explode(',', $color[1]) : explode(',', $color[0]);
					
					$attributes = $this->main->lib->dejson($product['attributes']);
					$new_attributes = array();
					$stages = $this->main->lib->dejson($product['stages']);
					
					if (isset($stages->stages))
						$stages = $stages->stages;
					
					if (isset($stages->colors))
						unset($stages->colors);
						
					if (!empty($product['color'])) {
						$id = $this->main->generate_id(4);
						$new_attributes[$id] = array(
							"id" => $id,
							"name" => "Product color",
							"type" => "product_color",
							"use_variation" => false,
							"values" => implode("\n", $color)
						);
					}
					
					foreach ($attributes as $attribute) {
						$id = $this->main->generate_id(4);
						$values = array();
						if (isset($attribute->options)) {
							foreach ($attribute->options as $op) {
								array_push($values, $op->title);
							}
						}
						if (isset($attribute->title) && isset($attribute->type) && count($values) > 0) {
							if ($attribute->type == 'size')
								$attribute->type = 'select';
							$new_attributes[$id] = array(
								"id" => $id,
								"name" => $attribute->title,
								"type" => $attribute->type,
								"use_variation" => false,
								"values" => implode("\n", $values)
							);
						}
					}
					
					$this->main->lib->edit_row( 
						$product['id'], 
						array(
							"attributes" => $this->main->lib->enjson($new_attributes),
							"stages" => $this->main->lib->enjson($stages),
						), 
						'products' 
					);
					
				}
			}
		}
		
	}
	
}
	
	
