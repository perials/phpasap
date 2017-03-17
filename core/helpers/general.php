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

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

/**
 * generate random string
 *
 * generates a alphanumeric random string of provided length. If no length is provided then
 * by default string of 20 characters are returned
 *
 * @param int $length no of characters to be returned in the random string
 *
 * @return string
 */
function generate_random_string($length=20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

/**
 * wraps raw text error string into css classes and html divs for display purpose
 *
 * echo's the provided error string after wrapping it in html tags and applying inline
 * css rules for display purpose
 *
 * @param string $error_msg any string message
 * @param boolean $truncate_script if true then further execution of program is terminated
 *
 * @return void
 */
function show_error($error_msg, $truncate_script=false) {
    echo "<p style='background-color: #f7f7f7; color: #EF6767; padding: 13px; border-left: 4px solid #EF6767; border-bottom: 1px solid #ddd;'>$error_msg</p>";
    
    if($truncate_script) die;
}

/**
 * var dumps the provided variable after wrapping in a pre tag
 *
 * @param mixed $variable
 */
function dd($variable, $truncate_script=false) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    
    if($truncate_script)
    die;
}