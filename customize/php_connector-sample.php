<?php

class lumise_connector {
    
    public $platform;
    
    protected $order_statuses;
    
    public function __construct() {
		
		global $lumise;
		
        $this->platform = 'php';
        $url = $this->url(1);
        
        $order_statuses = array(
            'pending' => 'Pending',
            'complete' => 'Complete',
            'processing' => 'Processing',
            'cancel' => 'Cancel',
        );
        
        $this->config = array(
			"url" => $url,
			"tool_url" => $url.'editor.php',
			"logo" => $url. 'core/assets/images/logo.v3.png',
			"ajax_url" => $url.'editor.php?lumise-router=ajax',
			"admin_ajax_url" => $url.'editor.php?lumise-router=ajax',
            "checkout_url" => $url.'editor.php?lumise-router=cart',
			"assets_url" => $url.'core/',
			"load_jquery" => true,
			"root_path" => dirname(__FILE__).DS.'core'.DS,
            
            "upload_path" => '{UPLOAD_PATH}',
			"upload_url" => '{UPLOAD_URL}',
            "admin_assets_url" => $url.'core/admin/assets/',
            "admin_url" => $url.'admin.php?',
            
			"database" => array(
                "host" => '{DB_HOST}',
                "user" => '{DB_USER}',
                "pass" => '{DB_PASS}',
                "name" => '{DB_DBNAME}',
                "prefix" => '{DB_PREFIX}'
            )
		);
        
        if(isset($lumise)){
            $this->order_statuses = array(
                'pending' => $lumise->lang('Pending'),
                'complete' => $lumise->lang('Complete'),
                'processing' => $lumise->lang('Processing'),
                'cancel' => $lumise->lang('Cancel'),
            );
        }
        
        
        if(isset($lumise)){
            $lumise->add_action('admin-verify', array(&$this, 'admin_verify'));
            $lumise->add_action('before_order_products', array(&$this, 'update_order_status'));
            $lumise->add_action('before_orders', array(&$this, 'update_orders'));
    		$lumise->add_filter('order_status', array(&$this, 'order_status_name'));
        }
    }
    
    public function url($f = 0) {
	    
	    global $lumise;
	    
	    if ($f === 0)
	    	$uri = '';
	    else if ($f === 1)
	    	$uri = (dirname($_SERVER['SCRIPT_NAME']) == '/')? '/' : dirname($_SERVER['SCRIPT_NAME']).'/';
	    else $uri = $_SERVER['REQUEST_URI'];
	    
	    $scheme = 'http';
	    
	    if (
		    (isset($_SERVER['HTTP_CF_VISITOR']) && isset($_SERVER['HTTP_CF_VISITOR']->scheme) && $_SERVER['HTTP_CF_VISITOR']->scheme == 'https') ||
		    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
		    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 
		    $_SERVER['SERVER_PORT'] == 443
	    ) $scheme = 'https';
	    
		return $scheme."://".$_SERVER['HTTP_HOST'].$uri;
		
	}
    
    
	public function admin_verify() {
		
		if (isset($_POST['nonce']))
			$this->process_login();
		
		if (isset($_POST['reset-token']))
			$this->process_reset();
		
		if (!$this->is_admin()) {
		
			include 'login.php';
			exit;
		} else if (isset($_GET['signout']) && $_GET['signout'] == 'true') {
			$this->process_logout();
		}
		
	}

