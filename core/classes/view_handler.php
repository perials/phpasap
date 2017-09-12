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

class View_Handler {
    
    private $markup = '';
    private $is_json = false;
    private $json_array = [];
    
    private $shared_variables = [];
    
    public function set($params_array = array()) {
        $this->shared_variables = array_merge($this->shared_variables, $params_array);
    }
    
    public function is_json() {
        return $this->is_json;
    }
    
    public function json($data) {
        $this->is_json = true;
        $this->json_array = $data;
        return $this;
    }
    
    public function output_json() {
        header('Content-Type: application/json');
        echo json_encode($this->json_array);
    }
    
    public function make($file_name,$params_array=array(), $echo=false) {
        $this->markup = $this->render($file_name,$params_array);
        if( $echo == true ) {
            echo $this->get_markup();
        }
        else {
            return $this;
        }
    }
    
    public function render($file_name,$params_array=array()) {
        
        $params_array = array_merge($this->shared_variables, $params_array);
        
        $file = $this->get_file_path($file_name);
        
        if( !file_exists($file) )
        show_error('View file '.$file_name.' not found at '.$file, true);
        
        extract($params_array);
        ob_start();
        //require ROOT . DS . 'application' . DS . 'views' . DS . $file_name . '.php';
        require $file;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
    /**
     * checks if view file exists
     *
     * @return boolean
     */
    public function exists($file_name='') {
        $file = $this->get_file_path($file_name);
        if( !file_exists($file) )
            return false;
        else
            return true;
    }
    
    /**
     * gets full view file path using the relative file name
     * Note that this function doesn't check if file exits or not
     *
     * @return string
     */
    public function get_file_path($file_name='') {
        return ROOT . DS . 'app' . DS . 'views' . DS . $file_name . '.php';
    }
    
    public function get_markup() {
        return $this->markup;
    }
    
}