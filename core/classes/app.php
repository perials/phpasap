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

namespace core\classes;

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

class App {
    
    /**
     * for singleton use
     */
    private static $instance = null;
    
    public function __construct() {
       
        //Conditionally sets debug mode 
        $this->debug_mode();
        
        //Save version of PHP 
        $this->php_version = $this->get_php_version();
        
        //Start the session. Session is required by our core Session_Handler class
        $this->start_session();        
    }
    
    protected function get_env() {
        
    }
    
    /**
     * Sets debug mode if debug set to true in config
     */
    private function debug_mode() {
        
        //Check if debug variable set in app.php config file
        if( Config::get('app.debug') === true ) {            
            //Set error reporting to true
            error_reporting(E_ALL);
            ini_set('display_errors', 1);            
        }
        else {
            error_reporting(0);
        }
    }
    
    /**
     * starts session if not started already
     * depending upon the version of php checks if already session has started
     */
    public function start_session() {
        if($this->php_version >= 5.4) {
            // This is how we check for session started or not as of 5.4
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
        else {
            /**
             * FIXME Support for < 5.4 should be removed
             * we are using [] arrays which is not supported by ver < 5.4
             * so below is not needed in first place
             * So if php ver < 5.4 then raise an exception
             */
            
            // Old way (PHP < 5.4) of checking if session has started or not
            if(session_id() == '') {
                session_start();
            }
        }
    }
    
    /*
     * returns current version of PHP on server
     */
    public function get_php_version() {
        return (float)phpversion();
    }
    
    /*
     * loads the routes files and checks for a match against current request
     */
    public function map() {
        $this->load('app\routes.php');
        $this->controller = Route::dispatch();
    }
    
    /*
     * includes files without needing to pass the entire path
     * also takes care of the directory separtor
     *
     * @param string $file_path path of the file relative to project root
     * Eg App::load('controller/home.php')
     */
    public function load($file_path) {
        include ROOT . DS . str_replace(['\\','/'], DS, $file_path );
    }
    
    /*
     * call the controller method using the response received from router
     */
    public function dispatch() {
        if( empty($this->controller) ) {
            Route::show_404(View::make(Config::get('app.404','modules/404')));
        }
        
        if( $this->controller['is_closure'] ) {
            //instead of calling the closure directly we use call_user_func_array so we can
            //pass captured variables if any to the closure
            $response = call_user_func_array( $this->controller['closure'], $this->controller['params']);
        }
        else {
            
            $controller = "app\\controllers\\".$this->controller['controller'];
                        
            if( !class_exists($controller) ) {
                show_error('Class app\\controllers\\'. $this->controller['controller'].' does not exists',true);
            }
            
            global $controller_instance;
            $controller_instance = new $controller;
            
            if( !method_exists( $controller_instance, $this->controller['method'] ) ) {
                show_error('Controller method doesn\'t exists',true);
            }
            
            /* call the controller method with passed arguments and capture returned response */
            $response = call_user_func_array( array( $controller_instance, $this->controller['method']), $this->controller['params'] );
                        
        }
        
        /* Now handle the response */
        if( $response instanceof Request_Handler ) {
            /* If request_handler object then we check if this is a redirect */
            if( $response->redirect_to )
            $response->redirect_header();
        }
        elseif( $response instanceof View_Handler ) {
            if( $response->is_json() ) {
                $response->output_json();
            }
			else
            /* For view object we echo the generated markup */
            echo $response->get_markup();
        }
        elseif( is_string($response) ) {
            echo $response;
        }        
        
        if( Config::get('app.db_profiler') == true ) {
            $profiler_array = [];
            foreach( DB::get_active_connections() as $db_obj ) {
                $query_array = $db_obj->sel("show profiles",[]);
                foreach($query_array as $row) {
                    //$profiler_array[] = ['Query'=>$row->Query, 'Duration'=>$row->Duration];
                    $profiler_array[] = "<b>Query:</b> ".$row->Query."<br/><b>Duration:</b> ".$row->Duration;
                }
            }
            echo implode("<br/><hr/>", $profiler_array);
        }
    }
    
    public static function get_instance() {
        if( !isset(self::$instance) ) self::$instance = new App();
        return self::$instance;
    }
    
}