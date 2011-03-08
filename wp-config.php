<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'northdown');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|%;:)]#$|vwdA-.{2|]AHLFrJ&f*q7rph)jNOd[@mj@M!|%XIeENN_Ni-RnVb<!$');
define('SECURE_AUTH_KEY',  'OVq%!!)&3gD|3XQ8F;IvsF+S+<Ogn]%}l.6=1{T$BJ[CsDu$LQzVsv5z5zY7y{xX');
define('LOGGED_IN_KEY',    '!Zl:PiPP/upUUrhfjY=rI1>N7WC{(,A+w}d@h6M]Jzf[v/oj(7V/n<@FUmS|WWpc');
define('NONCE_KEY',        '0D^6F|=>/*p@58fPI|nZMSk5C#oO#Oo-lcxKvQH:>mECXS-FLlgw*MIlv8P|sFZr');
define('AUTH_SALT',        'xH.4+?L%RZQ4vE%uXV(T${n1&6%p:#~,1L+2IJx_Qj:4<D)sm6C|| Ue .E8_{Q%');
define('SECURE_AUTH_SALT', 'G}+@|+UU-Zz=>!2y`$WZ,HEeLck~m6*Uf(=g+r[[1~Z`4&Qh*s(J{JB Kz >31xU');
define('LOGGED_IN_SALT',   'jm}P/PlF3Nz)JgdUx7x;K9;%%@N]IODxv) {j*?eZADNL*J-1!i`.16zawEx7i,9');
define('NONCE_SALT',       '-_FW|,h4&+/;#8MueQ0QFA!1hUS~h|7WEMDP*|0+l&Gp+|T|CfuL`3|+h~MlVGRs');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

