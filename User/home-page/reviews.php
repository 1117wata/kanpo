<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8",'root','', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$store_id = $_GET['store_id'] ?? 0;

// 並び替え・絞り込みパラメータ
$sort = $_GET['sort'] ?? 'new'; // new, old, high, low
$rating_filter = $_GET['rating'] ?? null;

// 店舗情報 + 平均評価 + 件数
$stmt = $pdo->prepare("
  SELECT 
    s.*, 
    AVG(r.rating) AS avg_rating,
    COUNT(r.review_id) AS review_count
  FROM store s
  LEFT JOIN review r ON s.store_id = r.store_id
  WHERE s.store_id = :id
  GROUP BY s.store_id
");
$stmt->execute([':id' => $store_id]);
$store = $stmt->fetch();

/* ------------------------------
   並び替え SQL を決定
------------------------------ */
$orderSql = "r.created_at DESC"; // デフォルト：新着順

switch ($sort) {
    case 'old':
        $orderSql = "r.created_at ASC";
        break;
    case 'high':
        $orderSql = "r.rating DESC";
        break;
    case 'low':
        $orderSql = "r.rating ASC";
        break;
}

/* ------------------------------
   評価フィルター SQL
------------------------------ */
$whereRating = "";
$params = [':store_id' => $store_id];

if ($rating_filter !== null) {
    $whereRating = " AND r.rating = :rating ";
    $params[':rating'] = $rating_filter;
}

// 口コミ + 写真 + 価格帯取得
$stmt = $pdo->prepare("
    SELECT r.*, u.nickname, u.username, p.photo_path, pr.label AS price_label
    FROM review r
    JOIN user u ON r.user_id = u.user_id
    LEFT JOIN review_photo_id p ON r.review_id = p.review_id
    LEFT JOIN price_range pr ON r.price_range_id = pr.price_range_id
    WHERE r.store_id = :store_id
    $whereRating
    ORDER BY $orderSql
");
$stmt->execute($params);
$rows = $stmt->fetchAll();


// レビューごとに写真をまとめる
$reviews = [];
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




// 店舗画像取得
$stmt = $pdo->prepare("SELECT * FROM store_photo WHERE store_id=:id");
$stmt->bindParam(':id', $store_id);
$stmt->execute();
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 星表示関数
function renderStars($rating) {
  if ($rating === null) return '<span class="no-rating">評価なし</span>';

  $fullStars = floor($rating);
  $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
  $emptyStars = 5 - $fullStars - $halfStar;

  $stars = '';
  for ($i = 0; $i < $fullStars; $i++) $stars .= '<span class="star full">★</span>';
  if ($halfStar) $stars .= '<span class="star half">★</span>';
  for ($i = 0; $i < $emptyStars; $i++) $stars .= '<span class="star empty">★</span>';

  return $stars;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($store['store_name']) ?> の口コミ一覧</title>
<link rel="stylesheet" href="css/review.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="js/reviews.js" defer></script>

<!-- Google Fonts 読み込み -->
<link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&family=Parisienne&family=Italianno&family=Kosugi+Maru&family=Sawarabi+Gothic&family=Noto+Serif+JP&family=Nanum+Brush+Script&family=Rock+Salt&family=Roboto&display=swap" rel="stylesheet">
</head>

<div class="header-bar">
  <a href="home.php" class="logo-link">
    <img src="../../images/Ukanpo.png" alt="サイトロゴ">
  </a>
  <!--<div class="page-title">店舗情報詳細</div>-->
</div>
<body>
<a class="back" href="store_detail.php?store_id=<?= htmlspecialchars($store_id) ?>">←</a>
<!-- 店舗名もカテゴリごとに装飾 -->
<span class="dai
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
  <?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?>
  </span>

<!-- ジャンルもカテゴリごとに装飾 -->
<span class="gen 
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
  <span class="genre"><?= htmlspecialchars($store['genre']) ?></span> ｜ 
  <span class="address"><?= htmlspecialchars($store['store_address']) ?></span>
  <div class="rating-summary">
  <div class="rating-left">
    評価: <?= renderStars($store['avg_rating']) ?>
    （<?= $store['avg_rating'] !== null ? number_format($store['avg_rating'], 1) : '評価なし' ?>）
  </div>
  <div class="rating-center">
    <a class="review-count" href="reviews.php?store_id=<?= htmlspecialchars($store_id) ?>">
      <?= $store['review_count'] ?>件の口コミ
    </a>
  </div>
</div>
</span>

<div class="store-detail">
  <?php if (!empty($photos)): ?>
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <?php foreach($photos as $photo): ?>
      <div class="swiper-slide">
        <img src="../../Administrator/<?= htmlspecialchars($photo['store_photo_path'], ENT_QUOTES) ?>" 
     alt="店舗画像" class="store-image"
     onclick="openImageLightbox(this.src)">

      </div>
      <?php endforeach; ?>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>
  <?php else: ?>
    <p>画像が登録されていません。</p>
  <?php endif; ?>
</div>

<div class="action-bar">
<!-- 投稿ボタン -->
  <div class="rating-right">
    <a class="review-btn" href="review_post.php?store_id=<?= htmlspecialchars($store_id) ?>">
      +口コミを投稿する
    </a>
  </div>
   <!-- 右：並び替え & 絞り込み -->
  <div class="right-actions">

    <!-- 並び替え -->
    <div class="sort-dropdown">
      <button class="sort-toggle">並び替え ▼</button>
      <div class="sort-menu">
        <a href="?store_id=<?= $store_id ?>&sort=new">新着順</a>
        <a href="?store_id=<?= $store_id ?>&sort=old">古い順</a>
        <a href="?store_id=<?= $store_id ?>&sort=high">評価が高い順</a>
        <a href="?store_id=<?= $store_id ?>&sort=low">評価が低い順</a>
      </div>
    </div>

    <!-- 絞り込み -->
    <div class="filter-dropdown">
      <button class="filter-toggle">絞り込み ▼</button>
      <div class="filter-menu">
        <?php foreach ([1,1.5,2,2.5,3,3.5,4,4.5,5] as $r): ?>
          <a href="?store_id=<?= $store_id ?>&rating=<?= $r ?>">
            ★<?= $r ?> の口コミ
          </a>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>




<!-- <h1><?= htmlspecialchars($store['store_name']) ?>の口コミ一覧</h1>-->
<!-- 口コミ一覧 -->
<div class="review-list">
  <?php foreach ($reviews as $review): ?>
    <div class="review-card">
      <div class="review-header-box">
        <span class="review-title">みんなの口コミ</span>
        <span class="review-date"><?= htmlspecialchars($review['created_at']) ?></span>
      </div>
      <div class="review-body-box">
        <div class="review-user">
          投稿者: <?= htmlspecialchars($review['nickname'] ?: $review['username']) ?>
        </div>
        <div class="review-rating">
          <?php
            $fullStars = floor($review['rating']);
            $halfStar = ($review['rating'] - $fullStars) >= 0.5 ? 1 : 0;
            $emptyStars = 5 - $fullStars - $halfStar;
            for ($i=0; $i<$fullStars; $i++) echo '<span class="star full">★</span>';
            if ($halfStar) echo '<span class="star half">★</span>';
            for ($i=0; $i<$emptyStars; $i++) echo '<span class="star empty">★</span>';
          ?>
        </div>
<div class="review-price">
  利用価格帯: <?= htmlspecialchars($review['price_label'] ?? '-') ?>
</div>



        <div class="review-visit">
          訪問日: <?= $review['visit_date'] ? htmlspecialchars($review['visit_date']) : '-' ?>
        </div>
        <div class="review-comment">
          <?= nl2br(htmlspecialchars($review['comment'])) ?>
        </div>
       <?php if (!empty($review['photos'])): ?>
  <div class="review-photo">
  <img src="../../uploads/reviews/<?= htmlspecialchars($review['photos'][0]) ?>" 
       alt="口コミ画像" class="review-thumb" onclick="openReviewGallery(<?= $review['review_id'] ?>)">
  <?php if (count($review['photos']) > 1): ?>
    <span class="photo-count">+<?= count($review['photos']) - 1 ?>枚</span>
  <?php endif; ?>
</div>


  <!-- モーダル（口コミごとに用意） -->
  <div id="reviewGalleryModal<?= $review['review_id'] ?>" class="modal">
  <span class="close" onclick="closeReviewGallery(<?= $review['review_id'] ?>)">&times;</span>
  <div class="modal-content">
    <?php foreach ($review['photos'] as $photo): ?>
      <img src="../../uploads/reviews/<?= htmlspecialchars($photo) ?>" 
           alt="口コミ画像" class="modal-image"
           onclick="openImageLightbox(this.src)">
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

      </div>
    </div>
  <?php endforeach; ?>
</div>



<footer class="footer">
  <div class="footer-content">
    &copy; <?= date('Y') ?> KANPO 管理者画面
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Lightbox本体 -->
<div id="imageLightbox">
  <span class="close" onclick="closeImageLightbox()">&times;</span>
  <img id="lightboxImg" src="" alt="拡大画像">
</div>


</body>
</html>