<?php

$apl_output = '';  
$theme_data =  wp_get_theme('BlogKids');
$theme_version = $theme_data['Version'];
$theme_name = $theme_data['Name'];
$theme_uri = $theme_data['ThemeURI'];
$author_uri = $theme_data['AuthorURI'];

define( 'ADMIN_PATH', get_template_directory() . '/admin/' );
define( 'ADMIN_DIR', get_template_directory_uri() . '/admin/' );
define( 'ADMIN_IMAGES', ADMIN_DIR . 'assets/images/' );
define( 'THEMENAME', $theme_name );
define( 'THEMEVERSION', $theme_version );
define( 'THEMEURI', $theme_uri );
define( 'THEMEAUTHORURI', $author_uri );
define( 'BACKUPS','backups' );

add_action('admin_head', 'optionsframework_admin_message');
add_action('admin_init','optionsframework_admin_init');
add_action('admin_menu', 'optionsframework_add_admin');

require_once( ADMIN_PATH . 'functions/functions.filters.php' );
require_once( ADMIN_PATH . 'functions/functions.interface.php' );
require_once( ADMIN_PATH . 'functions/functions.options.php' );
require_once( ADMIN_PATH . 'functions/functions.admin.php' );
require_once ( ADMIN_PATH . 'classes/class.options_machine.php' );

add_action('wp_ajax_of_ajax_post_action', 'of_ajax_callback');