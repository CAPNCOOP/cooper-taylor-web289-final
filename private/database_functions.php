<?php

/*
 * The function `db_connect` establishes a connection to a MySQL database using the provided server,
 * username, password, and database name.
 * 
 * @return The function `db_connect()` is returning a MySQLi database connection object.
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


/*
 * The function `confirm_db_connect` checks for a database connection error and exits with an error
 * message if a connection error occurs.
 * 
 * @param connection The `confirm_db_connect` function is used to check if a database connection was
 * successful. It takes a parameter ``, which is typically an instance of the `mysqli` class
 * representing the database connection.
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

/*
 * The function `db_disconnect` is used to close a database connection in PHP.
 * 
 * @param connection The `db_disconnect` function takes a database connection object as a parameter.
 * This object is used to interact with the database, such as executing queries and fetching results.
 * The function checks if the connection object is set and then closes the connection using the
 * `close()` method. This is important to properly release
 */
function db_disconnect($connection)
{
  if (isset($connection)) {
    $connection->close();
  }
}
