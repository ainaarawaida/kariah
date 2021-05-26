<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'kariah' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '41zTMmM2Edy0zGk9wL4Esq8zZ51SAIBQhEeUfHYTN9MwvGxZEQWu8EUGr52GIAXQ' );
define( 'SECURE_AUTH_KEY',  'pmdm8AYdZOJOpUT2E1lvuKdqovAuH7clLoYIoKQBGtljmyIn5kHsaVbWhAiJdiQi' );
define( 'LOGGED_IN_KEY',    'AINo5V881tzUDP305Oi9DOMaN7uC6hNuMdur00yByFLGIPD2RIzzL1j6DwQ4AFoX' );
define( 'NONCE_KEY',        'SoKICRDTfKYoWC9NiftfnoGjSfw5ig5yrZPduj35rihlJa4apv3AZxdDZbSwHWv9' );
define( 'AUTH_SALT',        'un8K0gAK2AiPWByF2Kb4AHu3CLmj3h2ReBcaNSsKlv1HU0fvkMZmlIZJNa7TGqtB' );
define( 'SECURE_AUTH_SALT', 'BCWT422YEWUNh6IwXLKM9ZIr2sGO3wDLDZPnqsYmiq4dLnsLfEkJiGNvw4eUDuD8' );
define( 'LOGGED_IN_SALT',   'ISM4YMsdghwLBzRoULiuGhWQra1j2MgF3nsz43tYel8ICBE2avE7eDAgfXnVByMD' );
define( 'NONCE_SALT',       'LtV46fd2NkmxoCy1WXKtVPZywALE8Dy9CNGKTKAlax86Di83dGGwlS2kuZhmbaaT' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
