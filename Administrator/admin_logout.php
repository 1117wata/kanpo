<?php
session_start();

// 管理者用セッションを破棄
$_SESSION = [];
session_destroy();

// ユーザー側ログインページにリダイレクト
header("Location: ../User/login-page/login.php");
exit;
