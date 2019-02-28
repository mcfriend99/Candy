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




define('VALIDATION_SUCCESS', 1);


/**
 * Class Form
 */
class Form {

    /**
     * @var array of controls.
     */
    var $controls = [];
    /**
     * @var string
     */
    var $form_open;
    /**
     * @var array of required fields.
     */
    var $requireds = [];
    /**
     * @var Name of CSRF field.
     */
    public $csrf_name;
    /**
     * @var Value of CSRF field.
     */
    public $csrf_value;
    /**
     * @var Name of Submit Field.
     */
    var $submit_name;
    /**
     * @var Indicates whether to add a CSRF field to a form or not.
     */
    var $no_csrf;
    /**
     * @var Indicates whether to add a submit field to a form if it contains no submit control or not.
     * Default: False
     */
    var $no_submit;
    /**
     * @var Name of the form.
     * Default: False
     */
    var $name;
    /**
     * @var string Method of the form POST, GET or PUT
     */
    var $method;
    /**
     * @var string The location the form submits to.
     * Default: POST
     */
    var $action;
    /**
     * @var bool Indicates whether a form has a file input control or not.
     */
    var $has_files;
    /**
     * @var array of options in the form.
     */
    var $options;
    /**
     * @var Indicates whether to validate a form upon submit or not.
     */
    var $no_validate;
    /**
     * @var Error generated while validating if any.
     */
    public $validation_error;

    /**
     * Form constructor.
     * @param $name
     * @param string $method
     * @param string $action
     * @param bool $has_files
     * @param array $attr           Attributes
     */
    function __construct($name, $method = 'post', $action = '', $has_files = true, $attr = [], $options = []){

        if(!isset($options['validate']))
            $options['validate'] = true;
        if(!isset($options['no_submit']))
            $options['no_submit'] = false;
        if(!isset($options['no_csrf']))
            $options['no_csrf'] = false;

        if(empty($action)){


        }

        $this->name = $name;
        $this->method = apply_filters('form_method', $method, $name);
        $this->action = apply_filters('form_action', $action, $name);
        $this->has_files = $has_files;
        $this->options = apply_filters('form_options', $options, $name);
        $this->no_validation = !$options['validate'];




		$this->fixMissingNames();

		if(!$options['no_csrf']){
			if(!session_set('CSRF_' . $this->csrf_name)){
				$this->csrf_value = randomize(32);
				session('CSRF_' . $this->csrf_name, $this->csrf_value);
			} else {
				$this->csrf_value = session('CSRF_' . $this->csrf_name);
			}
		} else $this->no_csrf = true;

        $inline = '';
        foreach($attr as $key => $value){

            $inline .= ' ' . $key . '="' . $value . '"';
        }

        $this->form_open = "<form name='{$name}' method='{$method}' action='{$action}'" .($has_files ? ' enctype="multipart/form-data"' : ''). "{$inline}>";
    }

    /**
     *
     * Adds an array of controls to the form.
     *
     * @param array $controls
     */
    function addControls($controls = []){

		foreach($controls as $control){
			
			foreach($control as $key => $value){

				$name = $key;
				$type = $value['type'];
				$default = $value['default'];
				$options = $value['options'];

				$this->addControl($name, $type, $default, $options);
			}
		}
    }

    /**
     *
     * Adds a single control to the form.
     *
     * @param $name
     * @param string $type
     * @param array $attr           Attributes.
     * @param string $default
     * @param array $options
     */
    function addControl($name, $type = 'text', $attr = [], $default = '', $options = []){

        if(!isset($options['text']))
            $options['text'] = ucwords($name);
        else {
            $vr = get_text($options['text']);
            if(!empty($vr))
                $options['text'] = $vr;
        }
        if(!isset($options['rules']))
            $options['rules'] = '';

        if(!isset($options['wrap']))
            $options['wrap'] = '%s';

        $this->controls[$name] = [
            'type'      => $type,
            'default'   => $default,
            'attr'      => $attr,
            'options'   => $options
        ];
    }

    /**
     *
     * Create a control that can be reused anywhere outside the form context.
     * Just in case someone needs it. Oh... Yes. We actually do.
     *
     * @param $name
     * @param string $type
     * @param array $attr
     * @param string $default
     * @param array $options
     * @return array
     */
    static function createControl($name, $type = 'text', $attr = [], $default = '', $options = []){
        return [
            $name => [
                'type'      => $type,
                'default'   => $default,
                'attr'      => $attr,
                'options'   => $options
            ]
        ];
    }

    /**
     *
     * Allows you to specify required controls in the form context and not in individual controls options or attribute.
     *
     * @param string $controls          Array list or a comma delimited string list of control names.
     */
    function addRequiredControls($controls = ''){

        if(is_string($controls)){

            $controls = explode(',', $controls);
        }

        foreach($controls as $control){

            array_push($this->requireds, trim($control));
        }
    }

