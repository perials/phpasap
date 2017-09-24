<?php
require_once(realpath(dirname(__FILE__). '/../../..') . '/bootstrap.php');

use \PHPUnit\Framework\TestCase;

class Model_Test extends TestCase
{
    public function test_where_count() {
        $where_count = DB::table("employees")
            ->where("first_name", "=", "Georgi")
            ->where("last_name", "=", "Facello")
            ->count();
        $this->assertEquals(2, $where_count);
    }
    
    public function test_join_where_count() {
        $join_where_count = DB::table("employees")
            ->join("dept_emp", "employees.emp_no = dept_emp.emp_no")
            ->where("employees.first_name", "=", "Georgi")
            ->where("employees.last_name", "=", "Facello")
            ->count();
        $this->assertEquals(2, $join_where_count);
    }
    
    public function test_multiple_db_instance() {
        $where_count = DB::table("employees")
            ->where("first_name", "=", "Georgi");
            
        $join_where_count = DB::table("employees")
            ->join("dept_emp", "employees.emp_no = dept_emp.emp_no")
            ->where("employees.first_name", "=", "Bezalel");
        
        $where_count = $where_count->where("last_name", "=", "Facello")
            ->count();
        
        $join_where_count = $join_where_count->where("employees.last_name", "=", "Simmel")
            ->count();
            
        $this->assertEquals(2, $where_count);
        $this->assertEquals(1, $join_where_count);
    }
}