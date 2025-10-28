<?php
session_start();
$_SESSION['user_id'] = 1; // テスト用にユーザーIDをセット
$user_id = $_SESSION['user_id'];
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$sql = "SELECT * FROM review INNER JOIN store 
ON review.store_id = store.store_id 
WHERE review.user_id = ? 
ORDER BY review.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll();

$user_sql = "SELECT username FROM user WHERE user_id = ?";
$user_stmt = $pdo->prepare($user_sql);
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();
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
  <header>
    <img src="../../images/Kinpo.png" alt="Logo" class="logo">
    プロフィール
  </header>
  <div class="profile">
    <div class="name">
      <?= $user['username'] ?>
    </div>
    <div class="review">
      <?= count($reviews) ?><br>
      口コミ
    </div>
  </div>
  <button type="submit" class="profile_edit">プロフィール編集</button>

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
      <a href="profile_edit.php?id=<?= $review['review_id'] ?>">編集</a><br>
      <a href="profile_delete.php?id=<?= $review['review_id'] ?>">削除</a>
      <ul class="menu">
        <li id="edit">編集</li>
        <li id="delete">削除</li>
      </ul>
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