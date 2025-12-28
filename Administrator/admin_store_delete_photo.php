<?php
header('Content-Type: application/json');
try {
   include '../kanpo/DB/db_connect.php';
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $photo_id = $_GET['id'] ?? null;
    if(!$photo_id) throw new Exception('画像IDが不正です');

    $stmt = $pdo->prepare("SELECT store_photo_path FROM store_photo WHERE store_photo_id=?");
    $stmt->execute([$photo_id]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$photo) throw new Exception('画像が存在しません');

    if(file_exists(__DIR__.'/'.$photo['store_photo_path'])) unlink(__DIR__.'/'.$photo['store_photo_path']);

    $stmt = $pdo->prepare("DELETE FROM store_photo WHERE store_photo_id=?");
    $stmt->execute([$photo_id]);

    echo json_encode(['success'=>true]);
} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
