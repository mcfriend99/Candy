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
 * @see class Hook.
 */


function __hook(){
		
    static $hooks;

    if(!isset($hooks)){

        $hooks = new Hook();
    }

    return $hooks;
}

/**
Functions Implementation
*/

function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1){
    return __hook()->add_filter($tag, $function_to_add, $priority, $accepted_args);
}

function remove_filter($tag, $function_to_remove, $priority = 10){
    return __hook()->remove_filter($tag, $function_to_remove, $priority);
}

function remove_all_filters($tag, $priority = false){
    return __hook()->remove_all_filters($tag, $priority);
}

function has_filter($tag, $function_to_check = false){
    return __hook()->has_filter($tag, $function_to_check);
}

function apply_filters($tag, $value){
    return __hook()->apply_filters($tag, $value);
}

function apply_filters_ref_array($tag, $args){
    return __hook()->apply_filters_ref_array($tag, $args);
}

function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1){
    return __hook()->add_action($tag, $function_to_add, $priority, $accepted_args);
}

function remove_action($tag, $function_to_remove, $priority = 10){
    return __hook()->remove_action($tag, $function_to_remove, $priority);
}

function remove_all_actions($tag, $priority = false){
    return __hook()->remove_all_actions($tag, $priority);
}

function has_action($tag, $function_to_check = false){
    return __hook()->has_action($tag, $function_to_check);
}

function do_action($tag, $arg = ''){
    __hook()->do_action($tag, $arg);
}

function do_action_ref_array($tag, $args){
    __hook()->do_action_ref_array($tag, $args);
}

function did_action($tag){
    return __hook()->did_action($tag);
}

function current_filter(){
    return __hook()->current_filter();
}

function current_action(){
    return __hook()->current_action();
}

function doing_filter(){
    return __hook()->doing_filter();
}

function doing_action(){
    return __hook()->doing_action();
}




// INIT ---
do_action('After_Hook_Setup', __hook());


