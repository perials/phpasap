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

class Validation_Handler {

    /*
     * the error message array which will be set when validation fails
     */
    protected $errors = array();

    /*
     * the validation rules array
     */
    protected $validation_rules = array();
    
    /*
     * pretty field names to be displayed on validation error
     */
    protected $alt_field_labels = array();
    
    protected $caller_obj = false;
     
    /*
     * the source 
     */
    private $source = array();
    
    public function reset() {
		$this->errors = [];
		$this->validation_rules = [];
		$this->alt_field_labels = [];
		$this->caller_obj = false;
	}
    
    public function validate($source, $rules_array, $caller_obj=false) {
        $this->reset();
        $this->add_source($source);
        $this->add_rules($rules_array);
        $this->caller_obj = $caller_obj;
        $this->run();
        if( $this->has_errors() )
            return false;
        else
            return true;
    }
    
    public function has_errors() {
        if( empty($this->errors) )
            return false;
        else
            return true;
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public function set_error($field_name, $error_msg) {
        if(isset($this->alt_field_labels[$field_name]))
        $error_msg = str_replace("{label}", $this->alt_field_labels[$field_name], $error_msg);
        $this->errors[$field_name] = $error_msg;
    }

    /*
     * add source
     * @param array $source
     */
    public function add_source($source) {
        $this->source = $source;
    }


    /*
     * the validation rules
     */
    public function run() {
        
        foreach( $this->validation_rules as $var=>$opt) {
            
            /*
            $default_opt = [
                            'min' => false,
                            'max' => false,
                            'required' => false,
                            ];
            $opt = array_merge($default_opt,$opt);
            */
                        
            //if compulsary field is not set then no point validating further
            if(isset($opt['set']) && !$this->is_set($var)) {
                continue;
            }            
            
            /* Trim whitespace from beginning and end of variable */
            if( array_key_exists('trim', $opt) ) {
                $this->source[$var] = trim( $this->source[$var] );
            }
            
            $is_required = false;
            
            //if required field is empty then no point validating further
            if( isset($opt['required']) && !$this->not_empty($var) ) {
                continue;
            }
            
            if( isset($opt['required']) ) {
                unset($opt['required']);
                $is_required = true;
            }
            
            if( isset($opt['set']) )
            unset($opt['set']);
            
            if( isset($opt['trim']) )
            unset($opt['trim']);
            
            foreach( $opt as $rule_type=>$rule_val ) {
                $raw_field_value = isset($this->source[$var]) ? $this->source[$var] : false;
                
                /*
                if(is_string($raw_field_value) && strlen($raw_field_value) == 0 && !$is_required)
                continue;
                */
                $result = true;
                if( method_exists($this, 'validate_'.$rule_type) ) {
                    $t_params = is_array($rule_val) ? array_merge([$var], $rule_val) : [$var, $rule_val];
                    //$result = call_user_func_array([$this, 'validate_'.$rule_type], [$var, $rule_val]);
                    $result = call_user_func_array([$this, 'validate_'.$rule_type], $t_params);
                }
                elseif( method_exists($this->caller_obj, $rule_type) ) {
                    $t_params = is_array($rule_val) ? array_merge([$this, $raw_field_value], $rule_val) : [$this, $raw_field_value, $rule_val];
                    $result = call_user_func_array([$this->caller_obj, $rule_type], $t_params);
                }
                elseif( function_exists($rule_type) ) {
                    $t_params = is_array($rule_val) ? array_merge([$this, $raw_field_value], $rule_val) : [$this, $raw_field_value, $rule_val];
                    $result = call_user_func_array($rule_type, $t_params);
                }
                
                if($result === false)
                break;
            }
        }
    }

    /**
     * Add multiple rules to the validation rules array
     *
     * @param array $rules_array The array of rules to add
     */
    public function add_rules(array $var_rules_array) {
        
        $rules_array = [];
        foreach( $var_rules_array as $var=>$rules_string ) {
            
            $rules_array[$var] = [];
            
            if(is_array($rules_string)) {
                $this->alt_field_labels[$var] = $rules_string[1];
                $rules_string = $rules_string[0];
            }
            
            $t_rules_array = explode('|', $rules_string);
            foreach( $t_rules_array as $rule ) {                
                $rule = explode(':', $rule, 2);
                
                if(isset($rule[1])) {
                    $t_rule = $rule;
                    array_shift($t_rule);
                    $passed_params = $t_rule;
                }
                else
                    $passed_params = true;
                
                //$rules_array[$var][$rule[0]] = isset($rule[1]) ? array_splice($rule, 0, 1) : true;
                $rules_array[$var][$rule[0]] = $passed_params;
            }
        }
        $this->validation_rules = $rules_array;
    }

    private function not_empty($var) {
        if(empty($this->source[$var]))
        {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is required';
            return false;
        }
        else
            return true;
    }

    private function is_set($var) {
        if(!isset($this->source[$var])) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is not set';
            return false;
        }
        return true;
    }
	
	private function validate_email($var) {
        if(filter_var($this->source[$var], FILTER_VALIDATE_EMAIL) === FALSE) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is an invalid email address';
            return false;
        }
        return true;
    }
    
    private function validate_max($var, $max) {
        if( strlen($this->source[$var]) > $max) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is too long';
            return false;
        }
        return true;
    }
    
    private function validate_min($var, $min) {
        if( strlen($this->source[$var]) < $min) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is too short';
            return false;
        }
        return true;
    }
	
	private function validate_regex($var, $regex) {
		if (!preg_match($regex, $this->source[$var])) {
			$this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is invalid';
			return false;
		}
		return true;
	}
	
	private function validate_numeric($var) {
        if( !is_numeric( $this->source[$var] )) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is an invalid number';
            return false;
        }
        return true;
    }
	
	private function validate_alnum($var) {
        if( !ctype_alnum( $this->source[$var] )) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is not alphanumeric';
            return false;
        }
        return true;
    }
	
	private function validate_natural($var) {
        if( filter_var( $this->source[$var], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === FALSE) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is an invalid natural number';
            return false;
        }
        return true;
    }

	private function validate_float($var) {
        if( !is_float($this->source[$var]) ) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is an invalid float';
            return false;
        }
        return true;
    }
    
	private function validate_url($var) {
        if(filter_var($this->source[$var], FILTER_VALIDATE_URL) === FALSE) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is an invalid URL';
            return false;
        }
        return true;
    }
		
	private function validate_string($var) {
        if(!is_string($this->source[$var])) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is an invalid string';
            return false;    
        }
        return true;
    }

    private function validate_bool($var) {
        if( filter_var($this->source[$var], FILTER_VALIDATE_BOOLEAN) === FALSE) {
            $this->errors[$var] = (isset($this->alt_field_labels[$var]) ? $this->alt_field_labels[$var] : $var) . ' is Invalid';
            return false;
        }
        return true;
    }

}