<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nickname'])) {
    $nickname = trim($_POST['nickname']);

    if ($nickname === '') {
        echo "empty";
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE nickname = :nickname");
        $stmt->bindParam(':nickname', $nickname);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            echo "used"; // 使用済み
        } else {
            echo "ok"; // 使用可能
        }

    } catch (PDOException $e) {
        echo "error";
    }
} else {
    echo "error";
}
