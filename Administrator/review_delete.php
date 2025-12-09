<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

$review_id = $_GET['id'] ?? null;

if (!$review_id) {
    die("レビューIDが指定されていません。");
}

// 削除前に user_id を取得（戻るため）
$stmt = $pdo->prepare("SELECT user_id FROM review WHERE review_id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    die("該当するレビューがありません。");
}

$user_id = $review['user_id'];

// --- レビュー削除 ---
$stmt = $pdo->prepare("DELETE FROM review WHERE review_id = ?");
$stmt->execute([$review_id]);

// --- 削除後に一覧へ戻す ---
header("Location: user_reviews.php?user_id=" . $user_id);
exit;
?>
