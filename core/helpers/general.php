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
    return "<div style='background-color: #fff; color: #EF6767; padding: 13px; border: 1px solid #eee; border-left: 4px solid #EF6767; font-family: Consolas, monaco, monospace; font-size: 14px; margin-bottom:15px;'>$error_msg</div>";
}

function pa_error_handler($number, $string, $file, $line, $context) {
	$message = "<strong>$string</strong><br/>
    File: $file <br/>
    Line: $line <br/>
    Level: ".error_level_to_string($number);
    show_error($message);
}

function error_level_to_string($intval, $separator = ',') {
    $error_levels = array(
        E_ALL => 'E_ALL',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        E_DEPRECATED => 'E_DEPRECATED',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_STRICT => 'E_STRICT',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_NOTICE => 'E_NOTICE',
        E_PARSE => 'E_PARSE',
        E_WARNING => 'E_WARNING',
        E_ERROR => 'E_ERROR');
    $result = '';
    foreach($error_levels as $number => $name)
    {
        if (($intval & $number) == $number) {
            $result .= ($result != '' ? $separator : '').$name; }
    }
    return $result;
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