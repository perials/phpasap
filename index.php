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

/* We now register our autoloader and include all the required files */
require 'bootstrap.php';

use core\classes\View_Hander;
use core\alias\Request;
use core\alias\Validator;
use core\alias\Session;
use core\alias\Db;

/* Create new app instance */
$app = core\classes\App::get_instance();

$app->register('view', function() {
    return new core\classes\View_Hander();
});

$app->register('request', function() {
    return new core\classes\Request_Hander();
});

$app->register('validator', function() {
    return new core\classes\Validation_Hander();
});

$app->register('session', function() {
    return new core\classes\Session_Handler();
});

$app->register('db', function() {
    return new core\classes\Model();
});

/* Map the current request with routing array and capture is any match occurs */
$app->map();

/* If match occurs the appropriate controller method will be called with passed arguments */
$app->dispatch();