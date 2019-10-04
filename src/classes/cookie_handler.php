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

class Cookie_Handler {
    
    public function session() {
        return null;
    }
    
    public function one_day() {
        return 86400;
    }
    
    public function seven_days() {
        return 604800;
    }
    
    public function thirty_days() {
        return 2592000;
    }
    
    public function six_months() {
        return 15811200;
    }
    
    public function one_year() {
        return 31536000;
    }
    
    public function lifetime() {
        return -1;
    }

    /**
     * Check if cookie with given name exists
     *
     * @param string $name
     * @return bool
     */
    public function exists($name) {
        return isset($_COOKIE[$name]);
    }

    /**
     * Check if cookie with given name is not empty
     *
     * @param string $name
     * @return bool
     */
    public function is_empty($name) {
        return empty($_COOKIE[$name]);
    }

    /**
     * Get the value of the given cookie
     *
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function get($name, $default = null) {
        return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default);
    }

    /**
     * Set a cookie
     *
     * @param string $name
     * @param string $value
     * @param mixed $expiry
     * @param string $path
     * @param string $domain
     * @return bool
     */
    public function set($name, $value, $expiry = -1, $path = '/', $domain = false) {
        $retval = false;
        if (!headers_sent()) {
            if ($domain === false)
            $domain = $_SERVER['HTTP_HOST'];
    
            if ($expiry === -1)
                $expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
            elseif (is_numeric($expiry))
                $expiry += time();
            else
                $expiry = strtotime($expiry);
            
            if( count(explode('.',$domain)) <= 1 ) {
                //work around for localhost
                //by design domain names must have at least two dots otherwise browser will say they are invalid
                $retval = @setcookie($name, $value, $expiry, $path);
            }
            else {
                $retval = @setcookie($name, $value, $expiry, $path, $domain);
            }
            
            if ($retval)
                $_COOKIE[$name] = $value;
            
        }
        return $retval;
    }

    /**
     * Delete a cookie.
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param bool $remove_from_global Set to true to remove this cookie from this request.
     * @return bool
     */
    public function remove($name, $path = '/', $domain = false, $remove_from_COOKIE = true) {
        $retval = false;
        if (!headers_sent()) {
            if ($domain === false)
            $domain = $_SERVER['HTTP_HOST'];
            
            $retval = setcookie($name, '', time() - 3600, $path, $domain);

            if ($remove_from_COOKIE)
            unset($_COOKIE[$name]);
        }
        return $retval;
    }
}