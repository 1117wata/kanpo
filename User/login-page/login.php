<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=kanpo', 'root', '');

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
$stmt->execute([$email, $password]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    header("Location: home.php");
    exit();
} else {
    $error = "メールアドレスまたはパスワードが間違っています。";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header></header>

    <img src="../../images/Kanpo.png" class="Kanpo_picture" alt="サンプル画像">

    <form action="home.php" method="post">
        <h1>ログイン</h1>

        <p>会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</p>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="message">メールアドレス</div><br>
        <input type="text" name="email" class="input"><br>

        <div class="message">パスワード</div><br>
        <input type="password" name="password" class="input"><br>

        <p><button type="submit" class="button">ログイン</button></p>
    </form>

    <hr>

    <form action="../signup-page/signup-page.php" method="get">
        <h3>まだ会員登録されてない方</h3>
        <p>初めてご利用される方は、こちらから会員登録すると便利にご利用できます。</p>
        <p><button type="submit" class="button">新規会員登録</button></p>
    </form>
</body>
</html>