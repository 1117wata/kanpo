<?php
require 'db_connect.php';  // DB接続

$id = $_GET['id'] ?? null;
if ($id) {
  $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
  $stmt->execute([$id]);
}

// 削除後に前のページへ戻る
header("Location: index.php");
exit;
