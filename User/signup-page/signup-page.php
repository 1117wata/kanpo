<?php
$error = '';
$username = $nickname = $email = $password = $address = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $error = "必須項目（お名前・メールアドレス・パスワード）を入力してください。";
    } else {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $icon_path = 'default.png';

            $sql = "INSERT INTO user (username, nickname, email, password_hash, address, icon_path)
                    VALUES (:username, :nickname, :email, :password, :address, :icon_path)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':nickname', $nickname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':icon_path', $icon_path);
            $stmt->execute();

            header("Location: ../home-page/home.php");
            exit;

        } catch (PDOException $e) {
            $error = "DBエラー: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>新規登録画面</title>
<link rel="stylesheet" href="signup.css">
<script>
function validateForm(event) {
    const username = document.forms["signupForm"]["username"].value.trim();
    const email = document.forms["signupForm"]["email"].value.trim();
    const password = document.forms["signupForm"]["password"].value.trim();
    if (username === "" || email === "" || password === "") {
        alert("必須項目（お名前・メールアドレス・パスワード）を入力してください。");
        event.preventDefault();
    }
}
</script>
</head>
<body>
<div class="header-bar">
    <a href="../home-page/home.php" class="logo-link">
        <img src="kanpo.png" alt="サイトロゴ">
    </a>
</div>

<div class="form-container">
    <h2>新規会員登録</h2>

    <?php if($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
    <?php endif; ?>

    <form name="signupForm" method="post" action="" onsubmit="validateForm(event)">
        <div class="form-group">
            <label class="form-label">お名前</label>
            <div class="input-wrapper">
                <input type="text" name="username" value="<?= htmlspecialchars($username) ?>">
                <div class="note">※苗字と名前の間にスペースは不要です</div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">ニックネーム</label>
            <div class="input-wrapper">
                <input type="text" name="nickname" value="<?= htmlspecialchars($nickname) ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">メールアドレス</label>
            <div class="input-wrapper">
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">パスワード</label>
            <div class="input-wrapper">
                <input type="password" name="password" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">住所</label>
            <div class="input-wrapper">
                <input type="text" name="address" value="<?= htmlspecialchars($address) ?>">
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="back-btn" onclick="history.back()">戻る</button>
            <button type="submit" class="submit-btn">この内容で会員登録する</button>
        </div>
    </form>
</div>
</body>
</html>
