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

namespace phpasap\classes;

class Session_Handler {
    
    private $cleared_old_flash = false;
    private $flash = [];
    
    public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
        $this->start_session();
        $this->clear_old_flash();
    }

    /**
     * starts session if not started already
     * depending upon the version of php checks if already session has started
     */
    public function start_session() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
    
    public function clear_old_flash() {
        
        // if flash already cleared don't do anything
        if($this->cleared_old_flash === true) return;
        
        $flash_vars = $this->get('flash');
        
        if(!empty($flash_vars) && is_array($flash_vars)) {
            foreach( $flash_vars as $var ) {
                $this->flash[$var] = $this->get($var);
                $this->remove($var);
            }
        }
        $this->remove('flash');
        $this->cleared_old_flash = true;
    }
    
    /*
     * set value of a session variable
     */
    public function set($name,$value) {
        $_SESSION[$name] = $value;
    }
    
    /*
     * get value of session variable
     * first checks for flash and then session
     */
    public function get($name) {
        if( isset($this->flash[$name]) ) {
            return $this->flash[$name];
        }
        elseif( isset($_SESSION[$name]) ) {
            return $_SESSION[$name];
        }
        else {
            return null;
        }
    }
    
    public function remove($name) {
        unset($_SESSION[$name]);
    }
    
    /*
     * @param $name: could be a string or an associative array
     * @param string $value: optional if $name is array
     *
     * Eg usage:    Session_Handler::flash(['var_1'=>'val_1', 'var_2'=>'val_2']);
     *              Session_Handler::flash('var_1','val_1');
     */
    public function flash($name,$value='') {
        
        if( empty($name) )
        return;
        
        $flash_array = [];
        
        if(!is_array($name)) {
            $flash_array = [$name=>$value];
        }
        else
            $flash_array = $name;
        
        foreach( $flash_array as $f_name=>$f_value ) {
            /* Set value of session variable */
            $this->set($f_name,$f_value);
            
            /* Get currently saved flash variables array */
            $flash = $this->get('flash') ? $this->get('flash') : [];
            
            /* Append the new passed variable */
            $flash[] = $f_name;
            
            /* Save the flash session variable */
            $this->set('flash',$flash);    
        }
        
    }
    
}