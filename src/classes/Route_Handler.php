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

class Route_Handler {

	public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
	}
	
	public function get_request() {
		return $this->app->request;
	}
    
	/**
	 * adds given route rule to rotues array
	 *
	 * @param string $http_verb the HTTP verb to handle eg: GET, POST
	 * 								OR
	 * 								string CONTROLLER for letting a controller
	 * 								handle all request to the specified url
	 * @param string $url_segment the url segment to handle
	 * @param mixed $controller_method @ seperated Controller and method name that will
	 * 								handle the request eg Home_Controller@welcome
	 * 								OR
	 * 								a closure eg function() { echo "Welcome"; }
	 * @param array $additonal_params to be implemented
	 */
    public function add($http_verb, $url_segment, $controller_method, $additonal_params=[]) {
        $this->routes_array[] = [$http_verb, $url_segment, $controller_method, $additonal_params=[]];
    }
	
	public function dispatch() {
		$route_callback_array = []; // This will hold list of matching routes
		$request_uri_array = $this->get_request()->get_request_uri_array();
		foreach( $this->routes_array as $route_array ) {
			
			$current_request_uri = $this->get_request()->get_request_uri();
			
			/* Check if HTTP verb matches current request */
			if( $route_array[0] != 'CONTROLLER' && $route_array[0] != 'ANY' && $this->get_request()->method() != $route_array[0] )
			continue;

			// handle global middlewares; app.use case
			if( $route_array[1] === true ) {
				$route_callback_array[] = $this->get_route_callback($route_array,[]);
				continue;
			}
			
			/* This will contain the route parameters to be captures and sent back as controller method arguments */
			$variables = array();
			
			$route_url_as_array = array_filter(explode('/',$route_array[1]));
						
			if( $route_array[0] == 'CONTROLLER') {
				if( count($route_url_as_array) > count($request_uri_array) ) {
					continue;
				}
			}
			elseif( count($route_url_as_array) != count($request_uri_array) )
			continue;
			
			$match_occured = true;
			for( $i=0; $i<count($route_url_as_array); $i++ ) {
				preg_match_all('/{(.*?)}/', $route_url_as_array[$i], $matches); //get all {} variables
				
				if( $route_url_as_array[$i] == $request_uri_array[$i] ) {
					
				}
				elseif( $route_array[0] != 'CONTROLLER' && !empty($matches[0]) ) {
					$variables[] = $request_uri_array[$i];
				}
				else {
					$match_occured = false;
					continue;
				}
			}
			
			if(!$match_occured) continue;
			
			if( $route_array[0] == 'CONTROLLER' ) {
				$variable_start_index = count($request_uri_array) - count($route_url_as_array);
				if( $variable_start_index > 0 ) {
					for( $i=count($route_url_as_array); $i <= ( count($request_uri_array)-1 ); $i++ ) {
						$variables[] = $request_uri_array[$i];
					}
				}
			}
						
			// return $this->get_route_callback($route_array,$variables);
			$route_callback_array[] = $this->get_route_callback($route_array,$variables);
			
		}
		return $route_callback_array;
	}
	
	public function get_route_callback($route_array,$variables) {
		$return_route_match_array = array();
		if( $route_array[2] instanceof \Closure ) { //check if third param is function
			$return_route_match_array['is_closure'] = true;
			$return_route_match_array['closure'] = $route_array[2];
			$return_route_match_array['params'] = $variables;
		}
		else {
			$return_route_match_array['is_closure'] = false;
			
			if( $route_array[0] == 'CONTROLLER' ) {
				$return_route_match_array['controller'] = $route_array[2];
				$method = isset($variables[0]) ? $variables[0] : 'index'; 
				// $return_route_match_array['method'] = strtolower($_SERVER['REQUEST_METHOD']). '_' . str_replace('-', '_', $method);
				$return_route_match_array['method'] = strtolower($this->request->method()). '_' . str_replace('-', '_', $method);
				array_shift($variables);
				$return_route_match_array['params'] = empty( $variables ) ? [] : $variables;
			}
			else {
				// $controller_method = explode('@',$route_array[2]);
				$controller_method = $route_array[2];
				$return_route_match_array['controller'] = $controller_method[0];
				$return_route_match_array['method'] = $controller_method[1];
				$return_route_match_array['params'] = $variables;	
			}
		}
		return $return_route_match_array;
	}
	
	public function show_404($view=false, $terminate_script=true) {
		header("HTTP/1.1 404 Not Found");
		if( $view && is_object($view) && is_a($view, 'core\classes\View_Handler') ) {
			echo $view->get_markup();			
		}
		elseif( is_string($view) ) {
			echo $view;
		}
		if($terminate_script)
		die;
	}
    
}