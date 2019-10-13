<?php
namespace phpasap\classes;

abstract class Request {
    /*
     * check if ajax request
     * returns true if ajax else returns false
     */
    abstract public function ajax();
    
    /**
     * Fetches the provided $_GET param
     *
     * @param string
     */
    abstract public function get($param);
    
    /**
     * Fetches the provided $_POST param
     *
     * @param string
     */
    abstract public function post($param);
    
    /**
     * Fetches the provided $_REQUEST param
     *
     * @param string
     */
    abstract public function fetch($param);
    
    abstract public function all();
    
    /**
     * check if given url is same as current url
     *
     * @param string $url url to check
     *
     * @return boolean
     */
    abstract public function is($url_to_check);

    abstract public function method();

    public function get_app() {
        return $this->app;
    }

    /**
	 * get base url for current app
	 *
	 * Will work even if installed in sub folder or as subdomain
	 *
	 * @return string
	 */
	public static function base_url() {
		if (Config::get('app.swoole_server') && Config::get('app.swoole_base_url')) {
			return Config::get('app.swoole_base_url');
		}

		$protocol = "http";
		if( isset($_SERVER['HTTPS'] ) ) {
			$protocol = "https";
		}

		$port = "";
		if ($_SERVER["SERVER_PORT"] !== 80) {
			$port = ":" . $_SERVER["SERVER_PORT"];
		}
        return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.implode('/',array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1));
    }
}