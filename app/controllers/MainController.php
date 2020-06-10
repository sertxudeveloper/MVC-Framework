<?php
require 'Controller.php';

class MainController extends Controller {

  public function index($request) {
    require_once 'public/views/index.view.php';
  }

  public function foo($request) {
    var_dump('foo page');
  }

}
