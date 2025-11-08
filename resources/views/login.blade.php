<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="p-6">
  <h1>Login</h1>
  <form id="login-form">
    <input id="email" type="email" placeholder="Email" required />
    <input id="password" type="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>

  <div style="margin-top:12px;">
    <button id="btn-me" type="button">Get /api/me</button>
    <button id="btn-logout" type="button">Logout</button>
  </div>

  <pre id="output" style="margin-top:12px;"></pre>
</body>
</html>