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
 * Convert almost anything in PHP to string.
 *
 * @param $s
 * @return mixed
 */
function to_string($s)
{
    return print_r($s, true);
}


/**
 * Converts almost anything to an object
 *
 * @param $s
 * @return mixed
 */
function to_object($s)
{
    if (is_array($s) || is_resource($s))
        return @json_decode(@json_encode($s));
    elseif (is_string($s))
        return (object) $s;
    elseif (is_object($s)) return $s;
    return json_encode([]);
}

/**
 * Converts almost anything into an array.
 *
 * @param $s
 * @return array
 */
function to_array($s)
{
    if (is_object($s))
        return json_decode(json_encode($s), true);
    elseif (is_string($s) || is_resource($s))
        return (array) $s;
    elseif (is_array($s)) return $s;
    else return [];
}

/**
 *
 * Returns an encoded URL string.
 *
 * @param $c
 * @return string
 */
function url($c)
{
    return urlencode($c);
}

/**
 *
 * Sends an email using PHP's inbuilt mailer.
 *
 * Why use php mail?
 * ===================
 * Objective. We want Candy to be simple and without third party dependencies by default.
 * You could always write a PHP Mailer plugin. :-)
 *
 * @param $to_add					Address to send email to.
 * @param $subject					The subject of the email.
 * @param $body						The content of the email (plain text or HTML).
 * @param string $from_add			The 'Name <email@example.com>' to use to send the email. I advise you just change #email_from in configs/main.candy
 * @param string $reply_to			A reply-to path. Usually the same as the sending address.
 * @return bool						True if mail is sent, Otherwise False.
 */
function send_email($to_add, $subject, $body, $from_add = '', $reply_to = '')
{

    if (empty($from_add))
        $from_add = get_config('email_from', 'main');

    if (empty($reply_to))
        $reply_to = $from_add;

    $message = "
		<!DOCTYPE html>
		<html>
			<head>
				<title>$subject</title>
				<meta http-equiv='content-type' content='text/html; charset=utf-8' />
			</head>
			<body>
				$body
			</body>
		</html>
	";

    $headers = [
        'From' => $from_add,
        'To' => $to_add,
        'Reply-To' => $reply_to,
        'MIME-Version' => 1.0,
        'Content-type' => 'text/html; charset=utf-8',
        'Return-Path' => $from_add,
        'X-Mailer' => 'Candy'
    ];


    $username = get_config('smtp_mail_address', 'main', '');
    $password = get_config('smtp_mail_password', 'main', '');

    $use_smtp = strtolower(get_config('use_smtp', 'main', 'no')) == 'yes';

    if (!$use_smtp && function_exists('mail')) {

        $headers_str = '';

        foreach ($headers as $key => $value) {
            $headers_str .= "{$key}: {$value} \r\n";
        }
        $headers_str .= "\r\n";

        if (@mail($to_add, $subject, $message, $headers_str)) {
            do_action('email_sent');
            return true;
        } else {
            do_action('email_failed');
            return false;
        }
    } elseif ($use_smtp && !empty($username) && !empty($password)) {

        require_once "Mail.php"; // PEAR Mail package
        require_once('Mail/mime.php'); // PEAR Mail_Mime packge

        $text = strip_tags(str_replace('<br>', "\n", $message));

        $mime = new Mail_mime("\n");
        $mime->setTXTBody($text);
        $mime->setHTMLBody($message);

        //do not ever try to call these lines in reverse order
        $body = $mime->get();
        $headers = $mime->headers($headers);

        $host = "localhost"; // all scripts must use localhost

        $smtp = Mail::factory('smtp', array(
            'host' => $host, 'auth' => true,
            'username' => $username, 'password' => $password
        ));

        $mail = $smtp->send($to_add, $headers, $body);

        if (!PEAR::isError($mail)) {
            do_action('email_failed');
            return false;
        } else {
            do_action('email_sent');
            return true;
        }
    } else {
        return false;
    }
}

/**
 *
 * Gets the time difference between two times.
 *
 * @param string|int $firstDate				First datetime.
 * @param string|int $secondDate			Second datetime. If left empty default to the current time.
 * @return string							A string representation of datetime difference. E.g. `2h` (2 Hours).
 */
