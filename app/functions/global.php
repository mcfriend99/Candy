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
 * Base function to read and modify globals.
 * NOT TO BE CALLED.
 *
 * @param $type
 * @param $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function ___global($type, $s, $new = '', $trim = false){

    global $$type;
	
	switch(strtoupper($type)){
		case '_GET':
			$g = &$_GET;
			break;
		case '_POST':
			$g = &$_POST;
			break;
		case '_PUT':
			$g = &$_PUT;
			break;
		case '_REQUEST':
			$g = &$_REQUEST;
			break;
		case '_FILES':
			$g = &$_FILES;
			break;
		case '_SESSION':
			$g = &$_SESSION;
			break;
		case '_SERVER':
			$g = &$_SERVER;
			break;
		default:
			$g = &$$type;
			break;
	}

    if(!empty($s)){

        if(empty($new))
            $p = isset($g[$s]) ? ($trim ? trim($g[$s]) : $g[$s]) : '';
        else
            $p = $g[$s] = $trim ? trim($new) : $new;

        return $p;
    } else {

        if($trim){

            foreach($g as $m => $n){

                $g[$m] = empty($new) ? trim($n) : $new;
            }
        }

        return $g;
    }

}

/**
 *
 * Base function to delete or clear globals.
 * NOT TO BE CALLED.
 *
 * @param $type
 * @param string $name
 */
function ___unglobal($type, $name = ''){

    global $$type;

    if(!empty($name)){
        if(!empty(___global($type, $name)))
            unset($$type[$name]);
    } else {
        if($type == '_SESSION')
            session_destroy();
        unset($$type);
    }
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function get($s = '', $new = '', $trim = false){

    return ___global('_GET', $s, $new, $trim);

}

/**
 * @param string $s
 */
function unget($s = ''){

    ___unglobal('_GET', $s);
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function post($s = '', $new = '', $trim = false){

    return ___global('_POST', $s, $new, $trim);

}

/**
 * @param string $s
 */
function unpost($s = ''){

    ___unglobal('_POST', $s);
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function files($s = '', $new = '', $trim = false){

    return ___global('_FILES', $s, $new, $trim);

}

/**
 * @param string $s
 */
function unfiles($s = ''){

    ___unglobal('_FILES', $s);
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function put($s = '', $new = '', $trim = false){

    return ___global('_PUT', $s, $new, $trim);

}

/**
 * @param string $s
 */
function unput($s = ''){

    ___unglobal('_PUT', $s);
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function request($s = '', $new = '', $trim = false){

    return ___global('_REQUEST', $s, $new, $trim);

}

/**
 * @param string $s
 */
function unrequest($s = ''){

    ___unglobal('_REQUEST', $s);
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function session($s = '', $new = '', $trim = false){

    return ___global('_SESSION', $s, $new, $trim);

}

/**
 * @param $name
 * @return bool
 */
function session_set($name){

    return isset($_SESSION[$name]);
}

/**
 * @param string $s
 */
function unsession($s = ''){

    ___unglobal('_SESSION', $s);
}

/**
 * @param string $s
 * @param string $new
 * @param bool $trim
 * @return string
 */
function server($s = '', $new = '', $trim = false){

    return ___global('_SERVER', strtoupper($s), $new, $trim);

}

/**
 * @param string $s
 */
function unserver($s = ''){

    ___unglobal('_SERVER', $s);
}

/**
 * @return string
 */
function user_agent(){

    return server('user_agent');
}

/**
 *
 * Create or return session keys for uses such as CSRF etc.
 *
 * @param string $s
 * @return string
 */
function get_session_key($s = ''){

	if(!empty(session($s)))
		session($s, apply_filters('session_key', randomize(32)));

	return session($s);
}

/**
 *
 * Delete session keys.
 *
 * @param string $s
 */
function delete_session_key($s = ''){

	unsession($s);
}

/**
 *
 * Adds a flash session concept.
 *
 * @param $name
 * @param string $expire
 */
function add_flash_session($name, $expire = '1m'){
    global $__flashsessions__;

    $__flashsessions__[$name] = real_time($expire);
}

/**
 *
 * Flashes a session.
 *
 * @param $name
 * @param string $value
 * @return string
 */
function flash_session($name, $value = ''){
    global $__flashsessions__;
    if(!empty($value)){
        if(isset($__flashsessions__[$name]) && !session_set('_CANDY_FLASH_SESSION_' . $name)){
            session('_CANDY_FLASH_SESSION_' . $name, ['value' => $value, 'expire' => $__flashsessions__[$name]]);
        }
    } else return  session('_CANDY_FLASH_SESSION_' . $name);
}

/**
 *
 * Deletes a flash a session.
 *
 * @param $name
 * @return bool
 */
function delete_flash_session($name){
    return  unsession('_CANDY_FLASH_SESSION_' . $name);
}

/**
 *
 * Gets the value of a flashed session.
 *
 * @param $name
 * @return mixed
 */
function flash_session_value($name){
    return !empty(flash_session($name)) ? flash_session($name)['value'] : false;
}



