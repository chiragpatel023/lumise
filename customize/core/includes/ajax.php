<?php
/**
*
*	(p) package: lumise
*	(c) author:	King-Theme
*	(i) website: https://www.lumise.com
*
*/

require_once('lib.php');

/*=============================*/
class lumise_ajax extends lumise_lib {

	private $actions = array(
		'extend' => true,
		'templates' => true,
		'cliparts' => true,
		'shapes' => true,
		'new_language' => false,
		'switch_status' => false,
		'duplicate_item' => false,
		'add_tags' => false,
		'remove_tags' => false,
		'lumise_set_price' => false,
		'change_lang' => true,
		'edit_language_text' => false,
		'categories' => false,
		'add_clipart' => false,
		'list_products' => true,
		'load_product' => true,
		'upload_share_design' => true,
		'get_shares' => true,
		'get_rss' => false,
		'list_colors' => false,
		'delete_link_share' => true,
		'upload' => false,
		'upload_product_images' => false,
		'checkout' => true,
		'pdf' => true,
		'load_more_bases' => false,
		'edit_name_product_image' => false,
		'delete_base_image' => false,
		'setup' => false,
		'addon' => true,
		'report_bug' => true
	);
	private $actions_get = array(
		'pdf',
		'setup'
	);
	private $action;
	private $nonce;
	protected $aid;

	public function __construct() {
		
		global $lumise;
		
		$this->action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : '';
		$this->nonce = isset($_POST['nonce']) ? explode(":", htmlspecialchars($_POST['nonce'])) : array('', '');
		
		if (isset($_GET['action']) && $_GET['action'] == 'check-update') {
			header('HTTP/1.0 200');
			print_r($lumise->update->check());
			exit;
		}
		
		if (
			isset($_FILES['file']) || 
			(
				isset($_REQUEST['action']) && 
				in_array($_REQUEST['action'], $this->actions_get)
			)
		) {
			$this->action = isset($_REQUEST['action']) ? htmlspecialchars($_REQUEST['action']) : '';
			$this->nonce = isset($_REQUEST['nonce']) ? explode(":", htmlspecialchars($_REQUEST['nonce'])) : array('', '');
		}
		
		if ($lumise->cfg->settings['report_bugs'] == 1 || $lumise->cfg->settings['report_bugs'] == 2)
			$this->actions['send_bug'] = true;
			
		// Call to actions
		
		if (
			empty($this->action) ||
			empty($this->nonce) ||
			!isset($this->actions[$this->action]) ||
			!method_exists($this, $this->action) ||
			(
				$this->actions[$this->action] !== true &&
				!$lumise->connector->is_admin()
			)
		) {
			return header('HTTP/1.0 403 Forbidden');
		}
		
		$this->main = $lumise;

		if ($this->action == 'extend')
			return $this->extend();
			
		$this->aid = str_replace("'", "", $lumise->connector->cookie('lumise-AID'));
		
		if (lumise_secure::check_nonce($this->nonce[0], $this->nonce[1])) {
			header('HTTP/1.0 200');
			call_user_func_array(array(&$this, $this->action), array());
		} else return header('HTTP/1.0 403 Forbidden');
		
	}

	public function extend() {

		$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
		$nonce = isset($_POST['nonce']) ? htmlspecialchars($_POST['nonce']) : '';

		if (empty($name) || empty($nonce) || !lumise_secure::nonce_exist($name, $nonce)) {
			echo '-1';
			exit;
		}else{
			echo lumise_secure::create_nonce($name);
			exit;
		}

	}

	public function templates() {
		
		$this->x_items('templates');

	}
	
	public function cliparts() {
		
		$this->x_items('cliparts');
		
	}

	public function shapes() {

		$index = htmlspecialchars(isset($_POST['index']) ? $_POST['index'] : 0);
		header('Content-Type: application/json');
		$items = $this->get_shapes($index);
		foreach($items as $ind => $item){
			$items[$ind]['content'] = $this->stripallslashes($item['content']);
		}
		echo json_encode(array(
			"items" => $items,
			"total" => $this->get_shapes('total'),
			"index" => $index,
			"limit" => 20
		));

	}

	public function new_language() {

		$code = $_POST['code'];
		$langs = $this->main->langs();

		if (!isset($code) || !isset($langs[$code])) {
			echo -1;
			exit;
		}

		$scan = $this->scan_languages();

		foreach ($scan as $key => $val) {

			$exist = $this->main->db->rawQueryOne("SELECT `id` as `text` FROM `{$this->main->db->prefix}languages` WHERE `lang`= ? && `original_text`= ? ", array($code, $val));
			if (count($exist) === 0) {
				$this->main->db->insert("languages", array(
					"text" => $val,
					"original_text" => $val,
					"lang" => $code,
					"created" => date("Y-m-d").' '.date("H:i:s"),
					"updated" => date("Y-m-d").' '.date("H:i:s"),
				));
			}
		}
		
		$this->main->connector->set_session('language_lang', $code);

		echo '1';
		exit;

	}

