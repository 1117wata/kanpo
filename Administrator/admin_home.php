
<?php
require_once 'admin_auth.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>管理者ホーム画面</title>
    <link rel="stylesheet" href="css/admin_home.css" />
</head>

<body>
    <header class="header-bar">
        <div class="page-title">管理者ホーム画面</div>
        <div class="admin-info">
            <?= htmlspecialchars($_SESSION['admin_name'], ENT_QUOTES) ?> さん
            <form method="post" action="admin_logout.php" style="margin:0;">
                <button type="submit" class="logout-btn">ログアウト</button>
            </form>
        </div>
    </header>


    <div class="btn-container">
        <button class="menu-btn" type="submit" onclick="location.href='members.php'">会員一覧</button>
        <button class="menu-btn" type="submit" onclick="location.href='admin_stores.php'">店舗一覧</button>
    </div>

    <!-- フッター -->
    <footer class="footer">
        <div class="footer-content">
            &copy; <?= date('Y') ?> KANPO 管理者
        </div>
    </footer>

</body>

</html>
