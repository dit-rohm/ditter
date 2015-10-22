<?php
require_once 'init.php';

// デーテベースへ接続
$db = connectDb();

$sql = '
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `screen_name` VARCHAR(15) NOT NULL UNIQUE,
    `user_name` VARCHAR(15) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `comment` VARCHAR(255),
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `in_reply_to_user_id` INT UNSIGNED,
    `text` VARCHAR(140) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
        REFERENCES users (id)
        ON DELETE CASCADE,
    FOREIGN KEY (in_reply_to_user_id)
        REFERENCES users (id)
        ON DELETE SET NULL,
    PRIMARY KEY (`id`)
);
';

// SQL文を実行
print($db->exec($sql) !== false ? 'OK' : 'NG');