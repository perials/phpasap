<?php
namespace phpasap\classes;

interface Request {
    /*
     * check if ajax request
     * returns true if ajax else returns false
     */
    public function ajax();
    
    /**
     * Fetches the provided $_GET param
     *
     * @param string
     */
    public function get($param);
    
    /**
     * Fetches the provided $_POST param
     *
     * @param string
     */
    public function post($param);
    
    /**
     * Fetches the provided $_REQUEST param
     *
     * @param string
     */
    public function fetch($param);
    
    public function all();
    
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
    public function redirect_to($url,$hard_redirect);
    
    public function redirect_header();
    
    public function with($flash_data_array);
    
    public function with_inputs();
    
    /**
     * check if given url is same as current url
     *
     * @param string $url url to check
     *
     * @return boolean
     */
    public function is($url_to_check);

    public function method();
}