    /**
     *
     * Removes a control from the form.
     *
     * @param $name
     */
    function removeControl($name){

        if(isset($this->controls[$name]))
            unset($this->controls[$name]);
    }

    private function fixMissingNames(){


        if(!isset($this->options['csrf_name']) || empty($this->options['csrf_name']))
            $this->csrf_name = $this->name . '_CANDY_CSRF_';
        else $this->csrf_name = empty($this->options['csrf_name']) ? $this->name . '_CANDY_CSRF_' : $this->options['csrf_name'];

        if(!isset($this->options['submit_name']) || empty($this->options['submit_name']))
            $this->submit_name = $this->name . '_CANDY_SUBMIT_';
        else $this->submit_name = empty($this->options['submit_name']) ? $this->name . '_CANDY_SUBMIT_' : $this->options['submit_name'];
    }

    /**
     *
     * Draws a form on screen.
     *
     * @param array $options
     * @param bool $return          This parameter allows us to return HTML instead of actually drawing the form to screen.
     * @return string               HTML returned when $return is set to true.
     */
    function draw($return = false){

        do_action('before_form_draw', $return);

        $options = $this->options;

        $result = $this->form_open;


        $wrap = isset($options['wrap']) ? $options['wrap'] : '<div class="candy-form">%s</div>';

        $has_submit = false;
        $submit_added = false;
        $csrf_added = false;

        foreach($this->controls as $control){
            if($control['type'] == 'submit' || $control['type'] == 'button')
                $has_submit = true;
        }

        if(!isset($options['no_submit']) || !$options['no_submit']){

            if(!$has_submit){

                $this->addControl($this->submit_name, 'submit', null, 'Submit');
                $submit_added = true;
            }

            $this->no_submit = false;
        } else $this->no_submit = isset($options['no_submit']) && $options['no_submit'] ? true : (!isset($options['no_submit']) ? true : false);

        if(!isset($options['no_csrf']) || !$options['no_csrf']){

            if(!session_set('CSRF_' . $this->csrf_name))
                session('CSRF_' . $this->csrf_name, $this->csrf_value);

            $this->addControl($this->csrf_name, 'hidden', ['required' => true], session('CSRF_' . $this->csrf_name), ['no_div' => true]);
            $csrf_added = true;

            $this->no_csrf = false;
        } else $this->no_csrf = isset($options['no_csrf']) && $options['no_csrf'] ? true : (!isset($options['no_csrf']) ? true : false);

        foreach($this->controls as $name => $value){

            if($this->controls[$name]['type'] != 'password'){

                //if( !empty($this->getControlValue($this->submit_name)) || !empty($this->getControlValue($this->csrf_name)) ){
                if( !empty($this->getControlValue($name)) ){

                    $this->controls[$name]['default'] = $this->getControlValue($name);
                }
            }

            $result .= $this->drawControl($name, $options, $this->controls[$name]['options']);
        }

        if($csrf_added)
            $this->removeControl($this->csrf_name);
        if($submit_added)
            $this->removeControl($this->submit_name);

        $result .= '</form>';

        $result = apply_filters('form_draw_html', sprintf($wrap, $result));

        do_action('after_form_draw', $result);

        if(!$return){
            echo $result;
        } else {
            return $result;
        }            
    }

    /**
     *
     * Verify that our CSRF token is valid.
     *
     * @return bool
     */
    function verifyCsrf(){

        if($this->no_csrf)
            return apply_filters('csrf_validation', true, $this->name);

        return apply_filters('csrf_validation', $this->csrf_value == $this->getControlValue($this->csrf_name), $this->name);
    }

    /**
     *
     * Draws a single control to HTML string.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    private function drawControl($name, $options = [], $control_options = []){

        if(!isset($this->controls[$name]))
            bad_implementation_error('Form control &quot;' . $name . '&quot;');

        $control = $this->_parseControl($name);

        do_action('before_draw_form_control', $this->name, $control);

		$text = '';

		$text .= isset($control_options['before']) ? $control_options['before'] : (isset($options['before']) ? $options['before'] : '');

        $text .= isset($control_options['hint']) ? '<span>' . $control_options['hint'] . '</span>' : '';

		$text .= $control;

        $text .= isset($control_options['after']) ? $control_options['after'] : (isset($options['after']) ? $options['after'] : '');

        do_action('after_draw_form_control', $this->name, $control);

        return apply_filters('draw_form_control', sprintf($control_options['wrap'], $text), $this->name, $control);
    }

    /**
     *
     * Returns a control with the specified name within the form.
     *
     * @param $name
     * @return mixed
     */
    function getControl($name){

        return $this->controls[$name];
    }

    /**
     *
     * Gets the text displayed on a control.
     *
     * @param $name
     * @return mixed
     */
    private function getControlText($name){

        return apply_filters('form_control_text', isset($this->getControl($name)['options']['nicename'])
        ? $this->getControl($name)['options']['nicename']
        : $this->getControl($name)['options']['text'], $this->name, $name);
    }

