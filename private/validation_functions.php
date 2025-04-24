<?php

/**
 * Check if a value is blank (not set or only whitespace).
 *
 * @param mixed $value The value to check.
 * @return bool True if blank, false otherwise.
 */
function is_blank($value)
{
  return !isset($value) || trim($value) === '';
}

/**
 * Check if a value's length is greater than a given minimum.
 *
 * @param mixed $value The value to check.
 * @param int $min Minimum number of characters.
 * @return bool True if longer than $min, false otherwise.
 */
function has_length_greater_than($value, $min)
{
  $length = (!is_null($value) && $value !== '') ? strlen($value) : 0;
  return $length > $min;
}

/**
 * Check if a value's length is less than a given maximum.
 *
 * @param mixed $value The value to check.
 * @param int $max Maximum number of characters.
 * @return bool True if shorter than $max, false otherwise.
 */
function has_length_less_than($value, $max)
{
  $length = (!is_null($value) && $value !== '') ? strlen($value) : 0;
  return $length < $max;
}

/**
 * Check if a value's length is exactly equal to a given number.
 *
 * @param mixed $value The value to check.
 * @param int $exact Exact number of characters.
 * @return bool True if length equals $exact, false otherwise.
 */
function has_length_exactly($value, $exact)
{
  $length = (!is_null($value) && $value !== '') ? strlen($value) : 0;
  return $length == $exact;
}

/**
 * Check if a value's length meets specified length constraints.
 *
 * @param mixed $value The value to check.
 * @param array $options Supported keys: 'min', 'max', 'exact'.
 * @return bool True if all constraints are satisfied, false otherwise.
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

/**
 * Check if a value is a valid email format.
 *
 * @param string $value The email address to validate.
 * @return bool True if valid email format, false otherwise.
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

/**
 * Check if a username is unique in the `users` table.
 *
 * @param string $username The username to check.
 * @return bool True if username does not exist, false otherwise.
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
