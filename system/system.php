<?php
/**
 * User: Abdullah Al Jahid
 * Date: 1/9/13
 * Time: 6:18 PM
 *
 * System file
 */


define( 'SYSTEM_DIR', dirname(__FILE__).'/' );
define( 'CONFIG_DIR', SYSTEM_DIR.'config/' );
define( 'CLASS_DIR', SYSTEM_DIR.'class/' );
define( 'FUNCTION_DIR', SYSTEM_DIR.'function/' );


// LOAD THE SITE CONFIG
require( CONFIG_DIR . 'config.php' );

// LOAD FUNCTION
require( FUNCTION_DIR . 'function.php' );

// LOAD CLASSES
require( CLASS_DIR . 'database.php' );

require( CLASS_DIR . 'pagination.php' );

require( CLASS_DIR . 'session.php' );

require( CLASS_DIR . 'form.php' );

require( CLASS_DIR . 'email.php' );

require( CLASS_DIR . 'upload.php' );
