<?php
// DB接続
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');

// GETで店舗ID取得
$store_id = $_GET['id'] ?? null;
if (!$store_id) {
    die('店舗IDが指定されていません。');
}

// 店舗情報取得
$stmt = $pdo->prepare("SELECT * FROM store WHERE store_id = ?");
$stmt->execute([$store_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$store) {
    die('店舗が見つかりません。');
}

// 画像情報取得
$stmt = $pdo->prepare("SELECT store_photo_path FROM store_photo WHERE store_id = ?");
$stmt->execute([$store_id]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>店舗情報詳細</title>
<link rel="stylesheet" href="css/store_info.css">

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>
<body>

<!-- ヘッダー -->
<div class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <div class="page-title">店舗情報詳細</div>
</div>

<div class="store-detail">
    <h1><?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?></h1>

    <?php if (!empty($photos)): ?>
    <div class="swiper">
        <div class="swiper-wrapper">
            <?php foreach ($photos as $photo): ?>
                <div class="swiper-slide">
                    <img src="<?= htmlspecialchars($photo['store_photo_path'], ENT_QUOTES) ?>" alt="店舗画像" class="store-image">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ページネーション -->
        <div class="swiper-pagination"></div>
        <!-- 左右ボタン -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
    <?php else: ?>
        <p>画像が登録されていません。</p>
    <?php endif; ?>
</div>

    <div class="store-info">
    <h2>店舗基本情報</h2>
    <table class="store-info-table">
        <tr>
            <th>店名</th>
            <td><?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>ジャンル</th>
            <td><?= htmlspecialchars($store['genre'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>お問い合わせ</th>
            <td><?= htmlspecialchars($store['contact_info'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>予約可否</th>
            <td><?= $store['reservation_available'] ? '可' : '不可' ?></td>
        </tr>
        <tr>
            <th>住所</th>
            <td><?= htmlspecialchars($store['store_address'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>交通手段</th>
            <td><?= htmlspecialchars($store['access'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>営業時間</th>
            <td><?= htmlspecialchars($store['opening_hours'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>予算口コミ</th>
            <td><?= htmlspecialchars($store['budget'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>支払い方法</th>
            <td><?= htmlspecialchars($store['payment_methods'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
            <th>貸し切り</th>
            <td><?= $store['private_available'] ? '可' : '不可' ?></td>
        </tr>
        <tr>
            <th>たばこ</th>
            <td><?= $store['non_smoking'] ? '禁煙' : '喫煙可' ?></td>
        </tr>
        <tr>
            <th>ホームページ</th>
            <td>
                <?php if(!empty($store['homepage_url'])): ?>
                    <a href="<?= htmlspecialchars($store['homepage_url'], ENT_QUOTES) ?>" target="_blank"><?= htmlspecialchars($store['homepage_url'], ENT_QUOTES) ?></a>
                <?php else: ?>
                    なし
                <?php endif; ?>
            </td>
        <tr>
            <th>オープン日</th>
            <td>
                <?php 
                if (!empty($store['open_date'])) {
                    $date = new DateTime($store['open_date']);
                    echo $date->format('Y年n月j日'); 
                } else {
                    echo '未登録';
                }
                ?>
            </td>
        </tr>
        </table>
    </div>

    <footer class="footer">
            <div class="footer-content">
                &copy; <?= date('Y') ?> KANPO 管理者画面
            </div>
    </footer>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const swiper = new Swiper('.swiper', {
    loop: true,
    slidesPerView: 3,        // PCは常に3枚表示
    spaceBetween: 20,        // スライド間の余白(px)
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    breakpoints: {
        1024: { slidesPerView: 3 }, // 大画面PCも3枚
        768: { slidesPerView: 2 },  // タブレットは2枚
        480: { slidesPerView: 1 }   // スマホは1枚
    }
});

</script>

</body>
</html>
