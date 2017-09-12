<?php
/**
 * This file is part of the PHPasap, a MVC framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2016, Perials Technologies
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
 * @package	    PHPasap
 * @author	    Perials
 * @copyright	Copyright (c) 2016, Perials Technologies (https://perials.com/)
 * @license	    http://opensource.org/licenses/MIT	MIT License
 * @link	    https://phpasap.com
 */

namespace core\classes;

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

class Config_Loader {
    
    private $params = array();
    
    public function __construct() {
        $this->load();
    }
    
    /**
     * load all config parameter in params property
     */
    protected function load() {
        //$config_files = ROOT . DS . 'core' . DS . 'config' . DS . '*.php';
        $config_files = ROOT . DS . 'app' . DS . 'config' . DS . '*.php';
        foreach(glob($config_files) as $file) {
            $this->params[basename($file, ".php")] = require $file;
        }
        
        //load environment config
        if( is_dir(ROOT . DS . 'app' . DS . 'config' . DS . ENVIRONMENT) ) {
            $config_files = ROOT . DS . 'app' . DS . 'config' . DS . ENVIRONMENT . DS . '*.php';
            foreach(glob($config_files) as $file) {
                if( !isset($this->params[basename($file, ".php")]) ) {
                    $this->params[basename($file, ".php")] = require $file;
                }
                else {
                    $temp = require $file;
                    $this->params[basename($file, ".php")] = array_merge($this->params[basename($file, ".php")], $temp);
                }
            }
        }
    }
    
    /**
     * fetch a config item
     *
     * @param string $param_name    . seperated file and variable name
     *                              if not provided then all the config paramaters are returned
     */
    public function get($param_name='', $default=null) {
        if(empty($param_name))
        return $this->params;
        
        $param_name_array = explode('.',$param_name);
        if( count($param_name_array) > 1 ) {
            $config_file = $param_name_array[0];
            $config_var = $param_name_array[1];
        }
        else {
            $config_file = $param_name_array[0];
            $config_var = false;
        }
        
        if( isset($this->params[$config_file]) ) {
            if( $config_var == false )
            return $this->params[$config_file];
        
            if(isset($this->params[$config_file][$config_var]))
            return $this->params[$config_file][$config_var];
        }            
        
        return $default;
    }
    
    public static function get_instance() {
        return new Config();
    }
    
}