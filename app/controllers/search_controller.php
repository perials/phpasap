<?php
namespace app\Controllers;

use core\classes\Controller;

class Search_Controller extends Controller {
    
    private $tables = ["purchase_order", "invoice", "order_confirmation", "sales_quotation", "inward_document"];
    
    private function setup() {
        $faker = \Faker\Factory::create();
        $products = [
            50 => ["name" => "LUX", "price" => 50],
            51 => ["name" => "Liril", "price" => 40],
            52 => ["name" => "Bata", "price" => 68],
            53 => ["name" => "Paragon", "price" => 72],
            54 => ["name" => "Denim", "price" => 9]
        ];
        
        foreach($this->tables as $table) {
            $from_company_name = $faker->name;
            $to_company_name = $faker->name;
            $document_number = $faker->randomNumber($nbDigits = NULL, $strict = false);
            $main_table = $table;
            $items_table = $table . "_items";
            $document_id = $this->db->table($main_table)->insert([
                "from_company_name" => $from_company_name,
                "to_company_name" => $to_company_name,
                "document_number" => $document_number,
            ]);
            for($i=0; $i<5; $i++) {
               $this->db->table($items_table)->insert([
                    "document_id" => $document_id,
                    "document_number" => $document_number,
                    "item_id" => $i+50,
                    "product_name" => $products[$i+50]["name"],
                    "product_price" => $products[$i+50]["price"],
                    "product_quantity" => 5
                ]); 
            }
        }
    }
    
    private function get_sub_query($table) {
        $query = "SELECT from_company_name, to_company_name, document_number FROM $table";
        $where = [];
        $bind = [];
        if ($this->request->get("from_company")) {
            $where[] = "from_company_name LIKE ?";
            $bind[] = "%" . $this->request->get("from_company") ."%";
        }
        if ($this->request->get("to_company")) {
            $where[] = "to_company_name LIKE ?";
            $bind[] = "%" . $this->request->get("to_company") ."%";
        }
        if ($this->request->get("document_number")) {
            $where[] = "document_number LIKE ?";
            $bind[] = "%" . $this->request->get("document_number") ."%";
        }
        
        if (empty($where)) {
            return ["query" => $query, "bind" => $bind];
        }
        
        $query = $query . " WHERE " . implode(" AND ", $where);
        return ["query" => $query, "bind" => $bind];
    }
    
    public function get_index() {
        $sub_queries = [];
        $bind = [];
        foreach($this->tables as $table) {
            $query_with_bind = $this->get_sub_query($table);
            $sub_queries[] = $query_with_bind["query"];
            $bind = array_merge($bind, $query_with_bind["bind"]);
        }
        
        $query = "SELECT * FROM
            (
                " . implode(" UNION ", $sub_queries) . "
            ) AS result";
        
        $result = $this->db->sel($query, $bind);
        
        $todos = $this->db->table("todos")->get();
        $data = [
            'todos' => $todos
        ];
        return $this->view->make("templates/main", ["content"=>$this->view->render("modules/todo/list", $data)]);
    }
    
}

/*
CREATE TABLE purchase_order(  
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   from_company_name VARCHAR(255) NOT NULL,
   to_company_name VARCHAR(255) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   PRIMARY KEY ( id )  
);
CREATE TABLE purchase_order_items(
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   document_id BIGINT(20) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   item_id BIGINT(20) NOT NULL,
   product_name VARCHAR(255) NOT NULL,
   product_price decimal(15,2) NOT NULL,
   product_quantity BIGINT(20) NOT NULL,
   PRIMARY KEY ( id )
);

CREATE TABLE invoice(  
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   from_company_name VARCHAR(255) NOT NULL,
   to_company_name VARCHAR(255) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   PRIMARY KEY ( id )  
);
CREATE TABLE invoice_items(
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   document_id BIGINT(20) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   item_id BIGINT(20) NOT NULL,
   product_name VARCHAR(255) NOT NULL,
   product_price decimal(15,2) NOT NULL,
   product_quantity BIGINT(20) NOT NULL,
   PRIMARY KEY ( id )
);

CREATE TABLE order_confirmation(  
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   from_company_name VARCHAR(255) NOT NULL,
   to_company_name VARCHAR(255) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   PRIMARY KEY ( id )  
);
CREATE TABLE order_confirmation_items(
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   document_id BIGINT(20) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   item_id BIGINT(20) NOT NULL,
   product_name VARCHAR(255) NOT NULL,
   product_price decimal(15,2) NOT NULL,
   product_quantity BIGINT(20) NOT NULL,
   PRIMARY KEY ( id )
);

CREATE TABLE sales_quotation(  
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   from_company_name VARCHAR(255) NOT NULL,
   to_company_name VARCHAR(255) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   PRIMARY KEY ( id )  
);
CREATE TABLE sales_quotation_items(
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   document_id BIGINT(20) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   item_id BIGINT(20) NOT NULL,
   product_name VARCHAR(255) NOT NULL,
   product_price decimal(15,2) NOT NULL,
   product_quantity BIGINT(20) NOT NULL,
   PRIMARY KEY ( id )
);

CREATE TABLE inward_document(  
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   from_company_name VARCHAR(255) NOT NULL,
   to_company_name VARCHAR(255) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   PRIMARY KEY ( id )  
);
CREATE TABLE inward_document_items(
   id BIGINT(20) NOT NULL AUTO_INCREMENT,  
   document_id BIGINT(20) NOT NULL,
   document_number VARCHAR(100) NOT NULL,
   item_id BIGINT(20) NOT NULL,
   product_name VARCHAR(255) NOT NULL,
   product_price decimal(15,2) NOT NULL,
   product_quantity BIGINT(20) NOT NULL,
   PRIMARY KEY ( id )
);
*/