	public function edit_language_text() {

		$text = $_POST['text'];
		
		if (is_array($text)) {
			foreach ($text as $id => $txt) {
				$this->main->db->where ("id='$id'")->update('languages', array(
					'text' => $txt,
					'updated' => date("Y-m-d").' '.date("H:i:s")
				));
			}

			header('Content-Type: application/json');
			echo json_encode($text);
			exit;
		} else if (isset($_POST['id']) && !empty($_POST['text'])) {
			$this->main->db->where ("id='{$_POST['id']}'")->update('languages', array(
				'text' => $text,
				'updated' => date("Y-m-d").' '.date("H:i:s")
			));
			header('Content-Type: application/json');
			echo json_encode(array("id" => $_POST['id'], "text" => $text));
			exit;
		}
		
		echo 0;
		exit;

	}

	public function change_lang() {

		$code = $_POST['code'];
		$this->main->set_lang($code);
		$this->main->do_action('change_language', $code);

		die('1');

	}

	public function list_products() {
		
		$task = isset($_POST['task']) ? $_POST['task'] : '';
		$s = isset($_POST['s']) ? $_POST['s'] : '';
		$category = isset($_POST['category']) ? $_POST['category'] : '';
		$index = isset($_POST['index']) ? $_POST['index'] : 0;
		
		$data = $this->get_products(array(
			"s" => $s,
			"category" => $category,
			"index" => $index,
			"no_cms_filter" => ($task == 'cms_product')
		));

		header('Content-Type: application/json');
		echo json_encode($data);

	}
	
	public function load_product() {

		$data = $this->get_product((Int)$_POST['id']);

		header('Content-Type: application/json');
		echo json_encode($data);

	}

	public function categories() {

		$post = $_POST['data'];

		if (isset($post) && !empty($post)) {

			$data = array(
				'name' => $post['name'],
				'parent' => $post['parent'],
				'type' => $post['type'],
				'updated' => date("Y-m-d").' '.date("H:i:s"),
				'created'=> date("Y-m-d").' '.date("H:i:s")
			);

			if (!empty($post['upload'])) {
				
				$path = 'thumbnails'.DS;
				$check = $this->main->check_upload();

				$process = $this->upload_file($post['upload'], $path);

				if (!isset($process['error']) && isset($process['thumbn']))
					$data['thumbnail'] = $process['thumbn'];

				@unlink($this->main->cfg->upload_path.$path.$process['name']);

			}

			$this->main->db->insert ('categories', $data);

		}
		
		$type = $this->esc('type');
		$cates = $this->get_categories($type);
		header('Content-Type: application/json');

		echo json_encode($this->get_categories());

	}

	public function add_clipart() {

		$post = @json_decode(stripslashes($_POST['data']));

		if ($post == null) {

			header('Content-Type: application/json');
			echo json_encode(array(
				"error" => 'Data struction',
				"name" => $data['name']
			));

			exit;
		}

		$data = array(
			'name' => $post->name,
			'price' => (int)$post->price,
			'featured' => $post->featured,
			'active' => 1,
			'updated' => date("Y-m-d").' '.date("H:i:s"),
			'created'=> date("Y-m-d").' '.date("H:i:s")
		);

		$time = time();
		$path = 'cliparts'.DS.date('Y', $time).DS.date('m', $time).DS;

		$check = $this->main->check_upload($time);

		if ($check !== 1) {

			header('Content-Type: application/json');
			echo json_encode(array(
				"error" => $this->main->lang('The system does not have permission to write files in the upload folder: ').$lumise->upload_path,
				"name" => $data['name']
			));

			exit;

		}else{

			$process = $this->upload_file($post->upload, $path);

			if (isset($process['error'])) {

				header('Content-Type: application/json');
				echo json_encode(array(
					"error" => $process['error'],
					"name" => $data['name']
				));
				exit;

			}else{

				$data['upload'] = $path.$process['name'];

				if (isset($process['thumbn'])) {
					$data['thumbnail_url'] = $this->main->cfg->upload_url.str_replace(DS, '/', $path.$process['thumbn']);
				}

			}

		}

		$clipart_id = $this->main->db->insert ('cliparts', $data);

		if ($clipart_id) {

			if (isset($post->cates) && count($post->cates) > 0) {
				foreach($post->cates as $cate) {
					$this->main->db->insert ('categories_reference', array(
						"category_id" => $cate,
						"item_id" => $clipart_id,
						"type" => "cliparts"
					));
				}
			}

			if (isset($post->tags) && count($post->tags) > 0) {
				foreach($post->tags as $tag) {

					$check = $this->main->db->rawQuery ("SELECT `id` FROM `{$this->main->db->prefix}tags` WHERE `slug`='".$this->slugify($tag)."'");

					if (count($check) > 0) {
						$this->main->db->insert ('tags_reference', array(
							"tag_id" => $check[0]['id'],
							"item_id" => $clipart_id,
							"type" => "cliparts"
						));
					}else{

						$tag_id = $this->main->db->insert ('tags', array(
							"name" => trim($tag),
							"slug" => $this->slugify($tag),
							"type" => "cliparts",
							'updated' => date("Y-m-d").' '.date("H:i:s"),
							'created'=> date("Y-m-d").' '.date("H:i:s")
						));

						$this->main->db->insert ('tags_reference', array(
							"tag_id" => $tag_id,
							"item_id" => $clipart_id,
							"type" => "cliparts"
						));
					}
				}
			}

		}

		header('Content-Type: application/json');
		echo json_encode(array("success" => $clipart_id));
		exit;

	}

