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

class Request_Handler extends Request {
    private $request_uri = '';
	private $routes_array = array();
	
	/**
	 * get url of current page
	 *
	 * @return string
	 */
	public function get_current_url() {
		//note that we haven't added a backslash after base_url since get_request_uri has
		//a backslash prepended to it
		return self::base_url().$this->get_request_uri();
	}

    public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
    }
    
    /*
     * check if ajax request
     * returns true if ajax else returns false
     */
    public function ajax() {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
            return true;
        else
            return false;
    }
    
    /**
     * Fetches the provided $_GET param
     *
     * @param string
     */
    public function get($param=null) {
        if($param==null)
        return $_GET;
        
        if( isset( $_GET[$param] ) )
        return $_GET[$param];
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
        return $_POST;
        
        if( isset( $_POST[$param] ) )
        return $_POST[$param];
        else
        return false;
    }
    
    /**
     * Fetches the provided $_REQUEST param
     *
     * @param string
     */
    public function fetch($param) {
        if( isset( $_REQUEST[$param] ) )
        return $_REQUEST[$param];
        else
        return false;
    }
    
    public function all() {
        return $_REQUEST;
    }
    
    /**
     * FIXME deprecate this as this is already there in HTML::url_to()
     */
    public function url_to($url) {
        return self::base_url().'/'.rtrim($url, '/');
    }
    
    /**
     * check if given url is same as current url
     *
     * @param string $url url to check
     *
     * @return boolean
     */
    public function is($url_to_check='') {
        if( HTML::url($url_to_check) == $this->route->get_current_url() )
            return true;
        else
            return false;
    }

    public function method() {
        return $_SERVER['REQUEST_METHOD'];
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
		return $_SERVER['REQUEST_URI'];
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