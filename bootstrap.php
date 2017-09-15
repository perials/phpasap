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

date_default_timezone_set('UTC');

/* Directory separator is set up here because separators are different on Linux and Windows operating systems */
define('DS', DIRECTORY_SEPARATOR);

/* constant ROOT will contain the path to base dir and will be used for including other files */
define('ROOT', dirname(__FILE__));

//Deny direct access
//if( !defined('ROOT') ) exit('Cheatin\' huh');

/* This contains the directories where autoloader will search into when a class is found */
$directories_to_autoload_classes = [
                            'core' . DS . 'classes',
                            'application' . DS . 'controllers',
                            'application' . DS . 'models',
                            'application' . DS . 'libraries',
                            ];

/* This contains all the files containing functions that will be included */
$directories_to_include_files = [
                            'core' . DS . 'helpers',     
                            'app' . DS . 'helpers',     
                                ];

/* One by one include all files in all include directories */
foreach ($directories_to_include_files as $dir) {
    $dir_files = glob(ROOT . DS . $dir . DS . '*.php');
    foreach ($dir_files as $file) {
        require($file);   
    }
}

/* load the alias array */
$alias = (include ROOT . DS . 'core' . DS . 'config' . DS . 'alias.php');

/* include the alias loader */
include ROOT . DS . 'core' . DS . 'classes' . DS . 'alias_loader.php';

/* Register our autoload function */
spl_autoload_register('autoload_files');
function autoload_files($class_name) {
    
    global $alias;
    
    $namespaced_dir_array = explode(DS, str_replace(['/','\\'], DS, $class_name));
    $class_name_without_ns = end($namespaced_dir_array);
    
    /* fist check if any alias with given name exists */
    if(
        !empty($alias[$class_name]) //if directly referenced without namespace
        ||
        (
            //check if called through core or app namespace
            (
                strpos(str_replace(['/','\\'], DS, $class_name), "core".DS."classes") === 0
                ||
                strpos(str_replace(['/','\\'], DS, $class_name), "app".DS) === 0
            ) &&
            !empty($alias[$class_name_without_ns])
        )
       ) {
        
        /* since our aliased classes can be called from different namespace like
         *  app/Controller/Request
         *  core/Classes/Request
         * in all cases we'll be creating a class without namespace in global namespace
         *  for above two example Request will be created in global namespace
         * so we firt check if aliased class exists and only then we go ahead and create new one
         */
        if( !class_exists($class_name_without_ns) )
        eval("class {$class_name_without_ns} extends Alias_Loader { public static function get_my_class_name() {return \"$class_name_without_ns\";} }");
        
        //workaround for namespace
        if( $class_name !== $class_name_without_ns )
        class_alias($class_name_without_ns,$class_name);
        
        return;        
    }
    elseif( file_exists( strtolower(str_replace(["/","\\"], DS, ROOT . DS . $class_name).'.php') ) ) {
        require strtolower(str_replace(["/","\\"], DS, $class_name).'.php');
    }
}

/**
 * provide support for composer if some one uses it
 */
if( file_exists(ROOT. DS . 'vendor' . DS . 'autoload.php') ) {
    require ROOT. DS . 'vendor' . DS . 'autoload.php';
}