<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nickname'] = $user['nickname'];
        header("Location: ../home-page/home.php");
        exit();
    } else {
        $error = "メールアドレスまたはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="card">

    <!-- 楽天風：左上ロゴ配置 -->
    <img src="../../images/Kanpo.png" class="logo-left" alt="KANPO ロゴ">

    <h1 class="title">ログイン</h1>
    <p class="subtitle">メールアドレスとパスワードを入力してください。</p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="" method="post">

        <label class="label">メールアドレス</label>
        <input type="text" name="email" class="input-field" required>

        <label class="label">パスワード</label>
        <input type="password" name="password" class="input-field" required>

        <button type="submit" class="btn-primary">ログイン</button>

    </form>

    <div class="divider"></div>

    <h3 class="subtitle2">まだ登録されていない方</h3>
    <p class="subtext">新規会員登録はこちらから</p>

    <form action="../signup-page/signup-page.php" method="post">
        <button type="submit" class="btn-secondary">新規会員登録</button>
    </form>

</div>

</body>
</html>
