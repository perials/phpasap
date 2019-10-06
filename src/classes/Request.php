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
     * check if given url is same as current url
     *
     * @param string $url url to check
     *
     * @return boolean
     */
    public function is($url_to_check);

    public function method();
}