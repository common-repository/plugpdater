<?php
//handle fatal errors
$wp_load = realpath("../../../../wp-load.php");
if(!file_exists($wp_load)) {
  $wp_config = realpath("../../../../wp-config.php");
  if (!file_exists($wp_config)) {
      exit("Can't find wp-config.php or wp-load.php");
  } else {
      require_once($wp_config);
  }
} else {
  require_once($wp_load);
}
if ( ! WP_DEBUG ) {
	if ( defined('E_RECOVERABLE_ERROR') )
		error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
	else
		error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);
}

@ini_set('display_errors', true);

function plugpdater_sandbox_scrape( $plugin ) {
	include( PLUGPDATER_TEMP_DIR . '/' . $plugin );
}
plugpdater_sandbox_scrape($_GET['plugin'] );
?>