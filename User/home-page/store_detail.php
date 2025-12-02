<?php
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

if (!$store) {
    echo "店舗が見つかりません";
    exit;
}

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>店舗情報詳細</title>
<link rel="stylesheet" href="css/store_detail.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>
<body>

<!-- ヘッダー -->
<div class="header-bar">
    <a href="home.php" class="logo-link">
        <img src="../../images/Ukanpo.png" alt="サイトロゴ">
    </a>
    <div class="page-title">店舗情報詳細</div>
</div>

<!-- 戻るボタン -->
<div class="back-btn-container">
    <a href="javascript:history.back();" class="back-link">
        <img src="../../images/back.png" alt="戻る" class="back-icon">
    </a>
</div>

<!-- 店舗カルーセル -->
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

<!-- 口コミリンク -->
<div class="review-actions">
    <a href="review_post.php?store_id=<?= htmlspecialchars($store['store_id']) ?>" class="review-btn">
        この店舗に口コミを投稿する
    </a>
    <a href="reviews.php?store_id=<?= htmlspecialchars($store['store_id']) ?>" class="review-btn">
        この店舗の口コミ一覧を見る
    </a>
</div>

<!-- 店舗基本情報 -->
<div class="store-info">
    <div class="store-info-header">
        <h2>店舗基本情報</h2>
    </div>
    <table class="store-info-table">
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
