<?php
require_once 'admin_auth.php';

try {
    // フラッシュメッセージがあれば取得して消す
    $flash_message = $_SESSION['flash_message'] ?? '';
    unset($_SESSION['flash_message']);

    // DB接続
    $pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // GETパラメータ
    $search = trim($_GET['search'] ?? '');
    $sort = $_GET['sort'] ?? 'default';

    // ページング追加
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    // 総件数取得
    $count_query = "SELECT COUNT(*) FROM user WHERE 1";
    $count_params = [];
    if ($search !== '') {
        $count_query .= " AND (user_id LIKE :count_search OR username LIKE :count_search)";
        $count_params[':count_search'] = "%$search%";
    }

    $count_stmt = $pdo->prepare($count_query);
    if ($count_stmt === false) {
        throw new Exception('COUNT query prepare failed');
    }

    foreach ($count_params as $k => $v) {
        $count_stmt->bindValue($k, $v, PDO::PARAM_STR);
    }

    $count_stmt->execute();
    $total_users = (int)$count_stmt->fetchColumn();
    $total_pages = ($total_users > 0) ? (int)ceil($total_users / $limit) : 1;

    // 現在ページが総ページを超えていたら最後のページに揃える
    if ($page > $total_pages) {
        $page = $total_pages;
        $offset = ($page - 1) * $limit;
    }

    // クエリ組立
    $query = "SELECT user_id, username, icon_path FROM user WHERE 1";
    $select_params = [];

    if ($search !== '') {
        $query .= " AND (user_id LIKE :search2 OR username LIKE :search2)";
        $select_params[':search2'] = "%$search%";
    }

    switch ($sort) {
        case 'id_asc':  $query .= " ORDER BY user_id ASC"; break;
        case 'id_desc': $query .= " ORDER BY user_id DESC"; break;
        case 'name_asc':  $query .= " ORDER BY username ASC"; break;
        case 'name_desc': $query .= " ORDER BY username DESC"; break;
        default: $query .= " ORDER BY user_id ASC"; break;
    }

    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query);
    if ($stmt === false) {
        throw new Exception('SELECT query prepare failed');
    }

    foreach ($select_params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
 
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // DB 関連の例外を取得
    $error_message = 'DBエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
    $users = [];
    $total_pages = 1;
    $page = 1;
} catch (Exception $e) {
    // その他の例外
    $error_message = 'エラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
    $users = [];
    $total_pages = 1;
    $page = 1;
}
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
    <div class="flash-message" id="flash-message"><?= htmlspecialchars($flash_message, ENT_QUOTES) ?></div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="error-box"><?= $error_message ?></div>
<?php endif; ?>

<!-- ヘッダー -->
<header class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <h1 class="page-title">会員一覧</h1>
</header>

<!-- 検索 + 並び替えフォーム -->
<div class="search-container">
    <form method="get" action="">
        <div class="search-box-wrapper">
            <input type="text" name="search" class="search-box" placeholder="検索" value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
            <button type="submit" class="search-btn" aria-label="検索">
                <img src="../images/kensaku.png" alt="検索" class="search-icon">
            </button>
        </div>

        <div class="sort-container">
            <label for="sort">並び替え：</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>標準</option>
                <option value="id_asc" <?= $sort === 'id_asc' ? 'selected' : '' ?>>ID昇順</option>
                <option value="id_desc" <?= $sort === 'id_desc' ? 'selected' : '' ?>>ID降順</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>名前昇順</option>
                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>名前降順</option>
            </select>
        </div>
    </form>
</div>

<hr class="divider">

<!-- ユーザーリスト -->
<div class="members-container">
    <?php if (count($users) === 0): ?>
        <p class="no-user">該当するユーザーはいません。</p>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <div class="member-card">
                <div class="member-id-row">
                    <span class="user-id">ユーザーID：<?= htmlspecialchars($user['user_id'], ENT_QUOTES) ?></span>
                    <button class="detail-btn" onclick="location.href='member_detail.php?id=<?= urlencode($user['user_id']) ?>'">詳細</button>
                </div>

                <div class="member-name-row">
                    <span class="username">ユーザー名：<?= htmlspecialchars($user['username'], ENT_QUOTES) ?></span>
                    <img src="../images/<?= htmlspecialchars($user['icon_path'] ?: 'default.png', ENT_QUOTES) ?>" alt="アイコン" class="user-icon">
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ページネーション -->
<?php if (!empty($total_pages) && $total_pages > 1): ?>
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

<!-- フッター -->
<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO 管理者
    </div>
</footer>

</body>
<script>
const flash = document.getElementById('flash-message');
if(flash){
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.top = '0px';
        setTimeout(() => flash.remove(), 500);
    }, 2000);
}
</script>
</html>
