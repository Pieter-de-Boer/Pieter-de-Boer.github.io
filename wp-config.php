<?php
//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '4nvD5FOIoYFUTzImG%f[^SL,kTjNMvVAAq^-+<.g<}?BB~! ,/b Og}(LjQ$?<fz' );
define( 'SECURE_AUTH_KEY',  'vyFYDNHgIUqliy5)u#+9AD3pw9+kP.=OTkPBfcr@r#vGlg9@aG:kY<U,qL!K.cT]' );
define( 'LOGGED_IN_KEY',    '_BE7P^o]N~:^ATB#86/--`j,on^m;UL;np:IrPgZl4(d^vL7L*!m].%$~8IY!E>Z' );
define( 'NONCE_KEY',        '@}n[+RHxMULoMtvii=]4/A&uBV%-}ieqDD8jDnY?GHfj5}oAR_h]%=h)awDDYHa7' );
define( 'AUTH_SALT',        'N:?tNuh&i6u;l>B8]MF5FAS{cq0I%gZW/[6,/@<m!/IRG=K6;8hgnPX)69H;BeN6' );
define( 'SECURE_AUTH_SALT', 'kS27Yf)N.e ?1s;ayeQWPhVw9.K|s~W9RDih|]C }6OC3G(Gn^R7u=9TNZ{5*|Gq' );
define( 'LOGGED_IN_SALT',   'd8z]I}.wC*MkZ>Xvh9S70=_y[{D@]w pu2EN-Em:!DXDH<p`}HLY7jE<}YjJsm]7' );
define( 'NONCE_SALT',       'LA?Pc$7p]jOmJg~}Yd[kT&%O}-r!B#p?f3pAIejg0#`qM<HCk5Xbh^u&.;^v_?uW' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
