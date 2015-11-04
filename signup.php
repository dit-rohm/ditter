<?php
require_once 'init.php';

$error = [];
$user_name = '';
$screen_name = '';
$email = '';
$password = '';
$comment = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setToken();
} else {
    checkToken();

    $user_name = $_POST['user_name'];
    $screen_name = $_POST['screen_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $comment = $_POST['comment'];
    $db = connectDb();

    if (mb_strlen($user_name) < 3 || mb_strlen($user_name) > 15) {
        $error['user_name'] = '3文字以上15文字以下にしてください';
    }
    if (!preg_match('/^[a-zA-Z0-9]+$/', $screen_name)) {
        $error['screen_name'] = '英数字にしてください';
    } elseif (strlen($screen_name) < 3 || strlen($screen_name) > 15) {
        $error['screen_name'] = '3文字以上15文字以下にしてください';
    } elseif (screenNameExists($screen_name, $db)) {
        $error['screen_name'] = 'このidは既に登録されています';
    }
    if ($email === '') {
        $error['email'] = 'メールアドレスを入力してください';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'メールアドレスの形式が正しくないです';
    } elseif (emailExists($email, $db)) {
        $error['email'] = 'このメールアドレスは既に登録されています';
    }
    if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
        $error['password'] = '英数字にしてください';
    } elseif (strlen($password) < 4 || strlen($password) > 8) {
        $error['password'] = 'パスワードは4文字以上8文字以下にしてください';
    }
    if (mb_strlen($comment) > 150) {
        $error['comment'] = '150文字以下にしてください';
    } elseif (empty($error)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (screen_name, user_name, email, password, comment) VALUES (:screen_name, :user_name, :email, :password, :comment)';
        $statement = $db->prepare($sql);
        $statement->bindValue(':screen_name', $screen_name, PDO::PARAM_STR);
        $statement->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':password', $hash, PDO::PARAM_STR);
        $statement->bindValue(':comment', $comment, PDO::PARAM_STR);
        $statement->execute();

        $signin_url = 'signin.php';
        header("Location: {$signin_url}");
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
            <h1>新規登録</h1>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="userName">ユーザ名</label>
                    <input type="text" class="form-control" id="userName" name="user_name" placeholder="3文字以上15文字以下"
                           value="<?php print escape($user_name); ?>">

                    <p><?php if (array_key_exists('user_name', $error)) {
                            print escape($error['user_name']);
                        } ?></p>
                </div>
                <div class="form-group">
                    <label for="screenName">ユーザID</label>
                    <input type="text" class="form-control" id="screenName" name="screen_name"
                           value="<?php print escape($screen_name); ?>" placeholder="3文字以上15文字以下">

                    <p><?php if (array_key_exists('screen_name', $error)) {
                            print escape($error['screen_name']);
                        } ?></p>
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?php print escape($email); ?>">

                    <p><?php if (array_key_exists('email', $error)) {
                            print escape($error['email']);
                        } ?></p>
                </div>
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="4文字以上8文字以下">

                    <p><?php if (array_key_exists('password', $error)) {
                            print escape($error['password']);
                        } ?></p>
                </div>
                <div class="form-group">
                    <label for="comment">一言</label>
                    <textarea class="form-control" id="comment" name="comment" placeholder="150文字以内" rows="3"
                              style="resize:none"><?php print escape($comment); ?></textarea>

                    <p><?php if (array_key_exists('comment', $error)) {
                            print escape($error['comment']);
                        } ?></p>
                </div>
                <input type="hidden" name="token" value="<?php print escape($_SESSION['token']); ?>">
                <button type="submit" class="btn btn-default">新規登録</button>
            </form>
            <p>ログインは<a href="./signin.php">こちら</a></p>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
