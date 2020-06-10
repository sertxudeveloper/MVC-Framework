<?php

abstract class Controller {

  public static function __callStatic($name, $arguments) {
    return (new static())->$name($arguments);
  }

  public function __call($name, $arguments) {
    return $this->$name($arguments);
  }
}
