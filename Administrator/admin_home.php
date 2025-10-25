<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>管理者ホーム画面</title>
    <link rel="stylesheet" href="css/admin_home.css" />
</head>

<body>
    <div class="header-bar">
        <a href="admin_home.php" class="logo-link">
            <img src="../images/Akanpo.png" alt="サイトロゴ">
        </a>
        <div class="page-title">管理者ホーム画面</div>
    </div>

    <div class="btn-container">
        <button class="menu-btn" type="submit" onclick="location.href='members.php'">会員一覧</button>
        <button class="menu-btn" type="submit" onclick="location.href='admin_stores.php'">店舗一覧</button>
    </div>

    <footer>
        &copy; 2025 KANPO 管理者
    </footer>

</body>

</html>
