<?php
use core\alias\Route;

Route::add('GET', '/', 'Welcome_Controller@index');
Route::add('CONTROLLER', 'crud', 'Crud_Controller');