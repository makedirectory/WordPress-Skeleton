<?php
/*
This is a sample local-config.php file
In it, you *must* include the four main database defines

You may include other settings here that you only want enabled on your local development checkouts
*/

define( 'DB_NAME', 'local_db_name' );
define( 'DB_USER', 'local_db_user' );
define( 'DB_PASSWORD', 'local_db_password' );
define( 'DB_HOST', 'localhost' ); // Probably 'localhost'
define( 'WP_HOME', 'http://example.com'] ); //Local hostname

// ===========================================================================================
// This can be used to programatically set the stage when deploying (e.g. production, staging)
// ===========================================================================================
define( 'WP_STAGE', 'staging'); // 'production' || 'staging'
define( 'STAGING_DOMAIN', 'stage_domain'); // Does magic in WP Stack to handle staging domain rewriting
