<?php

function connectDb() {
  try {
    return new PDO(DSN, DB_USER, DB_PASSWORD);
  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }
}

function escape($s) {
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function setToken() {
  $token = sha1(uniqid(mt_rand(), true));
  $_SESSION['token'] = $token;
}

function checkToken() {
  if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
    echo "不正なPOSTが行われました！";
    exit;
  }
}

function getUserId($email, $password, $db) {
    $sql = "SELECT id, password FROM users WHERE email = :email";
    $statement = $db->prepare($sql);
    $statement->bindValue(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    if (password_verify($password, $row['password'])) {
      return $row['id'];
    } else {
      return false;
    }
}