	public function switch_status() {

		$post = $_POST;
		$data = array();
		
		$cap = 'lumise_edit_'.$post['data']['type'].'-s';
		$cap = str_replace(array('s-s', '-s'), array('s', 's'), $cap);
		
		if (!$this->main->caps($cap)) {
			echo json_encode(array(
				"status" => 'error',
				"action" => $post['data']['action'],
				"value" => $this->main->lang('Sorry, you are not allowed to do this action')
			));
			exit;
		}
		
		if ($post['data']['status'] == 0)
			$post['data']['status'] = 1;
		else
			$post['data']['status'] = 0;

		if ($post['data']['action'] == 'switch_feature')
			$data['featured'] = $post['data']['status'];
		else
			$data['active'] = $post['data']['status'];
		
		if ($post['data']['type'] == 'addons') {
			
			if ($post['data']['status'] == 1)
				$ps = $this->main->addons->active_addon($post['data']['id']);
			else $ps = $this->main->addons->deactive_addon($post['data']['id']);
			
			if (empty($ps['error'])) {
				echo json_encode(array(
					"status" => 'success',
					"action" => $post['data']['action'],
					"value" => $ps['status'],
					"msg" => $ps['msg']
				));
			} else {
				echo json_encode(array(
					"status" => 'error',
					"action" => $post['data']['action'],
					"value" => strip_tags($ps['error']),
					"msg" => ''
				));
			}
			exit;
		}
		
		$id = $this->edit_row( $post['data']['id'], $data, $post['data']['type'] );

		if (isset($id) && $id == true )
			$val['status'] = 'success';
		else
			$val['status'] = 'error';

		echo json_encode(array(
			"status" => $val['status'],
			"action" => $post['data']['action'],
			"value" => $post['data']['status']
		));

	}

	public function duplicate_item() {

		global $lumise;
		$post = $_POST;
		$data = array();
		
		$cap = 'lumise_edit_'.$post['data']['table'].'-s';
		$cap = str_replace(array('s-s', '-s'), array('s', 's'), $cap);
		
		if (!$this->main->caps($cap)) {
			echo json_encode(array(
				"status" => 'error',
				"action" => $post['data']['duplicate'],
				"value" => $this->main->lang('Sorry, you are not allowed to do this action')
			));
			exit;
		}
		
		$data = $this->get_row_id($post['data']['id'], $post['data']['table']);
		
		if (isset($data['name']))
			$data['name'] = $data['name'].'(Copy)';
		
		if (isset($data['title']))
			$data['title'] = $data['title'].'(Copy)';

		if ($data['created'])
			$data['created'] = date("Y-m-d").' '.date("H:i:s");

		if ($data['updated'])
			$data['updated'] = '';

		if ($data['id'])
			unset($data['id']);

		$id = $this->add_row($data, $post['data']['table']);

		$data = array();

		$data = $this->get_row_id($id, $post['data']['table']);

		if (isset($data['name']))
			$data['url'] = $lumise->cfg->admin_url.'lumise-page=product&id='.$data['id'];

		if (isset($data['title'])){
			$data['name'] = $data['title'];
			$data['url'] = $lumise->cfg->admin_url.'lumise-page=printing&id='.$data['id'];
		}

		if (count($data) > 0)
			$val['status'] = 'success';
		else
			$val['status'] = 'error';

		echo json_encode(array(
			"status" => $val['status'],
			"data" => $data
		));

	}

