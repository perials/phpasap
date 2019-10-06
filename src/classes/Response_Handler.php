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

use phpasap\alias\Request;

class Response_Handler extends View_Handler {
    // use Loader;

    public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
    }
    
    public function handle($response) {
        /*
        if( is_object($response) && get_class($response) === get_class($this->request) ) {
            // If request_handler object then we check if this is a redirect
            if( $response->redirect_to )
            $response->redirect_header();
        }
        elseif( is_object($response) && get_class($response) === get_class($this) ) {
        // elseif( get_class($response) === get_class($this->view) ) {
            if( $response->is_json() ) {
                $response->output_json();
            }
            else
            // For view object we echo the generated markup
            echo $response->get_markup();
        }
        */
        if( is_object($response) && get_class($response) === get_class($this) ) {
            if( $response->redirect_to ) {
                $response->redirect_header();
            }
            elseif( $response->is_json() ) {
                $response->output_json();
            }
            else {
                // For view object we echo the generated markup
                echo $response->get_markup();
            }
        }
        elseif( is_string($response) ) {
            echo $response;
        }
    }

    public function show_404($html) {
        header("HTTP/1.0 404 Not Found");
        $this->handle($html);
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
        Session::flash($this->app->request->all());
        return $this;
    }
}