<?php
session_start();
include '../kanpo/DB/db_connect.php';
// ログイン中のユーザーIDを取得
$user_id = $_SESSION['user_id'] ?? null;

// URLから削除対象の review_id を取得
$review_id = $_GET['id'] ?? null;

if ($user_id && $review_id) {
    // 自分のレビューのみ削除可能にする（セキュリティ対策）
    $stmt = $pdo->prepare("DELETE FROM review WHERE review_id = ? AND user_id = ?");
    $stmt->execute([$review_id, $user_id]);
}

// 削除後、プロフィールページに戻る
header("Location: profile.php");
exit;
