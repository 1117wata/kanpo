<?php
session_start();

require_once '../../DB/db_connect.php';
$pdo = getDB();

$user = null;

if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

$reviews = [];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</header>

  <div class="profile">
    <div class="nickname-label">
      <?= htmlspecialchars($user['nickname']) ?>
    </div>
    <div class="review">
      <?= count($reviews) ?><br>
      口コミ
    </div>
  </div>
  <form action="profile_edit.php" method="get">
    <button type="submit" class="profile_edit">プロフィール編集</button>
  </form>

  <?php foreach ($reviews as $review): ?>
  <div class="border_box"></div>
  <div class="store">
    <div class="store_name">
      <?= htmlspecialchars($review['store_name'], ENT_QUOTES, 'UTF-8') ?><br>
    </div>
    <div class="store_genre">
      <?= htmlspecialchars($review['genre'] ?? 'ジャンル未登録', ENT_QUOTES, 'UTF-8') ?>
    </div>
    <div class="ellipsis-menu">
      <a href="review_edit.php?id=<?= $review['review_id'] ?>">編集</a><br>
      <a href="profile_delete.php?id=<?= $review['review_id'] ?>">削除</a>
    </div>
    <hr>
    <div class="store_review">
      <?= htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8') ?> 訪問<br>
      ☆☆☆☆☆ <?= htmlspecialchars($review['rating'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
    </div>
    <div class="store_review_comment">
      <?= nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')) ?>
    </div>
  </div>

  </div>
  <?php endforeach; ?>
<hr>
  

<script>
const button = document.querySelector('.ellipsis-button');
const menu = document.querySelector('.menu');

button.addEventListener('click', () => {
  menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});

// メニュー外をクリックしたら閉じる
document.addEventListener('click', (e) => {
  if (!button.contains(e.target) && !menu.contains(e.target)) {
    menu.style.display = 'none';
  }
});

// 編集・削除のクリックイベント
document.getElementById('edit').addEventListener('click', () => {
  window.location.href = 'review_edit.php';
  menu.style.display = 'none';
});

document.getElementById('delete').addEventListener('click', () => {
  window.location.href = 'profile_delete.php';
  menu.style.display = 'none';
});

</script>
</body>
</html>