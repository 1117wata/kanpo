<?php
session_start();
require_once '../../DB/db_connect.php';
$pdo = getDB();

// URLパラメータ store_id 取得
$store_id = isset($_GET['store_id']) ? (int)$_GET['store_id'] : 0;
if ($store_id <= 0) {
    echo "店舗IDが不正です";
    exit;
}

// 店舗情報取得
$stmt = $pdo->prepare("SELECT * FROM store WHERE store_id=:id");
$stmt->bindParam(':id', $store_id);
$stmt->execute();
$store = $stmt->fetch(PDO::FETCH_ASSOC);




    // 画像取得
    $stmt = $pdo->prepare("SELECT * FROM store_photo WHERE store_id=:id");
    $stmt->bindParam(':id', $store_id);
    $stmt->execute();
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>店舗情報詳細</title>
<link rel="stylesheet" href="css/store_detail.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&family=Parisienne&family=Italianno&family=Kosugi+Maru&family=Sawarabi+Gothic&family=Noto+Serif+JP&family=Nanum+Brush+Script&family=Rock+Salt&family=Roboto&display=swap" rel="stylesheet">

</head>
<body>

<!-- ヘッダー -->
<div class="header-bar">
    <a href="home.php" class="logo-link">
        <img src="../../images/Ukanpo.png" alt="サイトロゴ">
    </a>
    <!--<div class="page-title">店舗情報詳細</div>-->
</div>

<a class="back" href="javascript:history.back()">←</a>


<!-- 店舗カルーセル -->
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

<!-- 店舗基本情報 -->
<div class="store-info">
    <span class="store-info-header
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
        <h2>店舗基本情報</h2>
</span>
    <table class="store-info-table <?php 
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
        <tr><th>店名</th><td><?= htmlspecialchars($store['store_name']) ?></td></tr>
        <tr><th>ジャンル</th><td><?= htmlspecialchars($store['genre']) ?></td></tr>
        <tr><th>お問い合わせ</th><td><?= htmlspecialchars($store['contact_info']) ?></td></tr>
        <tr><th>予約可否</th><td><?= $store['reservation_available'] ? '可' : '不可' ?></td></tr>
        <tr><th>住所</th><td><?= htmlspecialchars($store['store_address']) ?></td></tr>
        <tr><th>交通手段</th><td><?= htmlspecialchars($store['access']) ?></td></tr>
        <tr><th>営業時間</th><td><?= htmlspecialchars($store['opening_hours']) ?></td></tr>
        <tr><th>予算口コミ</th><td><?= htmlspecialchars($store['budget']) ?></td></tr>
        <tr>
            <th>支払い方法</th>
            <td>
                <?php
                $methods = json_decode($store['payment_methods'], true) ?? [];
                $details = json_decode($store['payment_details'], true) ?? [];

                $groups = [
                    "クレジットカード" => ["VISA", "MasterCard", "JCB", "AMEX", "Diners"],
                    "電子マネー"        => ["Suica", "PASMO", "iD", "QUICPay"],
                    "QR決済"            => ["PayPay", "楽天ペイ", "d払い"],
                ];

                $display = [];

                foreach ($methods as $method) {
                    if (!isset($groups[$method])) {
                        $display[] = htmlspecialchars($method);
                        continue;
                    }

                    $children = array_intersect($details, $groups[$method]);

                    if (!empty($children)) {
                        $display[] = htmlspecialchars($method) . "（" . htmlspecialchars(implode(", ", $children)) . "）";
                    } else {
                        $display[] = htmlspecialchars($method);
                    }
                }

                echo implode("<br>", $display);
                ?>
            </td>
        </tr>
        <tr><th>貸切</th><td><?= $store['private_available'] ? '可' : '不可' ?></td></tr>
        <tr><th>たばこ</th><td><?= $store['non_smoking'] ? '禁煙' : '喫煙可' ?></td></tr>
        <tr><th>ホームページ</th>
            <td>
                <?php if(!empty($store['homepage_url'])): ?>
                    <a href="<?= htmlspecialchars($store['homepage_url']) ?>" target="_blank"><?= htmlspecialchars($store['homepage_url']) ?></a>
                <?php else: ?>なし<?php endif; ?>
            </td>
        </tr>
        <tr><th>オープン日</th>
            <td><?= !empty($store['open_date']) ? date('Y年n月j日', strtotime($store['open_date'])) : '未登録' ?></td>
        </tr>
    </table>
</div>

<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO
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
