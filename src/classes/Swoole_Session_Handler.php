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

class Swoole_Session_Handler extends Session_Handler {

    /**
     * starts session if not started already
     * depending upon the version of php checks if already session has started
     */
    public function start_session() {
        // if Session has not started then start it
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    
        if (isset($this->app->swoole_request->cookie[session_name()])) {
            // Client has session cookie set, but Swoole might have session_id() from some
            // other request, so we need to regenerate it
            session_id($this->app->swoole_request->cookie[session_name()]);
        } else {
            $params = session_get_cookie_params();
            $unique_id = session_id();
    
            if (session_id()) {
                $unique_id = \bin2hex(\random_bytes(32));
                session_id($unique_id);
            }

            // Clear session variable
            $_SESSION = [];
    
            $this->app->swoole_response->rawcookie(
                session_name(),
                $unique_id,
                $params['lifetime'] ? time() + $params['lifetime'] : null,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
    }
}