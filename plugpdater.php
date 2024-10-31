<?php
/*
Plugin Name: Plugpdater
Plugin URI: http://imath.owni.fr/2011/04/25/plugpdater/
Description: Makes it easier to locally/manually update plugins.
Version: 0.1
Requires at least: 3.0
Tested up to: 3.1.1
License: GNU/GPL 2
Author: imath
Author URI: http://imath.owni.fr/
Network: true
*/

define ( 'PLUGPDATER_PLUGIN_NAME', 'plugpdater' );
define ( 'PLUGPDATER_URL', WP_PLUGIN_URL . '/' . PLUGPDATER_PLUGIN_NAME );
define ( 'PLUGPDATER_DIR', WP_PLUGIN_DIR . '/' . PLUGPDATER_PLUGIN_NAME );
define ( 'PLUGPDATER_TEMP_DIR', plugpdater_temp_dir() );
define ( 'PLUGPDATER_VERSION', '0.1');


/**
* plugpdater_temp_dir
* defines what will be the path to PLUGPDATER_TEMP_DIR
*/
function plugpdater_temp_dir(){
	$temp_dir = wp_upload_dir();
	return $temp_dir['basedir'].'/plugpdater';
}


/**
* plugpdater_is_django_multisite
* little trick to know if network_admin_menu or admin_menu
*/
function plugpdater_is_django_multisite(){
	if(get_option('db_version')>=17056 && is_multisite()) return true;
	else return false;
}

add_action( plugpdater_is_django_multisite() ? 'network_admin_menu' : 'admin_menu', 'plugpdater_admin' );

/**
* plugpdater_admin
* creates the plugins submenu in WP backend
*/
function plugpdater_admin() {
	if(!file_exists(PLUGPDATER_TEMP_DIR)){
		mkdir(PLUGPDATER_TEMP_DIR);
	}
	if (is_multisite()){
		add_plugins_page('PlugPdater', 'PlugPdater', 'manage_network_plugins', 'plugpdater', 'plugpdater_manager');
	}
	else{
		add_plugins_page('PlugPdater', 'PlugPdater', 'manage_options', 'plugpdater', 'plugpdater_manager');
	}
}


/**
* plugpdater_manager
* main function to manage updates
*/
function plugpdater_manager(){
	if(is_multisite() && !current_user_can( 'manage_network_plugins' )){
		wp_die(__('Only Network administrator can access this page !', 'plugpdater'));
	}
	elseif(!is_multisite() && !current_user_can('manage_options')){
		wp_die(__('Only administrator can access this page !','plugpdater'));
	}
	if(isset($_POST['_plugin_data'])){
		require (dirname(__FILE__).'/includes/plugpdater-uploader.php');
	}
	elseif(isset($_GET['action']) && $_GET['action']=="update"){
		require (dirname(__FILE__).'/includes/plugpdater-installer.php');
	}
	else{
		$plugins = get_plugins();
		if(count($_POST)>=1 && !$_POST['_plugin_data']){
			?>
			<div class="error fade"><p><?php _e('Please, use radio button to select the plugin you want to update','plugpdater');?></p></div>
			<?php
		}
		if(isset($_GET['nozip'])){
			?>
			<div class="error fade"><p><?php _e('Oops, the zip files was not uploaded, please try again. You will need to reactivate the plugin if it was activated','plugpdater');?></p></div>
			<?php
		}
		?>
		<div class="wrap"><h2>PlugPdater Manager</h2>
			<p><?php _e('Please, select a plugin to update :','plugpdater');?></p>
			<form action="plugins.php?page=plugpdater" method="post">
				<div class="plugpdater_btn"><input type="submit" class="button-primary" value="<?php _e('Begin Update Process','plugpdater');?>" name="_plugpdater_maj_top"/></div>
			<?php plugpdater_print_plugins_table($plugins); ?>
				<div class="plugpdater_btn"><input type="submit" class="button-primary" value="<?php _e('Begin Update Process','plugpdater');?>" name="_plugpdater_maj_bottom"/></div>
			</form>
		</div>
		<?php
	}
}


