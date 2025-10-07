<?php
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
<style>
.detail-container {
    width: 90%;
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    border: 2px solid #000;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}
.detail-container p {
    font-size: 16px;
    margin: 12px 0;
    color: #222;
}
.delete-btn {
    display: block;
    width: 180px;
    margin: 30px auto 0;
    background-color: #ff6b6b;
    color: #fff;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    padding: 10px;
    cursor: pointer;
    transition: background-color 0.2s;
}
.delete-btn:hover {
    background-color: #ff4b4b;
}
.no-data {
    text-align: center;
    font-size: 18px;
    color: #777;
    margin-top: 80px;
}
</style>
</head>

<body>
<div class="header-bar">
    <a href="../home-page/home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a>
    <span class="page-title">会員詳細</span>
</div>

<?php if ($user): ?>
<div class="detail-container">
    <p><strong>ユーザーID：</strong><?= htmlspecialchars($user['user_id']) ?></p>
    <p><strong>ユーザー名：</strong><?= htmlspecialchars($user['username']) ?></p>
    <p><strong>ニックネーム：</strong><?= htmlspecialchars($user['nickname']) ?></p>
    <p><strong>メールアドレス：</strong><?= htmlspecialchars($user['email']) ?></p>
    <p><strong>登録日：</strong><?= htmlspecialchars($user['created_at'] ?? '不明') ?></p>

    <form method="post" onsubmit="return confirm('本当にこのアカウントを削除しますか？');">
        <button type="submit" name="delete" class="delete-btn">アカウント削除</button>
    </form>
</div>
<?php else: ?>
<p class="no-data">該当するユーザーが見つかりません。</p>
<?php endif; ?>

</body>
</html>
