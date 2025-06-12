<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Senyawa EO</title>
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

    .register-container {
      background-color: #2c2c2c;
      padding: 40px 30px;
      border-radius: 20px;
      width: 380px;
      box-shadow: 0 0 15px rgba(255, 165, 0, 0.5);
      text-align: center;
      position: relative;
      transition: transform 0.3s ease-in-out;
    }

    .register-container:hover {
      transform: scale(1.03);
    }

    .logo {
      width: 120px;
      height: 120px;
      margin: 0 auto 5px auto;
      background-image: url('../img/logo.png'); /* Sesuaikan jika file register berada dalam folder */
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }

    .register-container h2 {
    margin-top: 0;
    margin-bottom: 15px; 
    color: #FFA500;
    font-size: 24px;
    }

    .register-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 10px;
      background-color: #3a3a3a;
      color: white;
      font-size: 14px;
    }

    .register-container input::placeholder {
      color: #ccc;
    }

    .register-container button {
      width: 100%;
      padding: 12px;
      background-color: #FFA500;
      color: black;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      font-size: 15px;
      transition: background-color 0.3s ease;
    }

    .register-container button:hover {
      background-color: #FFA500;
    }

    .register-container p {
      margin-top: 15px;
      font-size: 14px;
    }

    .register-container a {
      color: #FFA500;
      text-decoration: none;
      font-weight: bold;
    }

    .register-container a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="register-container">
    <a href="#" class="logo">
  <img src="/senyawa-wo/sections/img/logo.png" alt="Logo senyawa" style="height: 100px;">
    <h2>Create an Account</h2>
    <form method="POST" action="register_process.php">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />
      <button type="submit">Register</button>
      <p>Already have an account?<a href="login.php"> Login here</a></p>
    </form>
  </div>
</body>

</html>