	public function add_tags() {

		$post = $_POST;
		
		$cap = 'lumise_edit_'.$post['data']['type'].'-s';
		$cap = str_replace(array('s-s', '-s'), array('s', 's'), $cap);
		
		if (!$this->main->caps($cap)) {
			echo json_encode(array(
				"status" => 'error',
				"action" => $post['data']['action'],
				"value" => $this->main->lang('Sorry, you are not allowed to do this action')
			));
			exit;
		}
		
		$arr = array('id', 'name', 'type');
		$tags = $this->get_rows_custom($arr, 'tags', $orderby = 'name', $order='asc');
		$flag = false;

		foreach ($tags as $key => $value) {
			
			if ($value['name'] == $post['data']['value'] && $value['type'] == $post['data']['type']){
				$id = $value['id'];
				$flag = true;
				break;
			}
		}

		if (!$flag) {
			
			$data = array();
			$data['name'] = $post['data']['value'];
			$data['type'] = $post['data']['type'];
			$data['created'] = date("Y-m-d").' '.date("H:i:s");

			$data_slug = array();
			$data['slug'] = $this->slugify($data['name']);
			$val = $this->get_rows_custom(array('slug', 'type'), 'tags');

			foreach ($val as $key => $value) {
				if ($value['type'] == $data['type']) {
					$data_slug[] = $value['slug'];
				}
			}
			if (in_array($data['slug'], $data_slug))
				$data['slug'] = $this->add_count($data['slug'], $data_slug);

			$id = $this->add_row( $data, 'tags' );

		}

		if (isset($id)){

			$val['status'] = 'success';
			$tags = $this->get_tag_item($post['data']['id'], $post['data']['type']);
			$flag = false;
			
			foreach ($tags as $key => $value) {
				if ($value['id'] == $id){
					$flag = true;
					break;
				}
			}

			if (!$flag) {
				$data = array();
				$data['tag_id'] = $id;
				$data['item_id'] = $post['data']['id'];
				$data['type'] = $post['data']['type'];
				$id = $this->add_row( $data, 'tags_reference' );

				$data_type = $this->get_row_id($post['data']['id'],$post['data']['type']);
				$data_tag = array();
				if (empty($data_type['tags'])) {
					$data_tag['tags'] =  $post['data']['value'];
				} else{
					$data_tags = explode(',', $data_type['tags']);
					$data_tags[] = $post['data']['value'];
					$data_tag['tags'] = implode( ',', $data_tags);
				}
				$this->edit_row($post['data']['id'], $data_tag, $post['data']['type']);
			}

			if (isset($id) && $id == true)
				$val['status'] = 'success';
			else
				$val['status'] = 'error';	

		} else {
			$val['status'] = 'error';
		}

		echo json_encode(array(
			"status" => $val['status'],
			"value" => $post['data']['value']
		));

	}

	public function remove_tags() {

		$post = $_POST;
		$tags = $this->get_tag_item($post['data']['id'], $post['data']['type']);
		
		foreach ($tags as $key => $value) {
			if ($value['name'] == $post['data']['value']) {
				$id = $value['id'];
				break;
			}
		}

		$arr = array('id', 'tag_id', 'item_id', 'type');
		$tags = $this->get_rows_custom($arr, 'tags_reference', $orderby = 'id', $order='asc');

		if (isset($id)){
			$data_tags = array();
			$item_type = $this->get_row_id($post['data']['id'], $post['data']['type']);
			$tag_name = $this->get_row_id($id, 'tags');
			trim($item_type['tags']);
			$data_tags['tags'] = str_replace( $tag_name['name'], ' ' , $item_type['tags'] );
			$data_tags['tags'] = str_replace( ', ,', ',' , $data_tags['tags'] );
			$data_tags['tags'] = str_replace( ', ', '' , $data_tags['tags'] );
			$data_tags['tags'] = str_replace( ' ,', '' , $data_tags['tags'] );
			trim($data_tags['tags'], ',');
			$this->edit_row($post['data']['id'],$data_tags,$post['data']['type']);
			foreach ($tags as $key => $value) {
				if ($value['tag_id'] == $id && $value['item_id'] == $post['data']['id'] && $value['type'] == $post['data']['type'])
					$this->delete_row($value['id'], 'tags_reference');
			}
			$val['status'] = 'success';
		} else{
			$val['status'] = 'error';
		}

		echo json_encode(array(
			"status" => $val['status'],
			"value" => $post['data']['value']
		));

	}

