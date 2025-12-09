<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

// URLパラメータから user_id を取得
$user_id = $_GET['id'] ?? null;
$user = null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ▼ 口コミ件数取得（追加）
$stmt = $pdo->prepare("SELECT COUNT(*) FROM review WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$user_review_count = $stmt->fetchColumn();

// 削除ボタン押されたとき
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $delete_id = $_POST['user_id'];

    // 削除前にユーザー名を取得
    $stmt = $pdo->prepare("SELECT username FROM user WHERE user_id = ?");
    $stmt->execute([$delete_id]);
    $userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

    // ユーザー削除
    $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$delete_id]);

    // 削除メッセージを保存
    $_SESSION['flash_message'] = $userToDelete['username'] . " さんを削除しました";

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

    <p>
        <strong>口コミ：</strong>
        <?= $user_review_count ?> 件
        <a href="user_reviews.php?user_id=<?= $user['user_id']; ?>" class="review-list-btn">
            一覧を見る
        </a>

    </p>

    <p><strong>メールアドレス：</strong><?= htmlspecialchars($user['email']) ?></p>

    <p><strong>登録日：</strong>
        <?= !empty($user['created_at']) ? date('Y/m/d', strtotime($user['created_at'])) : '未登録' ?>
    </p>

    <!-- 削除フォーム -->
    <form method="POST" onsubmit="return confirm('本当にこのアカウントを削除しますか？');">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
        <button type="submit" name="delete" class="delete-btn">アカウント削除</button>
    </form>
</div>

<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO 管理者
    </div>
</footer>

</body>
</html>
