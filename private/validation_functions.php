<?php
/*
 * The function `is_blank` checks if a value is empty or consists only of whitespace characters in PHP.
 * 
 * @param value The `is_blank` function checks if a value is considered blank. It returns `true` if the
 * value is not set or if the trimmed value is an empty string, and `false` otherwise.
 * 
 * @return The function `is_blank` returns `true` if the value is not set or if the trimmed value is an
 * empty string, otherwise it returns `false`.
 */

function is_blank($value)
{
  return !isset($value) || trim($value) === '';
}


/*
 * The function `has_length_greater_than` checks if the length of a given value is greater than a
 * specified minimum length.
 * 
 * @param value The `value` parameter is the input value that you want to check the length of. It can
 * be a string, array, or any other data type that has a length property.
 * @param min The `` parameter in the `has_length_greater_than` function represents the minimum
 * length that the `` parameter should have in order for the function to return `true`. It is
 * used to compare against the length of the `` string to determine if it meets the minimum
 * length requirement.
 * 
 * @return The function `has_length_greater_than` returns a boolean value indicating whether the length
 * of the input value is greater than the specified minimum length.
 */
function has_length_greater_than($value, $min)
{
  $length = (!is_null($value) && $value !== '') ? strlen($value) : 0;
  return $length > $min;
}


/*
 * The function `has_length_less_than` checks if the length of a given value is less than a specified
 * maximum length.
 * 
 * @param value The `value` parameter is the input value that you want to check the length of. It can
 * be a string, array, or any other data type that has a length property.
 * @param max The `max` parameter in the `has_length_less_than` function represents the maximum length
 * that the `` should have in order for the function to return `true`. The function calculates
 * the length of the `` and compares it with this `max` value to determine if the length is
 * 
 * @return The function `has_length_less_than` returns a boolean value indicating whether the length of
 * the input value is less than the specified maximum length.
 */
function has_length_less_than($value, $max)
{
  $length = (!is_null($value) && $value !== '') ? strlen($value) : 0;
  return $length < $max;
}

/*
 * The function "has_length_exactly" checks if a given value has a specific length.
 * 
 * @param value The `value` parameter is the input value for which you want to check the length. It can
 * be a string, array, or any other data type that has a length property.
 * @param exact The `exact` parameter in the `has_length_exactly` function represents the desired
 * length that you want to check for in the input value. The function will return `true` if the length
 * of the input value matches the exact length specified by the `exact` parameter, and `false`
 * otherwise
 * 
 * @return The function `has_length_exactly` returns a boolean value indicating whether the length of
 * the input value is exactly equal to the specified exact length.
 */
function has_length_exactly($value, $exact)
{
  $length = (!is_null($value) && $value !== '') ? strlen($value) : 0;
  return $length == $exact;
}


/*
 * The function `has_length` checks if a value's length meets specified minimum, maximum, or exact
 * requirements.
 * 
 * @param value The `has_length` function you provided is used to validate the length of a given value
 * based on the options provided. The function checks if the length of the value meets certain criteria
 * specified in the options array.
 * @param options The `has_length` function takes two parameters: `` and ``. The
 * `` parameter is an associative array that can contain the following keys:
 * 
 * @return The function `has_length` is returning a boolean value - `true` if the length of the
 * `` meets the specified conditions (minimum, maximum, or exact length) provided in the
 * `` array, and `false` otherwise.
 */
function has_length($value, $options)
{
  if (isset($options['min']) && !has_length_greater_than($value, $options['min'] - 1)) {
    return false;
  } elseif (isset($options['max']) && !has_length_less_than($value, $options['max'] + 1)) {
    return false;
  } elseif (isset($options['exact']) && !has_length_exactly($value, $options['exact'])) {
    return false;
  } else {
    return true;
  }
}

/*
 * The function `has_valid_email_format` checks if a given value has a valid email format using a
 * regular expression in PHP.
 * 
 * @param value The function `has_valid_email_format` is designed to check if a given email address has
 * a valid format. The function uses a regular expression to validate the email format. The parameter
 * `` should be the email address that you want to validate.
 * 
 * @return bool The function `has_valid_email_format` returns a boolean value indicating whether the
 * provided email address `` has a valid format according to the regular expression defined in
 * the function.
 */
function has_valid_email_format($value): bool
{
  $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';

  if (!empty($value)) {
    $value = trim($value); // Trim whitespace to avoid false negatives
    return (bool) preg_match($email_regex, $value);
  }

  return false; // Prevents deprecated warning
}

/*
 * The function `has_unique_username` checks if a given username already exists in a database table
 * named `users`.
 * 
 * @param username The function `has_unique_username()` checks if a given username already
 * exists in the database. It queries the database to count the number of rows where the username
 * matches the input username. If the count is 0, it means the username does not exist in the database,
 * and the function returns
 * 
 * @return The function `has_unique_username` returns a boolean value - `true` if the username does not
 * exist in the database (i.e., it is unique), and `false` if the username already exists in the
 * database.
 */
function has_unique_username($username)
{
  global $db;

  $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$username]);
  $count = $stmt->fetchColumn();

  return $count == 0; // Returns true if username does NOT exist
}
