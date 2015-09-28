<?php

function connectDb() {
  try {
    return new PDO(DSN, DB_USER, DB_PASSWORD);
  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }
}

function isSignin() {
  if (!isset($_SESSION["user_id"])) {
    // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
    $no_signin_url = "signin.php";
    header("Location: {$no_signin_url}");
    exit;
  }
}

function getUserData($pdo, $id) {
  $statement = $pdo -> prepare("SELECT * FROM users WHERE id=:id");
  $statement -> bindValue(':id', $id, PDO::PARAM_INT);
  $statement -> execute();

  if ($rows = $statement -> fetch()) {
    return $rows;
  } else {
    return exit;
  }
}

function writePost($pdo, $id, $text) {
  $statement = $pdo -> prepare("INSERT INTO posts (id,user_id,text) VALUES ('', :user_id, :text)");
  $statement -> bindValue(':user_id', $id, PDO::PARAM_STR);
  $statement -> bindValue(':text', $text, PDO::PARAM_STR);
  $statement -> execute();
}

function getTimeline($pdo, $start, $postsNum) {
  $statement = $pdo -> prepare("SELECT * FROM `posts` ORDER BY `created_at` DESC LIMIT $start, $postsNum");
  $statement -> execute();

  if ($rows = $statement -> fetchAll(PDO::FETCH_ASSOC)) {
    return $rows;
  } else {
    return exit;
  }
}

function recordCounter($pdo, $table) {
  $statement = $pdo -> prepare("SELECT COUNT(*) FROM $table");
  $statement -> execute();

  $rows = $statement -> fetch();
  return $rows[0];
}
