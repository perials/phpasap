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
 * This array contains the alias short name of the core and user defined classses
 * Using alias name the non static methods of core classes can be called statically
 *
 * Eg: The below code
 * $model = new Model();
 * $users = $model->query("SELECT * FROM users");
 *
 * can be written as
 *
 * $users = DB::query("SELECT * FROM users");
 *
 * To do the above the Model class has been aliased as DB
 */
return [
    
    /* Core classes. Donot modify these */
    'DB'                => '\core\classes\Model',
    'Config'            => '\core\classes\Config_Loader',
    'Cookie'            => '\core\classes\Cookie_Handler',
    'Session'           => '\core\classes\Session_Handler',
    'Route'             => '\core\classes\Route_Handler',            
    'Request'           => '\core\classes\Request_Handler',
    'View'              => '\core\classes\View_Handler',
    'Form'              => '\core\classes\Form_Builder',
    'HTML'              => '\core\classes\Html_Builder',
    'Validator'         => '\core\classes\Validation_Handler',
    'Mail'              => '\core\classes\Mail_Handler',
    'PA_Exception'      => '\core\classes\PA_Exception',
];