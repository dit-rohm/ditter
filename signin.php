<?php
require_once 'init.php';

if (!empty($_SESSION['user_id'])) {
    $index_url = 'index.php';
    header('Location: {$index_url}');
    exit;
}

$error = [];
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setToken();
} else {
    checkToken();

    $email = $_POST['email'];
    $password = $_POST['password'];
    $db = connectDb();

    if ($email === '') {
        $error['email'] = 'メールアドレスを入力してください';
    }
    if ($password === '') {
        $error['password'] = 'パスワードを入力してください';
    } elseif (!$user_id = getUserId($email, $password, $db)) {
        $error['password'] = 'パスワードとメールアドレスが正しくありません';
    } elseif (empty($error)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $index_url = 'index.php';
        header("Location: {$index_url}");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>signin - Ditter</title>
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
                    <input type="email" class="form-control" id="inputEmail" name="email"
                           value="<?php print escape($email); ?>">

                    <p><?php if (array_key_exists('email', $error)) {
                            print escape($error['email']);
                        } ?></p>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">パスワード</label>
                    <input type="password" class="form-control" id="inputPassword" name="password">

                    <p><?php if (array_key_exists('password', $error)) {
                            print escape($error['password']);
                        } ?></p>
                </div>
                <input type="hidden" name="token" value="<?php print escape($_SESSION['token']); ?>">
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
