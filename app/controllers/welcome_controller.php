<?php
namespace app\Controllers;

use core\classes\Controller;

class Welcome_Controller extends Controller {
    
    public function index() {
        return $this->view->make("templates/main", ["content"=>$this->view->render("modules/welcome")]);
    }
    
}