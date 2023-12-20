<?php

/**
 * Helper function to allow false in dotenv
 * which treats everything as strings.
 */
function env($key, $default = null)
{
    $value = getenv($key);
    if ($value === 'false') { return false; }
    if ($value === 'true') { return true; }
    if ($value === false) { return $default; }
    return $value;
}

define( 'WEB_ROOT', __DIR__ );
define( 'PROJECT_ROOT', dirname(__DIR__) );

/***
 * Composer Autoloader - Register your theme's namespace in composer.json 
 * to utilise the autoloader.
 */
require_once( PROJECT_ROOT . '/vendor/autoload.php' );

/**
 * Dotenv is used for local development, Platform.sh deployments pull directly from
 * the environment.
 */
if ( file_exists(PROJECT_ROOT . '/.env' ) ) {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable( PROJECT_ROOT );
    $dotenv->load();
    $dotenv->required(['WP_ENV', 'WP_DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_URL']);
}

/**
 * Database Credentials
 */

define('WP_ENV', env('WP_ENV', 'dev'));
define('DB_NAME', env('WP_DB_NAME'));
define('DB_USER', env('DB_USER'));
define('DB_PASSWORD', env('DB_PASSWORD'));
define('DB_HOST', env('DB_HOST', 'localhost'));

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/**
 * Control WP Cache via environment
 * does not affect PLatform.sh cache
 */

define('WP_CACHE', env('WP_CACHE', false));

/**
 * Allow WordPress to respond to incoming URL
 * Directory setup for composer-based wordpress projects
 */

$protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ) ? 'https' : 'http';
$httpHost = isset($_SERVER['HTTP_HOST']) ? $protocol . '://'. $_SERVER['HTTP_HOST'] : '';

define('WP_CORE_DIRECTORY', env('WP_DIR', 'wordpress'));

define('WP_HOME', $httpHost);
define('WP_SITEURL', env('WP_SITEURL', WP_HOME.'/'.WP_CORE_DIRECTORY));

/**
 * Use '/content' instead of '/wp-content' in URLS and paths
 */

define('WP_CONTENT_DIR', env('WP_CONTENT_DIR', __DIR__. '/content'));
define('WP_CONTENT_URL', env('WP_CONTENT_URL', WP_HOME. '/content'));

/**
 * Default theme (autoloads it, doesn't require activation)
 * Set this on a project level, not an env level
 */
// TODO: Define default theme once created
// define('WP_DEFAULT_THEME', 'frame-custom');

$isDev = WP_ENV === 'dev';

define('WP_DEBUG', env('WP_DEBUG', $isDev));
define('WP_DEBUG_DISPLAY', env('WP_DEBUG_DISPLAY', $isDev));
define('WP_DEBUG_LOG', env('WP_DEBUG_LOG', $isDev));
define('SCRIPT_DEBUG', env('WP_SCRIPT_DEBUG', $isDev));
define('DISABLE_WP_CRON', env('DISABLE_WP_CRON', false));
define('TEMPLATE_DEBUG', env('TEMPLATE_DEBUG', false));

if ($isDev) {

    if ( env('WHOOPS', false ) ){
        // Register the Whoops error handler
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
     }

    define('WP_ENVIRONMENT_TYPE', 'development');

} elseif (WP_ENV === 'staging') {
    // Specific config can go here
    define('WP_ENVIRONMENT_TYPE', 'staging');
} elseif (WP_ENV === 'live' ||  WP_ENV === 'production') {
    // Specific config can go here
}

// https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY', env('AUTH_KEY'));
define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
define('NONCE_KEY', env('NONCE_KEY'));
define('AUTH_SALT', env('AUTH_SALT'));
define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Table prefix - not reccomended to be set via enviornment
 * as this makes sharing DB's between enviornments difficult
 */
$table_prefix = 'rsa_';

define('WPLANG', env('WPLANG', 'en_AU'));

/**
 * Common Plugins Licence Keys
 */
if ( $wpmdbLicence = getenv('WPMDB_LICENCE') ) {
    define('WPMDB_LICENCE', $wpmdbLicence);
}

if ( $gfLicence = getenv('GF_LICENCE') ) {
    define( 'GF_LICENSE_KEY', $gfLicence );
}

/* Set the trash to less days to optimize WordPress. */
define('EMPTY_TRASH_DAYS', env('EMPTY_TRASH_DAYS', 7));

/* Specify the Number of Post Revisions. */
define('WP_POST_REVISIONS', env('WP_POST_REVISIONS', 2));

/* Cleanup image edits. */
define('IMAGE_EDIT_OVERWRITE', env('IMAGE_EDIT_OVERWRITE', true));

/* Prevent file edit from the dashboard. */
define('DISALLOW_FILE_EDIT', env('DISALLOW_FILE_EDIT', true));

/* Vendor path to help load custom plugins */
define('VENDORPATH', __DIR__.'/../vendor');

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