function get_date_diff($firstDate, $secondDate = null)
{

    if (empty($secondDate)) $now = time();

    if (is_string($firstDate))
        $firstDate = strtotime($firstDate);
    if (null != $secondDate && is_string($secondDate))
        $secondDate = strtotime($secondDate);

    $time = $secondDate - $firstDate;

    if ($time > 2 && $time < 60)
        return floor($time) . 's';
    elseif ($time > 60 && $time < 3600)
        return floor($time / 60) . 'm';
    elseif ($time > 3600 && $time < 86400)
        return floor($time / 3600) . 'h';
    elseif ($time > 86400 && $time < 604800)
        return floor($time / 86400) . 'd';
    elseif ($time > 604800 && $time < 217728000)
        return floor($time / 604800) . 'w';
    else if ($time > 217728000)
        return floor($time / 217728000) . 'y';

    return "Now";
}

#-------- Start of is mobile -------------------#

/**
 *
 * Check if the broswer is mobile or not using class Mobile.
 *
 * @return bool
 */
function is_mobile2()
{
    global $api_mode;
    return ($api_mode == 'm');
}

/**
 *
 * Check if the broswer is mobile or not using RegEx.
 *
 * @return bool
 */
function is_mobile()
{
    $useragent = server('http_user_agent');
    if (
        preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm(os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows(ce|phone)|xda|xiino/i', $useragent)
        || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s)|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-||_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt(|\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v)|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v)|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g|nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))
    ) {
        return true;
    } else {
        return false;
    }
}


/**
 *
 * Deletes contents from an array.
 *
 * @param $array			The original array
 * @param $j				What to delete.
 * @return array			The original array without the deleted item.
 */
function array_delete($array, $j)
{
    $arr = [];
    if (is_numeric($j)) {
        for ($i = 0; $i < count($array); $i++) {
            if ($i != $j) {
                $arr = array_merge($arr, [$array[$i]]);
            }
        }
    } else {
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i] != $j) {
                $arr = array_merge($arr, [$array[$i]]);
            }
        }
    }
    return $arr;
}

/**
 *
 * Checks if two arrays as related i.e. one array contains some or all the elements of the other.
 *
 * @param $array
 * @param $array2
 * @param bool $strict		Indicates if the second array is allowed to have empty items or not.
 * @return bool
 */
function array_associated($array, $array2, $strict = false)
{

    if ((empty($array) && !empty($array2)) || (empty($array2) && !empty($array))) return false;

    foreach ($array2 as $ar) {
        if (!isset($array[$ar])) {
            return false;
        } else {
            if ($strict) {
                if (empty($array[$ar])) return false;
            }
        }
    }

    return true;
}

/**
 *
 * Returns an array with all elements in it trimmed or starting and ending whitespaces.
 *
 * @param $data				The array to trim
 * @param array $skips		Elements that should not be trimmed.
 * @return mixed			A trimmed array.
 */
function trim_assoc_data($data, $skips = [])
{

    foreach ($data as $key => $val) {
        if (!in_array($key, $skips)) {
            $data[$key] = trim($val);
        }
    }

    return $data;
}

/**
 *
 * Cleans up a name of invalid characters.
 *
 * @param $s
 * @return string
 */
function clean_name($s)
{
    return trim(preg_replace('/[^a-zA\-\s]/', '', $s));
}

/**
 *
 * Creates random strings.
 *
 * Note:
 * =========
 * As a rule, Candy's random string for reasons of verification never contains the small letter k.
 * This way we can always verify if a string may or not have been created by Candy's randomize.
 *
 * For framework beauty sake: Lets all keep to this rule.
 *
 * @param int $k				The length of the returned string.
 * @param bool $pr				If the string should contain numbers or not.
 * @return null|string			A random string.
 */
