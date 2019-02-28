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
 * Class Chocolate
 * Candy's Blade-like template implementation.
 * I choose Blade-like syntax to leverage on its simplicity and popularity.
 * Looking at: Laravel's success. #BigThumbsUp.
 */

// TODO: Allow nesting of @section directives.


/**
 * Class Chocolate
 */
class Chocolate
{
    /**
     * The template file being processed.
     */
    protected $file;

    /**
     * The content of the current template file as it is being processed.
     */
    protected $str = '';

    /**
     * Whether the template should automatically escape echoes.
     */
    var $auto_escape = true;

    /**
     * The directory to store template cache files.
     */
    var $cache_dir;

    /**
     * Whether the template already exists in the cache.
     */
    var $cached = false;

    /**
     * The name of the current template file in the cache directory.
     */
    var $cache_file = '';

    /**
     * The name of the parent template from which this template inherits in the cache folder if any.
     */
    protected $parent = '';

    /**
     * The cached content of the parent template file from which this template inherits if any.
     */
    protected $parent_str = '';

    /**
     * The path of the template file.
     */
    private $sfile;

    /**
     * A container of sections within the template.
     */
    protected $sections = [];

    /**
     * A container of sections within the extended parent template if any.
     */
    protected $parent_sections = [];


    /**
     * Chocolate constructor.
     * @param $dir                  The cache directory.
     * @param bool $auto_escape     Whether to automatically escape echoes or not.
     */
    public function __construct($dir, $auto_escape = true)
    {
        if(!file_exists($dir) || !is_dir($dir)) {
            if (file_exists(CACHE_DIR) && is_dir(CACHE_DIR)) {
                @mkdir($dir);
            } else {
                $this->error('Chocolate cache directory not found.');
            }
        }

        $this->auto_escape = $auto_escape;
        $this->cache_dir = $dir;
    }

    /**
     * Sets the template file to be processed.
     * @param string $file      The full path to the file.
     */
    public function set_file($file = ''){
        if(!file_exists($file)){
            $this->error('Chocolate file not found.');
        }

        $this->file = $file;

        $sha1 = sha1($this->file . filemtime($this->file));
        $this->cache_file = $this->cache_dir . '/' . $sha1 . '.php';



        $this->sfile = pathinfo($this->cache_file, PATHINFO_DIRNAME)
            . '/' . pathinfo($this->cache_file, PATHINFO_FILENAME) . '.'
            . pathinfo($this->cache_file, PATHINFO_EXTENSION);

        if(file_exists($this->cache_file)){
            $this->cached = true;
        } else {
            $this->cached = false;

            $file = file($this->file);
            
            preg_match('~(?<!@)@extend\s*\(([\'"])([^\'"]+)\\1\)\s*(\r?\n)?~x', trim($file[0]), $match);
            if(!empty($match)){
                $this->parent = $match[2];
                unset($file[0]);
            }

            foreach($file as $f){
                $this->str .= $f;
            }
        }

    }

    /**
     * Renders the template to the browser.
     */
    public function render($show = true){

        if(!$this->cached){
            $this->parse();
            $this->save();
        }
        if($show) {
            include "{$this->cache_file}";
            $grs = '_' . pathinfo($this->cache_file, PATHINFO_FILENAME);
            new $grs();

            do_action('on_chocolate_render');
        }
    }

    /**
     *  Saves the processed template to file.
     */
    protected function save(){

        $grs = pathinfo($this->cache_file, PATHINFO_FILENAME);
        $gry = !empty($this->parent) ? pathinfo($this->parent, PATHINFO_FILENAME) : '';

        $extend = !empty($this->parent) ? " extends _{$gry}" : '';

        $sects = '';
        if(!empty($this->sections)){
            foreach ($this->sections as $key => $value) {
                $sects .=
    "/**###<{$key}>###**/public function {$key}(){
     foreach(\$GLOBALS as \$v => \$s) if(!isset($\$v) && \$v != '_{$grs}') $\$v = \$s;
     ?>";
$sects .= $value;
$sects .=
    "<?php }";
            }
        }

        $yvstr = '';
        foreach ($this->parent_sections as $key){
            $yvstr .= "/**###<{$key}>###**/\n";
        }

        $str =
            "<?php //CACHE-FOR: {$this->file}\n" . $yvstr
            .(!empty($this->parent) ? "include \"{$this->parent}\";" : ''). "
class _{$grs} {$extend} {
    function __construct(){ " .(!empty($this->parent) ? "parent::__construct();" : ''). "
        foreach(\$GLOBALS as \$v => \$s) if(!isset($\$v) && \$v != '_{$grs}') $\$v = \$s;
    ?>
" .trim($this->str). "
    <?php } {$sects}
}
?>";

        file_put_contents($this->cache_file, $str );
    }

