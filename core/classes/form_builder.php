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

// use core\alias\Session;

//Deny direct access
if( !defined('ROOT') ) exit('Cheatin\' huh');

class Form_Builder {
    use Loader;

    public function __construct(&$app) {
        $this->app = $app;
    }
    
    public function input($name=null, $default=null, $attr_assoc_array=array()) {
        if($default===null) {
            if( $this->session->get($name) )
            $default = $this->session->get($name);
        }
        $attributes_to_append = '';
        foreach($attr_assoc_array as $key=>$value) {
            $attributes_to_append .= "$key='$value' ";
        }
        return "<input type='text' name='$name' value='$default' $attributes_to_append>";
    }
    
    public function date($name=null, $default=null, $attr_assoc_array=array()) {
        if($default===null) {
            if( $this->session->get($name) )
            $default = $this->session->get($name);
        }
        $attributes_to_append = '';
        foreach($attr_assoc_array as $key=>$value) {
            $attributes_to_append .= "$key='$value' ";
        }
        return "<input type='date' name='$name' value='$default' $attributes_to_append>";
    }
    
    public function pass($name=null, $default=null, $attr_assoc_array=array()) {
        $attributes_to_append = '';
        foreach($attr_assoc_array as $key=>$value) {
            $attributes_to_append .= "$key='$value' ";
        }
        return "<input type='password' name='$name' value='$default' $attributes_to_append>";
    }
    
    public function textarea($name=null,$default=null,$attr_assoc_array=array()) {
        if($default===null) {
            if( $this->session->get($name) )
            $default = $this->session->get($name);
        }
        $attributes_to_append = '';
        foreach($attr_assoc_array as $key=>$value) {
            $attributes_to_append .= "$key='$value' ";
        }
        return "<textarea name='$name' $attributes_to_append>$default</textarea>";
    }
    
    public function select( $name=null, $options, $default=null, $attr_assoc_array=array() ) {
        if($default===null) {
            if( $this->session->get($name) )
            $default = $this->session->get($name);
        }
        $attributes_to_append = '';
        foreach($attr_assoc_array as $key=>$value) {
            $attributes_to_append .= "$key='$value' ";
        }
        $return_markup = "";
        foreach( $options as $key=>$value ) {
            if( $key == $default )
            $selected = "selected";
            else
            $selected = "";
            $return_markup .= "<option value='$key' $selected>$value</option>";
        }
        return "<select name='$name' $attributes_to_append>$return_markup</select>";
    }
    
    public function checkbox($name=null, $cb_value="", $default=null, $attr_assoc_array=array()) {
        if($default===null) {
            $default = false;
            
            if($this->session->get($name) == $cb_value)
            $default = true;
        }
        
        $attributes_to_append = '';
        foreach($attr_assoc_array as $key=>$value) {
            $attributes_to_append .= "$key='$value' ";
        }
        
        if($default)
        $attributes_to_append .= "checked ";
        
        return "<input type='checkbox' name='$name' value='$cb_value' $attributes_to_append >";
    }
    
}