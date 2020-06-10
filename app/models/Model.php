<?php
require_once 'bootstrap/Connection.php';

abstract class Model {

  private $connection;

  public function __construct() {
    $this->connection = Connection::make();
  }

  public static function query() {
    return new static();
  }

  public function __get($name) {
    return $this->$name;
  }

  public function __set($name, $value) {
    return $this->$name = $value;
  }

  public function __toString() {
    return $this->toJson();
  }

  public function all() {
    $table = (new static)->getTableName();
    $sql = "SELECT * FROM $table";
    $query = $this->connection->prepare($sql);
    return !$query->execute() ?: $query->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  public function delete() {
    $this->connection = Connection::make();
    $columnName = $this->getPrimaryKey();
    $primaryKey = $this->$columnName;

    $table = $this->getTableName();
    $sql = "DELETE FROM $table WHERE $columnName = $primaryKey";
    return $this->connection->query($sql);
  }

  public function find($id) {
    $columnName = (new static)->getPrimaryKey();

    $table = (new static)->getTableName();
    $sql = "SELECT * FROM $table WHERE $columnName = $id";
    $query = $this->connection->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_CLASS, static::class)[0];
  }

  public function getTableName() {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', static::class));
  }

  public function save() {
    $columns = [];
    foreach ($this->toArray() as $key => $value) {
      if ($value !== null) $columns[] = $key;
    }

    $update = [];
    foreach ($this->toArray() as $key => $value) $update[] = "$key = :$key";

    $columnName = $this->getPrimaryKey();
    $primaryKey = $this->$columnName;

    if (!$primaryKey) {
      $sql = "INSERT INTO " . $this->getTableName() . " (" . implode(', ', $columns) . ") VALUES (" . ':' . implode(', :', $columns) . ")";

      $parameters = [];
      foreach ($this->toArray() as $key => $value) {
        if ($value !== null) $parameters[":$key"] = $value;
      }

      $query = $this->connection->prepare($sql);
      $query->execute($parameters);
      return $this->connection->lastInsertId();
    } else {
      $update = [];
      foreach ($this->toArray() as $key => $value) $update[] = "$key = :$key";

      $sql = "UPDATE " . $this->getTableName() . " SET " . implode(', ', $update) . " WHERE $columnName = $primaryKey";

      $parameters = [];
      foreach ($this->toArray() as $key => $value) {
        $parameters[":$key"] = $value;
      }

      $query = $this->connection->prepare($sql);
      return $query->execute($parameters);
    }
  }

  public function toArray() {
    $data = json_decode(json_encode(get_object_vars($this)), true);
    unset($data['connection']);
    return $data;
  }

  public function toJson($options = 0) {
    return json_encode($this->toArray(), $options);
  }

  public function where(...$conditions) {
    $condition = [];

    foreach ($conditions as $array) {
      if (count($array) == 3) {
        $condition[] = "$array[0] $array[1] '$array[2]'";
      } else {
        $condition[] = "$array[0] = '$array[1]'";
      }
    }

    $table = (new static)->getTableName();
    $sql = "SELECT * FROM $table WHERE " . implode(', ', $condition);
    $query = $this->connection->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  private function getPrimaryKey() {
    $table = (new static)->getTableName();
    $sql = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
    $query = $this->connection->prepare($sql);
    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC)['Column_name'];
  }

}
