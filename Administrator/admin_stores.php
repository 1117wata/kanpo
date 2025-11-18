<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

// 検索
$search = trim($_GET['search'] ?? '');

// 並び替え
$sort = $_GET['sort'] ?? 'default';
switch ($sort) {
    case 'store_id_desc': $order = 'store_id DESC'; break;
    case 'store_name_asc': $order = 'store_name ASC'; break;
    case 'store_name_desc': $order = 'store_name DESC'; break;
    case 'store_id_asc':
    case 'default':
    default:
        $order = 'store_id ASC';
        break;
}

// ページング
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// 件数取得
$count_query = "SELECT COUNT(*) FROM store WHERE 1";
$count_params = [];

if ($search !== '') {
    $count_query .= " AND (store_id LIKE :count_search OR store_name LIKE :count_search)";
    $count_params[':count_search'] = "%$search%";
}

$count_stmt = $pdo->prepare($count_query);
foreach ($count_params as $k => $v) {
    $count_stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$count_stmt->execute();
$total_stores = (int)$count_stmt->fetchColumn();
$total_pages = ($total_stores > 0) ? (int)ceil($total_stores / $limit) : 1;

// ページ調整
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// データ取得
$query = "SELECT * FROM store WHERE 1";
$params = [];

if ($search !== '') {
    $query .= " AND (store_id LIKE :search2 OR store_name LIKE :search2)";
    $params[':search2'] = "%$search%";
}

$query .= " ORDER BY $order LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
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

    <?php if ($total_pages > 1): ?>
<div class="pagination" aria-label="ページネーション">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">&laquo; 前へ</a>
    <?php endif; ?>

    <?php
    $display_range = 5;
    $start = max(1, $page - intval($display_range / 2));
    $end = min($total_pages, $start + $display_range - 1);
    if ($end - $start + 1 < $display_range) {
        $start = max(1, $end - $display_range + 1);
    }
    for ($i = $start; $i <= $end; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"
           class="<?= $i == $page ? 'active-page' : '' ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">次へ &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>



    <footer class="footer">
            <div class="footer-content">
                &copy; <?= date('Y') ?> KANPO 管理者
            </div>
    </footer>
</body>
</html>
