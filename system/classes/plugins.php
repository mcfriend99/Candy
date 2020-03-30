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
 * Class Plugin
 * Implements the Candy plugin system.
 *
 * A plugin is simply a folder containing an index.php file and a plugin.candy file,
 * that contains functions hooked with add_filter or add_action users can call using
 * apply_filter or do_action respectively.
 */
class Plugin
{
    /**
     * Array of all available plugins within the system.
     * @var array
     */
    private $plugins_available = array();

    /**
     * Array of headers in all available plugins within the system.
     * @var array
     */
    private $plugins_headers = array();

    /**
     * Plugin constructor.
     */
    function __construct()
    {
    }


    /**
     * Detects and assembles all plugins found in the #plugin_dir.
     * @param bool $auto_load
     * @throws Exception
     */
    function init($auto_load = false)
    {

        $dirs = get_directory(PLUGIN_DIR, CANDY_SCAN_DIRECTORIES, '', 1);

        foreach ($dirs as $dir) {
            $dir2 = get_directory_name($dir);

            if (file_exists("{$dir}/{$dir2}.json")) {

                $this->plugin_headers[$dir2] = load_configs("{$dir}/{$dir2}.json", false);

                // Minimum config requirement for a plugins:
                // #name and #version.
                if (isset($this->plugin_headers[$dir2]['name']) && isset($this->plugin_headers[$dir2]['version'])) {

                    $this->plugins_available[] .= get_directory_name($dir);
                }
            }
        }

        if ($auto_load) {
            $this->load_all_plugins();
        }
    }

    /**
     * Makes all plugins available for use in the current context.
     * An explicit use of this is discouraged but may become absolutely necessary in scenarios we do not foresee, hence the inclusion.
     * @see init() for a better way to call this function.
     */
    function load_all_plugins()
    {

        foreach ($this->plugins_available as $plugin) {

            require_once PLUGIN_DIR . '/' . trim($plugin) . '/index.php';
            do_action('on_plugin_load', trim($plugin));
        }
    }

    /**
     * Loads and makes a specific plugin available for use in the current context.
     * @param $plugin
     */
    function load_plugin($plugin)
    {

        if (is_string($plugin))
            $plugin = explode(',', $plugin);

        foreach ($plugin as $plg) {

            if (in_array($plg, $this->plugins_available)) {

                require_once PLUGIN_DIR . '/' . trim($plg) . '/index.php';
                do_action('on_plugin_load', trim($plg));
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
    function headers($plugin_name)
    {
        if (in_array($plugin_name, $this->plugins_available))
            $result = $this->plugin_headers[$plugin_name];
        else $result = [];

        $result = apply_filters('plugin_header', $plugin_name, $result);
        return $result;
    }
}
