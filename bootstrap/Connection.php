<?php

class Connection {

  public static function make() {
    try {
      $config = require 'config/database.php';
      $database = new PDO("$config[connection]; dbname=$config[name]; charset=utf8", $config['username'], $config['password'], [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true
      ]);
    } catch (PDOException $PDOException) {
      die($PDOException->getMessage());
    }

    return $database;
  }
}
