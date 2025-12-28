<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nickname'])) {
    $nickname = trim($_POST['nickname']);

    if ($nickname === '') {
        echo "empty";
        exit;
    }

    try {
        $pdo = new PDO('mysql:host=mysql322.phy.lolipop.lan;dbname=LAA1681943-watabe17;charset=utf8', 'LAA1681943', 'Watabe17');
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
