<?php
namespace app\controllers;

use core\alias\View;
use core\classes\Controller;

class Crud_Controller extends Controller {
    
    public function get_index() {
        return $this->view->make("templates/main", ["content"=>$this->view->render("modules/welcome")]);
    }
    
    public function get_add() {
        return $this->view->make("templates/main", ["content"=>$this->view->render("modules/crud/form")]);
    }
    
    public function get_validation_rules() {
        return [
            'title' => ['required', 'Title'],
            'description' => ['required', 'Description']
        ];
    }
    
    public function post_save() {
        if(!$this->validator->validate(
            $this->request->post(),
            $this->get_validation_rules()
        )) {
            //validation failed
            $error_array = $this->validator->errors();
        }
    }
    
}