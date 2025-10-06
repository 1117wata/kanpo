<?php
$error = '';
$success = '';
$username = $nickname = $email = $password = $address = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    // 未入力チェック（どれが未入力か判定）
    $missing = [];
    if ($username === '') $missing[] = "お名前";
    if ($nickname === '') $missing[] = "ニックネーム";
    if ($email === '') $missing[] = "メールアドレス";
    if ($password === '') $missing[] = "パスワード";
    if ($address === '') $missing[] = "住所";

    if (!empty($missing)) {
        $error = "未入力の項目があります：" . implode("、", $missing);
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

            // 成功メッセージを表示
            $success = "登録が完了しました！2秒後にホームへ移動します。";

            // 2秒後にホームページに遷移
            echo "<meta http-equiv='refresh' content='2;url=../home-page/home.php'>";
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
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    flex-direction: column;
}
.header-bar {
    width: 100%;
    background-color: #FFEC6E;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
    border-bottom: 2px solid black;
}
.header-bar img {
    height: 40px;
    width: auto;
    margin-left: 20px;
    cursor: pointer;
}
.logo-link { display: inline-block; }

.form-container {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
    margin-top: 40px;
}
h2 { text-align: left; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; }
.form-label { display: block; background-color: #ccc; padding: 8px; border-radius: 5px 5px 0 0; }
.input-wrapper { background-color: #eee; padding: 10px; border-radius: 0 0 5px 5px; }
input[type="text"], input[type="email"], input[type="password"] {
    width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;
    box-sizing: border-box; background-color: white;
}
.note { font-size: 0.9em; color: #555; margin-top: 5px; }

.button-group { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; }
button { padding: 12px; border-radius: 5px; cursor: pointer; font-size: 1em; width: 100%; border: 2px solid black; }
.back-btn { background-color: #FFEC6E; color: black; }
.back-btn:hover { background-color: #ddc50bff; }
.submit-btn { background-color: #454646ff; color: white; }
.submit-btn:hover { background-color: #212120ff; }

.alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
.alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
</style>
<script>
function validateForm(event) {
    const username = document.forms["signupForm"]["username"].value.trim();
    const nickname = document.forms["signupForm"]["nickname"].value.trim();
    const email = document.forms["signupForm"]["email"].value.trim();
    const password = document.forms["signupForm"]["password"].value.trim();
    const address = document.forms["signupForm"]["address"].value.trim();

    let missing = [];
    if (username === "") missing.push("お名前");
    if (nickname === "") missing.push("ニックネーム");
    if (email === "") missing.push("メールアドレス");
    if (password === "") missing.push("パスワード");
    if (address === "") missing.push("住所");

    if (missing.length > 0) {
        alert("未入力の項目があります：" + missing.join("、"));
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
    <?php elseif($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES) ?></div>
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
