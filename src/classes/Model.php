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

// Swoole\Runtime::enableCoroutine();

class Model {
    use Loader;
    
	public $debug = true;
    
    //these will hold the database credentials
    private $hostname;
    private $database;
    private $username;
    private $password;
	
	// private static $common_connection = false;
	private $connection = false;

	protected $primary_key = [];
    
    //this will hold the current table being queried
    protected $table = '';
    
    //this will hold the query to be executed
    private $query = '';
    
    //where queries will be captured in this array
    private $where = array();
    
    //all binded parameter will be captured in this array
    private $bind = array();
	
    private $order_by = array();
    private $group_by = array();
    
    public function __construct(&$app = NULL) {
        $this->app = $app ? $app : App::get_instance();
        if( !$this->app->db_connection ) {
			$db_credentials_array = $this->config->get('database');
			
			try {
				$this->app->db_connection = new \PDO('mysql:host='.$db_credentials_array['hostname'].';dbname='.$db_credentials_array['database'].';charset=utf8', $db_credentials_array['username'], $db_credentials_array['password']);
				$this->app->db_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			catch(\PDOException $e) {
				
				throw new Pa_Exception("Unable to connect to database.<br/>". $e->getMessage(), 8888);
				
			}
			
            $this->connection = $this->app->db_connection;
            
			if( $this->config->get('app.debug') == true ) {
				$this->query("set profiling_history_size=1000");
				$this->query("set profiling=1");
			}
		}
		
		if( $this->connection === false )
		$this->connection = $this->app->db_connection;
    }
    
    /*
     * reset all the query properties
     * when creating a query for a table the properties if set by any previous query needs to be reset
     * 
     */
    public function reset() {
        $this->table = '';
        $this->join = array();
        $this->select = '';
		$this->order_by = array();
		$this->group_by = array();
        $this->where = array();
        $this->bind = array();
		
		return $this;
    }
    
    /*
     * @param string $table_name
     * @return current object
     */
    public function table($table_name) {
        $instance = $this->get_instance();
		$instance->set_table($table_name);
		return $instance;
    }
	
	public function set_table($table_name) {
		$this->table = $table_name;
	}
	
	public function get_instance() {
		return new self($this->app);
	}
    
    /*
     * get the current table to be queried
     *
     * if table property is not set then the lowercase name of the class is
     * assumed as the table name
     *
     * @return string
     */
    public function get_table() {
        if(empty($this->table)) {
			$namespaced_dir_array = explode(DS, str_replace(['/','\\'], DS, get_class($this)));
			$class_name_without_ns = end($namespaced_dir_array);
		}
		
		return !( empty($this->table) ) ? $this->table : strtolower($class_name_without_ns);
		return !( empty($this->table) ) ? $this->table : strtolower(get_class($this));
    }
    
    /*
     * sets the where condition of the query
     * 
     * @param string $column_name
     * @param string $condition
     * @param int/string $value
     *
     * @return current object
     */
    public function where($column_name, $condition, $value) {
        $this->where[] = $column_name.' '.$condition.' ?';
        $this->bind[] = $value;
        return $this;
    }
    
    public function or_where($column_name, $condition, $value) {
        $this->where[] = ' OR '.$column_name.' '.$condition.' ?';
        $this->bind[] = $value;
        return $this;
    }
    
    /*
     * sets the where in condition of the query
     *
     * @param string $column_name
     * @param array $value_array numeric array expected
     *
     * @return current object
     */
    public function where_in($column_name, $value_array) {
        $escaped_chars = array();
        foreach( $value_array as $value ) {
            $this->bind[] = $value;
            $escaped_chars[] = '?';
        }
        $this->where[] = $column_name.' IN ('.implode(',',$escaped_chars).')';
        return $this;
    }
    
    /*
     * sets where between condition of the query
     *
     * @param string $column_name
     * @param array $value_array 	numeric array with two elements
     *
     * @return current object
     */
    public function where_between($column_name, $value_array) {
        $escaped_chars = array();
        foreach( $value_array as $value ) {
            $this->bind[] = $value;
        }
        $this->where[] = $column_name.' BETWEEN ? AND ?';        
        return $this;
    }
	
	/**
	 * Add ORDER BY clause
	 *
	 * @param string $column_name	Database table column name
	 * @param string $sort			Could be either ASC or DESC. Default is ASC
	 */
	public function order_by($column_name, $sort='ASC') {
		$this->order_by[] = $column_name.' '.$sort;
		return $this;
	}
	
	public function group_by($column_name) {
		$this->group_by[] = $column_name;
		return $this;
	}
    
    /**
     * Execute the query after binding parameters
     *
     * @param string $query SQL query with optional ? for binding value
     * @param array $bind_array params to bind
     */
    public function query($query='',$bind_array=array()) {
        try {
            $this->query = $query;
            $this->stmt = $this->connection->prepare($query);
            $this->stmt->execute($bind_array);
            return true;
        }
        catch( PDOException $e ) {
            //FIXME raise exception
            if( $this->debug )
            echo $e->getMessage();
            return false;
        }
    }
    
    /**
     * Execute a select query
     */
    public function sel($raw_query='',$bind_params) {
        if( $this->query($raw_query,$bind_params) )
            return $this->fetch_results();
        else
            return false;
    }
    
    /**
     * fetch results for last select statement
     *
     * @return array
     */
    private function fetch_results() {
        return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    /**
     * fetch row count for last delete, insert or update statement
     *
     * @return int
     */
    private function fetch_row_count() {
        return $this->stmt->rowCount();
    }
    
    /**
     * build and return where condition
     *
     * @return string or null
     */
    private function get_where() {
        if( $this->where ) {
            $return_string = '';
            $count = 0;
            foreach( $this->where as $where ) {
                if($count != 0) {
                    if(strpos($where, " OR ") === 0) {
                        //$return_string .= " OR ";
                    }
                    else {
                        $return_string .= " AND ";
                    }    
                }                
                $return_string .= $where;
                $count++;
            }
            return " WHERE ".$return_string;    
            //return " WHERE ".implode(' AND ',$this->where);    
        }        
        else
        return NULL;
    }
	
	/**
	 * Get ORDER BY
	 */
	private function get_order_by() {
		$order = "";
		if( !empty($this->order_by) ) {
			$order = " ORDER BY ".implode(',', $this->order_by);
		}
		return $order;
	}
    
    /*
     * calls get method with limit 1 and offset 0
     * 
     * @return mixed empty array if no result found, object if result found and false if query fails
     */
    public function first() {
        $result = $this->get(1,0);
        if( is_array($result) ) {
            if(isset($result[0])) {
                return $result[0];
            }
            else
                return [];
        }
        else
            return false;
    }
    
    /*
     * builds and executes the select statement and returns the result
     *
     * @return array if success and false if any error occurs
     */
    public function get($limit=null,$offset=0) {
        $result = null;
        $query = "SELECT ".$this->get_select()." FROM ".$this->get_table(). $this->get_joins(). $this->get_where(). $this->get_group_by(). $this->get_order_by();
		
        if( $limit ) $query .= " LIMIT ".$offset.",".$limit;
        
        if( $this->query($query,$this->bind) )
            return $this->fetch_results();
        else
            return false;
    }
    
    /*
     * returns count of a select statement
     * This doesn't use row count but instead uses SELECT COUNT(0)
     *
     * @return int if success else boolean false if any error occurs
     */
    public function count() {
        $result = null;
        $query = "SELECT COUNT(0) AS total FROM ".$this->get_table(). $this->get_joins(). $this->get_where(). $this->get_group_by();
        if( $this->query($query,$this->bind) ) {
            $results = $this->fetch_results();
            return (int)$results[0]->total;
        }
        else
            return false;
    }
    
    /*
     * Get the join query
     */
    private function get_joins() {
        if( isset($this->join) && !empty($this->join) ) {
            return " ".implode(' ',$this->join)." ";
        }
        else
        return '';
    }
	
	private function get_group_by() {
		if( isset($this->group_by) && !empty($this->group_by) ) {
            return " GROUP BY ".implode(', ', $this->group_by)." ";
        }
        else
        return '';
	}
    
    /*
     * delete row/rows
     *
     * @return no of rows deleted
     */
    public function delete() {
        $no_of_args = func_num_args();
        
        if($no_of_args>=1) {
            $query = func_get_arg(0);
            $bind = ($no_of_args == 2) ? func_get_arg(1) : [];
        }
        else {
            $query = "DELETE FROM ".$this->get_table(). $this->get_where();
            $bind = $this->bind;
        }
        
        $this->query($query,$bind);
        return $this->fetch_row_count();
    }
    
    /*
     * insert
     *
     * @return last insert id
     */
    public function insert() {
        $no_of_args = func_num_args();
        if( $no_of_args == 2 || is_string(func_get_arg(0)) ) {
            $query = func_get_arg(0);
            $insert_values = ($no_of_args == 2) ? func_get_arg(1) : [];
        }
        else {
            $insert_array = func_get_arg(0);
            
            $insert_columns = $insert_values = $bind_values = array();
            foreach( $insert_array as $column=>$value ) {
                $insert_columns[] = $column;
                $insert_values[] = $value;
                $bind_values[] = '?';
            }
            $query = "INSERT INTO ".$this->get_table()." (".implode(',',$insert_columns).") VALUES (".implode(',',$bind_values).")";
        }
        
        $this->query($query,$insert_values);
        return $this->connection->lastInsertId();
    }
    
    /*
     * get the last query executed
     *
     * @return string
     */
    public function get_last_query() {
        return $this->query;
    }
    
    /*
     * get the select columns query
     */
    private function get_select() {
        if( isset($this->select) && !empty($this->select) ) {
            return $this->select;
        }
        else
            return '*';
    }
    
    /*
     * select columns
     *
     * @param string $select
     */
    public function select($columns) {
        $this->select = $columns;
        return $this;
    }
    
	/**
	 * Update function
	 *
	 * @param array $update_data
	 */
    public function update($update_data=array()) {
        $no_of_args = func_num_args();
        if( $no_of_args == 2 || is_string(func_get_arg(0)) ) {
            $query = func_get_arg(0);
            $bind_params = ($no_of_args == 2) ? func_get_arg(1) : [];
        }
        else {
            $insert_array = func_get_arg(0);
            
            $update_columns = $update_values = $bind_values = array();
            foreach( $update_data as $column=>$value ) {
                $update_columns[] = $column.' = ?';
                $update_values[] = $value;         
            }
            $query = "UPDATE ".$this->get_table() . $this->get_joins() ." SET ".implode(', ',$update_columns).$this->get_where();
            $bind_params = array_merge($update_values,$this->bind);    
        }
        
        
        $this->query($query,$bind_params);
        return $this->fetch_row_count();
    }
    
    /**
	 * Left Join
	 *
	 * @param string $table
	 * @param string $condition
	 */
	public function left_join($table, $condition) {
        $this->join[] = "LEFT JOIN ".$table." ON ".$condition;
        return $this;
    }
	
	/**
	 * Right Join
	 *
	 * @param string $table
	 * @param string $condition
	 */
    public function right_join($table, $condition) {
        $this->join[] = "RIGHT JOIN ".$table." ON ".$condition;
        return $this;
    }
	
	/**
	 * Join
	 *
	 * @param string $table
	 * @param string $condition
	 */
	public function join($table, $condition) {
        $this->join[] = "JOIN ".$table." ON ".$condition;
        return $this;
    }
    
	/**
	 * Fetch all results
	 *
	 * Ignores pagination and fetches all results with no LIMITS and OFFSET
	 */
    public function all() {
        return $this->table($this->get_table())->get();
    }
	
	public function get_by_id($id) {
		return $this->table($this->get_table())->where($this->primary_key,"=",$id)->first();
	}
}