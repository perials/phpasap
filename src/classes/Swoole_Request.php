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

class Swoole_Request extends Request {

    public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
    }
    
    /*
     * check if ajax request
     * returns true if ajax else returns false
     */
    public function ajax() {
        throw new Pa_Exception('Not supported in Swoole version');
    }
    
    /**
     * Fetches the provided $_GET param
     *
     * @param string
     */
    public function get($param=null) {
        if($param==null)
        return $this->app->swoole_request->get;
        
        if( isset( $this->app->swoole_request->get[$param] ) )
        return $this->app->swoole_request->get[$param];
        else
        return false;
    }
    
    /**
     * Fetches the provided $_POST param
     *
     * @param string
     */
    public function post($param=null) {
        if($param==null)
        return $this->app->swoole_request->post;
        
        if( isset( $this->app->swoole_request->post[$param] ) )
        return $this->app->swoole_request->post[$param];
        else
        return false;
    }
    
    /**
     * Fetches the provided $_REQUEST param
     *
     * @param string
     */
    public function fetch($param) {
        if($this->app->swoole_request->server['request_method'] === 'GET') {
            return $this->get($param);
        }
        return $this->post($param);
    }
    
    public function all() {
        if($this->app->swoole_request->server['request_method'] === 'GET') {
            return $this->get();
        }
        return $this->post();
    }
    
    /**
     * FIXME deprecate this as this is already there in HTML::url_to()
     */
    public function url_to($url) {
        return self::base_url().'/'.rtrim($url, '/');
    }
    
    /**
     * set the redirect_to property
     *
     * @param string $url relative url
     * @param boolean $hard_redirect if false then Request handler object is returned
     *                  if true then Location header is set and script is terminated
     *
     * @return mixed if $hard_redirect then void
     *                  if not $hard_redirect then current object
     */
    public function redirect_to($url,$hard_redirect=false) {
        if( strpos($url, "http://") === 0 || strpos($url, "https://") === 0 ) {
            $this->redirect_to = $url;
        }
        else
            $this->redirect_to = self::base_url().'/'.rtrim($url, '/');
        if( $hard_redirect === true ) {
            $this->redirect_header();    
        }
        else {
            return $this;
        }
    }
    
    public function redirect_header() {
        $this->app->swoole_response->redirect($this->redirect_to);
    }
    
    public function with($flash_data_array) {
        $this->session->flash($flash_data_array);
        return $this;
    }
    
    public function with_inputs() {
        $this->session->flash($this->all());
        return $this;
    }
    
    /**
     * check if given url is same as current url
     *
     * @param string $url url to check
     *
     * @return boolean
     */
    public function is($url_to_check='') {
        if( $this->html->url($url_to_check) == $this->route->get_current_url() )
            return true;
        else
            return false;
    }

    public function method() {
        return $this->app->swoole_request->server['request_method'];
    }
    
    /**
	 * Returns base path of current url
	 * Eg for http://some-domain.com/folder-1/folder-2/index.php will give /folder-1/folder-2
	 * for http://localhost/phpasap/index.php/docs/routes will return phpasap/ (project root is www/phpasap and web root is www)
	 * for http://localhost/phpasap/docs/routes will return phpasap/ (project root is www/phpasap and web root is www)
	 */
	public function get_base_path() {
		// return $this->base_path = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		return implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
	}

	public function get_server_request_uri() {
		return $this->app->swoole_request->server['request_uri'];
	}
    
    /**
	 * The following function will strip the script name from URL i.e.  http://www.something.com/search/book/fitzgerald will become /search/book/fitzgerald
	 * http://localhost/phpasapraw/docs/routes will return /docs/routes (project root is www/phpasap and web root is www)
	 */
	public function get_request_uri() {
		$uri = substr($this->get_server_request_uri(), strlen($this->get_base_path()));
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		return $this->request_uri = '/' . trim($uri, '/');
		//return $this->request_uri = trim($uri, '/');
	}
	
	/**
	 * returns request_uri in array
	 * http://www.something.com/search/book/java will return array(0=>search,1=>book,2=>java)
	 */
	public function get_request_uri_array() {
		$base_url = $this->get_request_uri();
		$request_uri_array = array();
		$array = explode('/', $base_url);
		foreach( $array as $route ) {
			if(trim($route) != '')
				array_push($request_uri_array, $route);
		}
		return $request_uri_array;
	}
}