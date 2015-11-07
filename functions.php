<?php

function connectDb()
{
    try {
        return new PDO(DSN, DB_USER, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    } catch (PDOException $e) {
        print $e->getMessage();
        exit;
    }
}

function escape($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function setToken()
{
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

function checkToken()
{
    if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
        print '不正なPOSTが行われました！';
        exit;
    }
}

function emailExists($email, PDO $pdo)
{
    $sql = 'SELECT * FROM users WHERE email = :email';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();

    return $row ? true : false;
}

function screenNameExists($screen_name, PDO $pdo)
{
    $sql = 'SELECT * FROM users WHERE screen_name = :screen_name';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':screen_name', $screen_name, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();

    return $row ? true : false;
}

function getUserId($email, $password, PDO $pdo)
{
    $sql = 'SELECT id, password FROM users WHERE email = :email';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    if (password_verify($password, $row['password'])) {
        return $row['id'];
    } else {
        return false;
    }
}

function isSignin()
{
    if (!isset($_SESSION['user_id'])) {
        // 変数に値がセットされていない場合
        return false;
    } else {
        return true;
    }
}

function getUserData(PDO $pdo, $id)
{
    $sql = 'SELECT * FROM users WHERE id=:id';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    if ($row = $statement->fetch()) {
        return $row;
    } else {
        throw new Exception('ユーザデータを取得できません');
    }
}

function writePost(PDO $pdo, $id, $text)
{
    $sql = 'INSERT INTO posts (user_id,text) VALUES (:user_id, :text)';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':user_id', $id, PDO::PARAM_INT);
    $statement->bindValue(':text', $text, PDO::PARAM_STR);
    $statement->execute();
}

function getTimeline(PDO $pdo, $start, $postsNum)
{
    $sql = 'SELECT * FROM posts ORDER BY `created_at` DESC LIMIT :start, :postsNum';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':start', $start, PDO::PARAM_INT);
    $statement->bindValue(':postsNum', $postsNum, PDO::PARAM_INT);
    $statement->execute();

    if ($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
        return $rows;
    } else {
        return false;
    }
}

function postsCounter(PDO $pdo)
{
    $sql = 'SELECT COUNT(*) FROM posts';
    $statement = $pdo->prepare($sql);
    $statement->execute();

    if ($row = $statement->fetch(PDO::FETCH_NUM)) {
        return $row[0];
    } else {
        return 0;
    }
}

function getPostData(PDO $pdo, $id)
{
    $sql = 'SELECT * FROM posts WHERE id = :id';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        return $row;
    } else {
        throw new Exception('投稿データを取得できません');
    }
}

function deletePost(PDO $pdo, $id)
{
    $sql = 'DELETE FROM posts WHERE id = :id';
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
}
