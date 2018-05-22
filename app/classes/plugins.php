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
 * Class Plugin
 * Implements the Candy plugin system.
 *
 * A plugin is simply a folder containing an index.php file and a plugin.candy file,
 * that contains functions hooked with add_filter or add_action users can call using
 * apply_filter or do_action respectively.
 */
class Plugin {

    /**
     * Array  of all available plugins within the system.
     * @var array
     */
    private $plugins_available = array();

    /**
     * Plugin constructor.
     */
    function __construct(){
	}


    /**
     * Detects and assembles all plugins found in the #plugin_dir.
     * @param bool $auto_load
     * @throws Exception
     */
    function init($auto_load = false){
		
		$dirs = get_directory(PLUGIN_DIR, CANDY_SCAN_DIRECTORIES, '', 1);
		
		foreach($dirs as $dir){
		    $dir2 = get_directory_name($dir);

			if(file_exists("{$dir}/{$dir2}.candy")){

				$plugin_headers = load_configs("{$dir}/{$dir2}.candy", false);

				// Minimum config requirement for a plugins:
                // #name and #version.
				if(isset($plugin_headers['name']) && isset($plugin_headers['version'])){

					$this->plugins_available[] .= get_directory_name($dir);
				}
			}
		}

		if($auto_load)
		    $this->load_plugins();
	}

    /**
     * Returns the headers found in a plugin file.
     * Minimalist load_configs function.
     * @param $file
     * @return array
     */
    function get_plugin_headers($file){
		
		$hash_matches = [];
		
		foreach(@file($file) as $line){
			
			if(strlen(trim($line)) > 0){
				
				if(trim($line)[0] == '#'){
					
					$hash_line = trim($line);
					
					$hash_matches = array_merge($hash_matches, [substr(preg_split('/[\s]/', $hash_line)[0], 1) => preg_replace('/^#[a-zA-Z]+/', '', $hash_line)]);
				}
			}
		}
		
		return $hash_matches;
	}

    /**
     * Makes all plugins available for use in the current context.
     * An explicit use of this is discouraged but may become absolutely necessary in scenarios we do not foresee, hence the inclusion.
     * @see init() for a better way to call this function.
     */
    function load_all_plugins(){
		
		foreach($this->plugins_available as $plugin){
			
			require_once PLUGIN_DIR . '/' . trim($plugin) . '/index.php';
		}
	}

    /**
     * Loads and makes a specific plugin available for use in the current context.
     * @param $plugin
     */
    function load_plugin($plugin){
        
        if(is_string($plugin))
            $plugin = explode(',', $plugin);
        
        foreach($plugin as $plg){
            
            if(in_array($plg, $this->plugins_available)){

                require_once PLUGIN_DIR . '/' . trim($plg) . '/index.php';
            } else {
                throw new Exception("Error loading unknown plugin {$plg}.");
            }
        }
    }


    /**
     * Returns the headers of a specific plugin.
     * @param $plugin_name
     * @return array
     */
    function headers($plugin_name){
        if(in_array($plugin_name, $this->plugins_available))
            return $this->get_plugin_headers(PLUGIN_DIR . "/{$plugin_name}/{$plugin_name}.candy");
        return [];
    }
}


