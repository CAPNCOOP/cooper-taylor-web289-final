<?php

/**
 * Establishes a PDO connection to the MySQL database.
 *
 * @return PDO The PDO database connection instance.
 */
function db_connect(): PDO
{
  $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8mb4';

  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
  } catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
  }
}


/**
 * Confirms a successful MySQLi database connection.
 *
 * @param mysqli $connection The MySQLi connection object.
 * @return void
 */
function confirm_db_connect($connection)
{
  if ($connection->connect_errno) {
    $msg = "Database connection failed: ";
    $msg .= $connection->connect_error;
    $msg .= " (" . $connection->connect_errno . ")";
    exit($msg);
  }
}

/**
 * Closes an active MySQLi database connection.
 *
 * @param mysqli $connection The MySQLi connection object.
 * @return void
 */
function db_disconnect($connection)
{
  if (isset($connection)) {
    $connection->close();
  }
}
