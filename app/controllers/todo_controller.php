<?php
namespace app\Controllers;

use core\classes\Controller;
use core\alias\View;
// use core\alias\Request;
// use core\alias\Validator;
// use core\alias\Session;
use core\alias\Db;

class Todo_Controller extends Controller {
    
    public function get_index() {
        $todos = $this->db->table("todos")->get();
        $data = [
            'todos' => $todos
        ];
        return View::make("templates/main", ["content"=>View::render("modules/todo/list", $data)]);
        // return $this->view->make("templates/main", ["content"=>$this->view->render("modules/todo/list", $data)]);
    }
    
    public function get_add() {
        $data = [
            'todos' => []
        ];
        return $this->view->make("templates/main", ["content"=>$this->view->render("modules/todo/form", $data)]);
    }
    
    public function get_edit($id) {
        $todo = Db::table("todos")->where("id", "=", $id)->first();
        if (!$todo) {
            return $this->request->redirect_to("todo")->with(["errors" => ["Invalid Id"]]);
        }
        
        return $this->view->make("templates/main", ["content"=>$this->view->render("modules/todo/form", ['todo' => $todo])]);
    }
    
    private function rules() {
        return [
            "title" => ["required|max:150", "Title"],
            "due_date" => ["required", "Due date"],
            "id" => ["exists", "Todo"]
        ];
    }
    
    public function exists($validator_obj, $id) {
        if (!$this->db->table("todos")->where("id", "=", $id)->count()) {
            $validator_obj->set_error("id", "Invalid Id");
        }
    }
    
    public function post_save() {
        // validate request
        if( !$this->validator->validate($this->request->all(), $this->rules()) ) {
            //validation failed
            $error_array = $this->validator->errors();
            $this->session->flash('errors', $error_array);
            return $this->request->redirect_to("todo/add")->with_inputs();
        }

        $this->session->set("title", $this->request->post("title"));
        if ($this->request->post("id")) {
            $this->db->table("todos")
            ->where("id", "=", $this->request->post("id"))
            ->update([
                "title" => $this->request->post("title"),
                "due_date" => $this->request->post("due_date")
            ]);
        }
        else {
            $this->db->table("todos")->insert([
                "title" => $this->request->post("title"),    
                "due_date" => $this->request->post("due_date")
            ]);
        }
        
        return $this->request->redirect_to("todo")->with(["success" => "Saved successfully"]);
    }
    
    public function post_delete($id) {
        $todo = $this->db->table("todos")->where("id", "=", $id)->first();
        if (!$todo) {
            return $this->request->redirect_to("todo")->with(["errors" => ["Invalid Id"]]);
        }
        
        $this->db->table("todos")
            ->where("id", "=", $id)
            ->delete();
            
        return $this->request->redirect_to("todo")->with(["success" => "Deleted successfully"]);
    }
    
}