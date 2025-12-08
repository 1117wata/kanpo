<?php
session_start();
require_once '../../DB/db_connect.php';
$pdo = getDB();

$user = null;
$reviews = [];

if (!empty($_SESSION['user_id'])) {
    // ユーザ情報取得
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // ユーザのレビュー一覧取得（店舗情報・価格帯・レビュー画像もJOIN）
    $stmt = $pdo->prepare("
        SELECT r.*, s.store_name, s.genre, pr.label AS price_label, rp.photo_path
        FROM review r
        JOIN store s ON r.store_id = s.store_id
        LEFT JOIN price_range pr ON r.price_range_id = pr.price_range_id
        LEFT JOIN review_photo_id rp ON r.review_id = rp.review_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $rows = $stmt->fetchAll();

    // レビューごとに写真をまとめる
    foreach ($rows as $row) {
        $rid = $row['review_id'];
        if (!isset($reviews[$rid])) {
            $reviews[$rid] = $row;
            $reviews[$rid]['photos'] = [];
        }
        if (!empty($row['photo_path'])) {
            $reviews[$rid]['photos'][] = $row['photo_path'];
        }
    }
}

function renderStars($rating) {
    $fullStars = floor($rating);               // 完全な星の数
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0; // 半分の星
    $emptyStars = 5 - $fullStars - $halfStar;  // 残りは空の星

    $stars = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<span class="star full">★</span>';
    }
    if ($halfStar) {
        $stars .= '<span class="star half">★</span>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<span class="star empty">★</span>';
    }
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>プロフィール画面</title>
  <link rel="stylesheet" href="css/profile.css">
</head>
<body>
<!-- ヘッダー -->
<header class="header-bar">
    <a href="./home.php" class="logo-link">
        <img src="../../images/Ukanpo.png" alt="サイトロゴ">
    </a>
    <h1 class="page-title">プロフィール</h1>
    <form method="post" action="user_logout.php" onsubmit="return confirmLogout();" style="margin:0;">
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
</header>

<!-- プロフィール情報 -->
<div class="profile">
    <div class="nickname-label"><?= htmlspecialchars($user['nickname']) ?></div>
    <div class="review"><?= count($reviews) ?><br>口コミ</div>
</div>

<form action="profile_edit.php" method="get">
    <button type="submit" class="profile_edit">プロフィール編集</button>
</form>

<!-- レビュー一覧 -->
<?php foreach ($reviews as $review): ?>
  <!-- 灰色ボックスに投稿日 -->
  <div class="border_box">
    投稿日: <?= htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8') ?>
  </div>

  <div class="store">
    <div class="store-header">
      <div class="store_name">
        <?= htmlspecialchars($review['store_name'], ENT_QUOTES, 'UTF-8') ?>
      </div>
      <!-- 横三点メニュー -->
      <div class="ellipsis-menu">
        <button class="ellipsis-button">...</button>
        <ul class="menu">
          <li><a href="review_edit.php?id=<?= $review['review_id'] ?>">編集</a></li>
          <li><a href="profile_delete.php?id=<?= $review['review_id'] ?>">削除</a></li>
        </ul>
      </div>
    </div>

    <div class="store_genre">
      <?= htmlspecialchars($review['genre'] ?? 'ジャンル未登録', ENT_QUOTES, 'UTF-8') ?>
    </div>

    <hr>
    <div class="store_review">
  利用価格帯: <?= htmlspecialchars($review['price_label'] ?? '-') ?><br>
  <?= renderStars($review['rating']) ?>
  <?= htmlspecialchars($review['rating']) ?>
</div>

    <div class="store_review_comment">
      <?= nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')) ?>
    </div>

       <!-- レビュー画像 -->
    <?php if (!empty($review['photos'])): ?>
      <div class="store_image">
        <?php foreach ($review['photos'] as $photo): ?>
          <img src="../../uploads/reviews/<?= htmlspecialchars($photo) ?>" alt="口コミ画像">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

<script>
function confirmLogout() {
    return confirm("ログアウトしますか？");
}

// ... メニュー開閉制御
document.querySelectorAll('.ellipsis-button').forEach(button => {
  button.addEventListener('click', (e) => {
    e.stopPropagation();
    const menu = button.nextElementSibling;
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
  });
});

// 外クリックで閉じる
document.addEventListener('click', () => {
  document.querySelectorAll('.menu').forEach(menu => {
    menu.style.display = 'none';
  });
});
</script>
</body>
</html>
