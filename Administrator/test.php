<?php
// DB接続
$pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8",'root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$store_id = $_GET['id'] ?? 1; // テスト用に固定してもOK

$msg = '';

if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_FILES['photo'])){
    $uploadDir = __DIR__.'/uploads/'; // Administrator/uploads/に合わせる
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $tmpName = $_FILES['photo']['tmp_name'];
    $fileName = basename($_FILES['photo']['name']);
    $targetFile = $uploadDir.$fileName;

    if(move_uploaded_file($tmpName, $targetFile)){
        // DBに保存
        $stmt = $pdo->prepare("INSERT INTO store_photo (store_id, store_photo_path) VALUES (?, ?)");
        $stmt->execute([$store_id, 'Administrator/uploads/'.$fileName]);
        $msg = "アップロード成功！";
    } else {
        $msg = "アップロード失敗";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>画像追加テスト</title>
</head>
<body>
<h1>画像追加テスト</h1>
<p><?= htmlspecialchars($msg) ?></p>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="photo">
    <button type="submit">アップロード</button>
</form>
</body>
</html>
