<?php
require_once 'admin_auth.php';

// DB接続
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// GETパラメータ
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'default';

// クエリ組立
$query = "SELECT user_id, username, icon_path FROM user WHERE 1";

if ($search !== '') {
    $query .= " AND (user_id LIKE :search OR username LIKE :search)";
}

switch ($sort) {
    case 'id_asc':  $query .= " ORDER BY user_id ASC"; break;
    case 'id_desc': $query .= " ORDER BY user_id DESC"; break;
    case 'name_asc':  $query .= " ORDER BY username ASC"; break;
    case 'name_desc': $query .= " ORDER BY username DESC"; break;
    default: $query .= " ORDER BY user_id ASC"; break;
}

$stmt = $pdo->prepare($query);
if ($search !== '') {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
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

<header class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <h1 class="page-title">会員一覧</h1>
</header>

<!-- 検索 + 並び替えフォーム -->
<div class="search-container">
    <form method="get" action="">
        <!-- 検索ボックス（ボタンはボックス内） -->
        <div class="search-box-wrapper">
            <input type="text" name="search" class="search-box" placeholder="検索" value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
            <button type="submit" class="search-btn" aria-label="検索">
                <img src="../images/kensaku.png" alt="検索" class="search-icon">
            </button>
        </div>

        <!-- 並び替え（検索結果保持のため search を hidden にしていないが form 共通なので OK） -->
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

<!-- フッター -->
<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO 管理者
    </div>
</footer>


</body>
</html>