    /**
     * Raizes a proper framework error.
     * @param $err      The error message
     * @throws Exception
     */
    protected function error($err){
        throw new Exception('CHOCOLATE_ERROR: ' . $err);
    }

    /**
     * Converts the source template to php codes.
     */
    protected function parse(){

            // Parse @extend commands before any other command. Else, it might not work as expected...
            $this->parseExtensions();

            $this->parseComments();
            $this->parseIncludes();

            $this->parseEchoes();
            $this->parseJson();
            $this->parseConditionals();
            $this->parseLoops();
            $this->parsePhp();
            $this->parseDate();


            // Parse Candy-PHP specific directives...
            $this->parseConfigurations();
            $this->parseCsrfs();
            $this->parseForms();
            $this->parseResources();
            $this->parseUploads();
            $this->parseUrls();
            $this->parseScripts();
            $this->parseStyles();
            $this->parseTexts();



            $this->str = apply_filters('on_chocolate_compile', $this->str);
            // ADD CUSTOM DIRECTIVES AS FOLLOWS:
            /*
             * function function_name($str){
             *
             *      return preg_replace_callback('pcre_compliance_regex', function($match){
             *          //return your proper php code...
             *      }, $str);
             * }
             *
             * add_filter('on_chocolate_compile', 'function_name');
             *
             * */


            // Parse sections and section calls lastly. This is the only way I know they can work for now.
            $this->parseSections();
            $this->parseShows();
    }

    /**
     * Sets the parent of the template if any.
     */
    protected function parseExtensions(){

        if(!empty($this->parent)){

            if($file = get_template_file($this->parent)) {

                if (file_exists(THEME_DIR . '/' . $file)) {

                    $t = $t = new Chocolate($this->cache_dir, $this->auto_escape);
                    $t->set_file(THEME_DIR . '/' . $file);
                    $t->render(false);

                    $this->parent = $t->cache_file;
                    $this->parent_str = file_get_contents($this->parent);

                    preg_replace_callback('~\/\*\*###<([a-zA-Z0-9_]+)>###\*\*\/~', function($s){
                        if(!in_array($s[1], $this->parent_sections))
                            $this->parent_sections[] .= $s[1];
                    }, $this->parent_str);
                } else {

                    $this->error("Can't extend a template file &quot;{$this->parent}&quot; that does not exist.");
                }
            }
        } else {
            $this->parent = '';
        }
    }

    /**
     * Parses comments in the template.
     * <!-- Comment -->
     */
    protected function parseComments(){
        $str = &$this->str;

        $str = preg_replace('~<\!\-\-(.+?)\-\->~is', '', $str);
    }

    /**
     * Parses echoes in the template.
     *
     * Escaped echoes... {# object #}
     * Unescaped echoes... {{ object }}
     */
    protected function parseEchoes(){
        $str = &$this->str;

        $str = preg_replace_callback('~{#\s*(.*?)\s*#}~', function($match) {
            return "<?=htmlspecialchars(safe_echo({$match[1]}));?>";
        }, $str);

        $str = preg_replace_callback('~{{\s*(.*?)\s*}}~', function($match) {
            return "<?=safe_echo({$match[1]});?>";
        }, $str);
    }


