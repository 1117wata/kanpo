<?php
session_start();

// DB接続
include '../kanpo/DB/db_connect.php';

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    exit("ログインしてください");
}

$user_id = $_SESSION['user_id'];

// 店舗IDを受け取る
$store_id = $_GET['store_id'] ?? $_POST['store_id'] ?? null;
if (!$store_id) {
    exit("店舗IDが指定されていません");
}

// 店舗名を取得
$stmt = $pdo->prepare("SELECT store_name FROM store WHERE store_id = :store_id");
$stmt->execute([':store_id' => $store_id]);
$store = $stmt->fetch();
if (!$store) {
    exit("店舗が存在しません");
}

// --------------------
// 投稿処理 (POST時)
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? '';
    $price_range_id = $_POST['price_range_id'] ?? null;
    $visit_date = $_POST['visit_date'] ?? null;

    if (!$rating) {
        exit("評価が入力されていません");
    }

    // reviewテーブルにINSERT
    $stmt = $pdo->prepare("
        INSERT INTO review (user_id, store_id, price_range_id, rating, comment, visit_date, created_at, updated_at)
        VALUES (:user_id, :store_id, :price_range_id, :rating, :comment, :visit_date, NOW(), NOW())
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':store_id' => $store_id,
        ':price_range_id' => $price_range_id,
        ':rating' => $rating,
        ':comment' => $comment,
        ':visit_date' => $visit_date
    ]);

    $review_id = $pdo->lastInsertId();

    // 写真アップロード
    if (!empty($_FILES['photos']['name'][0])) {
        $uploadDir = "../../uploads/reviews/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($_FILES['photos']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = uniqid() . "_" . basename($_FILES['photos']['name'][$i]);
                $filePath = $uploadDir . $filename;
                move_uploaded_file($tmpName, $filePath);

                $stmt = $pdo->prepare("
                    INSERT INTO review_photo_id (review_id, photo_path, uploaded_at)
                    VALUES (:review_id, :photo_path, NOW())
                ");
                $stmt->execute([
                    ':review_id' => $review_id,
                    ':photo_path' => $filePath
                ]);
            }
        }
    }

    // 投稿完了 → 店舗詳細へ戻す
    header("Location: store_detail.php?store_id=" . urlencode($store_id));
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>口コミ投稿</title>
<link rel="stylesheet" href="css/review_post.css">
<script src="js/review_post.js" defer></script>
</head>
<body>
<header>
  <a href="store_detail.php?store_id=<?= htmlspecialchars($store_id) ?>">← 店舗詳細へ戻る</a>
</header>

<main>
  <h2><?= htmlspecialchars($store['store_name']) ?> に口コミを投稿</h2>

  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="store_id" value="<?= htmlspecialchars($store_id) ?>">

    <!-- 星評価 -->
    <div class="stars">
      <span class="star" data-value="1">★</span>
      <span class="star" data-value="2">★</span>
      <span class="star" data-value="3">★</span>
      <span class="star" data-value="4">★</span>
      <span class="star" data-value="5">★</span>
    </div>
    <input type="hidden" name="rating" id="ratingInput">

    <!-- コメント -->
    <textarea name="comment" placeholder="体験や感想を共有しましょう"></textarea>

    <!-- 写真アップロード -->
    <div class="photo-v">
      <label class="photo-upload">
        📷 写真を追加
        <input type="file" name="photos[]" id="photoInput" accept="image/*" multiple>
      </label>
      <div class="uploaded-photos" id="photoPreview"></div>
    </div>

    <!-- 費用選択 -->
    <div class="cost-box">
      <h3>1人当たりの費用はいくらでしたか？</h3>
      <div class="cost-options">
        <button type="button" data-value="1">¥1〜1,000</button>
        <button type="button" data-value="2">¥1,000〜2,000</button>
        <button type="button" data-value="3">¥2,000〜3,000</button>
        <button type="button" data-value="4">¥3,000〜4,000</button>
        <button type="button" data-value="5">¥4,000〜5,000</button>
        <button type="button" data-value="6">¥5,000〜6,000</button>
        <button type="button" data-value="7">¥6,000〜7,000</button>
        <button type="button" data-value="8">¥7,000〜8,000</button>
        <button type="button" data-value="9">¥8,000〜9,000</button>
        <button type="button" data-value="10">¥10,000以上</button>
      </div>
      <input type="hidden" name="price_range_id" id="priceRangeInput">
    </div>

    <button type="submit" class="submit">投稿</button>
  </form>
</main>
</body>
</html>
