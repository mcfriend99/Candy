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

if (!defined('CANDY')) {
    header('Location: /');
}


/**
 * @see class Form.
 */


/**
 *
 * Adds a form concept.
 *
 * @param $name
 * @param string $method
 * @param string $action
 * @param bool $has_files
 * @param array $options
 * @param array $attr
 */
function add_form($name, $method = 'post', $action = '', $has_files = true, $attr = [], $options = [])
{
    $GLOBALS['__forms__'][$name] = new Form($name, $method, $action, $has_files, $attr, $options);
}

function copy_form($source, $destination)
{
    if (isset($GLOBALS['__forms__'][$source]))
        $GLOBALS['__forms__'][$destination] = clone $GLOBALS['__forms__'][$source];
    else __form_error__($source);
}

function add_form_control($form_name, $name, $type = 'text', $attr = [], $options = [], $default = '')
{
    if (isset($GLOBALS['__forms__'][$form_name]))
        $GLOBALS['__forms__'][$form_name]->addControl($name, $type, $attr, $default, $options);
    else __form_error__($form_name);
}

function add_form_controls($form_name, $controls = [])
{
    if (isset($GLOBALS['__forms__'][$form_name]))
        $GLOBALS['__forms__'][$form_name]->addControls($controls);
    else __form_error__($form_name);
}

function remove_form_control($form_name, $names)
{
    if (isset($GLOBALS['__forms__'][$form_name])) {
        if (!is_array($names) && !is_object($names)) {
            $GLOBALS['__forms__'][$form_name]->removeControl($names);
        } else {
            $names = to_array($names);
            foreach ($names as $name) {
                $GLOBALS['__forms__'][$form_name]->removeControl($name);
            }
        }
    } else __form_error__($form_name);
}

function add_required_form_controls($form_name, $controls = '')
{
    if (isset($GLOBALS['__forms__'][$form_name]))
        $GLOBALS['__forms__'][$form_name]->addRequiredControls($controls);
    else __form_error__($form_name);
}

function create_form_control($name, $type = 'text', $attr = [], $default = '', $options = [])
{
    $form = new Form($name);
    return $form::createControl($name, $type, $attr, $default, $options);
}


/**
 *
 * Draws a form concept.
 *
 * @param $name
 * @param array $options
 * @param bool $return
 * @return mixed
 */
function draw_form($name, $return = false)
{
    if (isset($GLOBALS['__forms__'][$name]))
        return $GLOBALS['__forms__'][$name]->draw($return);
    else __form_error__($name);
}

function verify_csrf($name)
{
    if (isset($GLOBALS['__forms__'][$name]))
        $GLOBALS['__forms__'][$name]->verifyCsrf();
    else __form_error__($name);
}

function validate_form($name)
{
    if (isset($GLOBALS['__forms__'][$name]))
        return $GLOBALS['__forms__'][$name]->validate();
    else __form_error__($name);
}

function form_csrf($name)
{
    if (isset($GLOBALS['__forms__'][$name]))
        return '<input type="hidden" name="' . $GLOBALS['__forms__'][$name]->csrf_name . '" value="' . $GLOBALS['__forms__'][$name]->csrf_value . '" />';
    else __form_error__($name);
}

function form_error($name)
{
    if (isset($GLOBALS['__forms__'][$name]))
        return $GLOBALS['__forms__'][$name]->validation_error;
    return null;
}

function form_submit($name, $function = null)
{
    if (isset($GLOBALS['__forms__'][$name]))
        return $GLOBALS['__forms__'][$name]->doSubmit($function);
    else __form_error__($name);
}

function form_to_db($name, $table = '', $unique_column = '', $exceptions = [], $should_update = true)
{
    if (isset($GLOBALS['__forms__'][$name])) {
        return $GLOBALS['__forms__'][$name]->toDb($table, $unique_column, $exceptions, $should_update);
    } else __form_error__($name);
}

function __form_error__($name)
{
    model_error('Form &quot;' . $name . '&quot;');
}