/**
* plugpdater_print_plugins_table
* print the plugins list 
*/
function plugpdater_print_plugins_table($plugins, $context = '') {
?>
<table class="widefat" cellspacing="0" id="<?php echo $context ?>-plugins-table">
<thead>
<tr>
	<th scope="col" class="manage-column check-column">&nbsp;</th>
	<th scope="col" class="manage-column"><?php _e('Plugin','plugpdater'); ?></th>
	<th scope="col" class="manage-column"><?php _e('Status','plugpdater'); ?></th>
	<th scope="col" class="manage-column"><?php _e('Description','plugpdater'); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column check-column">&nbsp;</th>
	<th scope="col" class="manage-column"><?php _e('Plugin','plugpdater'); ?></th>
	<th scope="col" class="manage-column"><?php _e('Status','plugpdater'); ?></th>
	<th scope="col" class="manage-column"><?php _e('Description','plugpdater'); ?></th>
</tr>
</tfoot>

<tbody class="plugins">
<?php

	if ( empty($plugins) ) {
		echo '<tr>
			<td colspan="3">' . __('No plugins to show','plugpdater') . '</td>
		</tr>';
	}
	foreach ( (array)$plugins as $plugin_file => $plugin_data) {
		//the only plugin not available for local updates is plugpdater ;)
		if($plugin_file == "plugpdater/plugpdater.php") continue;
		$class = "inactive";
		$net_active = 0;
		if ( is_plugin_active_for_network(esc_attr($plugin_file)) ) {
			$class = "active";
			$net_active=1;
		} elseif ( is_plugin_active(esc_attr($plugin_file)) ) {
			$class = "active";
		}
		
		$checkbox = "<input type='radio' name='_plugin_data' value='" . esc_attr($plugin_file) . "' />";
		$description = '<p>' . $plugin_data['Description'] . '</p>';
		$plugin_name = $plugin_data['Name'];
		

		echo "
<tr class='$class'>
	<th scope='row' class='check-column'>$checkbox</th>
	<td class='plugin-title'><strong>$plugin_name</strong></td>
	<td>";
	if($class=="active" && $net_active==1) _e('Network activated','plugpdater');
	elseif($class=="active" && $net_active!=1) _e('Blog activated','plugpdater');
	else _e('inactive','plugpdater');
	echo "</td>
	<td class='desc'>$description</td>
</tr>
<tr class='$class second'>
	<td></td>
	<td class='plugin-title'>";
		echo '<div class="row-actions-visible"></div></td><td></td><td class="desc">';
		$plugin_meta = array();
		if ( !empty($plugin_data['Version']) )
			$plugin_meta[] = sprintf(__('Version %s','plugpdater'), $plugin_data['Version']);
		if ( !empty($plugin_data['Author']) ) {
			$author = $plugin_data['Author'];
			if ( !empty($plugin_data['AuthorURI']) )
				$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage','plugpdater' ) . '">' . $plugin_data['Author'] . '</a>';
			$plugin_meta[] = sprintf( __('By %s','plugpdater'), $author );
		}
		if ( ! empty($plugin_data['PluginURI']) )
			$plugin_meta[] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin site','plugpdater' ) . '">' . __('Visit plugin site','plugpdater') . '</a>';

		$plugin_meta = apply_filters('plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $context);
		echo implode(' | ', $plugin_meta);
		echo "</td>
</tr>\n";
	}
?>
</tbody>
</table>
<?php
}


/**
* plugpdater_catch_errors
* redirects where it needs to be redirected ! 
*/
function plugpdater_catch_errors(){
	if(isset($_GET['check-plug'])){
		if(isset($_FILES['plugzip']['name']) && strlen($_FILES['plugzip']['name']) > 4){
			$uploadok = plugpdater_upload_plugin();
			if($uploadok) $unzipok = plugpdater_unzip_plugin($_FILES['plugzip']['name'], $_GET['plugin']);
			if($unzipok){
				if(file_exists(PLUGPDATER_TEMP_DIR . '/' . $_FILES['plugzip']['name'])) unlink(PLUGPDATER_TEMP_DIR . '/' . $_FILES['plugzip']['name']);
				if(!file_exists(PLUGPDATER_TEMP_DIR . '/' . $_GET['plugin'])){
					$output = "noplug";
					wp_redirect('plugins.php?page=plugpdater&action=update&error='.$output.'&plugin='.$_GET['plugin'].'&active='.$_GET['activated']);
					die();
				}
				wp_redirect('plugins.php?page=plugpdater&action=update&fatal-error=1&plugin='.$_GET['plugin'].'&active='.$_GET['activated']);
				ob_start();
				include(PLUGPDATER_TEMP_DIR . '/' . $_GET['plugin']);

				if ( ob_get_length() > 0 ) {
					$output = ob_get_clean();
					wp_redirect('plugins.php?page=plugpdater&action=update&error='.$output.'&plugin='.$_GET['plugin'].'&active='.$_GET['activated']);
				}
				else{
					wp_redirect('plugins.php?page=plugpdater&action=update&success=1&plugin='.$_GET['plugin'].'&active='.$_GET['activated']);
				}
				ob_end_clean();
			}
		}
		else{
			wp_redirect('plugins.php?page=plugpdater&nozip=1');
		}
	}
}

add_action('load-plugins_page_plugpdater', 'plugpdater_catch_errors');


/**
* plugpdater_upload_plugin
* upload zip archive to update folder.
* 
*/
function plugpdater_upload_plugin(){
	$success = false;
	$file_temp = $_FILES['plugzip']['tmp_name'];
	$file_name = $_FILES['plugzip']['name'];
	//complete upload
	$filestatus = move_uploaded_file($file_temp,PLUGPDATER_TEMP_DIR."/".$file_name);
	if(!$filestatus)
       $success = false;
    else 
       $success = true;

	return $success;
}

/**
* plugpdater_unzip_plugin
* unzip archive to update folder.
* 
*/
function plugpdater_unzip_plugin($archive, $archive_dir=""){
	global $wp_filesystem;
	
	$url = 'plugins.php?page=plugpdater&action=upload';

	$url = wp_nonce_url($url, 'plugpload');
	
	if ( false === ($credentials = request_filesystem_credentials($url, '', false, ABSPATH)) )
		return;

	if ( ! WP_Filesystem($credentials, ABSPATH) ) {
		request_filesystem_credentials($url, '', true, ABSPATH); //Failed to connect, Error and request again
		return;
	}
	
	if($archive_dir != ""){
		$parent_dir = explode('/', $archive_dir);
		if(file_exists(PLUGPDATER_TEMP_DIR.'/'.$parent_dir[0])) $wp_filesystem->delete(PLUGPDATER_TEMP_DIR.'/'.$parent_dir[0], true);
	}
	
	$archive = PLUGPDATER_TEMP_DIR ."/".$archive;
	
	return unzip_file($archive, PLUGPDATER_TEMP_DIR);
}


/**
* plugpdater_print_admin_style
* print styles for plugpdater.
* 
*/
function plugpdater_print_admin_style(){
	if($_GET['page']=='plugpdater'){
		wp_enqueue_style('plugpdater-style', PLUGPDATER_URL.'/css/plugpdater-style.css');
	}
}

add_action('admin_print_styles','plugpdater_print_admin_style');


/**
* plugpdater_activate
* store plugin's version
* 
*/
function plugpdater_activate() {	
	//if first install
	if(!get_option('plugpdater-version')){
		update_option( 'plugpdater-version', PLUGPDATER_VERSION );
	}
	else{
		update_option( 'plugpdater-version', PLUGPDATER_VERSION );
	}
}
register_activation_hook( __FILE__, 'plugpdater_activate' );

/**
* plugpdater_load_language_file
* loads the translation if found!
* 
*/
function plugpdater_load_language_file(){
	load_plugin_textdomain(PLUGPDATER_PLUGIN_NAME, PLUGPDATER_DIR.'/languages/', PLUGPDATER_PLUGIN_NAME.'/languages/');
}

add_action('plugins_loaded', 'plugpdater_load_language_file');
?>