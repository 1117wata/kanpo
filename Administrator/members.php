<?php
session_start();
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';

$pdo = getDB();

// フラッシュメッセージ
$flash_message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

// GETパラメータ
$search = trim($_GET['search'] ?? '');
$sort   = $_GET['sort'] ?? 'default';

// ページング
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// --- 件数カウント ---
$count_query = "SELECT COUNT(*) FROM user WHERE 1";
$count_params = [];

if ($search !== '') {
    $count_query .= " AND (user_id LIKE :s OR username LIKE :s)";
    $count_params[':s'] = "%$search%";
}

$cstmt = $pdo->prepare($count_query);
foreach ($count_params as $k => $v) {
    $cstmt->bindValue($k, $v, PDO::PARAM_STR);
}
$cstmt->execute();
$total_users = (int)$cstmt->fetchColumn();
$total_pages = max(1, ceil($total_users / $limit));

if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// --- ユーザー取得 ---
$query = "SELECT user_id, username, icon_path FROM user WHERE 1";
$params = [];

if ($search !== '') {
    $query .= " AND (user_id LIKE :s2 OR username LIKE :s2)";
    $params[':s2'] = "%$search%";
}

switch ($sort) {
    case 'id_asc':     $query .= " ORDER BY user_id ASC"; break;
    case 'id_desc':    $query .= " ORDER BY user_id DESC"; break;
    case 'name_asc':   $query .= " ORDER BY username ASC"; break;
    case 'name_desc':  $query .= " ORDER BY username DESC"; break;
    default:           $query .= " ORDER BY user_id ASC"; break;
}

$query .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>会員一覧</title>
<link rel="stylesheet" href="css/members.css">
</head>
<body>

<!-- フラッシュメッセージ -->
<?php if (!empty($flash_message)): ?>
<div class="flash-message" id="flash-message">
    <?= htmlspecialchars($flash_message, ENT_QUOTES) ?>
</div>
<?php endif; ?>

<header class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <h1 class="page-title">会員一覧</h1>
</header>

<!-- 検索 -->
<div class="search-container">
    <form method="get" action="">
        <div class="search-box-wrapper">
            <input type="text" name="search" class="search-box" placeholder="検索"
                   value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
            <button type="submit" class="search-btn">
                <img src="../images/kensaku.png" alt="検索" class="search-icon">
            </button>
        </div>

        <div class="sort-container">
            <label for="sort">並び替え：</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="default"   <?= $sort==='default'?'selected':'' ?>>標準</option>
                <option value="id_asc"    <?= $sort==='id_asc'?'selected':'' ?>>ID昇順</option>
                <option value="id_desc"   <?= $sort==='id_desc'?'selected':'' ?>>ID降順</option>
                <option value="name_asc"  <?= $sort==='name_asc'?'selected':'' ?>>名前昇順</option>
                <option value="name_desc" <?= $sort==='name_desc'?'selected':'' ?>>名前降順</option>
            </select>
        </div>
    </form>
</div>

<hr class="divider">

<!-- ユーザー一覧 -->
<div class="members-container">
<?php if (empty($users)): ?>
    <p class="no-user">該当するユーザーはいません。</p>

<?php else: ?>
    <?php foreach ($users as $u): ?>

<?php
$raw = trim($u['icon_path']);

if ($raw === "" || $raw === "../../images/aikon.png") {

    $icon_path = "../images/aikon.png";

} else {
    $icon_path = str_replace("./", "../User/home-page/", $raw);
}
?>

    <div class="member-card">
        <div class="member-id-row">
            <span class="user-id">
                ユーザーID：<?= htmlspecialchars($u['user_id'], ENT_QUOTES) ?>
            </span>
            <button class="detail-btn"
                onclick="location.href='member_detail.php?id=<?= urlencode($u['user_id']) ?>'">
                詳細
            </button>
        </div>

        <div class="member-name-row">
            <span class="username">
                ユーザー名：<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>
            </span>

            <img src="<?= htmlspecialchars($icon_path, ENT_QUOTES) ?>"
                 class="user-icon"
                 alt="ユーザーアイコン">
        </div>
    </div>

    <?php endforeach; ?>
<?php endif; ?>
</div>

<!-- ページネーション -->
<div class="pagination">

    <!-- 先頭へ -->
    <?php if ($page > 1): ?>
    <a href="?page=1&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">
        « 先頭へ
    </a>
    <?php endif; ?>

    <!-- 前へ -->
    <?php if ($page > 1): ?>
    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">
        « 前へ
    </a>
    <?php endif; ?>

    <!-- 数字ページ -->
    <?php
    $range = 5;
    $start = max(1, $page - floor($range/2));
    $end = min($total_pages, $start + $range - 1);

    for ($i=$start; $i<=$end; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"
           class="<?= $i==$page ? 'active-page' : '' ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>

    <!-- 次へ -->
    <?php if ($page < $total_pages): ?>
    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">
        次へ »
    </a>
    <?php endif; ?>

    <!-- 最後へ -->
    <?php if ($page < $total_pages): ?>
    <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">
        最後へ »
    </a>
    <?php endif; ?>

</div>


<footer class="footer">
    <div class="footer-content">
        © <?= date('Y') ?> KANPO 管理者
    </div>
</footer>

<script>
const flash = document.getElementById('flash-message');
if (flash) {
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.top = '0px';
        setTimeout(() => flash.remove(), 500);
    }, 2000);
}
</script>
</body>
</html>
