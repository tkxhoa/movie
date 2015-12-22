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
define('DB_NAME', 'movies');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'uvHxd6ieT!-2s:M9+(!l? CT=.-w%FvTG -jD_y4{d_.<MU`]PUz! kt$k,qV^XS');
define('SECURE_AUTH_KEY',  'yk}eL8|/|EXwWG!_KK8Eg0+T/@V0`Y]nQ3G1+||$ZY`zB.G=f-F|lmQvk.i aRx=');
define('LOGGED_IN_KEY',    '*uyL1 gEgp$Hu5e^0B7Tady}Dxm-56Y:BahHDfIhiXpw=KK,*X%pxWwAt#S8fk$e');
define('NONCE_KEY',        'f{|y8<e3vRzNC)KQ[:a_>hCaX+U#+zM,~lw2$5hem;6-l!lY.-f35!-#6+Uy~taj');
define('AUTH_SALT',        ']vmOAm#5+loh36;pTL5%PJm7K4d(>{EuH=r^59j:OAB+4hP8]drq_Sj.D=?P}`t;');
define('SECURE_AUTH_SALT', '_;FqhyamNEYI+ys9JB|xE$jjNmoMt&)&}d|B&WLqh-76gXU{m(>z[?2];p?XAMSK');
define('LOGGED_IN_SALT',   'PKT8f}nJWe,,8?0Fd&fNjb+Ih)>k9+gX|oYo/6a[F|E^S:xIETrv_ZVx,Iqp3;q9');
define('NONCE_SALT',       '& uArVxgR~M&oi4?hr/YqKKvkisjXY#v-JPD3*])@:KZGcXN#2O?UGqUruP(Pctw');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'tbl_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
