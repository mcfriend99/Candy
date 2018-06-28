<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2017 Onehyr Technologies Limited
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

if(!defined('CANDY')){
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
function load_configs($file, $is_global = true){
    
    if(substr($file, -6) != '.candy'){
        throw new Exception('Invalid config file found in configs folder.');
    }
    
    $fname = strtolower(substr(basename($file), 0, -(strlen('.candy'))));

    $gname = sha1($file . filemtime($file));
    $gfile = CACHE_DIR . "/configs/{$gname}.php";

    if(!file_exists($gfile) || !$is_global) {

        $hash_matches = [];

        foreach (@file($file) as $line) {

            if (strlen(trim($line)) > 0) {

                if (trim($line)[0] == '#') {

                    $hash_line = trim($line);
                    $pline = preg_split('/[\s]/', $hash_line);

                    $key = substr($pline[0], 1);
                    $value = '';
                    if (count($pline) > 1) {
                        unset($pline[0]);
                        $value = implode(' ', $pline);
                    }

                    $value = __clear_configs_comments($value);
                    // { Comments here... } in configs files, [config_name] => Include another config in config value.

                    if (!in_array($key, $hash_matches)) {

                        $hash_matches = array_merge($hash_matches, [strtolower($key) => $value]);
                    } else {

                        $hash_matches[strtolower($key)] = $value;
                    }
                }
            }
        }

        if ($is_global) {
            if(!file_exists(CACHE_DIR .'/configs') || !is_dir(CACHE_DIR . '/configs'))
                mkdir(CACHE_DIR . '/configs');
			$ur = addslashes(json_encode($hash_matches));
            file_put_contents($gfile, "<?php \$GLOBALS['__CONFIG__{$fname}__'] = json_decode(stripslashes(\"{$ur}\"), true); ?>");
            include $gfile;
        } else return $hash_matches;
    } else {
        (include $gfile);
    }
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
function get_config($conf, $type = 'site', $default = ''){

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
function load_texts($file){

    if(substr($file, -6) != '.candy'){
        throw new Exception('Invalid language file found in language folder.');
    }

    $fname = strtolower(substr(basename($file), 0, -(strlen('.candy'))));

    $gname = sha1($file . filemtime($file));
    $gfile = CACHE_DIR . "/langs/{$gname}.php";

    if(!file_exists($gfile)) {

        $hash_matches = [];
        $last_key = '';

        foreach (@file($file) as $line) {

            if (strlen(trim($line)) > 0) {

                if (trim($line)[0] == '#') {

                    $hash_line = trim($line);
                    $pline = preg_split('/[\s]/', $hash_line);

                    $key = $last_key = substr($pline[0], 1);
                    $value = '';
                    if (count($pline) > 1) {
                        unset($pline[0]);
                        $value = implode(' ', $pline);
                    }

                    $value = __clear_configs_comments($value);
                    // { Comments here... } in configs files, [config_name] => Include another config in config value.

                    if (!in_array($key, $hash_matches)) {

                        $hash_matches = array_merge($hash_matches, [strtolower($key) => $value]);
                    } else {

                        $hash_matches[strtolower($key)] = $value;
                    }
                } else if($last_key != ''){
                    $hash_matches[strtolower($last_key)] .= trim($line);
                }
            }
        }

        if(!file_exists(CACHE_DIR . '/langs') || !is_dir(CACHE_DIR . '/langs'))
            mkdir(CACHE_DIR . '/langs');
		$ur = addslashes(serialize($hash_matches));
        file_put_contents($gfile, "<?php \$GLOBALS['__LANGUAGE__{$fname}__'] = @unserialize(stripslashes(\"{$ur}\")); ?>");

        include $gfile;
    } else {
		
        include $gfile;
    }
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
function get_text($id, $language = '', $default = ''){
    
    if(empty($language))
        $language = get_config('language', 'main');
	
    $__langs__ = $GLOBALS["__LANGUAGE__{$language}__"];
	
    return isset($__langs__[strtolower($id)]) ? __replace_cfgs($__langs__[strtolower($id)], 0, $language) : $default;
}

// Removes comments from configs and text files using syntax: {This is a comment}.
function __clear_configs_comments($value){
    return addslashes(trim(preg_replace_callback('~(?<!{){[^}]+}\s*(\r?\n)?~', function(){
        return '';
    }, $value)));
}

// Allows the include of a text or config within another text or config respectively using syntax:  [another]
function __replace_cfgs($text, $type, $ul = 'main'){
    
    preg_match_all('~(?<!\[)\[[^\]]+\]~sim', $text, $matches);
    
    if(!empty($matches[0])){
        
        $matches = $matches[0];
        
        foreach($matches as $match){
            
            $cr = substr($match, 1, -1);
            
            if($type == 0)
                $cr = get_text($cr, $ul, $match);
            else if($type == 1)
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
function c($name, $type = 'site', $default = ''){
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
function t($name, $language = '', $default = ''){
    return get_text($name, $language, $default);
}


