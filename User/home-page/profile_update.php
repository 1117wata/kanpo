<?php
session_start();
require_once '../../DB/db_connect.php';
$pdo = getDB();

// ログイン確認
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// フォームから取得
$nickname = $_POST['nickname'] ?? '';
$email    = $_POST['email'] ?? '';
$name     = $_POST['name'] ?? '';
$address  = $_POST['address'] ?? '';
$gender   = $_POST['gender'] ?? '';

// 画像保存フォルダ（home-page/uploads/icons）
$upload_dir = './uploads/icons/';
$icon_path = null;

// 画像アップロード処理
if (!empty($_FILES['icon']['name'])) {
    $file = $_FILES['icon'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'icon_' . $user_id . '_' . time() . '.' . $ext;
    $save_path = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $save_path)) {
        $icon_path = $save_path;
    }
}

// DB更新
$sql = "UPDATE user 
        SET nickname = ?, email = ?, username = ?, address = ?, gender = ?" .
        ($icon_path ? ", icon_path = ?" : "") .
        " WHERE user_id = ?";

$params = [$nickname, $email, $name, $address, $gender];
if ($icon_path) $params[] = $icon_path;
$params[] = $user_id;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// 更新完了後、プロフィール編集画面に戻る
header("Location: profile_update_success.php");
exit;