	public function lumise_set_price() {
		
		$post = $_POST;
		$data = array();
		
		$cap = 'lumise_edit_'.$post['data']['type'].'-s';
		$cap = str_replace(array('s-s', '-s'), array('s', 's'), $cap);
		
		if (!$this->main->caps($cap)) {
			echo json_encode(array(
				"status" => 'error',
				"action" => $post['data']['action'],
				"value" => $this->main->lang('Sorry, you are not allowed to do this action')
			));
			exit;
		}

		if ($post['data']['value'] == '')
			$post['data']['value'] = 0;

		$data['price'] = isset($post['data']['value']) && !empty($post['data']['value']) ? $post['data']['value'] : 0;
		
		$data['updated'] = date("Y-m-d").' '.date("H:i:s");
		$id = $this->edit_row( $post['data']['id'], $data, $post['data']['type'] );

		if (isset($id))
			$val['status'] = 'success';
	    else
			$val['status'] = 'error';

		echo json_encode(array( 
			"status" => $val['status'],
			"value" => $post['data']['value']
		));

	}
	
	public function upload_share_design() {
		
		@ini_set('memory_limit','5000M');
		
		$hist = $this->main->connector->get_session('share-design');
		
		if ($hist === null)
			$hist = 0;
		
		if (!isset($_POST['aid']) || $_POST['aid'] != $this->main->connector->cookie('lumise-AID')) {
			echo json_encode(array( 
				"success" => 0,
				"message" => $this->main->lang('Error, user is not authenticated')
			));
			exit;
		}
		
		if ($hist >= 50) {
			echo json_encode(array( 
				"success" => 0,
				"message" => $this->main->lang('Error, has exceeded the allowable limit')
			));
			exit;
		}
		
		if ($this->main->connector->is_admin() || $this->main->cfg->settings['share'] == '1') {
			
			$this->main->connector->set_session('share-design', $hist+1);
			
			$id = $this->main->generate_id();
			
			$check = $this->main->check_upload(time());
			
			if ($check !== 1) {
				echo json_encode(array( 
					"success" => 0,
					"message" => $this->main->lang('Error, could not write files on the server')
				));
				exit;
			}
			
			$path = $this->main->cfg->upload_path.'shares'.DS.date('Y').DS.date('m').DS;
			$screenshot = urldecode(base64_decode($_POST['screenshot']));
			$screenshot = explode(',', $screenshot);
			
			if (
				file_put_contents($path.$id.'.jpg', base64_decode($screenshot[1])) &&
				file_put_contents($path.$id.'.lumi', urldecode(base64_decode($_POST['data'])))
			) {
			
				$insert = $this->main->db->insert('shares', array(
					'name' => $this->main->lib->esc('label'),
					'aid' => $this->main->lib->esc('aid'),
					'share_id' => $id,
					'product' => intval($this->main->lib->esc('product')),
					'product_cms' => intval($this->main->lib->esc('product_cms')),
					'view' => 0,
					'active' => 1,
					'created' => date("Y-m-d").' '.date("H:i:s")
				));
				
				if ($insert) {
					$result = json_encode(array(
						"success" => 1,
						"id" => $id,
						"product" => $this->main->lib->esc('product'),
						"product_cms" => $this->main->lib->esc('product_cms'),
						"path" => date('Y/m'),
						"aid" => $this->main->lib->esc('aid'),
						"name" => $this->main->lib->esc('label'),
						"created" => time()
					));
				}else{
					$result = json_encode(array(
						"success" => 0,
						"message" => $this->main->lang('Error, could not create the link share')
					));
				}
			
			} else {
				$result = json_encode(array(
					"success" => 0,
					"message" => $this->main->lang('Error, could not upload design to create link')
				));
			}
			
		}else{
			$result = json_encode(array( 
				"success" => 0,
				"message" => $this->main->lang('Error, this feature has been disabled by admin')
			));
		}
		
		echo $result; exit;
		
	}
	
	public function get_shares() {
		
		$aid = $this->main->connector->cookie('lumise-AID');
		$index = $this->main->lib->esc('index');
		
		if (empty($index))
			$index = 0;
		
		if (empty($aid) || $aid == '*') {
			$result = json_encode(array( 
				"success" => 0,
				"result" => array()
			));
		}else{
			
			$data = $this->getShares($index);
			
			$result = json_encode(array( 
				"success" => 1,
				"result" => $data[0],
				"total" => $data[1],
				"per_page" => 20,
				"index" => $index+count($data[0])
			));
		}
		
		echo $result; 
		exit;
		
	}
	
