<?php 

@session_start();
			
if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
	
define('LUMISE', 'installer');

define('INST_PATH', dirname(__FILE__));
$path_arr = explode(DS,INST_PATH);
define('SOURCE_PATH', str_replace(array_pop($path_arr),'', INST_PATH));

require(SOURCE_PATH . DS .'core' . DS . 'includes' . DS . 'database.php');
$uri = explode('installer', $_SERVER['REQUEST_URI']);
$scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
define('SITE_URI', $scheme."://$_SERVER[HTTP_HOST]" . $uri[0]);

$step = isset($_REQUEST['step'])? $_REQUEST['step'] : 0;
//write file
$connector_file = SOURCE_PATH . DS . 'php_connector-sample.php';
$installed_flag = INST_PATH . DS . 'installed.flag';
$connector_file_path = SOURCE_PATH . 'php_connector.php';

if(
	file_exists($installed_flag)
) $step = 100;

function installer_header(){
	
	header( 'Content-Type: text/html; charset=utf-8' );
	?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex,nofollow" />
		<title>Lumise &rsaquo; Setup Configuration File</title>
		<link rel="stylesheet" id="install-css" href="style.css" type="text/css" media="all">
	</head>
	<body>
	<p id="logo"><a href="https://www.lumise.com" target="_blank" tabindex="-1">Lumise</a></p>
	<?php
}

function installer_footer(){
	?>
	</body></html>
	<?php
}

function mysqli_import_sql( $args , $config) {
	// check mysqli extension installed

	$mysqli = @new mysqli( $config['host'], $config['user'], $config['pass'], $config['name'] );

	$querycount = 11;
	$queryerrors = '';
	$lines = (array) $args;

	if( is_string( $args ) ) {
		$lines =  array( $args ) ;
	}
	if ( ! $lines ) {
		return array('status' => false, 'msg' =>'Cannot execute ' . $args);
	}

	$scriptfile = false;
	$templine = '';
	$queries = array();
	
	foreach ($lines as $line) {
		
		$line = trim( $line );
		
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		
		$templine .= $line;

		if (substr(trim($line), -1, 1) == ';')
		{	
			$templine = str_replace('lumise_', $config['prefix'], $templine);
			if ( ! $mysqli->query( $templine ) ) {
				$queryerrors .= '' . 'Line ' . $querycount . ' - ' . $mysqli->error . '<br>';
			}
			
			$templine = '';
			
		}

	}
	
	if ( $queryerrors ) {
		return array('status' => false, 'msg' => 'There was an error on database.sql<br>' . $queryerrors);
	}

	if( $mysqli && ! $mysqli->error ) {
		@$mysqli->close();
	}   

	return array('status' => true, 'msg' =>'Complete dumping database !');
}


