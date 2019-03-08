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


/**
 *
 * Maps a URL route to a file in #site_dir.
 *
 * @param $route        Route (Regex or plain string)
 * @param $www          #site_dir file without the .php or .html extension.
 */
function add_route($route, $www = null){
    global $__routes__; 
    if(empty($www)) $www = $route;
    $__routes__ = array_merge($__routes__, [$route => $www]);
}

/**
 *
 * Gets the route for a specific URL.
 *
 * @param string $s
 * @return string
 */
function get_route($s = ''){
    
    global $__routes__;
    
    $return = get_config('404_page', 'main');
    
    if(empty($s))
        $s = ':index';
    
    foreach($__routes__  as $route => $page){
        
        if($route == $s){
            
            return _set_route($page);

        }
    }
    
    foreach($__routes__  as $route => $page){
        
        if(preg_match('~^' . str_replace(':any', '([^\n]*)', $route) . '$~', $s)){
            
            return _set_route($page);

        }
    }
    
    return _set_route($return);
}

/**
 *
 * Gets the proper actual file target of a route.
 *
 * @param $return
 * @return string
 */
function _set_route($return){
    
    if(file_exists(SITE_DIR . '/' . $return . '.php')){
    
        $file = SITE_DIR . '/' . $return . '.php';
    } else if(file_exists(SITE_DIR . '/' . $return . '.html')){
    
        $file = SITE_DIR . '/' . $return . '.html';
    } else if(file_exists(SITE_DIR . '/' . $return)){

        $file = SITE_DIR . '/' . $return;
    } else {

        $file = get_config('404_page', 'main');
    }
    
    return $file;
}


