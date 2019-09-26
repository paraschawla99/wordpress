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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'testteam' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Fcyh;+X~f6m%&ZUjgD4!a99JNJ>XPs6S:};k7eCjQxG8$_th8BeVp&%&&VutL`M$' );
define( 'SECURE_AUTH_KEY',  'GQX=Fx#?u#JfF_Vs;W1A6[:Q-5MhWEU/:De>ZGf56Mo;@&c3{+BB]f_jpIS#1,a-' );
define( 'LOGGED_IN_KEY',    'Alzd8j`og&mE,o7UoR-^&XVKaO~^iu#q,zk~u4-ows+pUT24E2db@}mlguso:z*C' );
define( 'NONCE_KEY',        '+#PeRc`_YiaHF2QODb{cA-4QCAN0kGWCvCSRJA,_[C!HnZ@V,Qf3Fp4`0g!8t;$j' );
define( 'AUTH_SALT',        '[?$}+]A#)u]X@E:3R;%5.vE_lQ!HQ4oQurQ5o>B]v0LfB(~%aJK{1-A:IITVUXS6' );
define( 'SECURE_AUTH_SALT', '5m5/MaR3^2=:>ZHFAs&bLQMrXziFC}2]e(]<o7PB2r*%<T%*_7txM(V:yFW%>(ix' );
define( 'LOGGED_IN_SALT',   ':,+g)b7{($ui5==![rOTZ)3%N{Ye0.t5ew%TByzE)t_9R.iuC8I97ao?/!yZDb~O' );
define( 'NONCE_SALT',       '&:f,~]z=s-5<FL_dNO5[6UO81o$19!&JHM]L`zWP{ApFg{;o8eV2eO>aT??Y|pR>' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