    /**
     * 
     * Gets the value of data in the control.
     *
     * @param $name
     * @return mixed
     */
    private function getControlValue($name){
        $val = call($this->method, $name);
        if(empty($val))
            $val = $this->getControl($name)['default'];
        return apply_filters('form_control_value', $val, $this->name, $name);
    }

    /**
     *
     * Passes a control into its relative class implementation.
     *
     * @param $name
     * @return string
     */
    private function _parseControl($name){

        $control = $this->controls[$name];
        $type = $control['type'];
        $default = $control['default'];
        $attrs = $control['attr'];
        $options = $control['options'];

        $choices = isset($options['choices']) ? $options['choices'] : [];
        $rows = isset($options['rows']) ? $options['rows'] : 0;
        $cols = isset($options['cols']) ? $options['cols'] : 0;
        $min = isset($options['min']) ? $options['min'] : 0;
        $max = isset($options['max']) ? $options['max'] : 0;
        $text = isset($options['text']) ? $options['text'] : '';

        switch($type){

            case 'hidden':
                return $this->Hidden($name, $default, $attrs);
            case 'submit':
                return $this->Submit($name, $default, $attrs);
            case 'button':
                return $this->Button($name, $default, $attrs);
            case 'reset':
                return $this->Reset($name, $default, $attrs);
            case 'text':
                return $this->Text($name, $default, $attrs);
            case 'date':
                return $this->Date($name, $default, $attrs);
            case 'time':
                return $this->Time($name, $default, $attrs);
            case 'month':
                return $this->Month($name, $default, $attrs);
            case 'email':
                return $this->Email($name, $default, $attrs);
            case 'password':
                return $this->Password($name, $default, $attrs);
            case 'search':
                return $this->Search($name, $default, $attrs);
            case 'number':
                return $this->Number($name, $default, $attrs);
            case 'phone':
                return $this->Tel($name, $default, $attrs);
            case 'url':
                return $this->Url($name, $default, $attrs);
            case 'range':
                return $this->Range($name, $default, $attrs, $min, $max);
            case 'textarea':
                return $this->Textarea($name, $default, $attrs, $rows, $cols);
            case 'select':
                return $this->Select($name, $choices, $default, $attrs);
            case 'radio':
                return $this->Radio($name, $choices, $default, $attrs);
            case 'check':
                return $this->Check($name, $text, $default, $attrs);
            case 'file':
                return $this->File($name, $attrs);
        }
    }

    /**
     *
     * Parses the default value of a control.
     * We use this to make sure setting default values do not result in PHP errors.
     *
     * @param $default
     * @param bool $parse_non_string
     * @return mixed|string
     */
    private function parseDefault($default, $parse_non_string = true){

        if(is_array($default)){
			if($parse_non_string)
            	$default = serialize($default);
        } elseif(is_object($default)){
			if($parse_non_string)
            	$default = @json_encode($default);
        } elseif(is_resource($default))
            $default = to_string($default);

        return apply_filters('parse_form_control_default', $default, $this->name);
    }

    /**
     *
     * Gets the attributes associated with a control into HTML tag attribute description.
     *
     * @param $name
     * @param array $attr
     * @return string
     */
    private function getAttrs($name, $attr = []){

        $inline = '';
        if(!empty($attr)){
            foreach($attr as $key => $value){

                $inline .= ' ' . $key . '="' . $value . '"';
            }
        }

        if(in_array($name, $this->requireds)){
            $inline .= ' required';
        }

        return apply_filters('get_form_control_attrs', $inline, $this->name, $name);
    }

    /**
     *
     * Base Textbox and related controls template.
     *
     * @param $name
     * @param string $type
     * @param string $default
     * @param array $attr
     * @return string
     */
    private function _Text($name, $type = 'text', $default = '', $attr = []){

        $default = $this->parseDefault($default);

        $inline = $this->getAttrs($name, $attr);

        return "<input name='{$name}' type='{$type}' value='{$default}'{$inline} />";
    }

    /**
     *
     * A file control.
     *
     * @param $name
     * @param array $attr
     * @return string
     */
    protected function File($name, $attr = []){

        $inline = $this->getAttrs($name, $attr);

		if(isset($attr['multiple']))
			$name .= '[]';

        return "<input name='{$name}' type='file'{$inline} />";
    }

    /**
     *
     * A submit control.
     *
     * @param $name
     * @param string $text
     * @param array $attr
     * @return string
     */
    protected function Submit($name, $text = 'Submit', $attr = []){
        return $this->_Text($name, 'submit', $text, $attr);
    }

    /**
     *
     * A button control.
     *
     * @param $name
     * @param string $text
     * @param array $attr
     * @return string
     */
    protected function Button($name, $text = 'Submit', $attr = []){

        $inline = $this->getAttrs($name, $attr);

        return "<button name='{$name}' {$inline}>{$text}</button>";
    }

