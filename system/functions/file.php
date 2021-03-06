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


define('CANDY_SCAN_ALL', 1);
define('CANDY_SCAN_FILES', 2);
define('CANDY_SCAN_DIRECTORIES', 3);
define('CANDY_SORT_DEFAULT', 1);
define('CANDY_SORT_FILES_FIRST', 2);
define('CANDY_SORT_FILES_LAST', 3);

define('CANDY_UPLOAD_TOO_LARGE', -1);
define('CANDY_UPLOAD_INVALID', -2);
define('CANDY_UPLOAD_FAIL', -3);

/**
 * Gets the contents of a directory.
 *
 * @param string $dir               The directory path.
 * @param int $type                 One of CANDY_SCAN_ALL, CANDY_SCAN_FILES and CANDY_SCAN_DIRECTORIES.
 * @param string $extension         Only search for files with specific extensions.
 * @param bool $show_hidden         Scan hidden files.
 * @param int $depth                Depth of the directory scan.
 * @param int $sort                 One of CANDY_SORT_DEFAULT, CANDY_SORT_FILES_FIRST and CANDY_SORT_FILES_LAST.
 * @return array                    List of directory content.
 */
function get_directory($dir = '/', $type = 1, $extension = '', $depth = 0, $show_hidden = false, $sort = 1)
{

    $files = [];
    $dirs = [];

    $iterator = new RecursiveIteratorIterator(
        (!$show_hidden ?
            new RecursiveDirectoryIterator($dir, (RecursiveDirectoryIterator::SKIP_DOTS
                | RecursiveDirectoryIterator::UNIX_PATHS))
            : new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::UNIX_PATHS)),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    if ($depth > 0)
        $iterator->setMaxDepth($depth - 1);

    foreach ($iterator as $file) {

        $filename = $file->getPathname();

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (('.' . $ext == $extension) || $extension == '') {

            if ($type == CANDY_SCAN_ALL) {

                if (!$file->isDir()) {

                    array_push($files, $filename);
                } else {

                    array_push($dirs, $filename);
                }
            } else if ($type == CANDY_SCAN_FILES) {

                if (!$file->isDir()) {

                    array_push($files, $filename);
                }
            } else {

                if ($file->isDir()) {

                    array_push($dirs, $filename);
                }
            }
        }
    }

    /* sort($files);
    sort($dirs);*/

    $result = [];

    if ($sort == CANDY_SORT_FILES_FIRST) {

        $result = array_merge($files, $dirs);
    } else {

        $result = array_merge($dirs, $files);
    }

    if (function_exists('do_action'))
        do_action('on_get_directory', $result);

    return $result;
}

/**
 * Gets the name of a directory from its path.
 *
 * @param $path
 * @return mixed
 */
function get_directory_name($path)
{

    $split = preg_split('/[\\/]+/', $path);
    return $split[count($split) - 1];
}

/**
 * Formats filesize to human-readable format.
 *
 * @param $bytes
 * @return string
 */
function format_file_size($bytes)
{
    if (!is_numeric($bytes)) {
        return '0B';
    }

    if ($bytes >= 1000000000000000) {
        return sprintf('0.2%f', ($bytes / 1000000000000000)) . 'PB';
    }

    if ($bytes >= 1000000000000) {
        return sprintf('0.2%f', ($bytes / 1000000000000)) . 'TB';
    }

    if ($bytes >= 1000000000) {
        return sprintf('0.2%f', ($bytes / 1000000000)) . 'GB';
    }

    if ($bytes >= 1000000) {
        return sprintf('0.2%f', ($bytes / 1000000)) . 'MB';
    }

    if ($bytes >= 1000) {
        return sprintf('0.2%f', ($bytes / 1000)) . 'KB';
    }

    return apply_filters('format_file_size', sprintf('0.2%f', ($bytes)) . 'B');
}

/**
 * Converts human-readable filesize to its respective numeric value.
 *
 * @param $s
 * @return int
 */
function real_file_size($s)
{
    $s = preg_replace('~([0-9.]+)\s*(B|K|M|G|T|P)[B]*~sim', '$1 $2', $s);
    $s = explode(' ', $s);

    if ($s[0][0] == '.') $s[0] = '0.' . $s[0];

    switch (strtoupper($s[1])) {

        case 'P':
            return $s[0] * 1000000000000000;
        case 'T':
            return $s[0] * 1000000000000;
        case 'G':
            return $s[0] * 1000000000;
        case 'M':
            return $s[0] * 1000000;
        case 'K':
            return $s[0] * 1000;
        case 'B':
            return $s[0];
    }

    if (is_numeric($s))
        return apply_filters('real_file_size', $s);

    return apply_filters('real_file_size', 0);
}

