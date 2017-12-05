<?php
/*
Plugin Name: WSUWP MU New Site Defaults
Version: 0.0.1
Description: A WordPress plugin that sets defaults for new sites created at WSU.
Author: washingtonstateuniversity
Author URI: https://web.wsu.edu/
Plugin URI: https://github.com/washingtonstateuniversity/wsuwp-plugin-mu-new-site-defaults
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin uses namespaces and requires PHP 5.3 or greater.
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices', create_function( '', // @codingStandardsIgnoreLine
	"echo '<div class=\"error\"><p>" . __( 'WSUWP MU New Site Defaults requires PHP 5.3 to function properly. Please upgrade PHP or deactivate the plugin.', 'wsuwp-mu-new-site-defaults' ) . "</p></div>';" ) );
	return;
} else {
	include_once __DIR__ . '/includes/wsuwp-mu-new-site-defaults.php';
}
