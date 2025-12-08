<?php
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');

$category_stmt = $pdo->query("SELECT category_id, category_name FROM category");
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

$area_stmt = $pdo->query("SELECT area_id, area_name FROM area");
$areas = $area_stmt->fetchAll(PDO::FETCH_ASSOC);

$where = [];
$params = [];

if (!empty($_GET['keyword'])) {
  $where[] = "(s.store_name LIKE :keyword OR s.genre LIKE :keyword)";
  $params[':keyword'] = '%' . $_GET['keyword'] . '%';
}
if (!empty($_GET['category_id'])) {
  $where[] = "s.category_id = :category_id";
  $params[':category_id'] = $_GET['category_id'];
}
if (!empty($_GET['area_id'])) {
  $where[] = "s.area_id = :area_id";
  $params[':area_id'] = $_GET['area_id'];
}

$sql = "
  SELECT 
    s.*, 
    AVG(r.rating) AS avg_rating,
    COUNT(r.review_id) AS review_count
  FROM store s
  LEFT JOIN review r ON s.store_id = r.store_id
";
if ($where) {
  $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " GROUP BY s.store_id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStorePhotos($pdo, $store_id) {
  $stmt = $pdo->prepare("SELECT store_photo_path FROM store_photo WHERE store_id = :store_id ORDER BY uploaded_at DESC");
  $stmt->execute([':store_id' => $store_id]);
  return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
function renderStars($rating) {
  if ($rating === null) return '<span class="no-rating">評価なし</span>';

  $fullStars = floor($rating);
  $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
  $emptyStars = 5 - $fullStars - $halfStar;

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
  <title>検索結果</title>
  <link rel="stylesheet" href="css/search_result.css">
  <script src="js/search_results.js" defer></script>

  <!-- Google Fonts 読み込み -->
  <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&family=Parisienne&family=Italianno&family=Kosugi+Maru&family=Sawarabi+Gothic&family=Noto+Serif+JP&family=Nanum+Brush+Script&family=Rock+Salt&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

<header>
  <a href="home.php">← 戻る</a>
</header>

<h2>検索結果</h2>

<form action="search_results.php" method="GET" class="search-box">
  <input type="text" name="keyword" placeholder="店舗名・キーワードで検索" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" />
  
  <select name="category_id">
    <option value="">カテゴリ選択</option>
    <?php foreach ($categories as $category): ?>
      <option value="<?= htmlspecialchars($category['category_id']) ?>"
        <?= ($_GET['category_id'] ?? '') == $category['category_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($category['category_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <select name="area_id">
    <option value="">エリア選択</option>
    <?php foreach ($areas as $area): ?>
      <option value="<?= htmlspecialchars($area['area_id']) ?>"
        <?= ($_GET['area_id'] ?? '') == $area['area_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($area['area_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <button type="submit">検索</button>
</form>

<?php if (count($stores) === 0): ?>
  <p>該当する店舗は見つかりませんでした。</p>
<?php else: ?>
  <ul class="store-list">
    <?php foreach ($stores as $store): ?>
      <?php $photos = getStorePhotos($pdo, $store['store_id']); ?>
      <li>
        <!-- 店舗名もカテゴリごとに装飾 -->
        <h2 class="
          <?php 
            switch ($store['category_id']) {
              case 1: echo 'cat-chinese'; break;
              case 2: echo 'cat-french'; break;
              case 3: echo 'cat-global'; break;
              case 4: echo 'cat-italian'; break;
              case 5: echo 'cat-izakaya'; break;
              case 6: echo 'cat-japanese'; break;
              case 7: echo 'cat-kaiseki'; break;
              case 8: echo 'cat-korean'; break;
              case 9: echo 'cat-robata'; break;
            }
          ?>">
          <a href="store_detail.php?store_id=<?= htmlspecialchars($store['store_id']) ?>">
            <?= htmlspecialchars($store['store_name']) ?>
          </a>
        </h2>

        <!-- ジャンルもカテゴリごとに装飾 -->
        <p class="store-genre 
          <?php 
            switch ($store['category_id']) {
              case 1: echo 'cat-chinese'; break;
              case 2: echo 'cat-french'; break;
              case 3: echo 'cat-global'; break;
              case 4: echo 'cat-italian'; break;
              case 5: echo 'cat-izakaya'; break;
              case 6: echo 'cat-japanese'; break;
              case 7: echo 'cat-kaiseki'; break;
              case 8: echo 'cat-korean'; break;
              case 9: echo 'cat-robata'; break;
            }
          ?>">
          <?= htmlspecialchars($store['genre']) ?> / <?= htmlspecialchars($store['store_address']) ?>
        </p>

        <p class="store-rating">
          評価: <?= renderStars($store['avg_rating']) ?>
          （<?= $store['avg_rating'] !== null ? number_format($store['avg_rating'], 1) : '評価なし' ?>）
          <a class="review-count" href="reviews.php?store_id=<?= htmlspecialchars($store['store_id']) ?>">[<?= $store['review_count'] ?>件の口コミ]</a>
        </p>

        <div class="store-photos-wrapper">
          <div class="store-photos" data-photos='<?= json_encode($photos) ?>'></div>
          <button class="photo-arrow photo-prev">←</button>
          <button class="photo-arrow photo-next">→</button>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<!-- モーダル -->
<div id="imageModal" class="modal">
  <span class="modal-close" onclick="closeModal()">×</span>
  <img class="modal-content" id="modalImage">
</div>

</body>
</html>