switch ($step) {
	
	case 0:
	
		installer_header();
		?>
		<p>Welcome to Lumise. Before getting started, we need some information on the database. You will need to know the following items before proceeding.</p>
		<ol>
			<li>Database name</li>
			<li>Database username</li>
			<li>Database password</li>
			<li>Database host</li>
			<li>Table prefix (if you want to run more than one Lumise in a single database)</li>
		</ol>
		<p>
            We’re going to use this information to create a <code>php_connector.php</code> file.	<strong>If for any reason this automatic file creation doesn’t work, don’t worry. All this does is fill in the database information to a configuration file. You may also simply open <code>php_connector-sample.php</code> in a text editor, fill in your information, and save it as <code>php_connector.php</code>.</strong>
			Need more help? <a href="https://docs.lumise.com/" target="_blank">We got it</a>.
        </p>
		<p class="step"><a href="index.php?step=1" class="button">Let’s go!</a></p>
		<?php
		installer_footer();
		break;
		
	case 1:
		installer_header();
		?>
		<form method="post" action="index.php?step=2">
			<p>Below you should enter your database connection details. If you’re not sure about these, contact your host.</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="dbname">Database Name</label></th>
						<td><input name="dbname" id="dbname" type="text" size="25" value="lumise">
						<em>The name of the database you want to use with Lumise.</em></td>
					</tr>
					<tr>
						<th scope="row"><label for="uname">Username</label></th>
						<td><input name="uname" id="uname" type="text" size="25" value="" placeholder="Database username">
						<em>Your database username.</em></td>
					</tr>
					<tr>
						<th scope="row"><label for="pwd">Password</label></th>
						<td><input name="pwd" id="pwd" type="text" size="25" value="" autocomplete="off" placeholder="Database password">
						<em>Your database password.</em>
                        </td>
					</tr>
					<tr>
						<th scope="row"><label for="dbhost">Database Host</label></th>
						<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost">
						<em>You should be able to get this info from your web host, if <code>localhost</code> doesn’t work.</em></td>
					</tr>
					<tr>
						<th scope="row"><label for="prefix">Table Prefix</label></th>
						<td><input name="prefix" id="prefix" type="text" value="lumise_" size="25">
						<em>If you want to run multiple Lumise installations in a single database, change this.</em></td>
					</tr>
				</tbody>
			</table>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="data_path">Upload path</label></th>
						<td><input name="data_path" id="data_path" type="text" size="25" value="<?php echo SOURCE_PATH . 'data' . DS;?>">
						<em>Your data folder path to store resource content such as clipart, templates, fonts,.. If you want to store into other folder, change it.</p></td>
					</tr>
					<tr>
						<th scope="row"><label for="data_url">Upload URL</label></th>
						<td><input name="data_url" id="data_url" type="text" size="25" value="<?php echo SITE_URI.'data/';?>">
						<em>Your data url for access resource.</em></td>
					</tr>
				</tbody>
			</table>
			<p class="step"><input name="submit" type="submit" value="Submit" class="button button-large"></p>
			<input type="hidden" name="do" value="action"/>
		</form>
		<?php
		installer_footer();
		break;
		
	case 2:

		installer_header();
		
		$msg = array();
		$tryagain_link = '<p class="step"><a href="index.php?step=1" onclick="javascript:history.go(-1);return false;" class="button">Try again</a></p>';
		
		if(isset($_POST['do']) && $_POST['do'] == 'action'){
			
			$dbname = trim( $_POST[ 'dbname' ] );
			$uname = trim( $_POST[ 'uname' ] );
			$pwd = trim( $_POST[ 'pwd' ] );
			$dbhost = trim( $_POST[ 'dbhost' ] );
			$prefix = trim( $_POST[ 'prefix' ] );
			$data_path = trim( $_POST[ 'data_path' ] );
			$data_url = trim( $_POST[ 'data_url' ] );
			
			
			
			if(empty($prefix))
				array_push($msg, '<strong>ERROR</strong>: "Table Prefix" must not be empty.');

			// Validate $prefix: it can only contain letters, numbers and underscores.
			if ( preg_match( '|[^a-z0-9_]|i', $prefix ) ){
				array_push($msg, '<strong>ERROR</strong>: "Table Prefix" can only contain numbers, letters, and underscores.');
			}

			if( ! function_exists('mysqli_connect') ) {
				array_push($msg, 'Lumise needs mysql extension to be running properly ! please resolve!!');
			}else{
				//try connect database
				define('DB_HOST', $dbhost);
				define('DB_DBNAME', $dbname);
				define('DB_USER', $uname);
				define('DB_PASS', $pwd);
				define('DB_PREFIX', $prefix);
				define('UPLOAD_PATH', $data_path);
				define('UPLOAD_URL', $data_url);

				
				@$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DBNAME);

				if ($mysqli->connect_error) {
					array_push($msg, '<p>This either means that the username and password information in your <code>php_connector.php</code> file is incorrect or we can’t contact the database server at <code><?php echo DB_HOST;?></code>. This could mean your host’s database server is down.</p>');
					array_push($msg, '<strong>ERROR</strong>: ' . 'Connect Error ' . $mysqli->connect_errno . ': ' . $mysqli->connect_error);
				}
			}
				
			if ( ! is_writable(SOURCE_PATH) ) array_push($msg, 'Sorry, but I can not write the file php_connector.php');
		}else{
			array_push($msg, 'Are you sure you were in the right place?');
		}
		
		if(count($msg)>0){
			
			?>
			<h1>Error establishing a database connection</h1>
			
			<?php
			foreach ($msg as $m) {
				echo '<p>' . $m . '</p>';
			}

			echo $tryagain_link;
			
		}else {
			
			$connector_content = file(SOURCE_PATH . DS . 'php_connector-sample.php');
			
			foreach ( $connector_content as $line_num => $line ) {
				if ( ! preg_match( '/{([A-Z_]+)}/', $line, $match ) )
					continue;
				$constant = $match[1];
				switch (str_replace(array('{', '}'), array('',''), $constant)) {
					
					case 'DB_HOST':
					case 'DB_PASS':
					case 'DB_PREFIX':
					case 'DB_USER':
					case 'DB_DBNAME':
					case 'UPLOAD_PATH':
					case 'UPLOAD_URL':
					case 'APP_URI':

						if(defined($constant)){
							$connector_content[ $line_num ] = str_replace('{'.$constant.'}', addcslashes( constant( $constant ), "\\'"), $connector_content[ $line_num ]);
						}
						
						break;
					
					default:
					break;
				}
			}

			$handle = fopen( $connector_file_path, 'w' );
			foreach ( $connector_content as $line ) {
				fwrite( $handle, $line );
			}
			fclose( $handle );
			chmod( $connector_file_path, 0666 );
			
			//create data folder
			if (!is_dir($data_path))
				mkdir($data_path, 0755);
			
			?>
			<h1 class="screen-reader-text">Successful database connection</h1>
			<p>All right, sparky! You&#8217;ve made it through this part of the installation. Lumise can now communicate with your database. If you are ready, time now to&hellip;</p>
			<p>To install fresh database, just press the button bellow.</p>
			<form method="post" action="index.php?step=3">
			<input name="submit" type="submit" value="Create database" class="button button-large"></p>
			<input type="hidden" name="do" value="action"/>
			</form>
			<?php
		}
		installer_footer();
		break;

	case 3 :
		
		$msg = array();
		if(isset($_POST['do']) && $_POST['do'] == 'action'){
			require(SOURCE_PATH . DS .'php_connector.php');
			$connector = new lumise_connector();

			$sql_file = SOURCE_PATH . DS .'installer'.DS.'data'.DS.'database.sql';
			$msg = mysqli_import_sql(file($sql_file), $connector->config['database']);

			//import sample products and shape
			if(isset($msg['status']) && $msg['status']){
				$sql_file = SOURCE_PATH . DS .'installer'.DS.'data'.DS.'sample.sql';
				mysqli_import_sql(file($sql_file), $connector->config['database']);
			}
						
			file_put_contents($installed_flag,'');
		}else{
			array_push($msg, 'Are you sure you were in the right place?');
		}
		
		installer_header();
		
		if ( isset($msg['status']) && $msg['status'] ) {
			
			$_SESSION['ROLE'] = null;
			$_SESSION['UID'] = null; 
			
		?>
			<h1 class="screen-reader-text">Successful import database </h1>
			<p>Lumise is ready on your site. You can access admin panel at <a href="<?php echo SITE_URI .'admin.php';?>"><?php echo SITE_URI .'admin.php';?></a> </p>

			<p class="step button-links">
				<a href="<?php echo SITE_URI;?>" class="button-link" target="_blank">Frontend</a>
				<a href="<?php echo SITE_URI .'admin.php';?>" class="button-link" target="_blank">Admin Panel</a>
			</p>
			<?php
		}else{
			?>
			<h1 class="screen-reader-text">Error establishing a database importing</h1>
			<p><?php echo $msg['msg'];?></p>

			<p class="step">
				<a href="index.php?step=1" class="button button-large">Try again</a>
			</p>
			<?php
		}
		installer_footer();
		break;		
	
	default:
		installer_header();
		?>
		<h1>Error establishing a Lumise instance</h1>
		<p>There is an ready Lumise instance in your folder. Please ensure you are doing right thing.</p>
		<p>If you want to reinstall Lumise. Please remove files below to then refesh this page again.</p>
		<ol>
			<li>/installer/<?php echo basename($installed_flag);?></li>
			<li><?php echo basename($connector_file_path);?></li>
		</ol>
		<p class="step"><a href="index.php" class="button">Refresh</a></p>
		<?php
		installer_footer();
		break;
}
