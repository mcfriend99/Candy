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
 * Adds an html template concept.
 *
 * @param $name             Name of the concept.
 * @param string $html      String of the template.
 * @param bool $return      Whether to return string or print to screen.
 */
function add_html($name, $html = '%s', $return = false){
    global $__htmls__;
    $__htmls__[$name][0] = $html;
    $__htmls__[$name][1] = $return;
}

/**
 *
 * Shows an html template.
 *
 * @args[0]                         Name of the concept.
 * @args[1] .... @args[n]           The replacements for %s.
 *
 */
function show_html(){
    global $__htmls__;

    $args = func_get_args();

    if(count($args) < 1){

        return;
    }

    $name = $args[0];

    if(!isset($__htmls__[$name]))
        model_error('Html &quot;' . $name . '&quot;');

    preg_match_all('/\%s/', $__htmls__[$name][0], $matches);

    if(count($matches[0]) != count($args) - 1)
        implementation_error('Html &quot;' . $name . '&quot;', 'correctly');

    //$args = array_delete($args, $args[0]);
    $args[0] = $__htmls__[$name][0];

	$return = $__htmls__[$name][1];

	if(!$return)
    	echo call('sprintf', $args);
	else return call('sprintf', $args);
}

/**
 *
 * Shorthand for show_html().
 *
 */
function html(){

    call('show_html', func_get_args());
}



