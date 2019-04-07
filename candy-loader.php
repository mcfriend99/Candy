<?php

if(!defined('CANDY')) exit(404);

if(file_exists(__DIR__ . '/.hide')){
    require_once __DIR__ . '/closed.html';
    exit;
}

// Initate sessions.
if(!isset($_SESSION))
    session_start();

// Basic requirement for loading Candy.
require 'system/functions/file.php';
require 'system/functions/configs.php';

// Define global configuration directory.
define('CONFIG_DIR', __DIR__ . '/' . 'configs');
define('CACHE_DIR', __DIR__ . '/' . 'system/cache');


// Global error wrapper.
define('CANDY_ERROR_PREPEND_STRING', "<div style='background:#def14d;padding:20px;margin-top:30px;color:#000000;font-size:13px;font-family:sans-serif;text-align:left!important;'><span style='font-size:20px;color:#20a700;'><strong>CANDY ERROR:</strong></span><br/><br/><pre>");
define('CANDY_ERROR_APPEND_STRING', "</pre></div>");

// Conform PHP error to Candy error.
ini_set('error_prepend_string', CANDY_ERROR_PREPEND_STRING);
ini_set('error_append_string', CANDY_ERROR_APPEND_STRING);

// Mask the fact that we are using PHP for security and anonymity sake.
ini_set('expose_php', 'Off');


// Load Configs.
$config_files = get_directory(CONFIG_DIR, CANDY_SCAN_FILES, '.candy');
foreach($config_files as $file){
    load_configs($file);
}
unset($config_files);

// Enable/Disable error reporting as defined in configuration.
// Do not change this option here. Rather change the show_error config value to yes or no as required.
if(strtolower(get_config('show_error', 'main', 'yes')) == 'yes'){

    error_reporting(E_ALL);
} else {

    ini_set('track_errors', 'Off');
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors', 'Off');
    error_reporting(0);
}


// Load Languages.
$langs_files = get_directory(__DIR__ . '/' . get_config('language_dir', 'main'), CANDY_SCAN_FILES, '.candy');
foreach($langs_files as $file){
    load_texts($file);
}
// Free variable name for use.
unset($lang_files);



// Load classes.
$app_files = get_directory(__DIR__ . '/system/classes', CANDY_SCAN_FILES, '.php');
foreach($app_files as $file){
    require $file;
}
unset($app_files);

// Load functions.
$app_files = get_directory(__DIR__ . '/system/functions', CANDY_SCAN_FILES, '.php');
foreach($app_files as $file){
    $dont_loads = [__DIR__ . '/system/functions/configs.php', 
      __DIR__ . '/system/functions/file.php'];

    if(!in_array($file, $dont_loads)){
        require $file;
    }
}
unset($app_files);


// Create global definition for directories configuration.
define('REWRITE_BASE', get_config('rewrite_base', 'main'));
define('ROOT', __DIR__ . '/');
define('PLUGIN_DIR', __DIR__ . '/' . get_config('plugin_dir', 'main'));
define('ASSETS_DIR', __DIR__ . '/' . get_config('assets_dir', 'main'));
define('THEME_DIR', __DIR__ . '/' . get_config('template_dir', 'main'));
define('SITE_DIR', __DIR__ . '/' . get_config('site_dir', 'main'));
define('UPLOADS_DIR', __DIR__ . '/' . get_config('uploads_dir', 'main'));


set_error_handler($candy_error, E_ALL | E_STRICT);
set_exception_handler($candy_error);


// load User custom functions.
$app_files = get_directory(__DIR__ . '/app', CANDY_SCAN_FILES, '.php');
foreach($app_files as $file){

    require $file;
}
unset($app_files);

// Initialize Candy
require 'candy-init.php';

// Build Candy.
require 'candy-builder.php';
