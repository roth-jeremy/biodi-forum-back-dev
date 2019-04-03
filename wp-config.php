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

define('DB_NAME', 'wp-biodi-dev');


/** MySQL database username */

define('DB_USER', 'root');


/** MySQL database password */

define('DB_PASSWORD', "");


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

define('AUTH_KEY',         'kfr9mP-9/L#@K9CEx[>AacXuu|!4d%~Vn;E{2lX3c].+d!V#x58>V`4Aq%U&%;6J');

define('SECURE_AUTH_KEY',  'CMza=Tr}B-8|JVg(Hb~ptd&Q@2&2B0o9[FGOCr%Z-PQm$g0|tq@B%?Q@I?7k2l*J');

define('LOGGED_IN_KEY',    '<R;W+*AABVOkl9Kc}h)ULyAY4Ukkb9-[76<6I3|Q<S&;sr$yP`MU.~ t/VFm)Y`Y');

define('NONCE_KEY',        'Qsp<FAaZIGoibdJ5hM[sf 7|HI+]-0R5kv)0Fc_=y@57IN@Pah}s5rOnDg|Gj}!g');

define('AUTH_SALT',        'b)!I[Z{<IR`3+t%b McgM6~xsZbe6. gU9tCJ0=$s@hAp|y*V@ueg3#!YsT.Il<&');

define('SECURE_AUTH_SALT', 'R7;6zvE:(mc;r-q:9pQ+AB9vD3U<`=fgZ9<7xE/M|L])nYZyzI3+a`*Y>7<ND+EZ');

define('LOGGED_IN_SALT',   'sPi ,G{v#|3=Ly5A]UAW[eb5^z}!}Lt@YUvCfLKf$hA}>5g},Y+h0:vawbbitMDy');

define('NONCE_SALT',       '.3OtS;3qDkm#tz$BCe+(%}rPi6fC0h(4R)HnHZ^S16!5?05$/hO>_DTK>%4uG] ]');

define('JWT_AUTH_SECRET_KEY', 'this-is-a-jwt-auth-secret-key');

define('JWT_AUTH_CORS_ENABLE', true);


/**#@-*/


/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix  = 'wp_';


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

