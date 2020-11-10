<?php
// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} else if ( file_exists( dirname( __FILE__ ) . '/eb-setup.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/eb-setup.php' );
} else {
	define( 'WP_LOCAL_DEV', false );
	define('DB_NAME', $_SERVER['RDS_DB_NAME'] );
	define('DB_USER', $_SERVER['RDS_USERNAME'] );
	define('DB_PASSWORD', $_SERVER['RDS_PASSWORD'] );
	define('DB_HOST', $_SERVER['RDS_HOSTNAME'] );
}

// ========================
// Define site Home and URL
// add https for use with SSL
// ========================
define( 'WP_HOME',  'http://' . $_SERVER['HTTP_HOST'] );
define( 'WP_SITEURL', WP_HOME . '/wp' );

// ========================
// Handle HTTPS on Amazon AWS Elastic Load Balancer, CloudFlare, and some others
// ========================
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    $_SERVER['HTTPS']='on';

// ========================
// Custom Content Directory
// ========================
// Custom content directory
define( 'WP_CONTENT_DIR',  dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL',  WP_HOME . '/content' );
// Custom plugin directory
define( 'WP_PLUGIN_DIR',   dirname( __FILE__ ) . '/content/plugins' );
define( 'WP_PLUGIN_URL',   WP_HOME . '/content/plugins' );
// Custom mu plugin directory
define( 'WPMU_PLUGIN_DIR', dirname( __FILE__ ) . '/content/mu-plugins' );
define( 'WPMU_PLUGIN_URL', WP_HOME . '/content/mu-plugins' );
// Custom Uploads directory
define( 'UPLOADS', '/content/uploads' );

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ==============================================================
// Salts, for security
// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
// ==============================================================

if ( file_exists( dirname( __FILE__ ) . '/eb-setup.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/eb-setup.php' );
} else {
	define('AUTH_KEY',         $_SERVER['AUTH_KEY'] );
	define('SECURE_AUTH_KEY',  $_SERVER['SECURE_AUTH_KEY'] );
	define('LOGGED_IN_KEY',    $_SERVER['LOGGED_IN_KEY'] );
	define('NONCE_KEY',        $_SERVER['NONCE_KEY'] );
	define('AUTH_SALT',        $_SERVER['AUTH_SALT'] );
	define('SECURE_AUTH_SALT', $_SERVER['SECURE_AUTH_SALT'] );
	define('LOGGED_IN_SALT',   $_SERVER['LOGGED_IN_SALT'] );
	define('NONCE_SALT',       $_SERVER['NONCE_SALT'] );
}

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// ===========
// Hide errors
// ===========
ini_set( 'display_errors', 0 );
define( 'WP_DEBUG_DISPLAY', false );

// =================================================================
// Debug mode
// Debugging? Enable these. Can also enable them in local-config.php
// =================================================================
// define( 'SAVEQUERIES', true );
define( 'WP_DEBUG', false );

// ======================================
// Load a Memcached config if we have one
// ======================================
if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) )
	$memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp' );
require_once( ABSPATH . '/wp-settings.php' );

// =================================================================
// Increase PHP upload limit
// =================================================================
#@ini_set( 'upload_max_filesize' , '512M' );
#@ini_set( 'post_max_size', '256M');
