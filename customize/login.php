<?php

if (!defined('LUMISE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require('autoload.php');

global $lumise;

$color = explode(':', $lumise->cfg->settings['primary_color']);
$action = isset($_POST['action']) ? $_POST['action'] : '';
$redirect = isset($_POST['redirect']) && !empty($_POST['redirect']) ? $_POST['redirect'] : urlencode((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : 'http') .'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport" />
		<title><?php echo $lumise->lang('Control Panel Login'); ?></title>
		<link rel="stylesheet" href="<?php echo theme('assets/css/login.css', true); ?>">
		<script type="text/javascript" src="<?php echo theme('assets/js/jquery.min.js', true); ?>"></script>
		<?php $lumise->do_action('editor-header'); ?>
	</head>
<body>
	
	<?php if (isset($_GET['reset-password']) && !empty($_GET['reset-password'])) { ?>
		<div id="login-form" class="reset-password-form">
			<a id="logo" href="<?php echo $lumise->cfg->settings['logo_link']; ?>">
				<img src="<?php echo theme('assets/images/logo_login.png', true); ?>" />
			</a>
			<h1>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="18px" height="18px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" fill="#9e9e9e"><g><path d="M394.667,214.421v-75.755C394.667,62.208,332.459,0,256,0S117.333,62.208,117.333,138.667v75.755    c-24.32,4.949-42.667,26.496-42.667,52.245v192C74.667,488.064,98.581,512,128,512h256c29.419,0,53.333-23.936,53.333-53.333v-192    C437.333,240.917,418.987,219.371,394.667,214.421z M279.659,378.24l8.235,57.579c0.448,3.072-0.469,6.165-2.496,8.491    c-2.027,2.325-4.971,3.691-8.064,3.691h-42.667c-3.093,0-6.037-1.344-8.064-3.669s-2.944-5.44-2.496-8.491l8.235-57.579    c-17.835-8.917-29.675-27.328-29.675-47.595c0-29.397,23.915-53.333,53.333-53.333s53.333,23.936,53.333,53.333    C309.333,350.933,297.493,369.344,279.659,378.24z M330.667,213.333H181.333v-74.667C181.333,97.493,214.827,64,256,64    s74.667,33.493,74.667,74.667V213.333z"></path></g></svg>
				<?php echo $lumise->lang('Reset admin password'); ?>
			</h1>
			<form method="post" action="" id="main-form">
				<?php if (
						$lumise->get_option('reset_token') != $lumise->esc('reset-password', '') ||
						time() > $lumise->get_option('reset_expires')
					) { ?>
				<p><center><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 486.463 486.463" style="enable-background:new 0 0 486.463 486.463;" xml:space="preserve" fill="#ff6c6c" width="50px" height="50px"><g><g><path d="M243.225,333.382c-13.6,0-25,11.4-25,25s11.4,25,25,25c13.1,0,25-11.4,24.4-24.4    C268.225,344.682,256.925,333.382,243.225,333.382z"/><path d="M474.625,421.982c15.7-27.1,15.8-59.4,0.2-86.4l-156.6-271.2c-15.5-27.3-43.5-43.5-74.9-43.5s-59.4,16.3-74.9,43.4    l-156.8,271.5c-15.6,27.3-15.5,59.8,0.3,86.9c15.6,26.8,43.5,42.9,74.7,42.9h312.8    C430.725,465.582,458.825,449.282,474.625,421.982z M440.625,402.382c-8.7,15-24.1,23.9-41.3,23.9h-312.8    c-17,0-32.3-8.7-40.8-23.4c-8.6-14.9-8.7-32.7-0.1-47.7l156.8-271.4c8.5-14.9,23.7-23.7,40.9-23.7c17.1,0,32.4,8.9,40.9,23.8    l156.7,271.4C449.325,369.882,449.225,387.482,440.625,402.382z"/><path d="M237.025,157.882c-11.9,3.4-19.3,14.2-19.3,27.3c0.6,7.9,1.1,15.9,1.7,23.8c1.7,30.1,3.4,59.6,5.1,89.7    c0.6,10.2,8.5,17.6,18.7,17.6c10.2,0,18.2-7.9,18.7-18.2c0-6.2,0-11.9,0.6-18.2c1.1-19.3,2.3-38.6,3.4-57.9    c0.6-12.5,1.7-25,2.3-37.5c0-4.5-0.6-8.5-2.3-12.5C260.825,160.782,248.925,155.082,237.025,157.882z"/></g></g></svg></center></p>
				<p class="login-msg msg-error" style="text-align: center;"><?php 
					echo $lumise->lang('The reset token is invalid or has expired'); 
				?></p>
				<?php } else { ?>
				<?php
					$msgs = (array)$lumise->connector->get_session('login-msg');
					if (isset($msgs) && count($msgs) > 0) {
						foreach ($msgs as $msg) {
							if (isset($msg['content'])) {
								echo '<p class="login-msg msg-'.$msg['type'].'">'.$msg['content'].'</p>';
							}
						}
						$lumise->connector->set_session('login-msg', null);
					} else {
				?>
					<p><strong><?php echo $lumise->lang('Please enter new password'); ?></strong></p>
				<?php  } ?>
				<div class="inputContainer">
					<input name="password" placeholder="<?php echo $lumise->lang('Enter new password'); ?>" type="password" />
				</div>
				<div class="inputContainer">
					<input name="password2" placeholder="<?php echo $lumise->lang('Re-Enter new password'); ?>" type="password" />
				</div>
				<input name="redirect" type="hidden" value="<?php echo urlencode($lumise->cfg->admin_url); ?>" />
				<input name="action" type="hidden" value="reset" />
				<button type="submit"><?php echo $lumise->lang('Reset password'); ?></button>
				<input name="token" type="hidden" value="<?php echo $lumise->esc('reset-password', ''); ?>" />
				<input name="nonce" type="hidden" value="<?php echo lumise_secure::create_nonce('LOGIN-SECURITY'); ?>" />
				<?php } ?>
			</form>
			<a href="<?php echo $lumise->cfg->admin_url; ?>ref=flink" class="form_homeLink">
				← <?php echo $lumise->lang('Back to login'); ?>
			</a>
		</div>
	
	<?php } else { ?>
	
	<div id="login-form">
		<a id="logo" href="<?php echo $lumise->cfg->settings['logo_link']; ?>">
			<img src="<?php echo theme('assets/images/logo_login.png', true); ?>" />
		</a>
		<h1>
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="18px" height="18px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" fill="#9e9e9e"><g><path d="M394.667,214.421v-75.755C394.667,62.208,332.459,0,256,0S117.333,62.208,117.333,138.667v75.755    c-24.32,4.949-42.667,26.496-42.667,52.245v192C74.667,488.064,98.581,512,128,512h256c29.419,0,53.333-23.936,53.333-53.333v-192    C437.333,240.917,418.987,219.371,394.667,214.421z M279.659,378.24l8.235,57.579c0.448,3.072-0.469,6.165-2.496,8.491    c-2.027,2.325-4.971,3.691-8.064,3.691h-42.667c-3.093,0-6.037-1.344-8.064-3.669s-2.944-5.44-2.496-8.491l8.235-57.579    c-17.835-8.917-29.675-27.328-29.675-47.595c0-29.397,23.915-53.333,53.333-53.333s53.333,23.936,53.333,53.333    C309.333,350.933,297.493,369.344,279.659,378.24z M330.667,213.333H181.333v-74.667C181.333,97.493,214.827,64,256,64    s74.667,33.493,74.667,74.667V213.333z"></path></g></svg>
			<?php echo $lumise->lang('Control Panel Login'); ?>
		</h1>
		<?php
				
			$msgs = (array)$lumise->connector->get_session('login-msg');
			$limit = $this->get_session('LIMIT');
			$email = isset($_POST['email']) ? $_POST['email'] : '';
			$admin_email = $lumise->get_option('admin_email');
		
		?>
		<form method="post" action="" id="main-form"<?php if ($action == 'reset'){echo ' style="display: none"';} ?>>
			<?php
							
				if (isset($msgs) && count($msgs) > 0) {
					foreach ($msgs as $msg) {
						if (isset($msg['content'])) {
							echo '<p class="login-msg msg-'.$msg['type'].'">'.$msg['content'].'</p>';
						}
					}
				}
				
				if (isset($limit) && is_array($limit) && $limit[0] >= 5 && time()-$limit[1] < 60*60) {
					
			?>	<p><center><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 486.463 486.463" style="enable-background:new 0 0 486.463 486.463;" xml:space="preserve" fill="#ff6c6c" width="50px" height="50px"><g><g><path d="M243.225,333.382c-13.6,0-25,11.4-25,25s11.4,25,25,25c13.1,0,25-11.4,24.4-24.4    C268.225,344.682,256.925,333.382,243.225,333.382z"/><path d="M474.625,421.982c15.7-27.1,15.8-59.4,0.2-86.4l-156.6-271.2c-15.5-27.3-43.5-43.5-74.9-43.5s-59.4,16.3-74.9,43.4    l-156.8,271.5c-15.6,27.3-15.5,59.8,0.3,86.9c15.6,26.8,43.5,42.9,74.7,42.9h312.8    C430.725,465.582,458.825,449.282,474.625,421.982z M440.625,402.382c-8.7,15-24.1,23.9-41.3,23.9h-312.8    c-17,0-32.3-8.7-40.8-23.4c-8.6-14.9-8.7-32.7-0.1-47.7l156.8-271.4c8.5-14.9,23.7-23.7,40.9-23.7c17.1,0,32.4,8.9,40.9,23.8    l156.7,271.4C449.325,369.882,449.225,387.482,440.625,402.382z"/><path d="M237.025,157.882c-11.9,3.4-19.3,14.2-19.3,27.3c0.6,7.9,1.1,15.9,1.7,23.8c1.7,30.1,3.4,59.6,5.1,89.7    c0.6,10.2,8.5,17.6,18.7,17.6c10.2,0,18.2-7.9,18.7-18.2c0-6.2,0-11.9,0.6-18.2c1.1-19.3,2.3-38.6,3.4-57.9    c0.6-12.5,1.7-25,2.3-37.5c0-4.5-0.6-8.5-2.3-12.5C260.825,160.782,248.925,155.082,237.025,157.882z"/></g></g></svg></center></p>
				<p class="login-msg msg-error" style="text-align: center;"><?php echo $lumise->lang('You have failed logging in for 5 times. For the security, please try again in ').round(60-((time()-$limit[1])/60)).' '.$lumise->lang('minutes'); ?></p>
				<a href="#forgot">
					<?php echo $lumise->lang('Forgot your password?'); ?>
				</a>
			<?php
				}else if (isset($admin_email) && !empty($admin_email)) {
			?>
				<div class="inputContainer">
					<input name="email" type="text" placeholder="<?php echo $lumise->lang('Email'); ?>" value="<?php echo $email; ?>" />
				</div> 
				<div class="inputContainer">
					<input name="password" placeholder="<?php echo $lumise->lang('Password'); ?>" type="password" />
				</div>
				<input name="action" type="hidden" value="login" />
				<input name="redirect" type="hidden" value="<?php echo $redirect; ?>" />
				<button type="submit"><?php echo $lumise->lang('Login'); ?></button> 
				<a href="#forgot" class="form_footLink">
					<?php echo $lumise->lang('Forgot your password?'); ?>
				</a>
			<?php }else{ ?>
				<p>
					<strong><?php echo $lumise->lang('Please setup the admin\'s email and password'); ?></strong>
				</p>
				<div class="inputContainer">
					<input name="email" type="text" placeholder="<?php echo $lumise->lang('Email'); ?>" value="<?php echo $email; ?>" />
				</div> 
				<div class="inputContainer">
					<input name="password" placeholder="<?php echo $lumise->lang('Password'); ?>" type="password" />
				</div>
				<div class="inputContainer">
					<input name="password2" placeholder="<?php echo $lumise->lang('Repassword'); ?>" type="password" />
				</div>
				<input name="redirect" type="hidden" value="<?php echo $redirect; ?>" />
				<input name="action" type="hidden" value="setup" />
				<button type="submit"><?php echo $lumise->lang('Setup account'); ?></button>
			<?php }?>
			<input name="nonce" type="hidden" value="<?php echo lumise_secure::create_nonce('LOGIN-SECURITY') ?>" />
		</form>
		<form method="post" action="" id="second-form"<?php if ($action != 'reset'){echo ' style="display: none"';} ?>>
			<?php		
				if (isset($msgs) && count($msgs) > 0) {
					foreach ($msgs as $msg) {
						if (isset($msg['content'])) {
							echo '<p class="login-msg msg-'.$msg['type'].'">'.$msg['content'].'</p>';
						}
					}
				}
				
				$lumise->connector->set_session('login-msg', null);
			?>
			<p>
				<strong><?php echo $lumise->lang('Please enter your email to reset the password'); ?></strong>
			</p>
			<div class="inputContainer">
				<input name="email" placeholder="<?php echo $lumise->lang('Email address'); ?>" type="text" />
			</div>
			<button type="submit"><?php echo $lumise->lang('Reset'); ?></button>
			<a href="#cancel-forgot" style="float:left" class="form_footLink">
				← <?php echo $lumise->lang('Back to login'); ?>
			</a>
			<input name="action" type="hidden" value="reset" />
			<input name="redirect" type="hidden" value="<?php echo $redirect; ?>" />
			<input name="reset-token" type="hidden" value="<?php echo lumise_secure::create_nonce('RESET-SECURITY') ?>" />
		</form>
		<a href="<?php echo $lumise->cfg->settings['logo_link']; ?>" class="form_homeLink">
			← <?php echo $lumise->lang('Back to home'); ?>
		</a>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$('a[href="#forgot"]').on('click', function(e) {
				$('form#main-form').hide();
				$('form#second-form').show();
				e.preventDefault();
			});
			$('a[href="#cancel-forgot"]').on('click', function(e) {
				$('form#main-form').show();
				$('form#second-form').hide();
				e.preventDefault();
			});
		});
	</script>
	<?php } ?>
</body>
</html>