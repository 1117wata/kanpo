<?php
// db_connect.php

function getDB() {
    static $pdo = null; 

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=kanpo;charset=utf8",
                "root",
                ""
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "DB接続エラー: " . $e->getMessage();
            exit;
        }
    }

    return $pdo;
}
?>
