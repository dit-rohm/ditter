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
  $sql = "SELECT * FROM users WHERE id=:id";
  $statement = $pdo->prepare($sql);
  $statement->bindValue(':id', $id, PDO::PARAM_INT);
  $statement->execute();

  if ($row = $statement->fetch()) {
    return $row;
  } else {
    return exit;
  }
}

function writePost($pdo, $id, $text) {
  $sql = "INSERT INTO posts (user_id,text) VALUES (:user_id, :text)";
  $statement = $pdo->prepare($sql);
  $statement->bindValue(':user_id', $id, PDO::PARAM_INT);
  $statement->bindValue(':text', $text, PDO::PARAM_STR);
  $statement->execute();
}

function getTimeline($pdo, $start, $postsNum) {
  $sql = "SELECT * FROM posts ORDER BY `created_at` DESC LIMIT :start, :postsNum";
  $statement = $pdo->prepare($sql);
  $statement->bindValue(':start', $start, PDO::PARAM_INT);
  $statement->bindValue(':postsNum', $postsNum, PDO::PARAM_INT);
  $statement->execute();

  if ($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
    return $rows;
  } else {
    return exit;
  }
}

function postsCounter($pdo) {
  $sql = "SELECT COUNT(*) FROM posts";
  $statement = $pdo->prepare($sql);
  $statement->execute();

  $row = $statement->fetch();
  return $row[0];
}

function getPostData($pdo, $id) {
  $sql = "SELECT * FROM posts WHERE id = :id";
  $statement = $pdo->prepare($sql);
  $statement->bindValue(':id', $id, PDO::PARAM_INT);
  $statement->execute();

  if ($row = $statement->fetchAll(PDO::FETCH_ASSOC)) {
    return $row[0];
  } else {
    return exit;
  }
}

function deletePost($pdo, $id){
  $sql = "DELETE FROM posts WHERE id = :id";
  $statement = $pdo->prepare($sql);
  $statement->bindValue(':id', $id, PDO::PARAM_INT);
  $statement->execute();
}