    /**
     * Parses @json directives.
     *
     * @json(object)
     */
    protected function parseJson(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@json\((.*?)\)\s*(\r?\n)?~x', function($match) {
            $white = isset($match[2]) ? $match[2] : '';
            return "<?=isset({$match[1]}) ? @json_encode({$match[1]}) : @json_encode([]);?>{$white}";
        }, $str);
    }

    /**
     * Parses conditions.
     *
     * @if...[[@elseif...]@else...]@endif
     * @not...@endnot
     * @set...@endset
     * @notset...@endnotset
     * @empty...@endempty
     * @switch...{@case...@break}+...@endswitch
     * @continue(condition)
     * @break(condition)
     */
    protected function parseConditionals(){
        $str = &$this->str;

        //// Handle @if...@elseif...@else...@endif conditions...

        // If...
        $str = preg_replace_callback('~(?<!@)@if\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if{$match[1]}: ?>{$white}";
        }, $str);

        // Else if...
        $str = preg_replace_callback('~(?<!@)@elseif\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php elseif{$match[1]}: ?>{$white}";
        }, $str);

        // Else...
        $str = preg_replace_callback('~(?<!@)@else\s*(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php else: ?>{$white}";
        }, $str);

        // End if...
        $str = preg_replace_callback('~(?<!@)@endif~x', function(){
            return "<?php endif; ?>";
        }, $str);


        //// Handle if not (@not...@endnot) conditions...
        $str = preg_replace_callback('~(?<!@)@not\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if(!{$match[1]}): ?>{$white}";
        }, $str);

        // End if not...
        $str = preg_replace_callback('~(?<!@)@endnot~x', function(){
            return "<?php endif; ?>";
        }, $str);


        //// Handle isset (@set...@endset) conditions...
        $str = preg_replace_callback('~(?<!@)@set\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if(isset{$match[1]}): ?>{$white}";
        }, $str);

        // End isset...
        $str = preg_replace_callback('~(?<!@)@endset~x', function(){
            return "<?php endif; ?>";
        }, $str);


        //// Handle isset (@notset...@endnotset) conditions...
        $str = preg_replace_callback('~(?<!@)@notset\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if(!isset{$match[1]}): ?>{$white}";
        }, $str);

        // End isset...
        $str = preg_replace_callback('~(?<!@)@endnotset~x', function(){
            return "<?php endif; ?>";
        }, $str);


        //// Handle empty iterable check (@empty...@endempty) conditions...
        $str = preg_replace_callback('~(?<!@)@empty\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if(empty{$match[1]}): ?>{$white}";
        }, $str);

        // End empty...
        $str = preg_replace_callback('~(?<!@)@endempty~x', function(){
            return "<?php endif; ?>";
        }, $str);


        //// Handle switch statement block conditions... @switch...{@case...@break}...@endswitch
        $str = preg_replace_callback('~(?<!@)@switch\s*(\([^()]+\))(.+?)@endswitch~s', function($match){
            if(!isset($match[2])) {
                $this->error("Invalid @switch directive in &quot;{$this->sfile}&quot;.");
            } else {
                $match[2] = preg_replace_callback('~(?<!@)@case\s*\(([^()]+)\)(\r?\n)?~x', function ($p) {
                    return "<?php case {$p[1]}: ?>";
                }, $match[2]);
                $match[2] = preg_replace_callback('~(?<!@)@break(?![(])(\r?\n)?~x', function () {
                    return "<?php break; ?>";
                }, $match[2]);
                $match[2] = preg_replace_callback('~(?<!@)@default(\r?\n)?~x', function () {
                    return "<?php default: ?>";
                }, $match[2]);
            }
            return "<?php switch{$match[1]}:"
                .substr(trim($match[2]), 5)
                ."<?php endswitch; ?>";
        }, $str);


        //// Handle loop exit conditions... @continue(condition), @break(condition)
        $str = preg_replace_callback('~(?<!@)@continue\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if{$match[1]} continue; ?>{$white}";
        }, $str);

        $str = preg_replace_callback('~(?<!@)@break\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php if{$match[1]} break; ?>{$white}";
        }, $str);
    }

    /**
     * Parses loops.
     *
     * @for...@endfor
     * @each...[@empty...]@endeach
     * @while...@endwhile
     */
    protected function parseLoops(){
        $str = &$this->str;


        //// Handle for loops (@for...@endfor)...
        $str = preg_replace_callback('~(?<!@)@for\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php for{$match[1]}: ?>{$white}";
        }, $str);

        // End for...
        $str = preg_replace_callback('~(?<!@)@endfor~x', function(){
            return "<?php endfor; ?>";
        }, $str);


        //// Handle foreach loops (@each...[@empty...]@endeach)...
        $str = preg_replace_callback('~(?<!@)@each\s*\(([^()]+)\)(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';

            $matches = preg_split('~\s+in\s+~', $match[1]);
            if(count($matches) > 1) {
                $mark = preg_split('~\s+as\s+~', $matches[0]);
                if(count($mark) > 1) $mark = "{$mark[0]} => {$mark[1]}";
                else $mark = $mark[0];

                $loop_condition = "{$matches[1]} as {$mark}";

                return "<?php \$__f0each__ = [1]; if(!empty({$matches[1]})): \$__counter__ = 0; foreach({$loop_condition}): ?>{$white}";
            } else {
                $this->error('Invalid @for directive.');
            }
        }, $str);

        // Empty foreach...
        $str = preg_replace_callback('~(?<!@)@empty~x', function(){
            return '<?php $__counter__++; endforeach; else: foreach($__f0each__ as $_F): ?>';
        }, $str);

        // End foreach...
        $str = preg_replace_callback('~(?<!@)@endeach~x', function(){
            return "<?php  \$__counter__++; endforeach; endif; unset(\$__counter__); unset(\$__f0each__); ?>";
        }, $str);


        //// Handle while loops (@while...@endwhile)...
        $str = preg_replace_callback('~(?<!@)@while\s*(\([^()]+\))(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php while{$match[1]}: ?>{$white}";
        }, $str);

        // End for...
        $str = preg_replace_callback('~(?<!@)@endwhile~x', function(){
            return "<?php endwhile; ?>";
        }, $str);

    }

    /**
     * Parse @php code block for raw php
     *
     * @php...@endphp
     */
    protected function parsePhp(){
        $str = &$this->str;
        $str = preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
            return "<?php{$matches[1]}?>";
        }, $str);
    }

    /**
     * Parse includes.
     *
     * @include(object_path)
     */
    protected function parseIncludes(){
        $str = &$this->str;
        $str = preg_replace_callback('~(?<!@)@include\s*\(([^()]+)\)~x', function($match){
            if($match[1][0] == '"' || $match[1][0] == "'")
                $match[1] = substr($match[1], 1, -1);

            if($file = get_template_file($match[1])) {

                if (file_exists(THEME_DIR . '/' . $file)) {

                    $t = $t = new Chocolate($this->cache_dir, $this->auto_escape);
                    $t->set_file(THEME_DIR . '/' . $file);
                    $t->render(false);

                    return "<?php include \"{$t->cache_file}\"; new _" .(preg_replace('~([^/]+/)|(.php)~x', '', $t->cache_file)). "(); ?>";

                } else {

                    $this->error("Can't extend a template file &quot;{$match[1]}&quot; that does not exist.");
                }
            }
        }, $str);
    }

    /**
     * Parse date...
     *
     * @date            # Current date in the format F d, Y g:i: A e.g. February 20, 2017 8:40 AM
     * @date(format)    # Formatted date.
     */
    protected function parseDate(){
        $str = &$this->str;
        $str = preg_replace_callback('~(?<!@)@date\s*(\([^()]+\))?(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?php echo date(" .(isset($match[1]) ? $match[1] : '"F d, Y g:i A"'). "); ?>{$white}";
        }, $str);
    }

    /**
     * Parses sections...
     *
     * @section('section_name')...@endsection
     */
    protected function parseSections(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@section\s*\(([\'"])([a-zA-Z0-9_]+)\\1\s*(,\s*false\s*)?\)\s*(.*?)(?<!@)@endsection~s', function($match){

            if(isset($this->sections[$match[2]]))
                $this->error("Section &quot;{$match[2]}&quot; is already defined in {$this->sfile}");
            elseif(strtolower($match[2]) == '__construct')
                $this->error("Invalid section name &quot;{$match[2]}&quot; in {$this->sfile}");

            $match[4] = preg_replace_callback('~(?<!@)@parent(\r?\n)?~', function() use ($match){
                return "<?php parent::{$match[2]}(); ?>";
            }, $match[4]);

            $match[4] = preg_replace_callback('~(?<!@)@endsection(\r?\n)?~', function() use ($match){
                return "";
            }, $match[4]);

            $this->sections = array_merge($this->sections, [$match[2] => trim($match[4])]);

            $this->sections[$match[2]] = preg_replace_callback('~(?<!@)@show\s*\(([\'"])([a-zA-Z0-9_]+)\\1\)~x', function($p){
                if(isset($this->sections[$p[2]]) || in_array($p[2], $this->parent_sections)) {
                    return "<?php \$this->{$p[2]}(); ?>";
                } else {
                    $this->error("Calling @show directive on a non-existing section &quot;{$p[2]}&quot;.");
                }
            }, $this->sections[$match[2]]);

            if(empty($this->parent) || !in_array($match[2], $this->parent_sections))
                if(!isset($match[3]) || empty($match[3]))
                    return "<?php \$this->{$match[2]}(); ?>";
            else return '';
        }, $str);


    }

    /**
     * Parse show section calls.
     *
     * @show('section_name')
     */
    protected function parseShows(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@show\s*\(([\'"])([a-zA-Z0-9_]+)\\1\)~x', function($match){
            if(isset($this->sections[$match[2]]) || in_array($match[2], $this->parent_sections)) {
                return "<?php \$this->{$match[2]}(); ?>";
            } else {
                $this->error("Calling @show directive on a non-existing section &quot;{$match[2]}&quot;.");
            }
        }, $str);
    }


    ///////// Candy-PHP specific template directives...

    /**
     * Parse texts (e() and get_text()) for translations...
     *
     * @e('text_name')          # Or --->
     * @text('text_name')       # Or <--
     */
    protected function parseTexts(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@(e|text)\s*\(([^()]+)\)~x', function($match){
            return "<?=e_text({$match[2]});?>";
        }, $str);
    }

    /**
     * Parse draw form calls.
     *
     * @form('form_name')
     */
    protected function parseForms(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@form\s*\(([^()]+)\)~x', function($match){
            return "<?php draw_form({$match[1]}); ?>";
        }, $str);
    }

    /**
     * Parses form CSRF echo calls.
     *
     * @csrf('form_name')
     */
    protected function parseCsrfs(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@csrf\s*\(([^()]+)\)~x', function($match){
            return "<?=form_csrf({$match[1]});?>";
        }, $str);
    }

    /**
     * Parses stylesheet display or link calls
     *
     * @style('style,names')            # include stylesheets
     * @style('style,names', true)      # inline stylesheets
     */
    protected function parseStyles(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@style\s*\(([\'"])([^\'"]+)\\1(\s*,\s*true\s*)?\)~x', function($match){
            if(isset($match[3])) {
                return "<?php inline_styles(\"{$match[2]}\"); ?>";
            } else return "<?php include_styles(\"{$match[2]}\"); ?>";
        }, $str);
    }

    /**
     * Parses scripts display or link calls
     *
     * @script('script,names')            # include scripts
     * @script('script,names', true)      # inline scripts
     */
    protected function parseScripts(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@script\s*\(([\'"])([^\'"]+)\\1(\s*,\s*true\s*)?\)~x', function($match){
            if(isset($match[3])) {
                return "<?php inline_scripts(\"{$match[2]}\"); ?>";
            } else return "<?php include_scripts(\"{$match[2]}\"); ?>";
        }, $str);
    }

    /**
     * Parses file resources url.
     *
     * @file('resource/path')
     */
    protected function parseResources(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@file\s*\(([^()]+)\)~x', function($match){
            return "<?=get_resource_url({$match[1]});?>";
        }, $str);
    }

    /**
     * Parses uploaded files url.
     *
     * @file('resource/path')
     */
    protected function parseUploads(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@upload\s*\(([^()]+)\)~x', function($match){
            return "<?=get_upload_url({$match[1]});?>";
        }, $str);
    }

    /**
     * Parses Candy configurations
     *
     * @config('config_name' [, 'config_type'])
     */
    protected function parseConfigurations(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@config\s*\(([^()]+)\)~x', function($match){
            return "<?=get_config({$match[1]});?>";
        }, $str);
    }

    /**
     * Parses Candy URLs
     *
     * @url
     * @url('path')
     */
    protected function parseUrls(){
        $str = &$this->str;

        $str = preg_replace_callback('~(?<!@)@url\s*(\([^()]+\))?(\r?\n)?~x', function($match){
            $white = isset($match[2]) ? $match[2] : '';
            return "<?=" .(isset($match[1]) ? "get_url({$match[1]})" : 'the_url()'). ";?>{$white}";
        }, $str);
    }
}




