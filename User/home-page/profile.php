<?php
session_start();

// ログインしていなければ、ログイン画面にリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// データベース接続
$pdo = new PDO('mysql:host=localhost;dbname=sample;charset=utf8', 'root', '');

// セッションに保存されたユーザーIDで検索
$stmt = $pdo->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール画面</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>プロフィール</h1></header>
</body>