	public function process_login() {
		
		global $lumise;
		$action = isset($_POST['action']) ? $_POST['action'] : '';
		$redirect = isset($_POST['redirect']) && !empty($_POST['redirect']) ? $_POST['redirect'] : $this->config->url.'?lumise-router=admin';
		$msg = array();
		$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
		$limit = $this->get_session('LIMIT');
		
		if ($limit[0] >= 5 && time()-$limit[1] < 60*60) {
			header('location:'.urldecode($redirect));
			exit;
		}
		
		$admin_email = $lumise->get_option('admin_email');
			
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		if ($limit === null || !is_array($limit) || time()-$limit[1] > 60*60)
			$limit = array(0, time());
            
        $check = lumise_secure::check_nonce('LOGIN-SECURITY', $nonce);
		if (!$check) {
			$limit[0] += 1;
			$limit[1] = time();
			array_push($msg, array(
				'type' => 'error',
				'content' => $lumise->lang('Invalid login token').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
			));
		}else 
		if ($action == 'login') {
			
			if (!isset($admin_email) || empty($admin_email)) {
				$limit[0] += 1;
				$limit[1] = time();
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('The admin account has not been setup').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
				));
			}else{
				
				if (empty($email)) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your email is empty')
					));
				}
				else if (empty($password)) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your password is empty')
					));
				}
				else if ($admin_email != $email || $lumise->get_option('admin_password', '') != md5($password)) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your email or password is incorrect')
					));
				}
				
				if (count($msg) > 0) {
					$limit[0] += 1;
					$limit[1] = time();
					$msg[count($msg)-1]['content'] .= ', '.$limit[0].' '.$lumise->lang('failed login attempts.');
				}else{
					$this->set_session('UID', $email);
					$this->set_session('ROLE', 1);
					header('location:'.urldecode($redirect));
					exit;
				}
			}
			
		}else 
		if ($action == 'setup') {
			
			if (!isset($admin_email) || empty($admin_email)) {
				
				$password2 = $_POST['password2'];
				
				if (strpos($email, '@') === false || strpos($email, '.') === false) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your email is invalid')
					));
				}
				
				if (strlen($password) < 8) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your password must be at least 8 characters')
					));
				}
				
				if ($password != $password2) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Repeat passwords do not match')
					));
				}
				
				if (count($msg) === 0) {
					$lumise->set_option('admin_email', $email);
					$lumise->set_option('admin_password', md5($password));
					$this->set_session('UID', $email);
					$this->set_session('ROLE', 1);
					header('location:'.urldecode($redirect));
					exit;
				}
				
			}else{
				$limit[0] += 1;
				$limit[1] = time();
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('The admin account has been setup').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
				));
			}
		}else 
		if ($action == 'reset') {
			
			if (
				!isset($_POST['password']) || 
				empty($_POST['password']) ||
				empty($_POST['password2']) ||
				$_POST['password'] != $_POST['password2'] ||
				strlen($_POST['password']) < 8
			) {
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('Passwords do not match or less than 8 characters')
				));
				$this->set_session('login-msg', $msg);
			} else {
				$lumise->set_option('admin_password', md5(trim($_POST['password'])));
				array_push($msg, array(
					'type' => 'success',
					'content' => $lumise->lang('Your password has been changed successfully')
				));
				$this->set_session('login-msg', $msg);
				header('location:'.$lumise->cfg->admin_url.'ref=reset');
				exit;
			}
			
		}else{
			$limit[0] += 1;
			$limit[1] = time();
			array_push($msg, array(
				'type' => 'error',
				'content' => $lumise->lang('Invalid action').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
			));
		}
		
		$this->set_session('LIMIT', $limit);
		$this->set_session('login-msg', $msg);
		
		if ($limit[0] >= 5 && time()-$limit[1] < 60*60) {
			header('location:'.urldecode($redirect));
			exit;
		}
		
	}
	
	public function process_logout() {
		
		global $lumise;
		
		$this->set_session('UID', null);
		$this->set_session('ROLE', null);
		
		header('location:'.$lumise->cfg->admin_url.'ref=signout');
		
	}
	
	public function process_reset() {
		
		global $lumise;
		
		$nonce = isset($_POST['reset-token']) ? $_POST['reset-token'] : '';
		$limit = $this->get_session('LIMIT');
		$email = $_POST['email'];
		$msg = array();
		
		if ($limit === null || !is_array($limit) || time()-$limit[1] > 60*60)
			$limit = array(0, time(), 0, time());
			
		if (!lumise_secure::check_nonce('RESET-SECURITY', $nonce)) {
			$limit[2] += 1;
			$limit[3] = time();
			array_push($msg, array(
				'type' => 'error',
				'content' => $lumise->lang('Invalid reset token').', '.$limit[0].' '.$lumise->lang('failed reset attempts.')
			));
		}else{
			if ($limit[2] >= 5 && time()-$limit[3] < 60*60) {
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('You have failed reseting for 5 times. For the security, please try again in ').round(60-((time()-$limit[3])/60)).' '.$lumise->lang('minutes')
				));
			}else if ($lumise->get_option('admin_email', '') != $email) {
				if ($limit[2] < 5)
					$limit[2] += 1;
				else $limit[2] = 1;
				$limit[3] = time();
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('The email does not exist').', '.$limit[2].' '.$lumise->lang('failed reset attempts.')
				));
			}else{
				
				$token = $lumise->generate_id();
				$lumise->set_option('reset_token', $token);
				$lumise->set_option('reset_expires', time()+(60*10));
				
				
				$to      =  $lumise->cfg->settings['admin_email'];
				$subject = 'Lumise - Reset control panel password';
				$message = "Please click to the link bellow to reset your password.\nThis link will expire within 10 minutes.\n".
					   $lumise->cfg->admin_url."reset-password=".$token;
				$message = wordwrap($message,70);
				$url = parse_url($lumise->cfg->url);
				$headers = 'From: no-reply@'.$url['host'] . "\r\n" .
				    'Reply-To: no-reply@'.$url['host'] . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
				
				if (mail($to, $subject, $message, $headers)) {
					$limit[2] = 0;
					$limit[3] = time();
					unset($_POST['action']);
					array_push($msg, array(
						'type' => 'success',
						'content' => $lumise->lang('A reset email has been sent, please check your inbox (including spam box)')
					));
				}else if (mail($to, $subject, $message, $headers)) {
					$limit[3] = time();
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Could not send mail, ensure that the mail() function on your webserver can work')
					));
				}
			}
		}
		
		$this->set_session('LIMIT', $limit);
		$this->set_session('login-msg', $msg);
		
	}

    public function get_session($name) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public function set_session($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function cookie($name) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }
    
    public function is_admin() {
		
		// return user is admin
		
		global $lumise;
		
		return ($this->get_session('ROLE') === 1 && $this->get_session('UID') !== null);
		
	}
	
	public function is_login() {
		
		// return user id, 0 if not login
		
		global $lumise;
		return $lumise->connector->cookie('uid') || 0;
		
	}
    
    public function get_currency() {
        return "$";
    }
    
    public function filter_product($data) {
		
        return $data;
        
    }
            
    public function filter_products($products) {
		
		return $results;
        
    }
    
	public function add_to_cart($data){
		
		global $lumise;
    	return $lumise->cfg->editor_url.'cart.php';
		
	}
	
    public function save_order(){
		
		global $lumise;
		$db = $lumise->get_db();

		$cart_data = $this->get_session('lumise_cart');

		$date = @date ("Y-m-d H:i:s");
        
        $guest_data = array(
            'name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
            'email' => $_POST['email'],
            'address' => $_POST['address'],
            'zipcode' => $_POST['zip'],
            'city' => $_POST['city'],
            'country' => $_POST['country'],
            'phone' => $_POST['phone'],
            'created' => $date,
            'updated' => $date
        );
        
        $guest_id = $db->insert ('guests', $guest_data);
        
        $order_data = array(
			'total' => $cart_data['total'],
			'status' => 'pending',
			'currency' => $lumise->cfg->settings['currency'],
			'user_id' => $guest_id,
            'payment' => $_POST['payment'],
            'txn_id' => '',
			'created' => $date,
			'updated' => $date
		);
        
		$order_id = $db->insert ('orders', $order_data);
        
        $order = $this->get_session('lumise_cart');
        
        $order['user'] = $guest_data;
        $order['created'] = $date;
        $order['id'] = $order_id;
        $order['payment'] = $_POST['payment'];
        $order['status'] = 'pending';
        
        $order_data['id'] = $order_id;
        
        $this->set_session('lumise_justcheckout', $order);
        
        $cart_data = $this->get_session('lumise_cart');
        
		$store = $lumise->lib->store_cart($order_id, $cart_data);
        
        if (!$store)
        	return $store;
        	
        $data = array(
            'order_id' => $order_id,
            'order_data' => $order_data,
            'user_data' => $guest_data,
        );
        
		return $data;
		
	}

	public function orders($filter, $orderby, $ordering, $limit, $limit_start) {
		
		global $lumise;
        $db = $lumise->get_db();


		$ops = $db->prefix . 'order_products';
		$os = $db->prefix . 'orders';

		$where = '';

		if (is_array($filter) && isset($filter['keyword']) && !empty($filter['keyword'])) {

            $where = array();
            $fields = explode(',', $filter['fields']);
            $arr_keyword = array();
            for ($i = 0; $i < count($fields); $i++) {
                $arr_keyword[] = sprintf(" %s LIKE '%s' ", $fields[$i], $filter['keyword']);
            }

            $fields = implode(' OR ', $arr_keyword);

            $where[] = $fields;

            if (count($where) > 0)
                $where = (count($where) > 0) ? ' WHERE ' . implode(' AND ', $where) : '';
        }


		$orderby_str = '';
        if ($orderby != null && $ordering != null)
            $orderby_str = ' ORDER BY ' . $orderby . ' ' . $ordering;

		$sql = "SELECT SQL_CALC_FOUND_ROWS "
		. " os.*, os.id as order_id "
		. " FROM $ops as ops "
		. " INNER JOIN $os as os ON os.id = ops.order_id"
		. $where
		. " GROUP BY ops.order_id "
		. $orderby_str
		. " LIMIT $limit_start, $limit";

		$items['rows'] = $db->rawQuery($sql);
		
		$sql = "SELECT FOUND_ROWS() as total";

        $total_count = $db->rawQuery($sql);

        $items['total_count'] = $total_count[0]['total'];

        if($limit != null)
        	$items['total_page'] = ceil($total_count[0]['total'] / $limit) ;
        else $items['total_page'] = 1;

        return $items;
	}
	
	public function redirect($url) {

		if (empty($url))
			return;

		ob_clean();

		@header("location: " . $url);
		exit;

	}

	public function products_order($order_id, $filter, $orderby, $ordering){
		
		global $lumise;
        
        $db = $lumise->get_db();
		
		$items = array('rows' => array());

		$ops = $db->prefix . 'order_products';
		$os = $db->prefix . 'orders';
		$usertb = $db->prefix . 'guests';
		
		$where = array();
		
		$where[] = 'ops.order_id = '. $order_id;

		if (is_array($filter) && isset($filter['keyword']) && !empty($filter['keyword'])) {

			$fields = explode(',', $filter['fields']);
			$arr_keyword = array();
			for ($i = 0; $i < count($fields); $i++) {
				$arr_keyword[] = sprintf(" %s LIKE '%s' ", $fields[$i], $filter['keyword']);
			}

			$fields = '(' . implode(' OR ', $arr_keyword) . ')';

			$where[] = $fields;
				
		}
		

		$orderby_str = '';
		if ($orderby != null && $ordering != null)
			$orderby_str = ' ORDER BY ' . $orderby . ' ' . $ordering;

		$sql = "SELECT "
			. "SQL_CALC_FOUND_ROWS *"
			. " FROM $ops as ops "
			. ' WHERE '. implode(' AND ', $where)
			. ' GROUP BY ops.id '
			. $orderby_str;
			
		$items['rows'] = $db->rawQuery($sql);
		
		$sql = "SELECT FOUND_ROWS() as total";

        $total_count = $db->rawQuery($sql);

        $items['total_count'] = $total_count[0]['total'];
        $items['total_page'] = 1;
		
		//get order data
		$sql = "SELECT "
			. "*"
			. " FROM $os as os "
			. ' WHERE id = '. $order_id;
			
			
		$order = $db->rawQuery($sql);
        $user = array();
        //get order data
        if(isset($order[0]['user_id'])){
            $sql = "SELECT "
    			. "*"
    			. " FROM $usertb as user "
    			. ' WHERE id = '. $order[0]['user_id'];
                
            $user = $db->rawQuery($sql);
        }
		$items['billing'] = (count($user)>0)? $user[0] : array();
        
        if(count($items['billing'])>0){
            $items['billing']['address'] = $items['billing']['address'].
            ', '.
            $items['billing']['city'].
            ', '.
            $items['billing']['country'];
        }
		
		$items['order'] = $order[0];
			
		return $items;
	}
    
    public function update_orders(){
	    
        if(isset($_REQUEST['action'])){
	        
            global $lumise;
            $id = $_REQUEST['id'];
            
            switch (trim($_REQUEST['action'])) {
                
                case 'delete':
                
                    $lumise->lib->delete_row($id, 'orders');
                    $lumise->lib->delete_order_products($id);
                    $lumise_msg = array(
                        'status' => 'success',
                        'msg' => sprintf($lumise->lang('Order #%s deleted.'), $id)
                    );
        			$lumise->connector->set_session('lumise_msg', $lumise_msg);
                    $lumise->redirect($lumise->cfg->admin_url.'lumise-page=orders');
                    break;
                
                default:
                
                    break;
            }
        }
    }
    
    public function update_order_status($order_id){
	    
        if(isset($_POST['order_status'])){
            global $lumise;
            
            $db = $lumise->get_db();
            $db->where ('id', $order_id)->update ('orders', array(
                'status' => $lumise->lib->sql_esc($_POST['order_status']),
                'updated' => @date ("Y-m-d H:i:s")
            ));
        }
    }
    
    public function statuses(){
        return $this->order_statuses;
    }
    
    public function order_status_name($status){
        return isset($this->order_statuses[$status]) ? $this->order_statuses[$status] : $status;
    }
    
	public function update() {
			
		global $lumise;
		
		$lumise_path = dirname(__FILE__);
		$update_path = $lumise->cfg->upload_path.'tmpl'.DS.'lumise';
		$backup_path = $lumise->cfg->upload_path.'update_backup';
		
		$lumise->lib->delete_dir($backup_path);
		
		$connector_content = @file_get_contents($update_path.DS.'php_connector-sample.php');
		
		if (!empty($connector_content)) {
			
			$connector_content = str_replace(array(
				'{UPLOAD_PATH}',
				'{UPLOAD_URL}',
				'{DB_HOST}',
                '{DB_USER}',
                '{DB_PASS}',
                '{DB_DBNAME}',
                '{DB_PREFIX}'
			), array(
				$this->config['upload_path'],
				$this->config['upload_url'],
				$this->config['database']['host'],
				$this->config['database']['user'],
				$this->config['database']['pass'],
				$this->config['database']['name'],
				$this->config['database']['prefix']
			), $connector_content);
			
			@file_put_contents($update_path.DS.'php_connector.php', $connector_content);
			
			/*
			*	Start replace files
			*/
			
			$dir = @opendir($update_path);
			$err = 0;
			
		    while (false !== ($file = @readdir($dir))) {
			    
		        if ($file != '.' && $file != '..') {
			        
			        if (is_dir($update_path.DS.$file)) {
				        
			            if (is_dir($lumise_path.DS.$file))
			            	$lumise->lib->delete_dir($lumise_path.DS.$file);
			            
			            $err += (@rename($update_path.DS.$file, $lumise_path.DS.$file) ? 0 : 1);
			            
			        } else if (is_file($update_path.DS.$file)) {
				        
				    	if (is_file($lumise_path.DS.$file))
				    		$err += (@unlink($lumise_path.DS.$file) ? 0 : 1);
				    	
				    	$err += (@rename($update_path.DS.$file, $lumise_path.DS.$file) ? 0 : 1);
				    	
			        }
		        }
		    }
			
			return $err === 0 ? true : false;
			
		}
		
		return false;
	}

}