	public function get_rss() {
		
		$curDate = date_default_timezone_get();
		date_default_timezone_set("Asia/Bangkok");
		$rss = $this->main->lib->remote_connect($this->main->cfg->api_url.'news/php.rss.xml?nonce='.date('dH'));
		date_default_timezone_set($curDate);
		
		$rss = @simplexml_load_string($rss);

		if ($rss !== null && is_object($rss)) {

			$count = count($rss->channel->item);
			$html = '';

			for ($i = 0; $i < $count; $i++) {

				$item = $rss->channel->item[$i];
				$title = $item->title;
				$link = $item->link;
				$cate = $item->cate;
				$time = $item->time;
				$thumb = $item->thumb;
				$description = $item->description;
				$html .= '<div class="lumise_wrap"><figure><img src="'.$thumb.'"></figure>';
				$html .= '<div class="lumise_right"><a href="'.$link.'" target="_blank">'.$title.'</a>';
				$html .= '<div class="lumise_meta"><span><i class="fa fa-folder-o" aria-hidden="true"></i>'.$cate.'</span><span><i class="fa fa-clock-o" aria-hidden="true"></i>'.$time.'</span></div>';
				$html .= '<p>'.$description.'</p></div></div>';

			}

			echo $html;
			
		} else {
			echo '<p>'.$this->main->lang('Could not load RSS feed').'</p>';
		}
			
		exit;
		
	}
		
	public function list_colors() {
		
		if (isset($_POST['save_action'])) {
			echo $this->main->set_option('colors_manage', $_POST['save_action']);
			exit;
		}
		
		$colors = $this->main->get_option('colors_manage');
		
		echo $colors;
		exit;
		
	}
		
	public function delete_link_share() {
		
		$aid = $this->main->connector->cookie('lumise-AID');
		$id = $this->main->lib->esc('id');
		$post_aid = $this->main->lib->esc('aid');
		
		if ($aid != $post_aid) {
			$result = array( 
				"success" => 0,
				"message" => $this->main->lang('Error Unauthorized 1: Could not delete the link')
			);
		}else{
			
			$data = $this->get_share($id);
			
			if ($data == null || $data['aid'] != $aid) {
				$result = array( 
					"success" => 0,
					"message" => $this->main->lang('Error Unauthorized 2: Could not delete the link')
				);
			}else{
				
				$path = $this->main->cfg->upload_path;
				$path .= 'shares'.DS.date('Y', strtotime($data['created'])).DS.date('m', strtotime($data['created'])).DS;
				
				@unlink($path.$data['share_id'].'.jpg');
				@unlink($path.$data['share_id'].'.lumi');
				
				$this->main->db->where('share_id', $id);
				$this->main->db->delete('shares');
				
				$result = array( 
					"success" => 1
				);
			}
		}
		
		echo json_encode($result); 
		exit;
		
	}
	
	public function upload_product_images() {
		
		$time = time();
		$check = $this->main->check_upload($time);
		
		if ($check !== 1) {
			echo 'Error: '.$check;
			exit;
		}
		
		$path = $this->main->cfg->upload_path.'products'.DS;
		$res = array();
		
		foreach ($_FILES as $name => $file) {
			
			$image = @file_get_contents($file["tmp_name"]);
			$type = strpos($image, 'data:image/png') !== false ? '.png' : '.jpg';
			$image = explode(',', $image);
			$image = base64_decode($image[1]);
			
			if (@file_put_contents($path.$name.$type, $image)) {
				$res[$name] = 'products/'.$name.$type;
			}
			
			unset($image);
			
		}
		
		echo json_encode($res);
		exit;
		
	}
	
	public function upload() {
		
		$time = time();
		$check = $this->main->check_upload($time);
		
		if ($check !== 1) {
			echo '{"error": "'.$check.'"}';
			exit;
		}
		
		if (isset($_GET['task']) && $_GET['task'] == 'stages') {
			
			$content = file_get_contents($_FILES["file"]["tmp_name"]);
			$content = preg_replace_callback("/(data\:image\/[^\"]+\")/i", array(&$this, 'upload_stages'), $content);
			
			$content = explode('-->|<--', $content);
			
			echo base64_encode(urlencode($content[0]));
			
			if (isset($content[1]))
				echo '-->|<--'.base64_encode(urlencode($content[1]));
			
			exit; 
			
		}
				
		if (isset($_GET['task']) && $_GET['task'] == 'files') {
			
			$data = @file_get_contents($_FILES["file"]["tmp_name"]);
			$tmpl = time().'.tmpl';
			
			if ($data && !empty($data) && strlen($data) > 0) {
				if (@file_put_contents($this->main->cfg->upload_path.'user_data'.DS.$tmpl, $data)) {
					echo $tmpl;
				} else echo 'Error: Could not write file';
			} else echo 'Error: Could not upload file';
				
			exit;
			
		}
		
		$path = $this->main->cfg->upload_path.'user_data'.DS;
		$file = date('Y', $time).DS.date('m', $time).DS.$this->main->generate_id().'.txt';
		
		$move_file = move_uploaded_file($_FILES["file"]["tmp_name"], $path.$file);
		
		if ($move_file)
			echo '{"success": "'.urlencode($file).'"}';
		else echo '{"error": "could not upload"}';
		
		exit;
		
	}
	
