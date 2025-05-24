<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PHV-Login</title>
  <link rel="icon" type="image/png" href="/images/logo.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">


  <style>
    :root {
      --primary: rgb(74, 144, 226);
      --white: #ffffff;
      --off-white: #f2f4f7;
      --text-dark: #1c1c1c;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: url('/images/hero-1.jpg') center center/cover no-repeat;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.35);
      backdrop-filter: blur(4px);
      z-index: 0;
    }

    .wrapper {
      width: 360px;
      background: var(--off-white);
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      position: relative;
      z-index: 1;
      animation: slideFade 0.6s ease;
    }

    @keyframes slideFade {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .title {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 16px;
      background: var(--white);
      padding: 20px 25px;
      border-bottom: 1px solid #e5e7eb;
      height: 90px;
    }

    .title img {
      width: 70px;
      height: 70px;
      transform: scale(1.6);
      object-fit: contain;
    }

    .title-text {
      display: flex;
      flex-direction: column;
      justify-content: center;
      line-height: 1.2;
    }

    .title-text h1 {
      font-family: 'Poppins', sans-serif;
      font-size: 24px;
      font-weight: 700;
      color: var(--text-dark);
      text-align: center;
      letter-spacing: 1.2px;
      text-shadow: 1px 0 0 #999;
      margin: 0;
    }

    @media (max-width: 320px){
      .title-text h1 {
      font-size: 20px !important;
      }

      body {
      background: linear-gradient(to bottom right, #f5faff, #dbeeff);

    }
    }

    .title-text p {
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      font-weight: 400;
      color: #555;
      text-align: center;
      letter-spacing: 1px;
      margin-top: 3px;
      text-shadow: 1px 0 0 #999;
    }

    form {
      padding: 20px 25px;
    }

    .row {
      position: relative;
      margin-top: 18px;
    }

    .row i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: var(--text-dark);
    }

    .row input {
      width: 100%;
      height: 42px;
      padding-left: 36px;
      border-radius: 6px;
      border: 1px solid #ccc;
      background: #fff;
      font-size: 14px;
      transition: border-color 0.3s ease;
    }

    .row input:focus {
      border-color: var(--primary);
      outline: none;
    }

    .pass {
      margin-top: 10px;
      text-align: right;
    }

    .pass a {
      font-size: 13px;
      color: var(--primary);
      text-decoration: none;
    }

    .pass a:hover {
      text-decoration: underline;
    }

    .button {
      margin-top: 20px;
      display: flex;
      justify-content: center;
    }

    .button input {
      background: var(--primary);
      color: #fff;
      padding: 10px 28px;
      font-size: 15px;
      font-weight: 500;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .button input:hover {
      background: #3b91c7;
    }

    .signup-link {
      text-align: center;
      margin-top: 28px;
      font-size: 13px;
    }

    .signup-link a {
      color: var(--primary);
      text-decoration: none;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .alert-danger {
      background-color: #fddede;
      color: #a94442;
      padding: 10px;
      font-size: 13px;
      border-radius: 4px;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="title">
      <img src="/images/logo.png" alt="Logo">
      <div class="title-text">
        <h1>Pet Health Vault</h1>
        <p>View • Check • Care</p>
      </div>
    </div>
    <form action="{{ route('login.user') }}" method="post">
      @csrf

      @if(Session::has('fail'))
      <div class="alert-danger">{{ Session::get('fail') }}</div>
      @endif

      @if ($errors->has('email'))
      <div class="alert-danger">{{ $errors->first('email') }}</div>
      @endif

      @if ($errors->has('password'))
      <div class="alert-danger">{{ $errors->first('password') }}</div>
      @endif

      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" name="email" placeholder="Email or Phone" required />
      </div>
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required />
      </div>
      <div class="pass"><a href="{{ route('forgot.password') }}">Forgot password?</a></div>
      <div class="button">
        <input type="submit" value="Login">
      </div>

    </form>
  </div>
</body>

</html>
