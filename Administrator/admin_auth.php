<?php
// セッション開始（まだ開始されていない場合のみ）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 管理者ログインチェック
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // ログインしていなければログイン画面へリダイレクト
    header("Location: admin_login.php");
    exit;
}

// 管理者情報をセッションから取得（表示用など）
$admin_email = $_SESSION['admin_email'] ?? '';
$admin_name  = $_SESSION['admin_name'] ?? '';
