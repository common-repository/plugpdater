<?php
//installer
global $wp_filesystem;
$plugin_name = explode('/', $_GET['plugin']);
?>
<div class="wrap">
	<h2><?php printf( __( 'Updating plugin &quot;%s&quot;', 'plugpdater' ), $plugin_name[0] ) ;?></h2>
	<?php 
	if(isset($_GET['fatal-error'])){
		?>
		<div class="error fade"><p><?php _e('<b>Fatal Error</b> : the upload process stopped. Please correct your plugin and try again once done.','plugpdater');?></p>
			<?php if($_GET['active']!="no"):?>
			<p><?php _e("the plugin was deactivated during the update process, you can reactivate it in the <a href='plugins.php'>Plugins Administration page</a>.",'plugpdater');?></p>
			<?php endif;?>
			<iframe src="<?php echo PLUGPDATER_URL.'/includes/plugpdater-error-handler.php?plugin='.$_GET['plugin'];?>" style="width:100%; height:auto"></iframe>
		</div>
		<?php
	}
	elseif(isset($_GET['error']) && $_GET['error']!="noplug"){
		?>
		<div class="error fade"><p><?php _e('<b>Unexpected Error</b> : the upload process stopped. Please correct your plugin and try again once done.','plugpdater');?></p>
			<p><?php _e('The plugin printed this unexpected string :','plugpdater');?> <b><?php echo $_GET['error'];?></b></p>
			<?php if($_GET['active']!="no"):?>
			<p><?php _e("the plugin was deactivated during the update process, you can reactivate it in the <a href='plugins.php'>Plugins Administration page</a>.",'plugpdater');?></p>
			<?php endif;?>
		</div>
		<?php
	}
	elseif(isset($_GET['error']) && $_GET['error']=="noplug"){
		?>
		<div class="error fade"><p><?php _e('<b>Unexpected Error</b> : the upload process stopped. Please correct your plugin and try again once done.','plugpdater');?></p>
			<p><?php _e('The update directory of the plugin was not found or does not match with the installed plugin you want to update','plugpdater');?></p>
			<?php if($_GET['active']!="no"):?>
			<p><?php _e("the plugin was deactivated during the update process, you can reactivate it in the <a href='plugins.php'>Plugins Administration page</a>.",'plugpdater');?></p>
			<?php endif;?>
		</div>
		<?php
	}
	elseif(isset($_GET['success'])){
		$active_plug = $_GET['active'];
		?>
		<div class="updated fade"><p><?php _e('Updating process began, please wait..','plugpdater');?></p></div>
		<?php
		//check if original plugin is still in /wp-content/plugins
		if(file_exists(WP_PLUGIN_DIR.'/'.$_GET['plugin'])){
			if(!file_exists(PLUGPDATER_TEMP_DIR.'/'.$plugin_name[0])){
				//in case the administrator is refreshing the page !
				echo "<div class='strange_error'><p>".__('Updating directory is missing ? Update stopped.','plugpdater')."</p></div>";
				return;
			}
			$url = 'plugins.php?page=plugpdater&action=update&success=1&plugin='.$_GET['plugin'];

			$url = wp_nonce_url($url, 'plugpdate');

			if ( false === ($credentials = request_filesystem_credentials($url, '', false, ABSPATH)) )
				return;

			if ( ! WP_Filesystem($credentials, ABSPATH) ) {
				request_filesystem_credentials($url, '', true, ABSPATH); //Failed to connect, Error and request again
				return;
			}
			echo  "<p>".__('Deleting old version of the plugin..','plugpdater')."</p>";
			$plugin_dir = WP_PLUGIN_DIR.'/'.$plugin_name[0];
			
			/*if something goes wrong with the get variable, then process is stopped to avoid deleting other plugins*/
			if($plugin_dir == WP_PLUGIN_DIR.'/'){
				echo "<div class='strange_error'><p>".__('Old version of the plugin was not found','plugpdater')."</p></div>";
				if($active_plug != "no" && $active_plug == "network") activate_plugin($_GET['plugin'], 'plugins.php?page=plugpdater&action=update&fatal-error=1&plugin='.$_GET['plugin'], true);
				elseif($active_plug != "no" && $active_plug == "blog") activate_plugin($_GET['plugin'], 'plugins.php?page=plugpdater&action=update&fatal-error=1&plugin='.$_GET['plugin'], false);
				return;
			}
			$delete_old_version = $wp_filesystem->delete($plugin_dir, true);
			if($delete_old_version){
				echo "<p>".__('Installing new version of the plugin','plugpdater')."</p>";
				if(rename(PLUGPDATER_TEMP_DIR.'/'.$plugin_name[0] ,WP_PLUGIN_DIR.'/'.$plugin_name[0])){
					echo "<div class='plugpdater_updated'><p>".__('Update done','plugpdater').", ";
					if($active_plug != "no" && $active_plug == "network") activate_plugin($_GET['plugin'], 'plugins.php?page=plugpdater&action=update&fatal-error=1&plugin='.$_GET['plugin'], true);
					elseif($active_plug != "no" && $active_plug == "blog") activate_plugin($_GET['plugin'], 'plugins.php?page=plugpdater&action=update&fatal-error=1&plugin='.$_GET['plugin'], false);
					if($active_plug != "no") echo __('the plugin has been successfully reactivated.','plugpdater')."</p></div>";
					else echo __("You can activate the plugin in <a href='plugins.php'>the Plugin Administration page</a>",'plugpdater')."</p></div>";
				}
			}
		}
		else{
			?>
			<div class="error fade"><p><?php _e('<b>Unexpected Error</b> : the plugin does not seem to be installed, so it cannot be updated!','plugpdater');?></p>
			</div>
			<?php
		}
		
	}
	?>
</div>