<?php
session_start();

// DB接続
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB接続エラー: " . $e->getMessage());
}

// 初期設定
$error_message = 'メールアドレスとパスワードを入力してください。';
$message_class = 'info';

// フォーム送信
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_email'], $_POST['admin_password'])) {
    $email = $_POST['admin_email'];
    $password = $_POST['admin_password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email=:email AND password=:password");
    $stmt->execute([':email'=>$email, ':password'=>$password]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $error_message = "ログイン成功！<br>3秒後に自動でホーム画面へ遷移します。";
        $message_class = 'success';
        header("Refresh:3; url=admin_home.php");
    } else {
        $error_message = "メールアドレスまたはパスワードが間違っています。";
        $message_class = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>管理者ログイン</title>
<link rel="stylesheet" href="css/admin_login.css">
</head>
<body>

<form method="post" class="login-form">
    <h1>管理者ログイン</h1>

    <div class="login-message <?= $message_class ?>"><?= $error_message ?></div>

    <div class="form-group">
        <label for="admin_email">メールアドレス</label>
        <input type="email" id="admin_email" name="admin_email" required value="<?= htmlspecialchars($_POST['admin_email'] ?? '', ENT_QUOTES) ?>">
    </div>

    <div class="form-group">
        <label for="admin_password">パスワード</label>
        <input type="password" id="admin_password" name="admin_password" required>
    </div>

    <button type="submit">ログイン</button>
</form>

<!-- アニメーション -->
<script>
function createJellyfish() {
    const jelly = document.createElement('div');
    jelly.classList.add('jellyfish');
    jelly.style.left = Math.random() * window.innerWidth + 'px';
    jelly.style.animationDuration = (8 + Math.random() * 4) + 's';
    document.body.appendChild(jelly);

    setTimeout(() => {
        jelly.remove();
    }, 10000);
}

setInterval(createJellyfish, 1000);
</script>

</body>
</html>
