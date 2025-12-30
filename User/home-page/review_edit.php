<?php
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: user_login.php");
    exit;
}

$pdo = new PDO(
    'mysql:host=mysql322.phy.lolipop.lan;dbname=LAA1681943-watabe17;charset=utf8',
    'LAA1681943',
    'Watabe17',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$review_id = $_GET['id'] ?? null;

/* ============================
   ★ 更新処理（POST）
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = $_POST['rating'] ?? null;
    $price = $_POST['price_range'] ?? null;
    $comment = $_POST['comment'] ?? null;

    // 更新SQL
    $sql = "UPDATE review 
            SET rating = ?, price_range_id = ?, comment = ?
            WHERE review_id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rating, $price, $comment, $review_id, $user_id]);

    // 写真アップロード（必要なら追加）
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
            if (!is_uploaded_file($tmp)) continue;

            $filename = time() . "_" . basename($_FILES['photos']['name'][$i]);
            $path = "../../uploads/reviews/" . $filename;
            move_uploaded_file($tmp, $path);

            $stmt = $pdo->prepare("INSERT INTO review_photo_id (review_id, photo_path) VALUES (?, ?)");
            $stmt->execute([$review_id, $filename]);
        }
    }

    // 更新後プロフィールへ戻る
    header("Location: profile.php");
    exit;
}

/* ============================
   ★ 表示用データ取得
============================ */
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$sql = "SELECT * FROM review 
        INNER JOIN store ON review.store_id = store.store_id 
        WHERE review.review_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$review_id]);
$reviews = $stmt->fetch();
?>
<form action="" method="post" enctype="multipart/form-data">

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
</div>

<h3><?= $reviews['store_name'] ?></h3>

<div class="user-info">
  <span><?= $user['nickname'] ?></span>
</div>

<!-- 星評価 -->
<div class="stars">
  <span class="star" data-value="1">★</span>
  <span class="star" data-value="2">★</span>
  <span class="star" data-value="3">★</span>
  <span class="star" data-value="4">★</span>
  <span class="star" data-value="5">★</span>
</div>

<!-- ★ name を追加 -->
<input type="hidden" id="ratingInput" name="rating" value="<?= $reviews['rating'] ?>">

<!-- 価格選択 -->
<div class="price-select">
  <select name="price_range">
    <option value="1">¥1〜1,000</option>
    <option value="2">¥1,000〜2,000</option>
    <option value="3">¥2,000〜3,000</option>
    <option value="4">¥3,000〜4,000</option>
    <option value="5">¥4,000〜5,000</option>
    <option value="6">¥5,000〜6,000</option>
    <option value="7">¥6,000〜7,000</option>
    <option value="8">¥7,000〜8,000</option>
    <option value="9">¥8,000〜9,000</option>
    <option value="10">¥10,000以上</option>
  </select>
</div>

<!-- コメント -->
<div class="comment-section">
  <h4>コメント</h4>
  <textarea name="comment" placeholder="この場所での自分の体験や感想を共有しましょう"><?= $reviews['comment'] ?></textarea>
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

  <!-- ★ name を追加 -->
  <input type="file" id="photoInput" name="photos[]" accept="image/*" multiple>
</div>

<hr class="divider">

<!-- ★ submit に変更 -->
<button type="submit" class="submit-btn">更新</button>

</body>
</html>

</form>
