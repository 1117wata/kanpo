<?php
session_start();
$_SESSION['user_id'] = 1; // テスト用にユーザーIDをセット
$user_id = $_SESSION['user_id'];
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$sql = "SELECT * FROM review LEFT JOIN review_photo_id 
ON review.review_id = review_photo_id.review_id WHERE review.user_id = ?
ORDER BY review.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll();
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
    <img src="../../images/Kanpo.png" alt="Logo" class="logo">
    プロフィール
  </header>
  <div class="profile">
    <div class="name">
      おいしいもの大好きマン
    </div>
    <div class="review">
      ４<br>
      口コミ
    </div>
  </div>
  <button type="submit" class="profile_edit">プロフィール編集</button>

  <div class="border_box"></div>

  <div class="store">
    <div class="store_name">
      バーガーキング 博多駅筑紫口店<br>
    </div>
    <div class="store_genre">
      博多/ハンバーガー、サンドウィッチ、ファーストフード
    </div>
    <div class="ellipsis-menu">
      <button class="ellipsis-button">...</button>
      <ul class="menu">
        <li id="edit">編集</li>
        <li id="delete">削除</li>
      </ul>
    </div>
    <hr>
    <div class="store_review">
      2025/09/01 訪問<br>
      ☆☆☆☆☆ 5.0
    </div>
    <div class="store_image">
      <img src="uploads/burger_king1.png">
      <img src="uploads/burger_king2.png">
      <img src="uploads/burger_king3.png">
    </div>
    <div class="store_review_comment">
      初バーガーキングのワッパーです。セットでDr.pepperとフレンチフライと王道の組み合わせでしょうか。肉肉しさも感じつつ野菜も割と入ってておいしかったです。ソースもケチャップとシンプルisベストでした。ソース味で食べてる感がしなかったのが良かったです。並んでなかったから入りました。
    </div>
  </div>

  <div class="border_box"></div>

  <div class="store">
    <div class="store_name">
      博多もつ鍋 徳永屋 総本店<br>
    </div>
    <div class="store_genre">
      祇園/もつ鍋、手羽先、郷土料理
    </div>
    <div class="ellipsis-menu">
      <button class="ellipsis-button">...</button>
      <ul class="menu">
        <li id="edit">編集</li>
        <li id="delete">削除</li>
      </ul>
    </div>
    <hr>
    <div class="store_review">
      2025/09/01 訪問<br>
      ☆☆☆☆☆ 5.0
    </div>
    <div class="store_image">
      <img src="uploads/burger_king1.png">
      <img src="uploads/burger_king2.png">
      <img src="uploads/burger_king3.png">
    </div>
    <div class="store_review_comment">
      初バーガーキングのワッパーです。セットでDr.pepperとフレンチフライと王道の組み合わせでしょうか。肉肉しさも感じつつ野菜も割と入ってておいしかったです。ソースもケチャップとシンプルisベストでした。ソース味で食べてる感がしなかったのが良かったです。並んでなかったから入りました。
    </div>
  </div>

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