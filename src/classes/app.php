<?php
/**
 * App Class
 *
 * The App class instance is created by bootstrap file. This instance sets up the
 * debug mode conditionally based on user configuration
 * It also calls the Route_Handler class instance which then decides which
 * Controller method to be called depending upon the current HTTP request and
 * user set routing rules
 * 
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

class App {
    
    /**
     * for singleton use
     */
    private static $instance = null;

    private $route_index = 0; // current middleware index to run
    
    public static $lazy_load_properties = [];
    public static $route_array = [];
    
    public function __construct() {
        // Conditionally sets debug mode 
        $this->debug_mode();
        
        // Save version of PHP 
        $this->php_version = $this->get_php_version();
        
        // Start the session. Session is required by our core Form_Handler class
        // $this->session->start_session();
        
        $this->set_error_handler();
    }
    
    public static function register($key, $closure_callback) {
        self::$lazy_load_properties[$key] = $closure_callback;
    }
    
    public function __get($property) {
        if (isset(self::$lazy_load_properties[$property])) {
            $this->{$property} = self::$lazy_load_properties[$property]($this);
            return $this->{$property};
        }
        return null;
    }

    public function clear() {
        foreach(array_keys(self::$lazy_load_properties) as $property) {
            if (isset($this->{$property})) {
                unset($this->{$property});
            }
        }

        // we are using closures
        // setting controller property to null is required for properly destructing $app variable
        $this->controller = null;
    }
    
    /**
     * Set our custom error handler
     */
    private function set_error_handler() {
        set_error_handler('pa_error_handler',E_ALL);
    }
    
    protected function get_env() {
        
    }
    
    /**
     * Sets debug mode if debug set to true in config
     */
    private function debug_mode() {
        
        //Check if debug variable set in app.php config file
        if( $this->config->get('app.debug') === true ) {
            //Set error reporting to true
            error_reporting(E_ALL);
            ini_set('display_errors', 1);            
        }
        else {
            error_reporting(0);
        }
    }
    
    /*
     * returns current version of PHP on server
     */
    public function get_php_version() {
        return (float)phpversion();
    }
    
    private function check_php_version_support() {
        if( $this->php_version < 5.4 ) {
            throw new Pa_Exception("PHP version not supported", 8888);
        }
    }

    public static function get($url, $callback) {
        self::$route_array[] = ['GET', $url, $callback];
        // $this->route->add('GET', $url, $callback);
    }

    public static function controller($url, $callback) {
        self::$route_array[] = ['CONTROLLER', $url, $callback];
        // $this->route->add('CONTROLLER', $url, $callback);
    }

    public static function use($callback) {
        self::$route_array[] = ['ANY', true, $callback];
    }
    
    /*
     * loads the routes files and checks for a match against current request
     */
    public function map() {
        // Check if using PHP version > 5.4
        // We are not checking this in constructor because it won't be captured in try catch 
        $this->check_php_version_support();

        foreach(self::$route_array as $route_array) {
            $this->route->add($route_array[0], $route_array[1], $route_array[2]);
        }

        $this->controller_array = $this->route->dispatch();
    }
    
    /*
     * call the controller method using the response received from router
     */
    public function dispatch() {
        try {
            if( empty($this->controller_array) ) {
                return $this->response->show_404($this->view->make($this->config->get('app.404','modules/404')));
            }

            if( !isset($this->controller_array[$this->route_index]) ) {
                return $this->response->show_404($this->view->make($this->config->get('app.404','modules/404')));
            }

            $this->controller = $this->controller_array[$this->route_index];
            $this->route_index++;

            array_push($this->controller['params'], function() {
                $this->dispatch();
            });
            
            if( $this->controller['is_closure'] ) {
                //instead of calling the closure directly we use call_user_func_array so we can
                //pass captured variables if any to the closure
                array_unshift($this->controller['params'], $this);
                $response = call_user_func_array( $this->controller['closure'], $this->controller['params']);
            }
            else {
                
                $controller = "app\\controllers\\".$this->controller['controller'];
                            
                if( !class_exists($controller) ) {
                    show_error('Class app\\controllers\\'. $this->controller['controller'].' does not exists',true);
                }
                
                $controller_instance = new $controller($this);
                
                if( !method_exists( $controller_instance, $this->controller['method'] ) ) {
                    show_error('Controller method doesn\'t exists',true);
                }
                
                // call the controller method with passed arguments and capture returned response
                $response = call_user_func_array(
                    array( $controller_instance, $this->controller['method']),
                    $this->controller['params']
                );
                
                unset($controller_instance);
            }
            
            // Now handle the response
            if($response) {
                $this->response->handle($response);
            }
        }
        catch( Pa_Exception $e ) {
            error_log($e->errorMessage());
            $this->response->handle($e->errorMessage());
        }
        catch( \Exception $e ) {
            error_log($e->getMessage());
            $this->response->handle($e->getMessage());
        }
    }
    
    public static function get_instance($new = false) {
        if ($new) return new App();

        if( !isset(self::$instance) ) self::$instance = new App();
        return self::$instance;
    }
    
}
