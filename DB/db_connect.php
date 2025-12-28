<?php

function getDB() {
    static $pdo = null; 

    if ($pdo === null) {
        try {
            
$pdo = new PDO('mysql:host=mysql322.phy.lolipop.lan;dbname=LAA1681943-watabe17;charset=utf8', 'LAA1681943', 'Watabe17');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "DB接続エラー: " . $e->getMessage();
            exit;
        }
    }

    return $pdo;
}
?>
