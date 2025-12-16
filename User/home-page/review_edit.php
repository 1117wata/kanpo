<?php
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: user_login.php");
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// ユーザ情報取得
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$review_id = $_GET['id'] ?? null;

$sql = "SELECT * FROM review INNER JOIN store 
ON review.store_id = store.store_id 
WHERE review.review_id = ? 
ORDER BY review.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$review_id]);
$reviews = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>口コミ編集画面</title>
    <link rel="stylesheet" href="css/review_edit.css">
    <script src="js/review_edit.js" defer></script>
</head>
<body>
<!-- ヘッダー -->
<div class="header-bar">
    <a href="home.php" class="logo-link">
        <img src="../../images/Ukanpo.png" alt="サイトロゴ">
    </a>
    <!--<div class="page-title">店舗情報詳細</div>-->
</div>


    <h3><?= $reviews['store_name'] ?></h3>

    <div class="user-info">
      <span><?= $user['nickname'] ?></span>
    </div>

    <!-- 星評価 -->
    <!-- 星評価 -->
<div class="stars">
  <span class="star" data-value="1">★</span>
  <span class="star" data-value="2">★</span>
  <span class="star" data-value="3">★</span>
  <span class="star" data-value="4">★</span>
  <span class="star" data-value="5">★</span>
</div>
<input type="hidden" id="ratingInput" name="rating" value="<?= $reviews['rating'] ?>">


    <!-- 価格選択 -->
    <div class="price-select">
      <select>
        <option>¥1〜1,000</option>
        <option>¥1,000〜2,000</option>
        <option>¥2,000〜3,000</option>
        <option>¥3,000〜4,000</option>
        <option>¥4,000〜5,000</option>
        <option>¥5,000〜6,000</option>
        <option>¥6,000〜7,000</option>
        <option>¥7,000〜8,000</option>
        <option>¥8,000〜9,000</option>
        <option>¥10,000以上</option>
      </select>
    </div>

    <!-- コメント -->
    <div class="comment-section">
      <h4>コメント</h4>
      <textarea placeholder="この場所での自分の体験や感想を共有しましょう"><?= $reviews['comment'] ?></textarea>
    </div>

    <!-- 写真セクション -->
    <div class="photo-section">
      <h4>写真</h4>
      <div class="photo-preview" id="photoPreview"></div>

      <div class="menu-wrapper">
        <button class="menu-btn">⋯</button>
        <div class="delete-menu" id="deleteMenu">
          <button class="select-all">すべて選択</button>
          <button class="delete">削除</button>
        </div>
      </div>

      <label for="photoInput" class="add-photo-btn">📷 写真を追加</label>
      <input type="file" id="photoInput" accept="image/*" multiple>
    </div>

    <hr class="divider">

    <!-- 更新ボタン -->
    <button class="submit-btn">更新</button>

  </body>
</html>