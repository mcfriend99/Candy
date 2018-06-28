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
 * @package	    Candy-PHP
 * @author		Ore Richard Muyiwa
 * @copyright      2017 Ore Richard Muyiwa
 * @license	    https://opensource.org/licenses/MIT	MIT License
 * @link	    https://candy-php.com/
 * @since	    Version 1.0.0
 */



/**
 *
 * Gets the dial code of a visitors's country.
 *
 * @param string $country
 * @return int
 */
function get_dial_code(){

    return is_object(visitor_ip_details()) ? dial_code(visitor_ip_details()->country) : 234 /* Default to Nigeria. */;
}

/**
 *
 * Formats a phone number for aesthetics.
 *
 * @param $phone
 * @param string $country
 * @return string
 */
function format_phone($phone, $country = ''){

    if(empty($country))
        $country = session('country');

    $country = strtolower($country);
    $dial_code = get_dial_code();

    if($phone[0] == '+')
        $phone = substr($phone, 1);
    elseif(preg_match('~^009~', $phone))
        $phone = substr($phone, 3);

    $f = preg_split('/\s+/',
        preg_replace('/([0-9])/', '$1 ',
            ($phone[0] != '0' ? substr($phone, strlen($dial_code)) : substr($phone, 1))));

    // TODO: Add formatting for specific countries.

    switch($country){
        default:
            $f[5] .= ' ';
            $f[2] .= ' ';
            $g = null;
            foreach($f as $r){
                $g .= $r;
            }
            return '0' . $g;
            break;
    }
}

/**
 *
 * creates a concealed string from a phone number.
 * E.g. Turns 08008008666 to 0800 xxx xxx6.
 *
 * @param $phone
 * @param string $country
 * @return null|string
 */
function conceal_phone($phone, $country = ''){
    $country = strtolower($country);

    if(empty($country))
        $country = get_config('country', 'main');

    // TODO: Add formatting for specific countries.

    switch($country){
        default:
            $f = preg_split('/\s+/', format_phone($phone, $country));
            $f[1] = ' xxx ';
            $f[2] = 'xxx' . substr(trim($f[2]), 3);
            $g = null;
            foreach($f as $r){
                $g .= $r;
            }
            return $g;
            break;
    }
}



