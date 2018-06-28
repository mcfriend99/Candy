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

if(!defined('CANDY')){
	header('Location: /');
}

/*
 * class Cookie
 * Handles all thing related to setting, getting and deleting cookies.
 */

/**
 * Configuration for: Cookies
 * Please note: The COOKIE_DOMAIN needs the domain where your app is,
 * in a format like this: .mydomain.com
 * Note the . in front of the domain. No www, no http, no slash here!
 * For local development .127.0.0.1 or .localhost is fine, but when deploying you should
 * change this to your real domain, like '.mydomain.com' ! The leading dot makes the cookie available for
 * sub-domains too.
 * @see http://stackoverflow.com/q/9618217/1114320
 * @see http://www.php.net/manual/en/function.setcookie.php
 *
 * cookie_runtime: How long should a cookie be valid ? 1209600 seconds = 2 weeks
 * cookie_domain: The domain where the cookie is valid for, like '.mydomain.com'
 * cookie_secret_key: Put a random value here to make your app more secure. When changed, all cookies are reset.
 *
 * DO NOT EDIT COOKIE_RUNTIME, COOKIE_DOMAIN OR COOKIE_SECRET_KEY DIRECTLY. EDIT VALUES IN configs/main.candy.
 *
 */
define("COOKIE_RUNTIME", get_config('cookie_life', 'main'));
define("COOKIE_DOMAIN", get_config('cookie_domain', 'main'));
define("COOKIE_SECRET_KEY", get_config('cookie_secret', 'main'));


class Cookie {


    /**
     * @var A random token to enhance cookie security.
     */
    public $random_token_string = null;
    /**
     * @var Hashed form of the original cookie.
     */
    public $cookie_string_hash = null;


    /**
     *
     * Checks if cookie exists.
     *
     * @param $name     Name of the cookie.
     * @return bool     True if cookie exists or False if not.
     */
    function exists($name){

		return isset($_COOKIE[$name]);

	}

    /**
     *
     * Sets a cookie at the specified location.
     *
     * @param $name                     The name of the cookie.
     * @param $value                    The value of the cookie.
     * @param string $location          The location at which the cookie operates within the domain.
     * @return string|bool              The cookie string or False if cookie cannot be set within the current domain.
     */
    function setCookie($name, $value, $location = "/"){

		$this->random_token_string = hash('sha256', mt_rand());
		$cookie_string_first_part = $this->random_token_string . ':' . $value;
        $this->cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
        $cookie_string = $cookie_string_first_part . ':' . $this->cookie_string_hash;

		if(setcookie($name, $cookie_string, time() + COOKIE_RUNTIME, $location)){
			return $this->random_token_string . ':' . $this->cookie_string_hash;
		} else return false;

	}

    /**
     *
     * Deletes a cookie from a location within a domain.
     *
     * @param $name                     The name of the cookie to delete.
     * @param string $location          The location from which to delete cookie.
     * @return bool                     True if deleted or False otherwise.
     */
    function deleteCookie($name, $location = '/'){

		return setcookie($name, false, time() - (3600 * 3650), $location);

	}


    /**
     *
     * Converts cookie to JSON string.
     *
     * @param string $name      Name of the cookie. Leave empty to return all cookies within the domain.
     * @return string           JSON string representing the cookie.
     */
    function toJSON($name = ''){
        
        if(empty($name))
		  $pre = $_COOKIE;
        else
            $pre = [$name => $this->getCookieValue($name)];
        
		return @json_encode($pre);

	}


    /**
     *
     * Gets the value of a cookie.
     *
     * @param $name             The name of the cookie.
     * @return string|bool      The value of the cookie or false if cookie cannot be found or its value cannot be determined.
     */
    function getCookieValue($name){


		list ($token, $cookie_id, $hash) = explode(':', $_COOKIE[$name]);

		if ($hash == hash('sha256', $token . ':' .$cookie_id . COOKIE_SECRET_KEY) && !empty($token)) {

			$this->random_token_string = $token;
			$this->cookie_string_hash = $hash;
			return $cookie_id;
		} else {
			return false;
		}

	}


}






