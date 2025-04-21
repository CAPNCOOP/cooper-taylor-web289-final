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

  /**
   * Sets the PDO database connection.
   *
   * @param PDO $database The PDO connection instance.
   * @return void
   */
  public static function setDatabase($database)
  {
    self::$db = $database;
  }

  /**
   * Gets the current PDO database connection.
   *
   * @return PDO|null The active PDO connection or null if not set.
   */
  public static function getDatabase()
  {
    return static::$db;
  }

  /**
   * Executes a raw SQL query and returns hydrated objects.
   *
   * @param string $sql The SQL query string.
   * @return array Array of objects based on the query result.
   * @throws Exception If the database connection is not set.
   */
  public static function find_by_sql(string $sql, array $params = []): array

  {
    if (self::$db === null) {
      throw new Exception("Database connection not initialized. Run DatabaseObject::setDatabase(\$pdo) first.");
    }

    $stmt = self::$db->prepare($sql);
    $stmt->execute($params);

    $object_array = [];
    while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $object_array[] = static::instantiate($record);
    }

    return $object_array;
  }

  /**
   * Retrieves all records from the table as objects.
   *
   * @return array Array of objects from the table.
   */
  public static function find_all()
  {
    $sql = "SELECT * FROM " . static::$table_name;
    return static::find_by_sql($sql);
  }

  /**
   * Finds a single record by its primary key.
   *
   * @param int $id The primary key value.
   * @return static|false Object if found, false if not.
   */
  public static function find_by_id($id)
  {
    $sql = "SELECT * FROM " . static::$table_name . " WHERE " . static::$primary_key . " = :id";
    $stmt = self::$db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    return $record ? static::instantiate($record) : false;
  }

  /**
   * Instantiates an object from an associative array.
   *
   * @param array $record Key-value pairs matching class properties.
   * @return static The instantiated object.
   */
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

  /**
   * Validates the object's data. Override in child classes.
   *
   * @return array List of validation errors.
   */
  protected function validate()
  {
    $this->errors = [];
    return $this->errors;
  }

  /**
   * Inserts the object into the database.
   *
   * @return bool True on success, false on failure.
   */
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

  /**
   * Updates the existing object in the database.
   *
   * @return bool True on success, false on failure.
   */
  protected function update()
  {
    $this->validate();
    if (!empty($this->errors)) return false;

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach ($attributes as $key => $value) {
      $attribute_pairs[] = "$key = :$key";
    }

    $primary_key = static::$primary_key ?? 'id';

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE {$primary_key} = :primary_key LIMIT 1";

    $stmt = self::$db->prepare($sql);

    // Bind attribute values
    foreach ($attributes as $key => $value) {
      $stmt->bindValue(':' . $key, $value);
    }

    // Bind the primary key value
    $stmt->bindValue(':primary_key', $this->$primary_key, PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Saves the object by creating or updating based on primary key presence.
   *
   * @return bool True on success, false on failure.
   */
  public function save()
  {
    $pk = static::$primary_key ?? 'id';
    return isset($this->$pk) && !empty($this->$pk) ? $this->update() : $this->create();
  }

  /**
   * Returns an array of database column values for the object.
   *
   * @return array Associative array of column => value pairs.
   */
  public function attributes()
  {
    $attributes = [];
    foreach (static::$db_columns as $column) {
      if ($column == 'id') continue;
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  /**
   * Returns a sanitized version of object attributes.
   *
   * @return array Associative array of safe data.
   */
  protected function sanitized_attributes(): array
  {
    $sanitized = [];
    foreach ($this->attributes() as $key => $value) {
      $sanitized[$key] = $value;
    }
    return $sanitized;
  }

  /**
   * Deletes the current object from the database.
   *
   * @return bool True on success, false on failure.
   */
  public function delete(): bool
  {
    $pk = static::$primary_key ?? 'id';
    $sql = "DELETE FROM " . static::$table_name . " WHERE {$pk} = :{$pk} LIMIT 1";
    $stmt = self::$db->prepare($sql);
    $stmt->bindValue(":{$pk}", $this->$pk, PDO::PARAM_INT);
    return $stmt->execute();
  }

  public function id()
  {
    $key = static::$primary_key ?? 'id';
    return $this->$key ?? null;
  }
}