    /**
     *
     * A reset control.
     *
     * @param $name
     * @param string $text
     * @param array $attr
     * @return string
     */
    protected function Reset($name, $text = 'Reset', $attr = []){

        return $this->_Text($name, 'reset', $text, $attr);
    }

    /**
     *
     * A textarea control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @param int $rows
     * @param int $cols
     * @return string
     */
    protected function Textarea($name, $default = '', $attr = [], $rows = 0, $cols = 0){

        $default = $this->parseDefault($default);

        $inline = '';

        if($rows > 0)
            $attr['rows'] = $rows;
        if($cols > 0)
            $attr['cols'] = $cols;

        $inline = $this->getAttrs($name, $attr);

        if(in_array($name, $this->requireds)){
            $inline .= ' required';
        }

        return "<textarea name='{$name}'{$inline}>{$default}</textarea>";
    }

    /**
     *
     * A select control.
     * Set multiple=>true in $attr to make it a multiselect control.
     *
     * @param $name
     * @param array $choices
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Select($name, $choices = [], $default = '', $attr = []){

		if(isset($attr['multiple']) && empty($default))
			$default = [];

		if(!is_array($choices))
			$choices = [$choices];

        $default = $this->parseDefault($default, isset($attr['multiple']) ? false : true);

        $inline = $this->getAttrs($name, $attr);

        $options = '';

        if(!empty($default) && !isset($attr['multiple']) && !isset($choices[$default]))
            $options .= "<option value=''>{$default}</option>";

		if(!empty($choices)){
			if(!isset($choices[0])){ // Associative Array.
		        foreach($choices as $key => $value){

					if(!is_array($value)){

						$_selected = isset($attr['multiple']) ? in_array($key, $default) : $key == $default;
						$options .= "<option value='{$key}'" .($_selected ? ' selected' : ''). ">{$value}</option>";
					} else {
						$options .= '<optgroup label="' . ucwords(trim(str_replace('_', ' ', $key))) . '">';
						foreach($value as $k => $v){
							$_selected = isset($attr['multiple']) ? in_array($k, $default) : $k == $default;
							$options .= "<option value='{$k}'" .($_selected ? ' selected' : ''). ">{$v}</option>";
						}
						$options .= '</optgroup>';
					}
		        }
			} else { // Ordinal Array.
				foreach($choices as $_key => $value){

					if(!is_array($value)){

						$_selected = isset($attr['multiple']) ? in_array($value, $default) : $value == $default;
						$options .= "<option value='{$_key}'" .($_selected ? ' selected' : ''). ">{$value}</option>";
					} else {
						if(isset($value[0])){
							$options .= '<optgroup label="' . ucwords(trim(str_replace('_', ' ', $value))) . '">';
							foreach($value as $key => $_value){
								$_selected = isset($attr['multiple']) ? in_array($key, $default) : $key == $default;
								$options .= "<option value='{$key}'" .($_selected ? ' selected' : ''). ">{$_value}</option>";
							}
							$options .= '</optgroup>';
						} else {
							foreach($value as $key => $_value){
								$options .= '<optgroup label="' . ucwords(trim(str_replace('_', ' ', $_value))) . '">';
								foreach($_value as $v){
									$_selected = isset($attr['multiple']) ? in_array($v, $default) : $v == $default;
									$options .= "<option" .($_selected ? ' selected' : ''). ">{$v}</option>";
								}
								$options .= '</optgroup>';
							}
						}
					}
		        }
			}
		}

        return "<select name='{$name}" .(isset($attr['multiple']) ? '[]' : ''). "'{$inline}>{$options}</select>";
    }

    /**
     *
     * A radio control.
     *
     * @param $name
     * @param array $choices
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Radio($name, $choices = [], $default = '', $attr = []){

        $default = $this->parseDefault($default);

        $inline = $this->getAttrs($name, $attr);

        $options = '';
        if(!empty($choices)){
			foreach($choices as $key => $value){
	            $options .= "<label><input type='radio' name='{$name}'{$inline} value='{$key}'" .($key == $default ? ' checked' : ''). " /><span>{$value}</span></label>";
	        }
		}

        return $options;
    }

    /**
     *
     * A Checkbox control.
     *
     * @param $name
     * @param string $text
     * @param int $checked
     * @param array $attr
     * @return string
     */
    protected function Check($name, $text = '', $checked = 0, $attr = []){

        $text = $this->parseDefault($text);

        $inline = $this->getAttrs($name, $attr);

        return "<label><input type='checkbox' name='{$name}'{$inline} " .($checked == 1 ? ' checked' : ''). " /><span>{$text}</span></label>";;
    }

