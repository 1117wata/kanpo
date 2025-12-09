<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    die("ユーザーIDが指定されていません。");
}

// ★ 星表示関数
function renderStars($rating) {
    if ($rating === null) return '<span class="no-rating">評価なし</span>';

    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    $html = '';
    for ($i = 0; $i < $full; $i++) $html .= '<span class="star full">★</span>';
    if ($half) $html .= '<span class="star half">★</span>';
    for ($i = 0; $i < $empty; $i++) $html .= '<span class="star empty">★</span>';

    return $html;
}

// ---- ユーザー名 ----
$stmt = $pdo->prepare("SELECT username FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ---- 口コミ一覧 ----
$stmt = $pdo->prepare("
    SELECT r.*, s.store_name
    FROM review r
    JOIN store s ON r.store_id = s.store_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>口コミ一覧</title>
<link rel="stylesheet" href="css/members.css">
</head>
<body>

<header class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <h1 class="page-title"><?= htmlspecialchars($user['username']) ?> さんの口コミ一覧</h1>
</header>

<div class="back-btn-container">
    <a href="javascript:history.back();" class="back-link">
        <img src="../images/back.png" alt="戻る" class="back-icon">
    </a>
</div>

<div class="detail-container">
<?php if (empty($reviews)): ?>
    <p>口コミはまだありません。</p>
<?php else: ?>
    <?php foreach ($reviews as $review): ?>
        <div class="review-card" style="padding:15px; margin-bottom:15px; border-bottom:1px solid #ddd;">
            
            <p><strong>店舗名：</strong><?= htmlspecialchars($review['store_name']) ?></p>

            <!-- 数字 + 星表示 -->
            <p><strong>評価：</strong>
                <?= htmlspecialchars($review['rating']) ?>
                <?= renderStars($review['rating']) ?>
            </p>

            <p><strong>コメント：</strong><br><?= nl2br(htmlspecialchars($review['comment'])) ?></p>

            <p><strong>投稿日：</strong><?= htmlspecialchars($review['created_at']) ?></p>

            <form action="review_delete.php" method="GET" 
                  onsubmit="return confirm('このレビューを削除しますか？');"
                  style="margin-top:10px;">
                <input type="hidden" name="id" value="<?= $review['review_id'] ?>">
                <button class="delete-btn">削除</button>
            </form>

        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO 管理者
    </div>
</footer>

</body>
</html>
