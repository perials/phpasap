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

namespace core\classes;

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

class Html_Builder {
    
    /**
     * creates the stylesheet link
     *
     * creates a stylesheet link using the relative path provided
     * if the provided path is starts with http:// or https:// or second param
     * is false then it is assumed that the provided path is not relative but absolute
     * and is used as it is
     *
     * @param string $stylesheet_path relative path (wrt to project root) of stylesheet
     *                              OR
     *                              absolute path beginning with http:// or https://
     * @param boolean $external if true then first param would be assumed as absolute
     * @param associative array $params $key=>$value pair of parameters to append
     *
     * @return string
     */
    public function style($stylesheet_path='', $external=false, $params=[]) {
        
        $absolute_stylesheet_path = '';
        
        if( strpos($stylesheet_path,'http://') === 0 || strpos($stylesheet_path,'https://') === 0 || $external==true ) {
            $absolute_stylesheet_path = $stylesheet_path;
        }
        else {
            $absolute_stylesheet_path = Route::base_url().'/'.$stylesheet_path;            
        }
        
        $append_params = '';
        if( is_array($params) ) {
            foreach( $params as $key=>$value ) {
                $append_params = $key.' = "'.$value.'"';
            }
            $append_params = ' '.$append_params.' ';
        }
        
        return '<link rel="stylesheet" type="text/css" href="'.$absolute_stylesheet_path.'"'.$append_params.'>';
    }
    
    /**
     * creates script tag
     *
     * same as self::style()
     *
     * @param string $script_path
     * @param boolean $external
     * @param array $params
     *
     * @return string
     */
    public function script($script_path='', $external=false, $params=[]) {
        $absolute_script_path = '';
        
        if( strpos($script_path,'http://') === 0 || strpos($script_path,'https://') === 0 || $external == true ) {
            $absolute_script_path = $script_path;
        }
        else {
            $absolute_script_path = Route::base_url().'/'.$script_path;            
        }
        
        $append_params = '';
        if( is_array($params) ) {
            foreach( $params as $key=>$value ) {
                $append_params = $key.' = "'.$value.'"';
            }
            $append_params = ' '.$append_params.' ';
        }
        
        return '<script src="'.$absolute_script_path.'"'.$append_params.'></script>';
    }
    
    public function img($script_path) {
        return '<script src="'.Route::base_url().'/'.$script_path.'" ></script>';
    }
    
    /**
     * returns anchor tag for provided url and label
     */
    public function link($url='', $text='', $external=false, $params=[]) {
        $absolute_url = '';
        if( strpos($url,'http://') ===0 || strpos($url,'https://') ===0 || $external == true ) {
            $absolute_url = $url;
        }
        else
            $absolute_url = Route::base_url().'/'.$url;
        
        $append_params = '';
        if( is_array($params) ) {
            foreach( $params as $key=>$value ) {
                $append_params .= " " . $key.' = "'.$value.'"';
            }
            //$append_params = ' '.$append_params.' ';
        }
        
        return '<a href="'.$absolute_url.'"'.$append_params.'>'.$text.'</a>';
    }
    
    /**
     * returns absolute url for provided relative url
     */
    public function url($relative_url) {
        return Route::base_url().'/'.$relative_url;
    }
}