    /**
     *
     * A hidden field control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Hidden($name, $default = '', $attr = []){

        return $this->_Text($name, 'hidden', $default, $attr);
    }

    /**
     *
     * A text control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Text($name, $default = '', $attr = []){

        return $this->_Text($name, 'text', $default, $attr);
    }

    /**
     *
     * A date picker control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Date($name, $default = '', $attr = []){

        return $this->_Text($name, 'date', $default, $attr);
    }

    /**
     *
     * A time picker control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Time($name, $default = '', $attr = []){

        return $this->_Text($name, 'time', $default, $attr);
    }

    /**
     *
     * A time picker control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Month($name, $default = '', $attr = []){

        return $this->_Text($name, 'month', $default, $attr);
    }

    /**
     *
     * An email control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Email($name, $default = '', $attr = []){

        return $this->_Text($name, 'email', $default, $attr);
    }

    /**
     *
     * A password control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Password($name, $default = '', $attr = []){

        return $this->_Text($name, 'password', $default, $attr);
    }

    /**
     *
     * A search control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Search($name, $default = '', $attr = []){

        return $this->_Text($name, 'search', $default, $attr);
    }

    /**
     *
     * A number control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Number($name, $default = '', $attr = []){

        return $this->_Text($name, 'number', $default, $attr);
    }

    /**
     *
     * A telephone control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Tel($name, $default = '', $attr = []){

        return $this->_Text($name, 'tel', $default, $attr);
    }

    /**
     *
     * A url control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @return string
     */
    protected function Url($name, $default = '', $attr = []){

        return $this->_Text($name, 'url', $default, $attr);
    }

    /**
     *
     * A range slider control.
     *
     * @param $name
     * @param string $default
     * @param array $attr
     * @param int $min
     * @param int $max
     * @return string
     */
    protected function Range($name, $default = '', $attr = [], $min = 0, $max = 0){

        if($min > 0)
            $attr['min'] = $min;
        if($max > 0)
            $attr['max'] = $max;

        return $this->_Text($name, 'range', $default, $attr);
    }


