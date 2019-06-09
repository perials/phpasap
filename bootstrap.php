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

$env = include 'env.php';
define('ENVIRONMENT',$env);

// Directory separator is set up here because separators are different on Linux and Windows operating systems
define('DS', DIRECTORY_SEPARATOR);

// constant ROOT will contain the path to base dir and will be used for including other files
define('ROOT', dirname(__FILE__));

// All files in the below directories will be required_once
// These directories should contains helper functions and preferably NO classes
// Path should be relative to project root
$helper_directories = [
    'core' . DS . 'helpers',     
    'app' . DS . 'helpers',     
];

// One by one include all files in all helper directories
foreach ($helper_directories as $dir) {
    $dir_files = glob(ROOT . DS . $dir . DS . '*.php');
    foreach ($dir_files as $file) {
        require_once($file);
    }
}

// Register our autoload function
spl_autoload_register('autoload_files');
function autoload_files($class_name) {
    $namespaced_dir_array = explode(DS, str_replace(['/','\\'], DS, $class_name));
    
    // if it doesn't starts with app or core then don't proceed further
    if( !in_array($namespaced_dir_array[0], ['app', 'core']) ) {
        return;
    }
    
    if( is_readable( strtolower(str_replace(["/","\\"], DS, ROOT . DS . $class_name).'.php') ) ) {
        require strtolower(str_replace(["/","\\"], DS, $class_name).'.php');
    }
}

// set default time zone
date_default_timezone_set('UTC');

// provide support for composer if some one uses it
if( file_exists(ROOT. DS . 'vendor' . DS . 'autoload.php') ) {
    require ROOT. DS . 'vendor' . DS . 'autoload.php';
}
