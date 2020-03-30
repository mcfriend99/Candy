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
 * Loads configuration data from a .candy file.
 *
 * @param $file             File to load configs from
 * @param bool $is_global   Whether to load the config globally or return it as an accessible array.
 * @return array            An array that can be traversed in non-global configuration context.
 */
function load_configs($file, $is_global = true)
{

    if (substr($file, -5) != '.json') {
        throw new Exception('Invalid config file found in configs folder.');
    }

    $fname = strtolower(substr(basename($file), 0, - (strlen('.json'))));

    $GLOBALS["__CONFIG__{$fname}__"] = json_decode(file_get_contents($file), true);
}

/**
 *
 * Gets a configuration value.
 *
 * @param $conf                 The configuration required.
 * @param string $type          The type of configuration. Deduced from the file name of the .candy file.
 * @param string $default       A default value to be returned if the configuration is not set.
 * @return mixed|string         Configuration value as string.
 */
function get_config($conf, $type = 'site', $default = '')
{
    $__configs__ = $GLOBALS["__CONFIG__{$type}__"];
    return isset($__configs__[strtolower($conf)]) ? __replace_cfgs($__configs__[strtolower($conf)], 1, $type) : $default;
}




// Texts are also config files so we will be keeping their code in this file.

/**
 *
 * Loads text/language/localization data from a file.
 * Texts are global.
 *
 * @param $file     The file to load.
 */
function load_texts($file)
{

    if (substr($file, -5) != '.json') {
        throw new Exception('Invalid language file found in language folder.');
    }

    $fname = strtolower(substr(basename($file), 0, - (strlen('.json'))));

    $GLOBALS["__LANGUAGE__{$fname}__"] = json_decode(file_get_contents($file), true);
}

/**
 *
 * Gets the value of a text/localization data for a language.
 * Default language: En.
 *
 * @param $id                       An identifier of the required text.
 * @param string $language          The language required. Specified via the filename of the .candy language file.
 * @param string $default           A default text to be returned if the text was not previously set.
 * @return mixed|string             The text as string.
 */
function get_text($id, $language = '', $default = '')
{
    if (empty($language))
        $language = get_config('language', 'main');

    $__langs__ = $GLOBALS["__LANGUAGE__{$language}__"];

    return isset($__langs__[strtolower($id)]) ? __replace_cfgs($__langs__[strtolower($id)], 0, $language) : $default;
}

// Removes comments from configs and text files using syntax: {This is a comment}.
function __clear_configs_comments($value)
{
    return addslashes(trim(preg_replace_callback('~(?<!{){[^}]+}\s*(\r?\n)?~', function () {
        return '';
    }, $value)));
}

// Allows the include of a text or config within another text or config respectively using syntax:  [another]
function __replace_cfgs($text, $type, $ul = 'main')
{

    preg_match_all('~(?<!\[)\[[^\]]+\]~sim', $text, $matches);

    if (!empty($matches[0])) {

        $matches = $matches[0];

        foreach ($matches as $match) {

            $cr = substr($match, 1, -1);

            if ($type == 0)
                $cr = get_text($cr, $ul, $match);
            else if ($type == 1)
                $cr = get_config($cr, $ul, $match);

            $text = str_replace($match, $cr, $text);
        }
    }

    return stripslashes($text);
}


/**
 *
 * Shorthand for get_config()
 *
 * @param $name
 * @param string $type
 * @param string $default
 * @return mixed|string
 */
function c($name, $type = 'site', $default = '')
{
    return get_config($name, $type, $default);
}


/**
 *
 * Shorthand for get_text()
 *
 * @param $name
 * @param string $language
 * @param string $default
 * @return mixed|string
 */
function t($name, $language = '', $default = '')
{
    return get_text($name, $language, $default);
}
