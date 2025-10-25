<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8",'root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $store_id = $_GET['id'] ?? 0;

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

} catch (PDOException $e) {
    echo "DBエラー: ".$e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>店舗情報詳細</title>
<link rel="stylesheet" href="css/store_info.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<style>
/* 更新メッセージ */
.alert-msg {
    position: fixed;
    top: -80px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #4CAF50;
    color: #fff;
    padding: 20px 40px;
    font-size: 24px;
    font-weight: bold;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 9999;
    transition: top 0.5s ease, opacity 0.5s ease;
    opacity: 0;
}
</style>
</head>
<body>

<?php if(!empty($_SESSION['success_msg'])): ?>
<div id="alertMsg" class="alert-msg"><?= htmlspecialchars($_SESSION['success_msg']) ?></div>
<?php unset($_SESSION['success_msg']); endif; ?>

<!-- ヘッダー -->
<div class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <div class="page-title">店舗情報詳細</div>
</div>

<!-- 戻るボタン -->
<div class="back-btn-container">
    <a href="javascript:history.back();" class="back-link">
        <img src="../images/back.png" alt="戻る" class="back-icon">
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
                <img src="<?= htmlspecialchars($photo['store_photo_path'], ENT_QUOTES) ?>" alt="店舗画像" class="store-image">
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
    <div class="store-info-header">
        <h2>店舗基本情報</h2>
        <button class="edit-btn" onclick="location.href='admin_store_edit.php?id=<?= $store['store_id'] ?>'">編集</button>
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
        <tr><th>支払い方法</th><td><?= htmlspecialchars($store['payment_methods']) ?></td></tr>
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

// 更新メッセージスライド表示
window.addEventListener('load', () => {
    const alertMsg = document.getElementById('alertMsg');
    if(alertMsg){
        alertMsg.style.top = '20px';
        alertMsg.style.opacity = '1';
        setTimeout(()=>{
            alertMsg.style.top='-80px';
            alertMsg.style.opacity='0';
        }, 3000);
    }
});
</script>
</body>
</html>
