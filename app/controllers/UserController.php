<?php
require 'Controller.php';

class UserController extends Controller {

  public function __construct() {
    // if(!isset($_SESSION['logged'])) header('Location: /login');
  }

  public function show($request, $id) {
    var_dump('Show user: ', $id);
    // $user = User::query()->find($id);
    // if (!$user) header("HTTP/1.0 404 Not Found");
    // require_once 'public/private/views/show.view.php';
  }

  public function edit($request, $id) {
    var_dump('GET Edit user: ', $id);
  }

  public function postEdit($request, $id) {
    var_dump('POST Edit user: ', $id);
  }

}
