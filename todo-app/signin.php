
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if email already exists
  $check = $conn->query("SELECT * FROM users WHERE email='$email'");
  if ($check->num_rows > 0) {
    $error = "Email already registered!";
  } else {
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
      header("Location: login.php");
      exit();
    } else {
      $error = "Error: " . $conn->error;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up – ToDo List</title>
  <style>
    /* 🌈 Reset */
    * { margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif; }

    /* Animated gradient background */
    body {
      height:100vh;
      display:grid;
      place-items:center;
      background:linear-gradient(-45deg,#667eea,#764ba2,#89f7fe,#66a6ff);
      background-size:400% 400%;
      animation:gradient 10s ease infinite;
      color:white;
    }
    @keyframes gradient {
      0%{background-position:0% 50%;}
      50%{background-position:100% 50%;}
      100%{background-position:0% 50%;}
    }

    /* Glass card */
    .signup-container{
      width:90%;
      max-width:400px;
      background:rgba(255,255,255,0.15);
      backdrop-filter:blur(12px);
      border-radius:20px;
      padding:40px;
      box-shadow:0 8px 25px rgba(0,0,0,0.3);
      text-align:center;
      transition:transform .3s;
    }
    .signup-container:hover{transform:scale(1.03);}

    h2{margin-bottom:20px;letter-spacing:1px;}

    form{display:flex;flex-direction:column;gap:15px;}

    input{
      padding:12px 15px;
      border:none;
      border-radius:10px;
      font-size:1em;
      background:rgba(255,255,255,0.2);
      color:#fff;
      outline:none;
      transition:.3s;
    }
    input::placeholder{color:rgba(255,255,255,0.8);}
    input:focus{background:rgba(255,255,255,0.3);transform:scale(1.02);}

    button{
      padding:12px;
      font-size:1.1em;
      border:none;
      border-radius:10px;
      background:linear-gradient(90deg,#667eea,#764ba2);
      color:#fff;
      cursor:pointer;
      transition:.3s;
    }
    button:hover{background:linear-gradient(90deg,#89f7fe,#66a6ff);transform:scale(1.05);}

    p{margin-top:10px;color:#e0e0e0;}
    a{color:#fff;text-decoration:underline;transition:.3s;}
    a:hover{color:#89f7fe;}

    .error{
      background:rgba(255,0,0,0.3);
      padding:10px;
      border-radius:8px;
      color:#fff;
      margin-bottom:10px;
    }
  </style>
</head>
<body>
  <div class="signup-container">
    <h2>Create Account</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
