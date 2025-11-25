<?php
$pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8",'root','', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$store_id = $_GET['store_id'] ?? 0;

// 店舗情報取得
$stmt = $pdo->prepare("SELECT store_name FROM store WHERE store_id=:id");
$stmt->execute([':id' => $store_id]);
$store = $stmt->fetch();

// 口コミ取得
$stmt = $pdo->prepare("
    SELECT r.*, u.nickname, u.username
    FROM review r
    JOIN user u ON r.user_id = u.user_id
    WHERE r.store_id = :store_id
    ORDER BY r.created_at DESC
");
$stmt->execute([':store_id' => $store_id]);
$reviews = $stmt->fetchAll();

    // 画像取得
    $stmt = $pdo->prepare("SELECT * FROM store_photo WHERE store_id=:id");
    $stmt->bindParam(':id', $store_id);
    $stmt->execute();
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($store['store_name']) ?> の口コミ一覧</title>
<link rel="stylesheet" href="css/review.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>
</head>
<body>
    <div class="header-bar">
    <a href="home.php" class="logo-link">
        <img src="../../images/Ukanpo.png" alt="サイトロゴ">
    </a>
    <div class="page-title">店舗情報詳細</div>
</div>

<div class="store-detail">
    <h1><?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?></h1>

    <?php if (!empty($photos)): ?>
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <?php foreach($photos as $photo): ?>
            <div class="swiper-slide">
                <img src="../../Administrator/<?= htmlspecialchars($photo['store_photo_path'], ENT_QUOTES) ?>" alt="店舗画像" class="store-image">
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
<h1><?= htmlspecialchars($store['store_name']) ?> の口コミ一覧</h1>

  
<div class="review-list">
  <?php foreach ($reviews as $review): ?>
    <div class="review-card">

      <!-- 上枠：タイトル＋投稿日 -->
      <div class="review-header-box">
        <span class="review-title">みんなの口コミ</span>
        <span class="review-date"><?= htmlspecialchars($review['created_at']) ?></span>
      </div>

      <!-- 下枠：投稿者以下の詳細 -->
      <div class="review-body-box">
        <!-- 投稿者 -->
        <div class="review-user">
          投稿者: <?= htmlspecialchars($review['nickname'] ?: $review['username']) ?>
        </div>

        <!-- 評価（星表示） -->
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

        <!-- 訪問日 -->
        <div class="review-visit">
          訪問日: <?= $review['visit_date'] ? htmlspecialchars($review['visit_date']) : '-' ?>
        </div>

        <!-- コメント -->
        <div class="review-comment">
          <?= nl2br(htmlspecialchars($review['comment'])) ?>
        </div>

        <!-- 画像 -->
        <?php if (!empty($review['photo_path'])): ?>
          <div class="review-photo">
            <img src="../../uploads/<?= htmlspecialchars($review['photo_path']) ?>" alt="口コミ画像">
          </div>
        <?php endif; ?>
      </div>

    </div>
  <?php endforeach; ?>
</div>


<a href="store_detail.php?store_id=<?= htmlspecialchars($store_id) ?>">← 店舗詳細へ戻る</a>

<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO 管理者画面
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const swiper = new Swiper('.mySwiper', {
    loop: true,
    slidesPerView: 3,
    spaceBetween: 20,
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    breakpoints: {
        1024: { slidesPerView: 3 },
        768: { slidesPerView: 2 },
        480: { slidesPerView: 1 }
    }
});
</script>
</body>
</html>

