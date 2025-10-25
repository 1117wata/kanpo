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
    <title>ログイン画面</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <div class="header-left"></div>
            <a href="../../Administrator/admin_login.php" class="admin-login-link">管理者ログイン</a>
    </header>

    <img src="../../images/Kanpo.png" class="Kanpo_picture" alt="サンプル画像">

    <form action="" method="post">
        <h1>ログイン</h1>

        <p>会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</p>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="message">メールアドレス</div><br>
        <input type="text" name="email" class="input" required><br>

        <div class="message">パスワード</div><br>
        <input type="password" name="password" class="input" required><br>

        <p><button type="submit" class="button">ログイン</button></p>
    </form>

    <hr>

    <form action="../signup-page/signup-page.php" method="post">
        <h3>まだ会員登録されてない方</h3>
        <p>初めてご利用される方は、こちらから会員登録すると便利にご利用できます。</p>
        <p><button type="submit" class="button">新規会員登録</button></p>
    </form>


    
</body>
</html>
