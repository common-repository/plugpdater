<?php
//uploader
$plugin_name = explode('/',$_POST['_plugin_data']);
$active_plug = "no";
if ( is_plugin_active_for_network($_POST['_plugin_data']) ) {
	$active_plug = "network";
} elseif ( is_plugin_active($_POST['_plugin_data']) ) {
	$active_plug = "blog";
}
//Deactivate if activated..
if($active_plug != "no"){
	deactivate_plugins($_POST['_plugin_data']);
}
?>
<div class="wrap">
	<?php if($active_plug != "no"):?>
	<div class="updated fade">
		<p><?php _e("During the update process, the plugin has been deactivated. Once updated, it will be reactivated. If an error occurs or if you do not want to continue the update process, you will have to activate the plugin manually in <a href='plugins.php'>the Plugins Administration page</a>",'plugpdater');?>
		</p>
	</div>
	<?php endif;?>
	<h2><?php _e('Upload your plugin update for :', 'plugpdater');?> &quot;<?php echo $plugin_name[0];?>&quot;</h2>
	<div id="new_unzip">
		<form method="post" enctype="multipart/form-data" action="plugins.php?page=plugpdater&action=upload&check-plug=1&plugin=<?php echo $_POST['_plugin_data'];?>&activated=<?php echo $active_plug;?>">
				<label class="screen-reader-text" for="plugzip"><?php _e('Zip Archive of the plugin update','plugpdater');?></label>
				<input type="file" id="plugzip" name="plugzip">
				<input type="submit" class="button-primary" value="<?php _e('Upload Zip','plugpdater');?>">
		</form>
	</div>
</div>