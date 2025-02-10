<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Blue Ridge Bounty - Log In</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="login">
  <header>
    <h1>Blue Ridge Bounty</h1>
    <nav>
      <ul>
        <li><a href="index.php"><img src="img/assets/barn.png" alt="An icon of a barn" height="25" width="25"></a></li>
        <li><a href="schedule.php">Schedule</a></li>
        <li><a href="ourvendors.php">Our Vendors</a></li>
        <li><a href="aboutus.php">About Us</a></li>
        <li><a href="login.php"><img src="img/assets/user.png" alt="A user login icon." height="25" width="25"></a></li>
      </ul>
    </nav>
  </header>

  <main>
    <form action="login" method="POST" class="user-login">
      <legend>Log In</legend>

      <label for="login-username">
        <img src="img/assets/user.png" alt="User icon" height="25" width="25">
        Username:
      </label>
      <input type="text" id="login-username" name="username" placeholder="Enter your username" required>

      <label for="login-password">
        <img src="img/assets/lock.png" alt="Lock icon" height="25" width="25">
        Password:
      </label>
      <input type="password" id="login-password" name="password" placeholder="Enter your password" required>

      <button type="submit">Log In</button>
    </form>

    <form action="login" method="POST" class="new-user-login">
      <legend>New User? Create an account!</legend>

      <p>Items with * are required.</p>

      <label for="register-username">Username: *</label>
      <input type="text" id="register-username" name="username" placeholder="Choose a username" required>

      <label for="register-fname">First Name: *</label>
      <input type="text" id="register-fname" name="fname" placeholder="Your first name" required>

      <label for="register-lname">Last Name: *</label>
      <input type="text" id="register-lname" name="lname" placeholder="Your last name" required>

      <label for="register-email">Email: *</label>
      <input type="email" id="register-email" name="email" placeholder="Your email" required>

      <label for="register-password">Password: *</label>
      <input type="password" id="register-password" name="password" placeholder="Create a password" required>

      <label for="register-confirmpass">Confirm Password: *</label>
      <input type="password" id="register-confirmpass" name="confirmpass" placeholder="Confirm your password" required>

      <button type="submit">Sign Up</button>
    </form>

    <footer>
      <span>Blue Ridge Bounty &copy; 2025</span>
      <a href="aboutus.php#contact">Contact Us</a>
    </footer>
</body>

</html>
