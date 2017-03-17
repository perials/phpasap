<?php
namespace app\Controllers;

class Welcome_Controller {
    
    public function index() {
        return View::make("templates/main", ["content"=>View::render("modules/welcome")]);    
    }
    
}