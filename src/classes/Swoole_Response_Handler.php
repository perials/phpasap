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

class Swoole_Response_Handler extends Response_Handler {

    public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
    }
    
    public function handle($response) {
        if( $response && is_object($response) && (get_class($response) === get_class($this)) ) {
            /* If request_handler object then we check if this is a redirect */
            if( $response->redirect_to ) {
                $response->redirect_header();
            }
            elseif ($response->is_json() ) {
                $response->output_json();
            }
            else {
                /* For view object we echo the generated markup */
                // echo $response->get_markup();
                $this->render_html($response->get_markup());
            }
        }
        elseif( is_string($response) ) {
            // echo $response;
            $this->render_html($response);
        }
        else {
            throw new \Exception("Unknown type " . gettype($response));
        }
    }

    public function redirect_header() {
        $this->app->swoole_response->redirect($this->redirect_to);
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