<?php

spl_autoload_register(function ($class) {
  include "app/models/$class.php";
});