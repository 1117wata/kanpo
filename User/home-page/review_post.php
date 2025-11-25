<?php
// DB接続
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// 店舗IDを受け取る
$store_id = $_GET['store_id'] ?? null;
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
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>口コミ投稿</title>
<link rel="stylesheet" href="css/review_post.css">
</head>
<body>
<header>
  <a href="store_detail.php?store_id=<?= htmlspecialchars($store_id) ?>">← 店舗詳細へ戻る</a>
</header>

<main>
  <h2><?= htmlspecialchars($store['store_name']) ?> に口コミを投稿</h2>

  <form action="review_submit.php" method="post" enctype="multipart/form-data">
    <!-- 店舗IDをhiddenで渡す -->
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
    <label class="photo-upload">
      📷 写真を追加
      <input type="file" name="photos[]" id="photoInput" accept="image/*" multiple>
    </label>
    <div class="uploaded-photos" id="photoPreview"></div>

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

<script>
// 星クリックで評価を反映
const stars = document.querySelectorAll(".star");
stars.forEach(star => {
  star.addEventListener("click", () => {
    const value = star.getAttribute("data-value");
    document.getElementById("ratingInput").value = value;
    stars.forEach(s => {
      s.classList.toggle("active", s.getAttribute("data-value") <= value);
    });
  });
});

// 写真プレビュー
const photoInput = document.getElementById("photoInput");
const photoPreview = document.getElementById("photoPreview");
photoInput.addEventListener("change", () => {
  photoPreview.innerHTML = "";
  Array.from(photoInput.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement("img");
      img.src = e.target.result;
      photoPreview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
});

// 費用選択
const costButtons = document.querySelectorAll(".cost-options button");
costButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    costButtons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    document.getElementById("priceRangeInput").value = btn.getAttribute("data-value");
  });
});
</script>
</body>
</html>
