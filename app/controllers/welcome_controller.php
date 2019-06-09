<?php
namespace app\Controllers;

use core\alias\View;

class Welcome_Controller {
    
    public function index() {
        return View::make("templates/main", ["content"=>View::render("modules/welcome")]);    
    }
    
}