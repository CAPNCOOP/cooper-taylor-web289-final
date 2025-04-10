<?php

class DatabaseObject
{
  protected static ?PDO $db = null;
  static protected $primary_key = 'id';
  static protected $table_name = "";
  static protected $columns = [];
  static protected $db_columns = [];
  public $errors = [];

  public $id;

  public static function setDatabase($database)
  {
    self::$db = $database;
  }

  public static function getDatabase()
  {
    return static::$db;
  }

  public static function find_by_sql($sql): array
  {
    if (self::$db === null) {
      throw new Exception("Database connection not initialized. Run DatabaseObject::setDatabase(\$pdo) first.");
    }

    $stmt = self::$db->prepare($sql);
    $stmt->execute();

    $object_array = [];
    while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $object_array[] = static::instantiate($record);
    }

    return $object_array;
  }

  public static function find_all()
  {
    $sql = "SELECT * FROM " . static::$table_name;
    return static::find_by_sql($sql);
  }

  public static function find_by_id($id)
  {
    $sql = "SELECT * FROM " . static::$table_name . " WHERE " . static::$primary_key . " = :id";
    $stmt = self::$db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    return $record ? static::instantiate($record) : false;
  }

  static protected function instantiate($record)
  {
    $object = new static;
    foreach ($record as $property => $value) {
      if (property_exists($object, $property)) {
        $object->$property = $value;
      }
    }
    return $object;
  }

  protected function validate()
  {
    $this->errors = [];
    return $this->errors;
  }

  protected function create()
  {
    $this->validate();
    if (!empty($this->errors)) return false;

    $attributes = $this->sanitized_attributes();
    $columns = array_keys($attributes);
    $placeholders = array_map(fn($key) => ':' . $key, $columns);

    $sql = "INSERT INTO " . static::$table_name . " (" . join(', ', $columns) . ") VALUES (" . join(', ', $placeholders) . ")";
    $stmt = self::$db->prepare($sql);

    foreach ($attributes as $key => $value) {
      $stmt->bindValue(':' . $key, $value);
    }

    $result = $stmt->execute();

    if ($result) {
      $pk = static::$primary_key;
      $this->$pk = self::$db->lastInsertId();
    }

    return $result;
  }

  protected function update()
  {
    $this->validate();
    if (!empty($this->errors)) return false;

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach ($attributes as $key => $value) {
      $attribute_pairs[] = "$key = :$key";
    }

    $sql = "UPDATE " . static::$table_name . " SET " . join(', ', $attribute_pairs) . " WHERE id = :id LIMIT 1";
    $stmt = self::$db->prepare($sql);

    foreach ($attributes as $key => $value) {
      $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  public function save()
  {
    return isset($this->id) ? $this->update() : $this->create();
  }

  public function merge_attributes($args = [])
  {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && $key != 'id') {
        $this->$key = $value;
      }
    }
  }

  public function attributes()
  {
    $attributes = [];
    foreach (static::$db_columns as $column) {
      if ($column == 'id') continue;
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  protected function sanitized_attributes(): array
  {
    $sanitized = [];
    foreach ($this->attributes() as $key => $value) {
      $sanitized[$key] = $value;
    }
    return $sanitized;
  }

  public function delete()
  {
    $sql = "DELETE FROM " . static::$table_name . " WHERE id = :id LIMIT 1";
    $stmt = self::$db->prepare($sql);
    $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
    return $stmt->execute();
  }
}
