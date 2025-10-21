<?php
$pdo = new PDO('mysql:host=localhost;dbname=kanpo', 'root', '');

// 並び替えの取得
$order = 'store_id ASC';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'store_id_desc': $order = 'store_id DESC'; break;
        case 'store_name_asc': $order = 'store_name ASC'; break;
        case 'store_name_desc': $order = 'store_name DESC'; break;
    }
}

// 検索の取得
$search = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM store WHERE store_id LIKE ? OR store_name LIKE ? ORDER BY $order");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM store ORDER BY $order");
}

$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>店舗一覧</title>
<link rel="stylesheet" href="css/stores.css">
</head>
<body>
<div class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <div class="page-title">店舗一覧</div>
</div>

<div class="search-container">
    <form method="get">
        <div class="search-box-wrapper">
            <input type="text" name="search" class="search-box" placeholder="検索" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-btn">
                <img src="../images/kensaku.png" alt="検索" class="search-icon">
            </button>
        </div>
    </form>
</div>

<div class="button-row">
    <div class="left-btn">
        <button class="add-btn" onclick="location.href='admin_store_add.php'">新規追加</button>
    </div>
    <div class="right-btn">
        <form method="get" id="sortForm">
            <label for="sort">並び替え:</label>
            <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit();">
                <option value="default" <?= (!isset($_GET['sort']) || $_GET['sort']=='default')?'selected':'' ?>>標準</option>
                <option value="store_id_asc" <?= (isset($_GET['sort']) && $_GET['sort']=='store_id_asc')?'selected':'' ?>>店舗ID昇順</option>
                <option value="store_id_desc" <?= (isset($_GET['sort']) && $_GET['sort']=='store_id_desc')?'selected':'' ?>>店舗ID降順</option>
                <option value="store_name_asc" <?= (isset($_GET['sort']) && $_GET['sort']=='store_name_asc')?'selected':'' ?>>店舗名昇順</option>
                <option value="store_name_desc" <?= (isset($_GET['sort']) && $_GET['sort']=='store_name_desc')?'selected':'' ?>>店舗名降順</option>
            </select>
        </form>
    </div>
</div>

<hr class="divider">

<div class="members-container">
    <?php if ($stores): ?>
        <?php foreach ($stores as $store): ?>
            <div class="member-card">
                <div class="member-id-row">
                    <p class="user-id">店舗ID：<?= htmlspecialchars($store['store_id']) ?></p>
                    <button class="detail-btn" onclick="location.href='admin_store_info.php?id=<?= urlencode($store['store_id']) ?>'">詳細</button>
                </div>
                <div class="member-name-row">
                    <p class="username">店舗名：<?= htmlspecialchars($store['store_name']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-user">データがありません</p>
    <?php endif; ?>
</div>

    <footer class="footer">
            <div class="footer-content">
                &copy; <?= date('Y') ?> KANPO 管理者画面
            </div>
    </footer>
</body>
</html>