function randomize($k = 0, $pr = false)
{

    if ($k == 0) $k = 8;

    $s = null;

    // REP: Our random CANNOT contain `k` (lower case K).
    $a = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    if ($pr) {
        $a = array_merge($a, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']);
    }

    for ($i = 0; $i < $k; $i++) {
        $s .= $a[rand(0, count($a) - 1)];
    }

    return $s;
}


// --------------------------- WE AREN'T GOING TO PUT THIS UNDER FILE.PHP --------------------------------------


/**
 *
 * 	Gets the url of a stylesheet.
 *
 * @param string $s		Name of stylesheet without .css extension.
 * @return string
 */
function get_style($s = '')
{
    return get_url(ASSETS_DIR . '/css/' . $s . '.css');
}

/**
 *
 * Gets the url of a resource file.
 *
 * @param string $s
 * @return string
 */
function get_resource($s = '')
{
    return ASSETS_DIR . '/' . $s;
}

/**
 *
 * Gets a template file.
 *
 * @param $s						Name of the template file to return.
 * @param bool $return				If to return the content to variable or print it out.
 * @return bool|mixed|string		Content of the template file.
 */
function get_template($s, $return = false)
{
    if (strtolower(get_config('dev_mode', 'main', 'yes')) == 'yes') {

        // Clean templates.
        foreach (get_directory(CACHE_DIR . '/templates', CANDY_SCAN_FILES, '', -1, false, CANDY_SORT_FILES_FIRST) as $file) {
            $crname = pathinfo($file, PATHINFO_BASENAME);
            if (starts_with($crname, '.')) continue;

            @unlink($file);
        }

        // Load Templates.
        $template_files = get_directory(ROOT . '/' . get_config('template_dir', 'main'), CANDY_SCAN_FILES);
        foreach ($template_files as $file) {
            $crname = pathinfo($file, PATHINFO_BASENAME);
            if (starts_with($crname, '.')) continue;

            $s2 = new Chocolate(CACHE_DIR . '/templates');
            $s2->set_file($file);
            $s2->render(false);
        }
    }

    foreach ($GLOBALS as $g => $f) {
        $$g = $f;
    }

    if ($file = get_template_file($s)) {
        if (file_exists(THEME_DIR . '/' . $file)) {
            $t = new Chocolate(CACHE_DIR . '/templates');
            $t->set_file(THEME_DIR . '/' . $file);
            $t->render();
        }
    }

    return '';
}

/**
 *
 * Gets the file of a template.
 *
 * @param $s
 * @return bool|string
 */
function get_template_file($s)
{
    if (is_mobile2()) {

        $_cg = 'mobile/' . $GLOBALS['__platform__'];
        $_fg = 'mobile/all';

        if (preg_match('~^([a-zA-Z]+)\_(tablet|10|series40|series60)$~', $GLOBALS['__platform__'], $match)) {
            if (!file_exists(THEME_DIR . "/{$_cg}/" . $s)) {
                $_cg = 'mobile/' . $match[0][1];
            }
        }
    } else {
        $_cg = $_fg = 'web';
    }

    if (file_exists(THEME_DIR . "/{$_cg}/_overrides_/" . $s)) {
        return "{$_cg}/_overrides_/" . $s;
    } elseif (file_exists(THEME_DIR . "/{$_cg}/" . $s)) {
        return "{$_cg}/" . $s;
    } elseif (file_exists(THEME_DIR . "/{$_fg}/_overrides_/" . $s)) {
        return "{$_fg}/_overrides_/" . $s;
    } elseif (file_exists(THEME_DIR . "/{$_fg}/" . $s)) {
        return "{$_fg}/" . $s;
    } elseif (file_exists(THEME_DIR . "/web/_overrides_/" . $s)) {
        return "web/_overrides_/" . $s;
    } else {
        return "web/" . $s;
    }

    return false;
}



// ------------------------ THAT'S ALL WE AREN'T GOING TO PUT UNDER FILE.PHP -------------------------


/**
 *
 * Use this instead of get_text if you declared your language data in concepts/langs.php.
 *
 * Use concatenation (.) to access child items if the data is an array.
 *
 * @param $s
 * @param $language
 * @return string
 */
function e($s, $language = '')
{
    global $_LANGS;

    if (empty($language)) {
        $language = get_config('language', 'main');
    }

    if (isset($_LANGS[$language])) {
        if (strpos($s, '.') == 0) {
            if (isset($_LANGS[$language][strtolower($s)])) {
                return $_LANGS[$language][strtolower($s)];
            }
        } else {

            $sr = explode('.', $s);

            if (isset($_LANGS[$language][strtolower($sr[0])])) {
                return $_LANGS[$language][strtolower($sr[0])][strtolower($sr[1])];
            }
        }
    }
    return '';
}

/**
 * Returns a text that is an hybrid between the e() and the get_text() methods.
 * @param $s
 * @param string $lang
 * @return mixed|string
 */
function e_text($s, $lang = '')
{
    $text = get_text($s, $lang);
    if (empty($text)) {
        $text = e($s);
    }

    return $text;
}

/**
 *
 * Creates pagination links.
 *
 * @param int $page				Current page.
 * @param int $total			Total items.
 * @param int $per_page			Number of items per page.
 * @param string $pager			The page object in the url query string. Default: 'p' e.g. ?p=1
 * @param string $url			The url the pagination points to.
 * @return string
 */
function paginate($page = 0, $total = 0, $per_page = 0, $pager = 'p', $url = '')
{

    $result = '<div class="pagination">';

    if (empty($url)) $url = '?';
    else {

        $url = preg_replace('/[&?]' . $pager . '\=\d+/', '', $url);

        if (strpos($url, '?') > -1) $url .= '&';
        else $url .= '?';
    }

    $total_pages = ceil($total / $per_page);
    if ($total_pages > 1) {

        if ($page < 1) $page = 1;
        elseif ($page > $total_pages) $page = $total_pages;

        if ($page > 1) {
            $result .= "<a href='{$url}{$pager}=" . ($page - 1) . "'>Previous</a>";
        }

        for ($i = $page - 5; $i < $page; $i++) {
            if ($i > 0) {
                $result .= "<a href='{$url}{$pager}={$i}'>$i</a>";
            } else {
                continue;
            }
        }
        $result .= "<a class='active'>$page</a>";
        for ($i = $page + 1; $i < ($total_pages > 10 ? $page + 6 : 11); $i++) {
            if ($i <= $total_pages) {
                $result .= "<a href='{$url}{$pager}={$i}'>$i</a>";
            } else {
                continue;
            }
        }

        if ($page < $total_pages) {
            $result .= "<a href='{$url}{$pager}=" . ($page + 1) . "'>Next</a>";
        }
    }

    return ($result .= "</div>");
}

/**
 *
 * Return the real dimension size from web format dimensions.
 *
 * Supported formats: px, em, rem, %.
 *
 * @param $size
 * @param $real
 * @return array|float|int|mixed|string
 */
function real_size($size, $real)
{

    if (preg_match('~[0-9.]+\s*(px|r?em|%)~sim', $size)) {

        $size = preg_replace('~([0-9.]+)\s*(px|r?em|%)~sim', '$1 $2', $size);
        $size = explode(' ', $size);
        if ($size[0][0] == '.') $size[0] = '0.' . $size[0];

        switch (strtolower($size[1])) {
            case 'px':
                $size = $size[0];
                break;
            case 'rem':
                $size =  $size[0] * 16;
                break;
            case 'em':
                $size =  $size[0] * 16;
                break;
            case '%':
                $size =  $size[0] / 100 * $real;
                break;
        }
    }

    return $size <= $real ? $size : $real;
}

/**
 *
 * Returns the real time as a php time integer from a human readable time.
 *
 * Specs:
 * =======================
 * s = Seconds
 * m = Minutes
 * h = Hours
 * d = Days
 *
 *
 * Without the base_date value set, it will return time as number of seconds from Oct 1, 1970.
 * With the base_date value set, it returns time from that date. E.g. 2h with base_date as Jan 1, 2017 12:00am
 * will return the integer time of Jan 1, 2017 2:00am.
 *
 * @param $time									The time string.
 * @param string $base_date						The base date to start calculating from.
 * @return array|false|int|mixed|string
 */
function real_time($time, $base_date = '')
{

    if (is_numeric($time)) {
        return time() + $time;
    }

    if (!empty($base_date)) {
        if (!is_numeric($base_date)) {
            $base_date = strtotime($base_date);
        }
    } else $base_date = time();

    if (preg_match('~[0-9.]+\s*(s|m|h|d)~sim', $time)) {

        $time = preg_replace('~([0-9.]+)\s*(s|m|h|d)~sim', '$1 $2', $time);
        $time = explode(' ', $time);
        if ($time[0][0] == '.') $time[0] = '0.' . $time[0];

        switch (strtolower($time[1])) {
            case 's':
                return $base_date + $time[0];
            case 'm':
                return $base_date + ($time[0] * 60);
            case 'h':
                return $base_date + ($time[0] * 60 * 60);
            case 'd':
                return $base_date + ($time[0] * 60 * 60 * 24);
        }
    }

    return $time;
}

/**
 *
 * Converts a comma delimited string to an array.
 *
 * @param $string
 * @param string $char
 * @param bool $trim
 * @return array
 */
function string_to_array($string, $char = ',', $trim = true)
{
    $string = explode($char, $string);
    if ($trim) {
        for ($i = 0; $i < count($string); $i++) {
            $string[$i] = trim($string[$i]);
        }
    }
    return $string;
}


/**
 *
 * Mimics the python range function.
 *
 * @param $x
 * @param $y
 * @return array
 */
function irange($x, $y = null)
{
    $ret = [];
    if ($y != null) {
        if ($y > $x) {
            for ($x; $x <= $y; $x++) {
                array_push($ret, $x);
            }
        } else {
            for ($x; $x >= $y; $x--) {
                array_push($ret, $x);
            }
        }
    } else {
        if ($x > 0) {
            for ($x; $x >= 0; $x--) {
                array_push($ret, $x);
            }
        } else if ($x < 0) {
            for ($x; $x <= 0; $x++) {
                array_push($ret, $x);
            }
        }
    }
    return $ret;
}


/**
 *
 * Calls a function with arguments passed in where applicable.
 *
 * @param $name
 * @param null $values
 * @return mixed
 */
function call($name, $values = null)
{
    if ($values == null) {
        return call_user_func($name);
    } else if (is_array($values)) {
        return call_user_func_array($name, $values);
    } else {
        return call_user_func($name, $values);
    }
}

/**
 *
 * Returns a secured password string.
 *
 * CAUTION
 * ==========
 * As the strength increases away from 10, more system resource will be consumed in generating the secure password.
 *
 * Why didn't we use this for CSRF fields?
 * Website visitors expect pages to load fast. Using this in CSRF will increase load time of a page.
 *
 * @param $s
 * @param int $strength
 * @return bool|string
 */
function secure_password($s, $strength = 10)
{
    return password_hash($s, PASSWORD_DEFAULT, ['cost' => $strength]);
}

/**
 *
 * Verifies a secure password string against a string.
 *
 * @param $pass
 * @param $real
 * @return bool
 */
function verify_password($pass, $real)
{
    if (get_config('encrypt_passwords', 'main', true) == true) {
        return password_verify($pass, $real);
    } else return ($pass == $real);
}


/**
 * Checks if a string starts with another.
 *
 * @param $string           The parent string
 * @param string $sub       The string that may occur at the beginning of the parent string or not
 * @param bool $strict      Whether to be case-sensitive or not
 * @return bool
 */
function starts_with($string, $sub = '', $strict = true)
{
    if (empty($sub)) {
        throw new Exception('Parameter 2 of starts_with with type string cannot be empty');
    }

    if (strpos($string, $sub) < 0) {
        return false;
    }

    $str = substr($string, 0, strlen($sub));
    if (!$strict) {
        $sub = strtolower($sub);
        $str = strtolower($str);
    }
    return $str === $sub;
}


/**
 * Checks if a string ends with another.
 *
 * @param $string           The parent string
 * @param string $sub       The string that may occur at the end of the parent string or not
 * @param bool $strict      Whether to be case-sensitive or not
 * @return bool
 */
function ends_with($string, $sub = '', $strict = true)
{
    if (empty($sub)) {
        throw new Exception('Parameter 2 of ends_with with type string cannot be empty');
    }

    if (empty($sub) || strpos($string, $sub) < 0) {
        return false;
    }

    $str = substr($string, strlen($string) - strlen($sub));
    if (!$strict) {
        $sub = strtolower($sub);
        $str = strtolower($str);
    }
    return $str === $sub;
}


/**
 * Return an object that can be safely echoed without verification.
 *
 * @param $obj              The object to safe
 * @return mixed|string     Echo safe version of the input object
 */
function safe_echo($obj)
{
    try {
        return isset($obj) ? to_string($obj) : '';
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}
