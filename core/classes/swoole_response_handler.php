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

// use core\alias\Route;
// use core\alias\Session;

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

class Swoole_Response_Handler {
    use Loader;

    public function __construct($app) {
        $this->app = $app;
    }
    
    public function handle($response) {
        if( $response && (get_class($response) === get_class($this->request)) ) {
            /* If request_handler object then we check if this is a redirect */
            if( $response->redirect_to )
            $response->redirect_header();
        }
        elseif( $response && (get_class($response) === get_class($this->view)) ) {
            if( $response->is_json() ) {
                $response->output_json();
            }
            else
            /* For view object we echo the generated markup */
            // echo $response->get_markup();
            $this->render_html($response->get_markup());
        }
        elseif( is_string($response) ) {
            // echo $response;
            $this->render_html($response);
        }
        else {
            echo "Unknown type" . gettype($response);
        }
    }

    public function render_html($html) {
        $this->app->swoole_response->header("Content-Type", "text/html");
        $this->app->swoole_response->end($html);
    }

    public function show_404($html) {
        $this->app->swoole_response->status(404);
        $this->handle($html);
    }
}