<?php
echo "Session Data: ";
var_dump($_SESSION);
echo "Welcome, " . ($_SESSION['username'] ?? 'Guest');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <p>testing</p>
</body>

</html>
