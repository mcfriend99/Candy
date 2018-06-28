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
 * A functional accessor to the Cookie class.
 *
 */


/**
 *
 * Checks if a cookie exist.
 *
 * @param $name     The name of the cookie.
 * @return bool     True if cookie is set, false otherwise.
 */
function cookie_exists($name){
    global $cook;
    return $cook->exists($name);
}

/**
 *
 * Sets a cookie.
 *
 * @param $name                 The name of the cookie
 * @param $value                The value to set in the cookie.
 * @param string $location      The location of the cookie on the server. Default '/'
 * @return bool|string          The value of the cookie or false if cokkie cannot be set.
 */
function cookie_set($name, $value, $location = "/"){
    global $cook;
    return $cook->setCookie($name, $value, $location);
}

/**
 *
 * Deletes an instance of a cookie.
 *
 * @param $name                 The name of the cookie to delete.
 * @param string $location      The path from which to delete the cookie.
 * @return bool                 True if cookie is deleted, Otherwise, False.
 */
function cookie_delete($name, $location = "/"){
    global $cook;
    return $cook->deleteCookie($name, $location);
}

/**
 *
 * Gets the value of a cookie.
 *
 * @param $name             The name of the cookie.
 * @return bool|string      The cookie string or bool if it does not exists.
 */
function cookie_value($name){
    global $cook;
    return $cook->getCookieValue($name);
}

/**
 *
 * Converts cookie to an object.
 *
 * @param string $name          The name of the cookie.
 * @return string               An object of the cookie (Not the Cookie class).
 */
function cookie_json($name = ''){
    global $cook;
    return $cook->toJSON($name);
}



