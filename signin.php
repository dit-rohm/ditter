<?php
require_once('config.php');
require_once('functions.php');
 
session_start();

if (!empty($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

function getUser($email, $password, $dbh) {
    $sql = "SELECT id, password FROM users WHERE email = :email";
    $sth = $dbh->prepare($sql);
    $sth->bindValue(':email', $email, \PDO::PARAM_STR);
    $sth->execute();
    $user = $sth->fetch();
    if (password_verify($password, $user['password'])) {
      return $user['id'];
    } else {
      return false;
    }
}

$err = [];
$email = '';
$password = ''; 

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  setToken();
} else {
  checkToken();

  $email = $_POST['email'];
  $password = $_POST['password'];
  $dbh = connectDb();

  if ($email === '') {
    $err['email'] = 'メールアドレスを入力してください';
  }
  else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err['email'] = 'メールアドレスの形式が正しくないです';
  }
  if ($password === '') {
    $err['password'] = 'パスワードを入力してください';
  }
  else if (!$user = getUser($email, $password, $dbh)) {
    $err['password'] = 'パスワードとメールアドレスが正しくありません';    
  }
  else if (empty($err)) {
    session_regenerate_id(true);
    $_SESSION['user'] = $uesr;
    header('Location: index.php');
    exit;
  }
}
  
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>signup - Ditter</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
</head>
<body>
  <div id="main" class="container">
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4">
        <h1>ログイン</h1>
        <form action="" method="POST">
          <div class="form-group">
            <label for="InputEmail">メールアドレス</label>
            <input type="email" class="form-control" id="inputEmail" name="email" value="<?php echo escape($email); ?>">
            <p><?php if (array_key_exists('email', $err)) : echo escape($err['email']); endif;?></p>
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">パスワード</label>
            <input type="password" class="form-control" id="inputPassword" name="password">
            <p><?php if (array_key_exists('password', $err)) : echo escape($err['password']); endif;?></p>
          </div>
          <input type="hidden" name="token" value="<?php echo escape($_SESSION['token']); ?>">
          <button type="submit" class="btn btn-default">ログイン</button>
        </form>  
        <p>新規登録は<a href="./signup.php">こちら</a></p>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
