<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2018 Ore Richard Muyiwa
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	Candy-PHP
 * @author		Ore Richard Muyiwa
 * @copyright      2017 Ore Richard Muyiwa
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://candy-php.com/
 * @since	Version 1.0.0
 */

if (!defined('CANDY')) {
    header('Location: /');
}


/**
 *
 * Redirects to a url.
 * [Set a site config redirect_parameter to enable auto-detection of redirect urls.]
 * [Set a site config no_redirect to true to disable redirection in the entire app.]
 *
 * @param null $url                 Url (relative or absolute) to redirect to.
 * @param bool $allow_redirect      An override for should_redirect.
 * @param bool $use_js              If to use JavaScript redirection instead of PHP's even when headers are not yet sent.
 */
function redirect($url = null, $allow_redirect = false, $use_js = false)
{

    // Shortcut to disable all redirection.
    if (get_config('no_redirect', 'site', false) == true) return;

    if ($url == null || $allow_redirect == true) {
        if (should_redirect()) $url = $_GET[get_config('redirect_parameter', 'site', '_ref')];
        elseif ($url == null) $url = '/';
    }

    do_action('redirecting');
    if (headers_sent() || $use_js) {
        echo "<script type='text/javascript'>document.location= {$url} + window.location.hash;</script>";
    } else {
        header("Location: $url");
    }
    exit;
}

/**
 * Checks if the page should be redirected.
 *
 * @return bool
 */
function should_redirect()
{

    return (isset($_GET[get_config('redirect_parameter', 'site', '_ref')]) && get_config('no_redirect', 'site', false) == true);
}



#--------- Start of make clickable ----------------------------#
#---------    Courtsey: Wordpress     -------------------------#

function _make_url_clickable_cb($matches)
{
    $ret = '';
    $url = $matches[2];

    if (empty($url))
        return $matches[0];
    # removed trailing [.,;:] from URL
    if (in_array(substr($url, -1), array('.', ',', ';', ':')) === true) {
        $ret = substr($url, -1);
        $url = substr($url, 0, strlen($url) - 1);
    }
    return apply_filters('clickable_url', $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret);
}

function _make_web_ftp_clickable_cb($matches)
{
    $ret = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;

    if (empty($dest))
        return $matches[0];
    # removed trailing [,;:] from URL
    if (in_array(substr($dest, -1), array('.', ',', ';', ':')) === true) {
        $ret = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest) - 1);
    }
    return apply_filters('clickable_ftp', $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret);
}

function _make_email_clickable_cb($matches)
{
    $email = $matches[2] . '@' . $matches[3];
    return apply_filters('clickable_email', $matches[1] . "<a href=\"mailto:$email\">$email</a>");
}

/**
 * Makes all links within a text into a clickable anchor tag.
 *
 * @param $ret
 * @return mixed|string
 */
function make_clickable($ret)
{
    $ret = ' ' . $ret;
    # in testing, using arrays here was found to be faster
    $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);

    # this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
    $ret = trim($ret);
    return apply_filters('clickable', $ret);
}

#--------- End of make clickable ----------------------------#

/**
 * Gets a user's real IP address.
 *
 * @return string
 */
function real_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
        $ipaddress =  $_SERVER['HTTP_CF_CONNECTING_IP'];
    else if (isset($_SERVER['HTTP_X_REAL_IP']))
        $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
    else if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = '';

    return apply_filters('real_ip', $ipaddress);
}


function is_ajax_request()
{
    return apply_filters('is_ajax_request', (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ||
        isset($_SERVER['HTTP_X_REAL_IP']) ||
        isset($_SERVER['HTTP_X_FORWARDED'])));
}

/**
 * Gets the content of a url.
 *
 * @param $url
 * @return bool|mixed
 */
function get_url_content($url)
{

    if (function_exists('curl_exec')) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, server('http_user_agent'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return to_object([
            'status' => $httpcode,
            'content' => $data
        ]);
    } else {

        return to_object([
            'status' => 200,
            'content' => file_get_contents($url)
        ]);
    }
}

/**
 * Gets a full internal url from a relative url.
 *
 * @param string $s
 * @return string
 */
function get_url($s = '')
{
    if (!empty($s) && $s[0] == '/')
        $s = substr($s, 1);

    return apply_filters('get_url', (isset($_SERVER['REQUEST_SCHEME']) ?
        $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST']
        . REWRITE_BASE . $s);
}

/**
 * Gets a full internal url of an api from a relative url.
 *
 * @param string $s
 * @param null $api
 * @return string
 */
function get_api_url($s = '', $api = null)
{
    if ($api == null) {
        global $api_mode;
        $api = $api_mode;
    }
    return apply_filters('get_api_url', get_url('api/' . $api . '/' . $s));
}

/**
 * Gets the url to a resource.
 *
 * @param string $s
 * @return string
 */
function get_resource_url($s = '')
{
    return get_url(preg_replace('~^' . str_replace('\\', '\\\\', ROOT) . '~', '', ASSETS_DIR) . '/' . $s);
}

/**
 * Gets the url to an uploaded file.
 *
 * @param string $s
 * @return string
 */
function get_upload_url($s = '')
{
    return get_url(preg_replace('~^' . str_replace('\\', '\\\\', ROOT) . '~', '', UPLOADS_DIR) . '/' . $s);
}

/**
 * Sets the http status code.
 *
 * @param $status_code
 * @param string $status_text
 */
function http_status($status_code, $status_text = '')
{

    if (!empty($status_text)) {

        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cgi')
            header("Status: {$status_code} {$status_text}");
        else
            header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . "  {$status_code} {$status_text}");
    } else {

        if (!function_exists('http_response_code') || headers_sent()) {

            header('Candy-Response-Code: ' . $status_code, true, $status_code);
            header('Response-Code: ' . $status_code, true, $status_code);
        } else {

            http_response_code($status_code);
        }
    }
}

/**
 * Returns a specific segment of the current url.
 *
 * E.g. If the current url is http://www.candy-php.com/docs/http
 * get_url_segment(0) = 'docs'
 * get_url_segment(1) = 'http'
 *
 * @param int $s
 * @return string
 */
function get_url_segment($s = 0)
{
    global $request_uri;

    $here = explode('/', $request_uri);

    if (isset($here[$s]))
        return $here[$s];

    return '';
}


/**
 * Removes parameter from the url query string.
 *
 * @param string $s
 * @return mixed|string
 */
function stripped_query($s = '')
{

    $query = the_query();

    if (is_string($s))
        $s = explode(',', $s);

    foreach ($s as $v) {
        $v = trim($v);

        $query = preg_replace('~[&]?' . $v . '\=[^&]+~', '', $query);
    }

    return apply_filters('stripped_query', $query);
}

#-------- End of is mobile -------------------#

/**
 * Gets details of a visitor based on the IP address.
 *
 * @return mixed
 */
function visitor_ip_details()
{
    $fallback = @json_encode(['country' => get_config('country', 'main')]);
    try {

        $json = @get_url_content("http://ipinfo.io/" . real_ip())->content;
        if (!empty($json))
            $details = @json_decode($json);
        else $details = @json_decode($fallback);
    } catch (Exception $e) {
        $details = @json_decode($fallback);
    }
    return $details;
}
