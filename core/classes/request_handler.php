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

use core\alias\Route;
use core\alias\Session;

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

class Request_Handler {
    
    /*
     * check if ajax request
     * returns true if ajax else returns false
     */
    public function ajax() {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
            return true;
        else
            return false;
    }
    
    /**
     * Fetches the provided $_GET param
     *
     * @param string
     */
    public function get($param=null) {
        if($param==null)
        return $_GET;
        
        if( isset( $_GET[$param] ) )
        return $_GET[$param];
        else
        return false;
    }
    
    /**
     * Fetches the provided $_POST param
     *
     * @param string
     */
    public function post($param=null) {
        if($param==null)
        return $_POST;
        
        if( isset( $_POST[$param] ) )
        return $_POST[$param];
        else
        return false;
    }
    
    /**
     * Fetches the provided $_REQUEST param
     *
     * @param string
     */
    public function fetch($param) {
        if( isset( $_REQUEST[$param] ) )
        return $_REQUEST[$param];
        else
        return false;
    }
    
    public function all() {
        return $_REQUEST;
    }
    
    /**
     * FIXME deprecate this as this is already there in HTML::url_to()
     */
    public function url_to($url) {
        return $this->base_url().'/'.rtrim($url, '/');
    }
    
    /**
     * set the redirect_to property
     *
     * @param string $url relative url
     * @param boolean $hard_redirect if false then Request handler object is returned
     *                  if true then Location header is set and script is terminated
     *
     * @return mixed if $hard_redirect then void
     *                  if not $hard_redirect then current object
     */
    public function redirect_to($url,$hard_redirect=false) {
        if( strpos($url, "http://") === 0 || strpos($url, "https://") === 0 ) {
            $this->redirect_to = $url;
        }
        else
            $this->redirect_to = Route::base_url().'/'.rtrim($url, '/');
        if( $hard_redirect === true ) {
            $this->redirect_header();    
        }
        else {
            return $this;
        }
    }
    
    public function redirect_header() {
        header('Location: '.$this->redirect_to);
        die;
    }
    
    public function with($flash_data_array) {
        Session::flash($flash_data_array);
        return $this;
    }
    
    public function with_inputs() {
        Session::flash($this->all());
        return $this;
    }
    
    /**
     * check if given url is same as current url
     *
     * @param string $url url to check
     *
     * @return boolean
     */
    public function is($url_to_check='') {
        if( HTML::url($url_to_check) == Route::get_current_url() )
            return true;
        else
            return false;
    }
    
}