<?php
try {
    $db = new PDO(
        'mysql:dbname=bbs_app;host=localhost;charset=utf8',
        'root',
        'root',
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );
} catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
}