/**
 * Creates a script concept.
 *
 * @param $name                 Name of the concept
 * @param $file                 The relative path of the script from #assets_dir without the .js extension.
 * @param string $requires      A comma separated or array list of concepts required by this concept.
 * @param bool $async			Whether to load the script asynchronously or not.
 */
function add_script($name, $file, $requires = '', $async = false)
{
    $GLOBALS['__scripts__'][$name] = ['file' => $file, 'requires' => $requires, 'async' => $async];
}

/**
 * Creates a style concept.
 *
 * @param $name                 Name of the concept
 * @param $file                 The relative path of the script from #assets_dir without the .css extension.
 * @param array $requires      A comma separated or array list of concepts required by this concept.
 */
function add_style($name, $file, $requires = [])
{
    $GLOBALS['__styles__'][$name] = ['file' => $file, 'requires' => $requires];
}

/**
 * Includes script concept in the file.
 *
 * @param string|array $s           A comma separated or array list of script concepts.
 * @return string
 */
function include_scripts($s = null)
{


    if (is_string($s)) {
        $s = explode(',', $s);
    }

    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__scripts__'][$key])) {

            if (descripted($key)) {

                model_error('Script &quot;' . $key . '&quot;');
            }

            $files = $GLOBALS['__scripts__'][$key]['file'];
            $r = $GLOBALS['__scripts__'][$key]['requires'];
            $async = $GLOBALS['__scripts__'][$key]['async'];

            if (!empty($r))
                include_scripts($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/js/' . $f . '.js')) {

                    echo '<script src="' . get_resource_url('js/' . $f . '.js') . '"' . ($async ? ' async="true"' : '') . '></script>';
                } else {

                    implementation_error('Script &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Script &quot;' . $key . '&quot;');
        }
    }
}

/**
 * Returns script concept as string.
 *
 * @param string|array $s           A comma separated or array list of script concepts.
 * @return string
 */
function get_scripts($s = null)
{


    $result = '';

    if (is_string($s)) {
        $s = explode(',', $s);
    }

    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__scripts__'][$key])) {

            if (descripted($key)) {

                model_error('Script &quot;' . $key . '&quot;');
            }

            $files = $GLOBALS['__scripts__'][$key]['file'];
            $r = $GLOBALS['__scripts__'][$key]['requires'];
            $async = $GLOBALS['__scripts__'][$key]['async'];

            if (!empty($r))
                include_scripts($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/js/' . $f . '.js')) {

                    $result .= '<script src="' . get_resource_url('js/' . $f . '.js') . '"' . ($async ? ' async="true"' : '') . '></script>';
                } else {

                    implementation_error('Script &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Script &quot;' . $key . '&quot;');
        }
    }

    return $result;
}

/**
 * Inline script concepts. directly in the file.
 *
 * @param string|array $s       A comma separated or array list of script concepts.
 */
function inline_scripts($s = null)
{
    if (is_string($s)) {
        $s = explode(',', $s);
    }

    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__scripts__'][$key])) {

            if (descripted($key)) {

                model_error('Script &quot;' . $key . '&quot;');
            }

            $files = $GLOBALS['__scripts__'][$key]['file'];
            $r = $GLOBALS['__scripts__'][$key]['requires'];

            if (!empty($r))
                inline_scripts($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/js/' . $f . '.js')) {

                    echo '<script type="application/javascript">' . file_get_contents(ASSETS_DIR . '/js/' . $f . '.js') . '</script>';
                } else {

                    implementation_error('Script &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Script &quot;' . $key . '&quot;');
        }
    }
}

/**
 * Returns inline script concepts. directly in the file.
 *
 * @param string|array $s       A comma separated or array list of script concepts.
 * @return string
 */
function get_inline_scripts($s = null)
{


    $result = '';

    if (is_string($s)) {
        $s = explode(',', $s);
    }

    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__scripts__'][$key])) {

            if (descripted($key)) {

                model_error('Script &quot;' . $key . '&quot;');
            }

            $files = $GLOBALS['__scripts__'][$key]['file'];
            $r = $GLOBALS['__scripts__'][$key]['requires'];

            if (!empty($r))
                inline_scripts($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/js/' . $f . '.js')) {

                    $result .= '<script type="application/javascript">' . file_get_contents(ASSETS_DIR . '/js/' . $f . '.js') . '</script>';
                } else {

                    implementation_error('Script &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Script &quot;' . $key . '&quot;');
        }
    }

    return apply_filters('get_inline_scripts', $result, $s);
}

/**
 * Make a script concept non-usable.
 *
 * @param string|array $s
 */