	public function upload_stages($matches) {
		
		$path = $this->main->cfg->upload_path.'products'.DS;
		$type = '.jpg';
		
		$data = str_replace('"', "", $matches[0]);
		$data = explode(',', $data);
		$data = base64_decode($data[1]);
		
		if (strpos($matches[0], 'data:image/png') !== false)
			$type = '.png';
		if (strpos($matches[0], 'data:image/svg') !== false)
			$type = '.svg';
		
		$file = $this->main->generate_id().$type;
		
		if (!@file_put_contents($path.$file, $data)) {
			echo 'Error: could not write file on your server';
			exit;
		}

		return 'products/'.$file.'"';
		
	}
	
	public function checkout() {
		
		require_once(dirname(__FILE__).DS.'..'.DS.'cart.php');

		exit;
	}
	
	public function pdf() {
		
		global $lumise;
		
		$fonts = isset($_GET['fonts']) ? urldecode($_GET['fonts']) : '';
		$fonts = explode('|', $fonts);
		
		$nocache = array();
		
		$fpath = $lumise->cfg->upload_path.'fonts'.DS;
		
		if (count($fonts) > 0) {
			
			foreach ($fonts as $f) {
				if (!is_file($fpath.$f.'.ttf'))
					array_push($nocache, $f);
			}
			
			if (count($nocache) > 0) {
				
				$query = "SELECT * FROM `{$lumise->db->prefix}fonts` WHERE `name` IN ('".implode("','", $nocache)."') AND `active`= 1";
				
				$db_fonts = $lumise->db->rawQuery($query);

				if (count($db_fonts) > 0) {
					foreach ($db_fonts as $font) {
						if (is_file($lumise->cfg->upload_path.$font['upload_ttf'])) {
							copy($lumise->cfg->upload_path.$font['upload_ttf'], $fpath.$font['name'].'.ttf');
							if (($key = array_search($font['name'], $nocache)) !== false) {
							    unset($nocache[$key]);
							}
						}
					}
				}
			}
			
			if (count($nocache) > 0) {
				
				$gcfg = $lumise->lib->remote_connect('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCrsTDigL61TFHYPHTZduQP1cGi8CLfp90');
				
				$gcfg = json_decode($gcfg);
				
				if (is_array($gcfg->items) && count($gcfg->items) > 0) {
					foreach ($gcfg->items as $item) {
						if (in_array($item->family, $nocache)) {
							if (!is_file($fpath.$item->family.'.ttf')) {
								$fdata = $lumise->lib->remote_connect($item->files->regular);
								if (!empty($fdata)) {
									file_put_contents($fpath.$item->family.'.ttf', $fdata);
								}
							}
						}
					}
				}
			
			}
		
		}
		
		$ratio = isset($_GET['ratio']) ? $_GET['ratio'] : '';
		
	?><!DOCTYPE html><html><head><title>Lumise</title><script type="text/javascript">var fonts = {<?php
		foreach ($fonts as $font) {
			if (is_file($fpath.$font.'.ttf')) {
				echo '"'.$font.'": "'.base64_encode(file_get_contents($fpath.$font.'.ttf')).'",';
			}
		}
	?>}, width = 612, height = 792, ratio = [<?php echo $ratio; ?>];function base64ToArrayBuffer(base64){var raw=window.atob(base64);var rawLength=raw.length;var array=new Uint8Array(new ArrayBuffer(rawLength));for(i=0;i<rawLength;i++){array[i]=raw.charCodeAt(i)}return(array.buffer)};function renderPDF(svgs, url){let doc=new PDFDocument({compress:false, size: [width, Math.round(width/ratio[0])-(14-(14*ratio[0]))]});Object.keys(fonts).map(function(f){doc.registerFont(f,base64ToArrayBuffer(fonts[f]))});svgs.map(function(s,i){var w = width, h = Math.round(width/ratio[i])-(14-(14*ratio[i])); if (i>0)doc.addPage({size: [w, h]});SVGtoPDF(doc,s,7,7,{fontCallback:function(f){try{doc.font(f.replace(/\'/g,''))}catch(ex){}}, width: w-14, height: h-14});<?php if (isset($_GET['cropmarks']) && $_GET['cropmarks'] == '1') { ?>var marks = '';['-1 19 15 1', (w-14)+' 19 15 1', '-1 '+(h-20)+' 15 1', (w-14)+' '+(h-20)+' 15 1', '19 0 1 15', (w-19)+' 0 1 15', '19 '+(h-14)+' 1 15', (w-19)+' '+Math.ceil(h-14)+' 1 15'].map(function(m) {m = m.split(' ');marks += '<rect x="'+m[0]+'" y="'+m[1]+'" width="'+m[2]+'" height="'+m[3]+'" style="fill: #333"></rect>';});SVGtoPDF(doc,'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 '+w+' '+h+'" xml:space="preserve"><rect x="19" y="19" width="'+(w-38)+'" height="'+(h-38)+'" style="stroke: #C41E3A; stroke-width: 1; stroke-dasharray: 5 5; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill-opacity: 0;" visibility="hidden"></rect>'+marks+'</svg>',0,0,{width: w, height: h});<?php } ?>});let stream=doc.pipe(blobStream());stream.on('finish',function(res){let blob=stream.toBlob('application/pdf');if(navigator.msSaveOrOpenBlob){navigator.msSaveOrOpenBlob(blob,'File.pdf')}else{window.location.href=url.createObjectURL(blob)}});doc.end()};</script><script type="text/javascript" src="<?php echo $lumise->cfg->assets_url.'assets/js/pdfkit.js?version='.LUMISE; ?>"></script></head><body></body></html><?php	
		exit;
		
	}
	
	public function load_more_bases() {
		
		$start = isset($_POST['start']) ? $_POST['start'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
		
		$items = $this->main->lib->get_uploaded_bases();
		$total = count($items);
		
		$items = array_splice($items, $start, $limit);
		
		$list = $this->main->get_option('product_image_names');
		
		if ($list === null)
			$list = array();
		else $list = json_decode($list, true);
		
		$names = array();
		
		for ($i=0; $i<count($items); $i++) {
			if (isset($list[$items[$i]]))
				$names[$i] = $list[$items[$i]];
			else $names[$i] = '';
		}
		
		echo json_encode(array(
			"total" => $total,
			"items" => $items,
			"names" => $names,
			"start" => (float)$start
		));
		
		exit;
		
	}
	
	public function edit_name_product_image() {
		
		$list = $this->main->get_option('product_image_names');
		
		if ($list === null)
			$list = array();
		else $list = json_decode($list, true);
		
		$list[$_POST['file']] = $_POST['name'];
		
		$this->main->set_option('product_image_names', json_encode($list));
		
		echo 1;
		exit;
		
	}
	
	public function delete_base_image() {
		
		if (!$this->main->caps('lumise_can_upload')) {
			echo $this->main->lang('Sorry, You do not have permission to delete');
			exit;
		}
		
		$file = isset($_POST['file']) ? $_POST['file'] : '';
		
		if (empty($file) || !is_file($this->main->cfg->upload_path.'products'.DS.$file)) {
			echo $this->main->lang('Error, the file does not exist');
			exit;
		}
		
		$del = unlink($this->main->cfg->upload_path.'products'.DS.$file);
		
		if ($del) {
			
			$list = $this->main->get_option('product_image_names');
			
			if ($list === null)
				$list = array();
			else $list = json_decode($list, true);
			
			unset($list[$file]);
			
			$this->main->set_option('product_image_names', json_encode($list));
		
		}
		
		echo $del; 
		exit;
		
	}
	
	public function setup() {
		if (method_exists($this->main->connector, 'setup')) {
			call_user_func_array(array(&$this->main->connector, 'setup'), array());
		}
	}
	
	public function addon() {
		
		$result = $this->main->do_action('addon-ajax');
		echo (is_array($result) ? json_encode($result) : $result);
		
		exit;
		
	}
		
	public function send_bug() {
		
		$hist = $this->main->connector->get_session('bug-reporting');
		
		if ($hist === null)
			$hist = 0;
		
		if ($hist >= 10) {
			echo json_encode(array( 
				"success" => 0,
				"message" => $this->main->lang('Error, has exceeded the allowable limit')
			));
			exit;
		}
		
		$this->main->connector->set_session('bug-reporting', $hist+1);
		
		$id = $this->main->db->insert('bugs', array(
			'content' => substr(urldecode(base64_decode($_POST['content'])), 0, 1500),
			'status' => 'new',
			'lumise' => 0,
			'created' => date("Y-m-d").' '.date("H:i:s"),
			'updated' => date("Y-m-d").' '.date("H:i:s")
		));
		
		if ($this->main->cfg->settings['report_bugs'] == 2)
			$this->report_bug_lumise($id);
		
		echo json_encode(array(
			"success" => 1
		));
		
	}
	
	public function report_bug() {
		echo $this->report_bug_lumise($_POST['id']) ? 'Success' : 'Fail';
	}

}
/*----------------------*/
new lumise_ajax();
/*----------------------*/
