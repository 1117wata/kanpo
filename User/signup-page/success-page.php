<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>登録完了</title>
<link rel="stylesheet" href="css/signup.css">
<style>
.success-message {
    text-align: center;
    margin-top: 100px;
    font-size: 1.5em;
}
.success-message img {
    width: 80px;
    margin-bottom: 20px;
}
</style>
<script>
    // 3秒後に自動で遷移
    setTimeout(() => {
        window.location.href = "../home-page/home.php";
    }, 3000);
</script>
</head>
<body>

<div class="success-message">
    <p>登録が完了しました！</p>
    <p>3秒後にホームページへ自動的に移動します。</p>
</div>
</body>
</html>
