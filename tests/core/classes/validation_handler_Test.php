<?php
require_once(realpath(dirname(__FILE__). '/../../..') . '/bootstrap.php');

use \PHPUnit\Framework\TestCase;

class Validation_Handler_Test extends TestCase {
    
    public function test_required() {
        $data = [
                    "first_name"    => "",
                    "last_name"     => "Doe",
                    "job_tite"      => false,
                    "address"       => 0,
                ];
        
        $rules = [
                    "first_name"    => ["required", "First name"],  
                    "last_name"     => ["required", "Last name"],  
                    "job_tite"      => ["required", "Job title"],  
                    "address"       => ["required", "Address"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "first_name" => "First name is required",
                    "job_tite" => "Job title is required",
                    "address" => "Address is required",
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_set() {
        $data = [
                    "first_name"    => "",
                    "last_name"     => "Doe",
                    "job_tite"      => false,
                    "department"    => 0,
                    //"address"       => 0,
                ];
        
        $rules = [
                    "first_name"    => ["set", "First name"],  
                    "last_name"     => ["set", "Last name"],  
                    "job_tite"      => ["set", "Job title"],  
                    "department"    => ["set", "Department"],  
                    "address"       => ["set", "Address"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "address" => "Address is not set",
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_email() {
        $data = [
                    "email_1"       => "info@perials.com",
                    "email_2"       => "abc",
                    "email_3"       => ""
                ];
        
        $rules = [
                    "email_1" => ["email", "Email 1"],  
                    "email_2" => ["email", "Email 2"],  
                    "email_3" => ["email", "Email 3"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "email_2" => "Email 2 is an invalid email address",
                    "email_3" => "Email 3 is an invalid email address",
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_max() {
        $data = [
                    "first_name"    => "John",
                    "last_name"     => "Doe",
                ];
        
        $rules = [
                    "first_name"    => ["max:3", "First name"],  
                    "last_name"     => ["max:10", "Last name"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "first_name" => "First name is too long"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_min() {
        $data = [
                    "first_name"    => "John",
                    "last_name"     => "Doe",
                ];
        
        $rules = [
                    "first_name"    => ["min:3", "First name"],  
                    "last_name"     => ["min:10", "Last name"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "last_name" => "Last name is too short"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_regex() {
        $data = [
                    "first_name"    => "John",
                    "last_name"     => "Doe",
                    "middle_name"   => "John.F.Doe",
                ];
        
        //regex checks
        //4-26 characters long
        //Start with atleast 2 letters
        //contain numbers and one underscore and one dot
        
        $rules = [
                    "first_name"    => ["regex:/^(?=[a-z]{2})(?=.{4,26})(?=[^.]*\.?[^.]*$)(?=[^_]*_?[^_]*$)[\w.]+$/iD", "First name"],  
                    "last_name"     => ["regex:/^(?=[a-z]{2})(?=.{4,26})(?=[^.]*\.?[^.]*$)(?=[^_]*_?[^_]*$)[\w.]+$/iD", "Last name"],  
                    "middle_name"   => ["regex:/^(?=[a-z]{2})(?=.{4,26})(?=[^.]*\.?[^.]*$)(?=[^_]*_?[^_]*$)[\w.]+$/iD", "Middle name"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "last_name" => "Last name is invalid",
                    "middle_name" => "Middle name is invalid",
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_numeric() {
        $data = [
                    "age"           => "50",
                    "salary"        => 25000,
                    "experience"    => 5.5,
                    "performance_score"  => "5.5",
                    "attendance_score"   => "5A5",
                    "department_code"    => "abc",
                ];
        
        $rules = [
                    "age"               => ["numeric", "Age"],
                    "salary"            => ["numeric", "Salary"],
                    "experience"        => ["numeric", "Experience"],
                    "performance_score" => ["numeric", "Performance score"],
                    "attendance_score"  => ["numeric", "Attendance score"],
                    "department_code"   => ["numeric", "Department code"],
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "attendance_score" => "Attendance score is an invalid number",
                    "department_code" => "Department code is an invalid number"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_alnum() {
        $data = [
                    "age"           => "50",
                    "salary"        => 25000,
                    "experience"    => 5.5,
                    "department_code"    => "abc505",
                    "password"      => "p@$$()R@",
                ];
        
        $rules = [
                    "age"               => ["alnum", "Age"],
                    "salary"            => ["alnum", "Salary"],
                    "experience"        => ["alnum", "Experience"],
                    "department_code"   => ["alnum", "Department code"],
                    "password"          => ["alnum", "Password"],
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "experience" => "Experience is not alphanumeric",
                    "password" => "Password is not alphanumeric"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_natural() {
        $data = [
                    "age"           => "50",
                    "salary"        => 25000,
                    "experience"    => 5.5,
                    "department_code"    => "abc505",
                    "password"      => "p@$$()R@",
                ];
        
        $rules = [
                    "age"               => ["natural", "Age"],
                    "salary"            => ["natural", "Salary"],
                    "experience"        => ["natural", "Experience"],
                    "department_code"   => ["natural", "Department code"],
                    "password"          => ["natural", "Password"],
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "experience" => "Experience is an invalid natural number",
                    "department_code" => "Department code is an invalid natural number",
                    "password" => "Password is an invalid natural number"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_url() {
        $data = [
                    "home"          => "https://perials.com",
                    "domain"        => "perials.com",
                    "experience"    => 5.5,
                    "department_code"    => "abc505"
                ];
        
        $rules = [
                    "home"              => ["url", "Home"],
                    "domain"            => ["url", "Domain"],
                    "experience"        => ["url", "Experience"],
                    "department_code"   => ["url", "Department code"],
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "domain" => "Domain is an invalid URL",
                    "experience" => "Experience is an invalid URL",
                    "department_code" => "Department code is an invalid URL"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    public function test_float() {
        $data = [
                    "age"           => "50",
                    "salary"        => 25000,
                    "experience"    => 5.5,
                    "department_code"    => "abc",
                ];
        
        $rules = [
                    "age"           => ["float", "Age"],
                    "salary"        => ["float", "Salary"],
                    "experience"    => ["float", "Experience"],
                    "department_code"    => ["float", "Department code"],
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        $expected = [
                    "age" => "Age is an invalid float",
                    "salary" => "Salary is an invalid float",
                    "department_code" => "Department code is an invalid float"
                    ];
        
        $this->assertEquals($expected, $error_array);
    }
    
    /*
    public function test_min_max_email() {
        $data = [
                    "first_name"    => "John",
                    "last_name"     => "Doe",
                    "job_title"     => "Developer",
                ];
        
        $rules = [
                    "first_name"    => ["required|min:5|max:20|email", "First name"],  
                    "last_name"     => ["required|min:10|max:20", "Last name"],  
                ];
        
        $result = Validator::validate($data, $rules);
        $error_array = Validator::errors();
        
        print_r($error_array);
        
        $this->assertEquals(2, 1);
    }
    */
}