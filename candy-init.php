<?php

if (!defined('CANDY')) exit(404);

// Init cross-globals.
$db = new DB;
$regex = new RegEx;
$cook = new Cookie;
$mobile = new Mobile();

$GLOBALS['__platform__'] = $mobile->matchedPlatformName;

// Load Plugins.
$__plugins__ = new Plugin();
$__plugins__->init(get_config('autoload_plugins', 'main', false));


// Init global variables.
// Do not access them directly unless you know what you are doing.
// Rather, access them via their respective functions.
$GLOBALS['__forms__'] = [];
$GLOBALS['__queries__'] = [];
$GLOBALS['__htmls__'] = [];
$GLOBALS['__routes__'] = [];

$GLOBALS['__scripts__'] = [];
$GLOBALS['__styles__'] = [];
$GLOBALS['__descripted__'] = [];
$GLOBALS['__destyled__'] = [];

$GLOBALS['__uploaders__'] = [];

$GLOBALS['__flashsessions__'] = [];

// Initialize Concepts.
$app_files = get_directory(__DIR__ . '/' . get_config('models_dir', 'main'), CANDY_SCAN_FILES, '.php');
foreach ($app_files as $file) {
    $dont_loads = [];

    if (!in_array($file, $dont_loads)) {
        require $file;
    }
}
unset($file);
unset($app_files);

// Clear flash sessions if they exist.
// We are doing it here because they need to be cleared before call/output to be valid as flash sessions.
foreach (session() as $s => $v) {

    if (preg_match('~_CANDY_FLASH_SESSION_~', $s)) {
        $s2 = str_replace('_CANDY_FLASH_SESSION_', '', $s);
        if ($__flashsessions__[$s2] == 0 || time() >= flash_session($s2)['expire'])
            unsession($s);
    }
}
// Whenever you see this, we are mainly looking out for PHP 5.4+.
unset($s);
unset($v);
