<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Senyawa EO</title>
  <link rel="stylesheet" href="../style.css">
<style>
  body {
    background: linear-gradient(135deg, #1c1c1c, #2c2c2c);
    color: white;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  .login-container {
    background-color: #2c2c2c;
    padding: 40px 30px;
    border-radius: 20px;
    width: 360px;
    box-shadow: 0 0 15px rgba(255, 165, 0, 0.3);
    text-align: center;
    position: relative;
  }

  .logo {
     width: 120px;
    height: 120px;
    margin: 0 auto 5px auto;
    background-image: url('../img/logo.png'); 
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
  }

  .login-container h2 {
    margin-top: 0;
    margin-bottom: 15px; 
    color: #FFA500;
    font-size: 24px;
}

  .login-container input {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border: none;
    border-radius: 10px;
    background-color: #3a3a3a;
    color: white;
    font-size: 14px;
  }

  .login-container input::placeholder {
    color: #ccc;
  }

  .login-container button {
    width: 100%;
    padding: 12px;
    background-color: #FFA500;
    color: black;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }

  .login-container button:hover {
    background-color: #FFA500;
  }

  .login-container p {
    margin-top: 20px;
    font-size: 14px;
  }

  .login-container a {
    color: #FFA500;
    text-decoration: none;
    font-weight: bold;
  }

  .login-container a:hover {
    text-decoration: underline;
  }
</style>

</head>
<body>
<div class="login-container">
  <a href="#" class="logo">
  <img src="/senyawa-wo/sections/img/logo.png" alt="Logo senyawa" style="height: 100px;">
  <h2>Login Senyawa.WO</h2>
  <form action="login_process.php" method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p>Don't have an account? <a href="register.php"> Register here</a></p>
</div>
</body>
</html>
