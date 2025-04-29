<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jelentkezés</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      padding: 20px;
      color: #333333;
    }
    .container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 5px;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    h1 {
      color: #00848b;
      font-size: 24px;
      margin-bottom: 10px;
    }
    p {
      font-size: 16px;
      line-height: 1.5;
      margin-bottom: 20px;
    }
    .register-btn {
      display: inline-block;
      background-color: #00848b;
      color: white !important;
      text-decoration: none;
      padding: 12px 20px;
      border-radius: 5px;
      font-size: 16px;
    }
    .register-btn:hover {
      background-color: #00707d;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Tisztelt {{ $name }}!</h1>
    <p>Kérjük, regisztráljon, hogy folytathassa a beiratkozást!</p>
    <p>
      Kattintson az alábbi gombra a regisztráció elindításához:
    </p>
    <a href="{{ $url }}" target="_blank" class="register-btn">Regisztráció</a>
  </div>
</body>
</html>
