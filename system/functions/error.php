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
 * Fires a Candy error.
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
$candy_error = function($errno, $errstr='', $errfile='', $errline='')
{
    // if error has been supressed with an @
    if (error_reporting() == 0) {
        return;
    } else if(strtolower(get_config('show_error', 'main')) == 'no'){
        return;
    }

    $cfg = [];

    // check if function has been called by an exception
    if(func_num_args() == 5) {
        // called by trigger_error()
        $exception = null;
        list($errno, $errstr, $errfile, $errline) = func_get_args();

        $backtrace = array_reverse(debug_backtrace());

    }else {
        // caught exception
        $exc = func_get_arg(0);
        $errno = $exc->getCode();
        $errstr = $exc->getMessage();
        $errfile = $exc->getFile();
        $errline = $exc->getLine();

        $backtrace = $exc->getTrace();
    }

    $errorType = array (
               E_ERROR            => 'ERROR',
               E_WARNING        => 'WARNING',
               E_PARSE          => 'PARSING ERROR',
               E_NOTICE         => 'NOTICE',
               E_CORE_ERROR     => 'CORE ERROR',
               E_CORE_WARNING   => 'CORE WARNING',
               E_COMPILE_ERROR  => 'COMPILE ERROR',
               E_COMPILE_WARNING => 'COMPILE WARNING',
               E_USER_ERROR     => 'USER ERROR',
               E_USER_WARNING   => 'USER WARNING',
               E_USER_NOTICE    => 'USER NOTICE',
               E_STRICT         => 'STRICT NOTICE',
               E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'
               );

    // create error message
    if (array_key_exists($errno, $errorType)) {
        $err = $errorType[$errno];
    } else {
        $err = 'ERROR';
    }

    $errMsg = "$err: $errstr in $errfile on line $errline\n";

	$trace = '';
    // start backtrace
	$i = 0;
    foreach ($backtrace as $v) {
		
        if (isset($v['class'])) {

		$trace .= "\n <strong>#{$i}</strong> Called Class: {$v['class']}::{$v['function']}(";

            if (isset($v['args'])) {
                $separator = '';

                foreach($v['args'] as $arg ) {
                    $trace .= "$separator".__get_error_arg($arg);
                    $separator = ', ';
                }
            }
			$trace .= ")" .(isset($v['file']) ? "\n   File: {$v['file']}" : ''). (isset($v['line']) ? "\n   Line: {$v['line']}" : '');
        }
        elseif (isset($v['function'])) {
            $trace .= "\n<strong>#{$i}</strong> Called Function: {$v['function']}(";
            if (!empty($v['args'])) {

                $separator = '';

                foreach($v['args'] as $arg ) {
                    $trace .= "$separator".__get_error_arg($arg);
                    $separator = ', ';
                }
            }
            $trace .= ")" .(isset($v['file']) ? "\n   File: {$v['file']}" : ''). (isset($v['line']) ? "\n   Line: {$v['line']}" : '');
        }
		$i++;
    }

    // display error msg, if debug is enabled
    $result = "$errMsg\n\n<strong style='font-size:1.25em'>Trace:</strong>\n{$trace}\n";

    // what to do
    switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
            return;
            break;

        default:
            die(CANDY_ERROR_PREPEND_STRING . $result . CANDY_ERROR_APPEND_STRING);
            break;

    }

};

function __get_error_arg($arg)
{
    switch (strtolower(gettype($arg))) {

        case 'string':
            return( '"'.preg_replace( '/\\n/', '', $arg ).'"' );

        case 'boolean':
            return (bool)$arg;

        case 'object':
            return "{".get_class($arg)."}";

        case 'array':
            $ret = "\n   [\n     ";
            $separtor = "";

            foreach ($arg as $k => $v) {
                //$ret .= $separtor.__get_error_arg($k).' => '.__get_error_arg($v);
				$g = @var_export($v, true);
				$ret .= "{$separtor}\${$k} => ".preg_replace('/\n+/', "\n     ", strip_tags($g));
                $separtor = ",\n     ";
            }
            $ret .= "\n   ]";

            return $ret;

        case 'resource':
            return 'resource('.get_resource_type($arg).')';

        default:
            return var_export($arg, true);
    }
}


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



