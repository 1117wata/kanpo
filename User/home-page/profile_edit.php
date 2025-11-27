<?php
session_start();
require_once '../../DB/db_connect.php';
$pdo = getDB();

$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>プロフィール変更画面</title>
<link rel="stylesheet" href="css/profile_edit.css">
</head>
<body>

<header class="header-bar">
    <a href="./home.php" class="logo-link">
        <img src="../../images/Ukanpo.png" alt="サイトロゴ">
    </a>
    <h1 class="page-title">プロフィール</h1>
</header>

<button class="back-btn" onclick="history.back()">←</button>

<form action="profile_update.php" method="post" enctype="multipart/form-data">

    <label>ニックネーム <span class="required">必須</span></label>
    <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'] ?? '', ENT_QUOTES) ?>" required>

    <label>メールアドレス</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>">

    <label>お名前</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES) ?>">

    <label>現住所</label>
    <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '', ENT_QUOTES) ?>">

    <label>性別</label>
    <select name="gender">
        <option value="">選択してください</option>
        <option value="男性" <?= ($user['gender'] ?? '')==='男性'?'selected':'' ?>>男性</option>
        <option value="女性" <?= ($user['gender'] ?? '')==='女性'?'selected':'' ?>>女性</option>
        <option value="その他" <?= ($user['gender'] ?? '')==='その他'?'selected':'' ?>>その他</option>
        <option value="無回答" <?= ($user['gender'] ?? '')==='無回答'?'selected':'' ?>>無回答</option>
    </select>

    <label>アイコン追加</label>
    <div class="icon-upload">
        <input type="file" id="iconInput" name="icon" accept="image/*" style="display:none;">
        <label for="iconInput" class="icon-btn">
            <img id="iconPreview" src="<?= !empty($user['icon_path']) ? $user['icon_path'] : '../../images/aikon.png' ?>" alt="アイコン">
            <?php if (empty($user['icon_path'])): ?>
            <span class="plus">＋</span>
            <?php endif; ?>
        </label>
    </div>

    <button type="submit" class="btn btn-update">更新</button>
    <button type="button" class="btn btn-logout" onclick="location.href='logout.php'">ログアウト</button>
</form>

<script>
const iconInput = document.getElementById('iconInput');
iconInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
        const img = document.getElementById('iconPreview');
        img.src = reader.result;
        const plus = document.querySelector('.icon-btn .plus');
        if (plus) plus.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>

</body>
</html>
