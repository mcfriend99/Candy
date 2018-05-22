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
 * Fires a Candy error.
 * @param $s
 */
$candy_error = function ($s){

    if(strtolower(get_config('show_error', 'main')) == 'no'){
        return '';
    }

    $gt = debug_backtrace();

    function gsf($ft){
        $ur = '';
        $ur .= ("<br /><br />Backtrace (most recent calls):<br /><br />\n");
        if(is_object($ft)){
            return print_r($ft, true);
        }
        foreach($ft as &$bt)
        {
            if(!isset($bt["file"]))
                $ur .= ("[PHP core called function]<br />");
            else
                $ur .= ("File: ".$bt["file"]."<br />");

            if(isset($bt["line"]))
                $ur .= ("&nbsp;&nbsp;&nbsp;&nbsp;line: ".$bt["line"]."<br />");
            $ur .= ("&nbsp;&nbsp;&nbsp;&nbsp;function called: ".print_r(isset($bt["function"]) ? $bt["function"] : '', true));

            if(isset($bt["args"]))
            {
                $ur .= ("<br />&nbsp;&nbsp;&nbsp;&nbsp;args: ");
                for($j = 0; $j <= count($bt["args"]) - 1; $j++)
                {
                    if(is_array($bt["args"][$j]) || is_object($bt["args"][$j]))
                    {
                        $ur .= gsf($bt["args"][$j]) . '<br/>';
                    }
                    else {
                        $ur .= print_r($bt["args"][$j], true) . '<br/>';
                    }

                    if($j != count($bt["args"]) - 1)
                        $ur .= ", ";
                }
            }
            $ur .= ("<br /><br />");

            return $ur;
        }
    };

    $uq = gsf($gt);


    die(CANDY_ERROR_PREPEND_STRING . $s . $uq . CANDY_ERROR_APPEND_STRING);
};

/**
 * Fires an error when it exists in a model (creation, existence, mode and/or details).
 * @param $s
 * @param string $append
 */
function model_error($s, $append = ''){

    throw new Exception('MODEL_ERROR: ' . $s . ' model is not found or is disabled' . (!empty($append) ? ' ' . $append : '') . '.');
}

/**
 * Fires an error when a model is bad (does not follow the rule of declaration).
 * @param $s
 * @param string $append
 */
function bad_model_error($s, $append = ''){

    throw new Exception('MODEL_ERROR: ' . $s . ' model is not quite okay' . (!empty($append) ? ' ' . $append : '') . '.');
}

/**
 * Fires an error when a model is implemented wrongly.
 * @param $s
 * @param string $append
 */
function implementation_error($s, $append = ''){

    throw new Exception('IMPLEMENTATION_ERROR: ' . $s . ' is not implemented' . (!empty($append) ? ' ' . $append : '') . '.');
}



