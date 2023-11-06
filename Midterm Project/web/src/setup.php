<?php

/**
 * Set up everything that we need, including loading *all source files that
 * are not entry points* because we are not using composer, and setting up
 * error reporting and sessions.
 * 
 * This file should be the first thing included in every entry point.
 */

/**
 * CONFIGURATION
 *
 * Set up configuration settings:
 *  - display all errors, including startup errors, and including all severities
 *    to help with debugging and because there are no secrets that need to be
 *    hidden
 *  - ensure server timezone is always UTC
 *  - user aborts are ignored on requests that affect the database
 */
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );
ini_set( 'date.timezone', 'UTC' );

if ( ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) === 'POST' ) {
    ignore_user_abort( true );
}

/**
 * DEPENDENCIES
 * 
 * EasyReader depends on the `mysqli` PHP extension (to interact with the
 * database) and on PHP 7.4 or later (to allow some of the PHP features used,
 * would prefer to require 8.0+ for `readonly`, etc. but siteground only has
 * 7.4). Polyfill str_starts_with which is from PHP 8
 */
if ( !extension_loaded( 'mysqli' ) ) {
    trigger_error( 'The `mysqli` extension is missing!', E_USER_ERROR );
}
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    trigger_error(
        'PHP 7.4+ is required, you are using ' . PHP_VERSION,
        E_USER_ERROR
    );
}
if ( !function_exists( 'str_starts_with' ) ) {
    function str_starts_with( string $haystack, string $needle ) {
        return ($needle === '' || strpos( $haystack, $needle ) === 0 );
    }
}

/**
 * FILE LOADING
 *
 * Instead of adding (relatively) complicated logic for only autoloading the
 * needed classes using `spl_autoload_register`, just load all of the classes
 * on every request.
 */
$includeFiles = [
    'HTML/HTMLBuilder.php', 'HTML/HTMLElement.php', 'HTML/HTMLPage.php',
    'Database.php', 'AuthManager.php',
    'Pages/SitePage.php',
    'Pages/ReaderPage.php', 'Pages/LoginPage.php', 'Pages/SignUpPage.php',
    'Pages/LogoutPage.php', 'Pages/AboutPage.php',
];
foreach ( $includeFiles as $file ) {
    require_once $file;
}
// Avoid globals
unset( $file );
unset( $includeFiles );

// Session
session_start();


/**
 * DATABASE SETUP
 *
 * To allow both local development with docker and working on siteground, with
 * needing to change the code in Database.php, define global constants for
 * mysqli host, user, and password.
 */
if ( getenv( 'EASY_READER_DOCKER' ) !== false ) {
    define( 'EASY_READER_DB_HOST', 'db' );
    define( 'EASY_READER_DB_USER', 'root' );
    define( 'EASY_READER_DB_PASS', 'root' );
    define( 'EASY_READER_DB_NAME', 'easy_reader_db' );
} else {
    // TODO once added to site ground
    define( 'EASY_READER_DB_HOST', 'localhost' );
    define( 'EASY_READER_DB_USER', '' );
    define( 'EASY_READER_DB_PASS', '' );
    define( 'EASY_READER_DB_NAME', '' );
}

// Database setup
\EasyReader\Database::setup();