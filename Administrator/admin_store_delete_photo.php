<?php
header('Content-Type: application/json');
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8",'root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $photo_id = $_GET['id'] ?? null;
    if(!$photo_id) throw new Exception('画像IDが不正です');

    // まず画像パス取得
    $stmt = $pdo->prepare("SELECT store_photo_path FROM store_photo WHERE store_photo_id=?");
    $stmt->execute([$photo_id]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$photo) throw new Exception('画像が存在しません');

    // サーバー上の画像ファイル削除
    if(file_exists($photo['store_photo_path'])) unlink($photo['store_photo_path']);

    // DB削除
    $stmt = $pdo->prepare("DELETE FROM store_photo WHERE store_photo_id=?");
    $stmt->execute([$photo_id]);

    echo json_encode(['success'=>true]);
} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