    /**
     *
     * Validates a form.
     *
     * @return int|mixed|string
     */
    function validate(){

        do_action('before_form_validate', $this->name);

        if($this->no_validate)
            return VALIDATION_SUCCESS;

        global $regex;

        foreach($this->controls as $name => $value){



            $type = $value['type'];
            $attrs = $value['attr'];
            $options = $value['options'];

            if($type == 'button')
                continue;

            $global = $this->method;

            $rules = apply_filters('form_control_rules', string_to_array($options['rules'], '|'), $this->name, $name); // E.g. required|max:50|min:35|email|required_with[password,email]|in[male,female]|unique:users.userId|function:example_function

            $_ccount = count($rules);

            if(!empty($options['rules'])){
                // Create associative entries.
                foreach($rules as $rule){

                    if(strpos($rule, ':') > -1){
                        $jxr = string_to_array($rule, ':');
                        $rules[$jxr[0]] = $jxr[1];
                    } else if(strpos($rule, '[') > -1 && strpos($rule, ']') > -1){
                        $jxr = string_to_array($rule, '[');

                        $rules[$jxr[0]] = string_to_array( trim(substr($jxr[1], 0, -1)) );
                    } else {

                        $rules[$rule] = true;
                    }
                }
            }

            // Remove ordinal entries.
            for($i = 0; $i < $_ccount; $i++){

                unset($rules[$i]);
            }

            $control_value = $this->getControlValue($name);

            if(isset($rules['error']))
                $default_error = get_text($rules['error']);
            else $default_error = '';

            $default_error = apply_filters('form_control_default_error', $default_error, $this->name, $name);


            if(!$this->verifyCsrf())
                return $this->_validation_error('validation_csrf_fail', [null, [], $default_error]);

            if(( isset($attrs['required']) || isset($rules['required']) || in_array($name, $this->requireds) ) && empty($control_value)) // Require that this must not be empty.
                return ($this->_validation_error('validation_required_fail', [$this->getControlText($name)], $default_error));

            if( isset($rules['required_with']) && empty($control_value) ){ // Require that this field is required when any of the other fields are present.
                foreach($rules['required_with'] as $req){

                    $cval = $this->getControlValue($req);
                    $psk = false;
                    if(!empty($cval)){
                        $psk = true;
                    }

                    if($psk)
                        return ($this->_validation_error('validation_required_with_fail', [$this->getControlText($name), $this->getControlText($req)], $default_error));
                }
            }

            if( isset($rules['required_without']) && !empty($control_value) ){ // Require that this field is required when all of the other fields are not present.
                foreach($rules['required_without'] as $req){

                    $cval = $this->getControlValue($req);
                    if( (empty($control_value) && empty($cval)) || (!empty($control_value) && !empty($cval)) )
                        return ($this->_validation_error('validation_required_without_fail', [$this->getControlText($name), $this->getControlText($req)], $default_error));
                }
            }

            if( isset($rules['same_as']) && isset($rules['required']) ){ // Require that this field must contain the same value with the specified field..
                $cval = $this->getControlValue($rules['same_as']);
                if( $cval != $control_value )
                        return ($this->_validation_error('validation_same_as_fail', [$this->getControlText($name), $this->getControlText($rules['same_as'])], $default_error));
            }

            if(isset($attrs['pattern']) && !preg_match($attrs['pattern'], $control_value) && isset($rules['required'])) // Validate this match specified pattern.
                return ($this->_validation_error('validation_pattern_fail', [$this->getControlText($name)], $default_error));

            if(isset($attrs['disabled']) && $control_value != $value['default'] && isset($rules['required'])) // Verify that disabled fields were not enabled for csrf.
                return ($this->_validation_error('validation_disabled_fail', [$this->getControlText($name)], $default_error));

            if(( isset($attrs['maxlength']) && strlen($control_value) > $attrs['maxlength'] && isset($rules['required']) )  || ( isset($rules['maxlength']) && strlen($control_value) > $rules['maxlength'] && isset($rules['required']) ) )
                return ($this->_validation_error('validation_maxlength_fail', [$name, isset($attrs['maxlength']) ? $attrs['maxlength'] : $rules['maxlength']], $default_error));
            // Value must be greater than the specified length.

            if( isset($rules['length']) && strlen($control_value) != $rules['length'] && isset($rules['required']) ) // Value must be the specified length.
                return ($this->_validation_error('validation_length_fail', [$this->getControlText($name)], $default_error));

            if( isset($rules['minlength']) && strlen($control_value) < $rules['minlength'] && isset($rules['required']) )// Value must be at least the specified length.
                return ($this->_validation_error('validation_minlength_fail', [$this->getControlText($name), $rules['minlength']], $default_error));

            if(( isset($attrs['min']) && $control_value < $attrs['min'] && isset($rules['required']) ) || ( isset($rules['min']) && $control_value < $rules['min'] && isset($rules['required']) ) ) // Value must be at least the specified value.
                return ($this->_validation_error('validation_range_min_fail', [$this->getControlText($name), isset($attrs['min']) ? $attrs['min'] : $rules['min']], $default_error));

            if( (isset($attrs['max']) && $control_value > $attrs['max'] && isset($rules['required'])) || (isset($rules['max']) && $control_value > $rules['max'] && isset($rules['required'])) )// Value must be not be more than the specified value.
                return ($this->_validation_error('validation_range_max_fail', [$this->getControlText($name), isset($attrs['max']) ? $attrs['max'] : $rules['max']], $default_error));

            if(( $type == 'email' || isset($rules['email']) ) && isset($rules['required']) && !filter_var($control_value, FILTER_VALIDATE_EMAIL)) // Value must be an email.
                return ($this->_validation_error('validation_email_fail', [], $default_error));

            if(( $type == 'number' || isset($rules['number']) ) && isset($rules['required']) && !regex_match($control_value, $regex::NUMBERS, true)) // Value must be numeric.
                return ($this->_validation_error('validation_number_fail', [$this->getControlText($name)], $default_error));

            if(( $type == 'phone' || isset($rules['phone']) ) && isset($rules['required']) && !regex_match($control_value, $regex::PHONE, true)) // Value must be a phone number.
                return ($this->_validation_error('validation_phone_fail', [$this->getControlText($name)], $default_error));

            if(( $type == 'url' || isset($rules['url']) ) && isset($rules['required']) && !filter_var($control_value, FILTER_VALIDATE_URL)) // Value must be a url.
                return ($this->_validation_error('validation_url_fail', [$this->getControlText($name)], $default_error));

            if( isset($rules['ipv4']) && !filter_var($control_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && isset($rules['required']) ) // Value must be valid ipv4 string.
                return ($this->_validation_error('validation_ipv4_fail', [$this->getControlText($name)], $default_error));

            if( isset($rules['ipv6']) && !filter_var($control_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && isset($rules['required']) ) // Value must be valid ipv6 string.
                return ($this->_validation_error('validation_ipv6_fail', [$this->getControlText($name)], $default_error));

            if( isset($rules['equal']) && $control_value != $rules['equal'] && isset($rules['required']) ) // Value must be equal  to the specified value.
                return ($this->_validation_error('validation_equal_fail', [$this->getControlText($name), $rules['equal']], $default_error));

            if( isset($rules['not_equal']) && $control_value == $rules['not_equal'] && isset($rules['required']) ) // Value must not be equal  to the specified value.
                return ($this->_validation_error('validation_not_equal_fail', [$this->getControlText($name), $rules['not_equal']], $default_error));

            if( isset($rules['less_than']) && $control_value >= $rules['less_than'] && isset($rules['required']) ) // Value must be less than the specified value.
                return ($this->_validation_error('validation_less_than_fail', [$this->getControlText($name), $rules['less_than']], $default_error));

            if( isset($rules['greater_than']) && $control_value <= $rules['greater_than'] && isset($rules['required']) ) // Value must be greater than the specified value.
                return ($this->_validation_error('validation_greater_than_fail', [$this->getControlText($name), $rules['greater_than']], $default_error));

            if( isset($rules['less_than_equal_to']) && $control_value > $rules['less_than_equal_to'] && isset($rules['required']) ) // Value must be less than or equal to the specified value.
                return ($this->_validation_error('validation_less_than_equal_fail', [$this->getControlText($name), $rules['less_than_equal_to']], $default_error));

            if( isset($rules['greater_than_equal_to']) && $control_value < $rules['greater_than_equal_to'] && isset($rules['required']) ) // Value must be greater than or equal to the specified value.
                return ($this->_validation_error('validation_greater_than_equal_fail', [$this->getControlText($name), $rules['greater_than_equal_to']], $default_error));

            if( isset($rules['in']) && !in_array($control_value, $rules['in']) && isset($rules['required']) ) // Value must be one of the specified value.
                return ($this->_validation_error('validation_in_fail', [$this->getControlText($name)], $default_error));

            if( isset($rules['unique']) && isset($rules['required']) ){

                list($utable, $ufield) = string_to_array($rules['unique'], '.');
                $db_ret = db_select($utable, null, [$ufield => $control_value]);

                if(!empty($db_ret)) {
                    $unique_failed = true;
                    if(isset($rules['unique_without'])){
                        $id = $rules['unique_without'];
                        $db_ret = to_array($db_ret[0]);
                        if($db_ret[$id] == $this->getControlValue($id))
                            $unique_failed = false;
                    }
                    if($unique_failed)
                        return ($this->_validation_error('validation_unique_fail', [$this->getControlText($name)], $default_error));
                }

            } // Value must only exist once in the database.

            if( isset($rules['similar']) && isset($rules['required']) ){

                $sm = $this->getControlValue($rules['similar']);
                if(!similar_text($control_value, $sm))
                    return ($this->_validation_error('validation_similar_fail', [$this->getControlText($rules['similar']), $this->getControlText($name)], $default_error));
            } // Value must be similar to the value of another field.

            if( isset($rules['not_similar']) && isset($rules['required']) ){

                $sm = $this->getControlValue($rules['not_similar']);
                if(similar_text($control_value, $sm))
                    return ($this->_validation_error('validation_not_similar_fail', [$this->getControlText($rules['not_similar']), $this->getControlText($name)], $default_error));
            } // Value must be similar to the value of another field.

            if( isset($rules['full_name']) && !regex_match($control_value, $regex::FULLNAME, true) && isset($rules['required']) )
                return $this->_validation_error('validation_fullname_fail', [$this->getControlText($name)], $default_error); // Value must be a person full name E.g. John Doe.

            if( isset($rules['alphabets']) && !regex_match($control_value, $regex::ALPHABETS, true) && isset($rules['required']) )
                return $this->_validation_error('validation_aplhabet_fail', [$this->getControlText($name)], $default_error); // Value must contain only alphabets.

            if( isset($rules['us_social_security_number']) && !regex_match($control_value, $regex::US_SOCIALSECURITY_NUMBER, true) && isset($rules['required']) )
                return $this->_validation_error('validation_us_social_security_fail', [$this->getControlText($name)], $default_error); // Value must contain a valid US social security number.

            if(isset($rules['function']) && isset($rules['required'])){ // Result on passing value into the function must not be false.

                $cvr = call($rules['function'], $control_value);
                if(!function_exists($rules['function']) || $cvr == false){

                    return $this->_validation_error('validation_fail', [], $default_error);
                } else {

                    $this->getControlValue($name, $cvr);
                }
            }

            if($type == 'password' && isset($rules['required'])){

                if( isset($rules['weak']) && !regex_match($control_value, $regex::WEAK_PASSWORD, true) )
                    return $this->_validation_error('validation_password_fail', [], $default_error); // Value can contain a weak password.

                if( isset($rules['fair']) && !regex_match($control_value, $regex::FAIR_PASSWORD, true) )
                    return $this->_validation_error('validation_password_fail', [], $default_error); // Value can contain a fairly strong password.

                if( isset($rules['strong']) && !regex_match($control_value, $regex::STRONG_PASSWORD, true) )
                    return $this->_validation_error('validation_password_fail', [], $default_error); // Value must contain a strong password.
            }

            if($type == 'file' && isset($rules['required'])){

                if(isset($rules['extension']) && isset($_FILES[$name]['tmp_name'])){

                    $ext = pathinfo($_FILES[$name]['tmp_name'], PATHINFO_EXTENSION);

                    if(!in_array($ext, $rules['extension']))
                        return $this->_validation_error('validation_filetype_fail', [trim(implode(', ', $rules['extension'])), $this->getControlText($name)], $default_error);
                } // File must be of one of the specified extensions.

                if(isset($rules['min_size']) && isset($_FILES[$name]['tmp_name'])){

                    if($_FILES[$name]['size'] < real_file_size($rules['min_size']))
                        return $this->_validation_error('validation_filemin_fail', [$this->getControlText($name)], $default_error);
                } // File must not be smaller than the specified size.

                if(isset($rules['max_size']) && isset($_FILES[$name]['tmp_name'])){

                    if($_FILES[$name]['size'] > real_file_size($rules['max_size']))
                        return $this->_validation_error('validation_filemax_fail', [$this->getControlText($name)], $default_error);
                } // File must not be larger than the specified size.

                if(isset($rules['safe_name']) && isset($_FILES[$name]['tmp_name'])){

                    if(!regex_match($_FILES[$name]['name'], $regex::SAFE_FILENAME, true))
                        return $this->_validation_error('validation_safename_fail', [$this->getControlText($name)], $default_error);
                } // File name must be a safe file name.

                if(isset($rules['clean_name']) && isset($_FILES[$name]['tmp_name'])){

                    if(!regex_match($_FILES[$name]['name'], $regex::CLEAN_FILENAME, true))
                        return $this->_validation_error('validation_clean_name_fail', [], $default_error);
                } // File name must be a clean file name.
            }

            // Allow custom validations to be added.
            do_action('form_control_validated', $this->name, $name, $control_value);


        }

        do_action('after_form_validate', $this->name);

        return VALIDATION_SUCCESS;
    }

    /**
     *
     * Parses validation errors for output.
     *
     * @param $text
     * @param $args
     * @param string $default
     * @return mixed|string
     */
    protected function _validation_error($text, $args, $default = ''){
        $res = '';
        if(!empty($default)) {
            $default = ucwords(str_replace('_', ' ', $default));
            $res = $default;
        } else {
            $text = get_config($text, 'validation');
            if(!empty($args)){
                $args = array_merge([$text], $args);
                $res = call('sprintf', $args);
            } else {
                $res = $text;
            }
        }

        return apply_filters('validation_error_text', $res, $default);
    }

    /**
     *
     * Submits a form.
     *
     * @param null $function
     * @return bool
     */
    function doSubmit($function = null){

        $this->fixMissingNames();

        // Make our CSRF field our default form submit test.
        if($this->no_csrf)
            $posted = $this->getControlValue($this->submit_name);
        else
           $posted = $this->getControlValue($this->csrf_name);

        if(!$this->no_submit && !empty($posted)){

            do_action('before_form_submit', $this->name);

            if(!empty($function)){
                call($function, $this->validate());
                do_action('after_form_submit', $this->name);
            } else {

                $result = $this->validate();

                if($result == VALIDATION_SUCCESS){
                    do_action('after_form_submit', $this->name);
                    return true;
                } elseif(!$result){
                    do_action('after_form_submit', $this->name);
                    return false;
                } else
                    $this->validation_error = $result;

                do_action('after_form_submit', $this->name);
            }
        } else if($this->no_submit){

            return true;
        }

        call('un' . $this->method, $this->csrf_name);
        call('un' . $this->method, $this->submit_name);

        return false;
    }

    /**
     *
     * Saves a form directly to the database.
     * @see DB->insertUnpdate();
     * @see DB->insertUnique();
     *
     * @param string $table
     * @param string $unique_column
     * @param array $exceptions
     * @param bool $should_update
     * @return bool|int|mixed
     */
    function toDb($table = '', $unique_column = '', $exceptions = [], $should_update = true){

        do_action('before_form_save', $this->name, $table, $unique_column, $exception, $should_update);

        $insert = [];

        if(empty($table))
            $table = $this->name;

        foreach($this->controls as $name => $values){

            if(!in_array($name, $exceptions)){
                $insert[$name] = $this->getControlValue($name);

                if($values['type'] == 'password' && strtolower(get_config('encrypt_passwords', 'main')) == 'yes'){

                    $insert[$name] = secure_password($insert[$name]);
                } elseif($values['type'] == 'file'){

                    if(isset($_FILES[$name]['tmp_name'])){

                        global $__uploaders__;

                        if(isset($__uploaders__[$name . '_uploader'])){

                            $dbms = do_upload($name . '_uploader');

                            if(!in_array($dbms, [-1, -2, -3])){

                                $insert[$name] = $dbms;
                            } else {

                                unset($insert[$name]);
                            }
                        }
                    } else {

                        unset($insert[$name]);
                    }
                } elseif($values['type'] == 'submit'){
                    unset($insert[$name]);
                } else {

                    $options = $values['options'];
                }
            }
        }

        if(empty($unique_column))
            $unique_column = $this->name . '_id';

        $result = $should_update ? db_insert_update($table, $unique_column, $insert) : false;

        if(!$result){

            $exception = $insert;

            if(isset($exception[$unique_column]))
                unset($exception[$unique_column]);

            $result = db_insert_unique($table, $insert, $exception);

            do_action('after_form_save', $this->name, $result);

            if($result == -1){

                $this->validation_error = $this->_validation_error('validation_unique_fail', [$this->getControlText($unique_column), []]);
                return false;
            } else if($result > 0){

                return (int)$result;
            }
        } else {

            do_action('after_form_save', $this->name, $result);
            return (int)$result;
        }

        return false;
    }

}



