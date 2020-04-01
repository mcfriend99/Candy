<?php

if (!defined('CANDY')) exit(404);

// Creating country. We want country to always be available for use.
// E.g. Feching default zipcode etc...
if (!isset($_SERVER['HTTP_USER_COUNTRY'])) {
    $__CXY__ = @visitor_ip_details()->country;
    $_SERVER['HTTP_USER_COUNTRY'] = !empty($__CXY__) ? $__CXY__ : get_config('country', 'main');
}
unset($__CXY__);

// Set API auto target mode. m => mobile, web => Desktop.
if (!empty($GLOBALS['__platform__'])) {

    $api_mode = 'm';
    $plaform = $GLOBALS['__platform__'];
} else {

    $api_mode = 'web';
    $platform = 'web';
}


################   MIMICKING APACHE'S MOD_REWRITE BEHAVIOR IN CASE OUR SERVER DOESN'T SUPPORT IT. ###################

// Don't access these variables directly. Rather, use the_request() or the_query() as appropriate.
$request_uri = substr(rtrim(urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
), '/'), strlen(get_config('rewrite_base', 'main')));

$query_string = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)
);

if ($request_uri !== '/' && !empty($request_uri) && file_exists(__DIR__ . $request_uri)) {
    return false;
}

###########################   END MIMICKING APACHE'S MOD_REWRITE ####################################################


$requests = explode('/', $request_uri);

// The routed file of the current request.
function the_route()
{
    global $request_uri;
    return get_route($request_uri);
}

// The current request.
function the_request()
{
    global $request_uri;
    return $request_uri;
}

// The query on the page as a single string. E.g. q=1&p=str
function the_query()
{
    return !empty(server('query_string')) ? server('query_string') : '';
}

// The current url as it appears on your browser.
function the_url($with_query = false)
{
    global $request_uri;

    return (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http')
        . '://' . $_SERVER['HTTP_HOST']
        . REWRITE_BASE
        . $request_uri
        . ($with_query ? '?' . the_query() : '');
}

// The url of the index page.
function home_url()
{
    return (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http')
        . '://' . $_SERVER['HTTP_HOST']
        . REWRITE_BASE;
}

// Do routing of Candy url to file.
if (file_exists(the_route())) {
    require the_route();
} else {
    // Tell browser we can't handle request.
    http_status(500);
}