function dequeue_script($s = null)
{
    $vr = $s;
    if (is_string($s)) {
        $vr = explode(',', $s);
    }

    foreach ($vr as $v) {
        $GLOBALS['__descripted__'][] .= trim($v);
    }
}

/**
 * Detect if a script concept is non-usable.
 *
 * @param $s
 * @return bool
 */
function descripted($s)
{
    return in_array($s, $GLOBALS['__descripted__']);
}

/**
 * Make a style concept non-usable.
 *
 * @param string|array $s
 */
function dequeue_style($s = null)
{
    $vr = $s;
    if (is_string($s)) {
        $vr = explode(',', $s);
    }

    foreach ($vr as $v) {
        $GLOBALS['__destyled__'][] .= trim($v);
    }
}

/**
 * Detect if a style concept is non-usable.
 *
 * @param $s
 * @return bool
 */
function destyled($s)
{
    return in_array($s, $GLOBALS['__destyled__']);
}

/**
 * Includes style concepts within a file.
 *
 * @param string|array $s   A comma separated or array list of style concepts.
 */
function include_styles($s = null)
{


    if (is_string($s)) {
        $s = explode(',', $s);
    }
    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__styles__'][$key])) {

            if (destyled($key)) {

                model_error('Style &quot;' . $key . '&quot;');
            }

            $files = trim($GLOBALS['__styles__'][$key]['file']);
            $r = $GLOBALS['__styles__'][$key]['requires'];

            if (!empty($r))
                include_style($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/css/' . $f . '.css')) {

                    echo '<link rel="stylesheet" href="' . get_resource_url('css/' . $f . '.css') . '" />';
                } else {

                    implementation_error('Style &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Style &quot;' . $key . '&quot;');
        }
    }
}

/**
 * Returns style concepts as a string.
 *
 * @param string|array $s   A comma separated or array list of style concepts.
 * @return string
 */
function get_styles($s = null)
{


    $result = '';

    if (is_string($s)) {
        $s = explode(',', $s);
    }
    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__styles__'][$key])) {

            if (destyled($key)) {

                model_error('Style &quot;' . $key . '&quot;');
            }

            $files = trim($GLOBALS['__styles__'][$key]['file']);
            $r = $GLOBALS['__styles__'][$key]['requires'];

            if (!empty($r))
                include_style($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/css/' . $f . '.css')) {

                    $result .= '<link rel="stylesheet" href="' . get_resource_url('css/' . $f . '.css') . '" />';
                } else {

                    implementation_error('Style &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Style &quot;' . $key . '&quot;');
        }
    }

    return apply_filters('get_styles', $result);
}

/**
 * Inlines a style concept within a file.
 *
 * @param string|array $s
 */
function inline_styles($s = null)
{


    if (is_string($s)) {
        $s = explode(',', $s);
    }
    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__styles__'][$key])) {

            if (destyled($key)) {

                model_error('Style &quot;' . $key . '&quot;.');
            }

            $files = $GLOBALS['__styles__'][$key]['file'];
            $r = $GLOBALS['__styles__'][$key]['requires'];

            if (!empty($r))
                inline_style($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/css/' . $f . '.css')) {

                    echo '<style type="text/css">' . file_get_contents(ASSETS_DIR . '/css/' . $f . '.css') . '</style>';
                } else {

                    implementation_error('Style &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Style &quot;' . $key . '&quot;');
        }
    }
}

/**
 * Returns an inline style concept within a file.
 *
 * @param string|array $s
 * @return string
 */
function get_inline_styles($s = null)
{


    $result = '';

    if (is_string($s)) {
        $s = explode(',', $s);
    }
    foreach ($s as $key) {

        $key = trim($key);

        if (isset($GLOBALS['__styles__'][$key])) {

            if (destyled($key)) {

                model_error('Style &quot;' . $key . '&quot;.');
            }

            $files = $GLOBALS['__styles__'][$key]['file'];
            $r = $GLOBALS['__styles__'][$key]['requires'];

            if (!empty($r))
                inline_style($r);

            if (is_string($files)) {

                $files = explode(',', $files);
            }

            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists(ASSETS_DIR . '/css/' . $f . '.css')) {

                    $result .= '<style type="text/css">' . file_get_contents(ASSETS_DIR . '/css/' . $f . '.css') . '</style>';
                } else {

                    implementation_error('Style &quot;' . $key . '&quot;');
                }
            }
        } else {

            model_error('Style &quot;' . $key . '&quot;');
        }
    }

    return apply_filters('get_inline_styles', $result);
}

/** @param $s
 */
function include_script($s)
{
    include_scripts($s);
}

/** @param $s
 */
function inline_script($s)
{
    inline_scripts($s);
}

/** @param $s
 */
function get_inline_script($s)
{
    return get_inline_scripts($s);
}

