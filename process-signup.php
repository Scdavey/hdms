<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $fname = $_POST["fname"];
  $lname = $_POST["lname"];
  $email = $_POST["email"];
  $username = $_POST["username"];
  $password = $_POST["password"];
  $rptpassword = $_POST["rptpassword"];

  if (
    empty($fname) ||
    empty($lname) ||
    empty($email) ||
    empty($username) ||
    empty($password) ||
    empty($rptpassword)
  ) {
    die("Missing valid information");
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Enter valid email");
  }

  if (strlen($password) < 8) {
    die("Password too short");
  }

  if (!preg_match("/[a-z]/i", $password)) {
    die("Password must contain atleast one letter");
  }

  if (!preg_match("/[0-9]/", $password)) {
    die("Password must contain atleast one number");
  }

  if ($password !== $rptpassword) {
    die("Passwords do not match");
  }

  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  require_once "connection.php";
  $query = "INSERT INTO users (Lastname, Firstname, Email, Username, Password)
              VALUES (?, ?, ?, ?, ?);";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sssss", $lname, $fname, $email, $username, $password_hash);
  try {
    $stmt->execute();
    echo "Signup successful";
  } catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
      die("Email already in use");
    } else {
      throw $e;
    }
  }
  $conn = null;
  $stmt = null;
  header("Location: ../HDMS/index.php");
  exit();
} else {
  header("Location: ../HDMS/signup.php");
}