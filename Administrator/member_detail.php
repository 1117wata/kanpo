<?php
require_once 'admin_auth.php';

// DB接続
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');

// URLパラメータから user_id を取得
$user_id = $_GET['id'] ?? null;
$user = null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 削除ボタン押されたとき
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header("Location: members.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>会員詳細</title>
<link rel="stylesheet" href="css/members.css">
</head>
<link rel="stylesheet" href="css/members.css">
<body>
    <header class="header-bar">
        <a href="admin_home.php" class="logo-link">
            <img src="../images/Akanpo.png" alt="サイトロゴ">
        </a>
        <h1 class="page-title">会員詳細</h1>
    </header>

    <!-- 戻るボタン -->
    <div class="back-btn-container">
        <a href="javascript:history.back();" class="back-link">
            <img src="../images/back.png" alt="戻る" class="back-icon">
        </a>
    </div>


    <div class="detail-container">
        <p><strong>ユーザー名：</strong><?= htmlspecialchars($user['username']) ?></p>
        <p><strong>ユーザーID：</strong><?= htmlspecialchars($user['user_id']) ?></p>
        <p><strong>ニックネーム：</strong><?= htmlspecialchars($user['nickname']) ?></p>
        <p><strong>メールアドレス：</strong><?= htmlspecialchars($user['email']) ?></p>
        <p><strong>登録日：</strong>
            <?php
            if (!empty($user['created_at'])) {
                echo date('Y/m/d', strtotime($user['created_at']));
            } else {
                echo '未登録';
            }
            ?>
        </p>

        <button class="delete-btn">アカウント削除</button>
    </div>

    <!-- フッター -->
    <footer class="footer">
        <div class="footer-content">
            &copy; <?= date('Y') ?> KANPO 管理者
        </div>
    </footer>


</body>

</html>
