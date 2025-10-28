<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = $conn->query("SELECT * FROM users WHERE email='$email'");

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['username'] = $row['username'];
      header("Location: index.php");
      exit();
    } else {
      $error = "Invalid password!";
    }
  } else {
    $error = "No account found!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - ToDo List</title>
  <style>
    /* 🌈 Reset and Base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    /* ✨ Animated Gradient Background */
    body.login-body {
      height: 100vh;
      display: grid;
      place-items: center;
      background: linear-gradient(-45deg, #667eea, #764ba2, #89f7fe, #66a6ff);
      background-size: 400% 400%;
      animation: gradientMove 10s ease infinite;
      color: white;
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* 🧱 Grid Layout */
    .grid-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      width: 80%;
      max-width: 950px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 40px;
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease;
    }

    .grid-container:hover {
      transform: scale(1.02);
    }

    /* Left Section */
    .welcome-section {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 20px;
      text-align: left;
    }

    .welcome-section h1 {
      font-size: 2.5em;
      font-weight: 700;
      margin-bottom: 15px;
      line-height: 1.2;
    }

    .welcome-section p {
      font-size: 1.1em;
      color: #f0f0f0;
    }

    /* Right Section (Form Card) */
    .form-section {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 30px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      box-shadow: 0 4px 30px rgba(255, 255, 255, 0.1);
    }

    .form-section h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.8em;
      letter-spacing: 1px;
      color: #fff;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="email"],
    input[type="password"] {
      padding: 12px 15px;
      border: none;
      border-radius: 10px;
      font-size: 1em;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      outline: none;
      transition: 0.3s;
    }

    input::placeholder {
      color: rgba(255, 255, 255, 0.8);
    }

    input:focus {
      background: rgba(255, 255, 255, 0.3);
      transform: scale(1.02);
    }

    button {
      margin-top: 10px;
      padding: 12px;
      font-size: 1.1em;
      border: none;
      border-radius: 10px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background: linear-gradient(90deg, #89f7fe, #66a6ff);
      transform: scale(1.05);
    }

    .form-section p {
      text-align: center;
      margin-top: 10px;
      color: #e0e0e0;
    }

    .form-section a {
      color: #fff;
      text-decoration: underline;
      transition: 0.3s;
    }

    .form-section a:hover {
      color: #89f7fe;
    }

    /* Error message */
    .error {
      background: rgba(255, 0, 0, 0.3);
      padding: 10px;
      text-align: center;
      border-radius: 8px;
      color: #fff;
      margin-bottom: 10px;
    }

    /* 📱 Responsive */
    @media (max-width: 768px) {
      .grid-container {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 30px 20px;
      }

      .welcome-section {
        display: none;
      }

      .form-section {
        background: rgba(255, 255, 255, 0.2);
      }
    }
  </style>
</head>
<body class="login-body">
  <div class="grid-container">
    <div class="welcome-section">
      <h1>Welcome Back!</h1>
      <p>Manage your daily goals and stay organized beautifully.</p>
    </div>

    <div class="form-section">
      <h2>Login</h2>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="password" placeholder="Enter password" required>
        <button type="submit">Login</button>
      </form>
      <p>Don’t have an account? <a href="signup.php">Sign up</a></p>
    </div>
  </div>
</body>
</html>