/** @param $s
 */
function include_style($s)
{
    include_styles($s);
}

/** @param $s
 */
function inline_style($s)
{
    inline_styles($s);
}

/** @param $s
 */
function get_inline_style($s)
{
    return get_inline_styles($s);
}

/**
 * Safely deletes a file.
 *
 * @param $file
 */
function delete($file)
{

    if (file_exists($file)) {
        unlink($file);
        do_action('on_file_deleted', $file);
    }
}

/**
 * Returns a clean file name.
 *
 * @param $s
 * @return mixed
 */
function clean_filename($s)
{

    return apply_filters('clean_filename', preg_replace('~[^a-zA-Z0-9\-_.]+~', '', $s));
}


/**
 * Creates an uploader concept.
 *
 * @param $name                 Name of the concept.
 * @param string $assoc         The name of the file element.
 * @param string $target_dir    The target directory of the upload.
 * @param array $options
 */
function add_uploader($name, $assoc = 'file', $target_dir = '', $options = [])
{
    if (empty($target_dir))
        $target_dir = ROOT . '/' . get_config('public_dir', 'main') . '/' . get_config('uploads_dir', 'main');
    else
        $target_dir = ROOT . '/' . get_config('public_dir', 'main') . '/' . $target_dir;

    $GLOBALS['__uploaders__'][$name] = ['name' => $assoc, 'target_dir' => $target_dir, 'options' => $options];
}

/**
 * Converts the files() array to the cleaner (IMHO) array.
 *
 * @param array $file_post
 * @return arrray IMHO files() array.
 */
function __re_array_files(&$file_post)
{

    $file_ary = array();
    $multiple = is_array($file_post['name']);

    $file_count = $multiple ? count($file_post['name']) : 1;
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $multiple ? $file_post[$key][$i] : $file_post[$key];
        }
    }

    return $file_ary;
}


/**
 * Perform upload of a file.
 *
 * @param $name                     Name of the concept.
 * @param string $target_name       The target name of the file.
 * @return int|string               One of CANDY_UPLOAD_INVALID, CANDY_UPLOAD_TOO_LARGE, CANDY_UPLOAD_FAIL or a valid filename.
 */
function do_upload($name, $target_name = '')
{
    if (!isset($GLOBALS['__uploaders__'][$name]))
        model_error('Uploader &quot;' . $name . '&quot;');

    $name2 = $GLOBALS['__uploaders__'][$name]['name'];

    if (!isset($_FILES[$name2]))
        implementation_error('Uploader &quot;' . $name . '&quot;');

    $options = $GLOBALS['__uploaders__'][$name]['options'];

    return _do_upload($name2, $name, $options, $target_name);
}


function _do_upload($name, $name2, $options, $target_name = '')
{
    if (!isset($options['max']))
        $options['max'] = real_file_size(ini_get('upload_max_filesize'));
    else $options['max'] = real_file_size($options['max']);

    $_fname = files($name);
    $_files = __re_array_files($_fname);

    foreach ($_files as $_file) {
        if (!is_array($_file)) {
            implementation_error('Uploader &quot;' . $name . '&quot; contains invalid files and');
        }
    }

    if (!empty($options['accepts'])) {

        if (is_string($options['accepts']))
            $options['accepts'] = explode(',', $options['accepts']);

        foreach ($_files as $_file) {
            $extension = pathinfo($_file['name'], PATHINFO_EXTENSION);

            if (!in_array(strtolower($extension), $options['accepts'])) {

                return CANDY_UPLOAD_INVALID;
            }
        }
    }

    foreach ($_files as &$_file) {
        if ($_file['size'] > $options['max']) {
            delete($_file['tmp_name']);

            return CANDY_UPLOAD_TOO_LARGE;
        }
    }

    $return_arr = [];

    foreach ($_files as &$_file) {
        if (empty($target_name))
            $target_name = clean_filename($_file['name']);

        $ext = pathinfo($_file['name'], PATHINFO_EXTENSION);
        if (!ends_with($target_name, '.' . $ext)) $target_name .= '.' . $ext;

        $filename = trim($GLOBALS['__uploaders__'][$name2]['target_dir'], '/') . '/' . $target_name;

        if (move_uploaded_file($_file['tmp_name'], $filename)) {

            // Set proper file permission.
            chmod($filename, 0777);

            if (count($_files) == 1) {
                return [$filename];
            } else {

                array_push($return_arr, $filename);
            }
        }
    }

    if (!empty($return_arr)) {

        do_action('on_file_uploaded', $return_arr); // Return an array of uploaded filename to event handlers.
        return $return_arr;
    }

    return CANDY_UPLOAD_FAIL;
}
