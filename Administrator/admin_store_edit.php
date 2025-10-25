<?php 
require_once 'admin_auth.php';

// DB接続
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { 
    echo "DB接続エラー: ".$e->getMessage(); 
    exit; 
}

$store_id = $_GET['id'] ?? $_POST['store_id'] ?? null;
if(!$store_id){ echo "店舗IDが不正です"; exit; }

// 店舗情報取得
$stmt = $pdo->prepare("SELECT * FROM store WHERE store_id=?");
$stmt->execute([$store_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$store){ echo "店舗情報が存在しません"; exit; }

// 既存画像取得
$stmt = $pdo->prepare("SELECT * FROM store_photo WHERE store_id=?");
$stmt->execute([$store_id]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $data = [
        'store_name'=>trim($_POST['store_name']??''),
        'contact_info'=>trim($_POST['contact_info']??''),
        'reservation_available'=>trim($_POST['reservation_available']??''),
        'category_id'=>trim($_POST['category_id']??''),
        'genre'=>trim($_POST['genre']??''),
        'store_address'=>trim($_POST['store_address']??''),
        'access'=>trim($_POST['access']??''),
        'opening_hours'=>trim($_POST['opening_hours']??''),
        'budget'=>trim($_POST['budget_review']??''),
        'payment_methods'=>trim($_POST['payment_method']??''),
        'private_available'=>trim($_POST['private_available']??''),
        'non_smoking'=>trim($_POST['non_smoking']??''),
        'homepage_url'=>trim($_POST['homepage_url']??''),
        'open_date'=>trim($_POST['open_date']??''),
        'store_id'=>$store_id
    ];
    try{
        $sql = "UPDATE store SET
                store_name=:store_name, contact_info=:contact_info, reservation_available=:reservation_available,
                category_id=:category_id, genre=:genre,
                store_address=:store_address, access=:access, opening_hours=:opening_hours,
                budget=:budget, payment_methods=:payment_methods, private_available=:private_available,
                non_smoking=:non_smoking, homepage_url=:homepage_url, open_date=:open_date
                WHERE store_id=:store_id";
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute($data);

        // 画像追加処理
        if(!empty($_FILES['new_photos']['name'][0])){
            $uploadDir = __DIR__.'/uploads/store_photos/';
            foreach($_FILES['new_photos']['name'] as $key => $name){
                $tmpName = $_FILES['new_photos']['tmp_name'][$key];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $filename = uniqid().'.'.$ext;
                $target = $uploadDir.$filename;
                if(move_uploaded_file($tmpName, $target)){
                    $stmt_photo = $pdo->prepare("INSERT INTO store_photo (store_id, store_photo_path) VALUES (?, ?)");
                    $stmt_photo->execute([$store_id, 'uploads/store_photos/'.$filename]);
                }
            }
        }

        $_SESSION['success_msg'] = "店舗情報を更新しました！";
        header("Location: admin_store_info.php?id=".$store_id);
        exit;
    }catch(PDOException $e){ $error = "DBエラー: ".$e->getMessage(); }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>店舗編集</title>
<link rel="stylesheet" href="css/admin_store_edit.css">
</head>
<body>

<!-- ヘッダー -->
<div class="header-bar">
    <a href="admin_home.php" class="logo-link"><img src="../images/Akanpo.png" alt="サイトロゴ"></a>
    <div class="page-title">店舗編集</div>
</div>

<!-- 戻るボタン -->
 <div class="back-btn-container">
    <a href="javascript:history.back();" class="back-link">
        <img src="../images/back.png" alt="戻る" class="back-icon">
    </a>
 </div>

<?php if($error): ?>
<p class="msg-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form class="store-info" method="post" enctype="multipart/form-data">
<input type="hidden" name="store_id" value="<?= htmlspecialchars($store['store_id']) ?>">

<!-- 店名 -->
<div class="form-group">
<label>店名：</label>
<input type="text" name="store_name" value="<?= htmlspecialchars($store['store_name']) ?>">
</div>

<!-- ジャンル -->
<div class="form-group">
<label>ジャンル：</label>
<div class="dropdown">
<input type="text" id="genreDisplay" value="<?= htmlspecialchars($store['genre']) ?>" readonly>
<input type="hidden" name="category_id" id="category_id" value="<?= htmlspecialchars($store['category_id']) ?>">
<input type="hidden" name="genre" id="genre_name" value="<?= htmlspecialchars($store['genre']) ?>">
<div class="dropdown-content">
<div data-id="1">中華料理</div>
<div data-id="2">フランス料理</div>
<div data-id="3">多国籍料理</div>
<div data-id="4">イタリア料理</div>
<div data-id="5">居酒屋</div>
<div data-id="6">和食</div>
<div data-id="7">懐石料理</div>
<div data-id="8">韓国料理</div>
<div data-id="9">炉端焼き</div>
</div>
</div>
</div>
<!-- 連絡先、予約可否 -->
<div class="form-group">
<label>お問い合わせ：</label>
<input type="text" name="contact_info" value="<?= htmlspecialchars($store['contact_info']) ?>">
</div>

<div class="form-group">
<label>予約可否：</label>
<div class="radio-group">
<label><input type="radio" name="reservation_available" value="1" <?= $store['reservation_available']?'checked':'' ?>>可</label>
<label><input type="radio" name="reservation_available" value="0" <?= !$store['reservation_available']?'checked':'' ?>>不可</label>
</div>
</div>

<!-- 他の情報 -->
<div class="form-group"><label>住所：</label><input type="text" name="store_address" value="<?= htmlspecialchars($store['store_address']) ?>"></div>
<div class="form-group"><label>交通手段：</label><input type="text" name="access" value="<?= htmlspecialchars($store['access']) ?>"></div>
<div class="form-group"><label>営業時間：</label><input type="text" name="opening_hours" value="<?= htmlspecialchars($store['opening_hours']) ?>"></div>
<div class="form-group"><label>予算口コミ：</label><input type="text" name="budget_review" value="<?= htmlspecialchars($store['budget']) ?>"></div>
<div class="form-group"><label>支払い方法：</label><input type="text" name="payment_method" value="<?= htmlspecialchars($store['payment_methods']) ?>"></div>
<div class="form-group">
<label>貸切：</label>
<div class="radio-group">
<label><input type="radio" name="private_available" value="1" <?= $store['private_available']?'checked':'' ?>>可</label>
<label><input type="radio" name="private_available" value="0" <?= !$store['private_available']?'checked':'' ?>>不可</label>
</div>
</div>
<div class="form-group">
<label>たばこ：</label>
<div class="radio-group">
<label><input type="radio" name="non_smoking" value="1" <?= $store['non_smoking']?'checked':'' ?>>禁煙</label>
<label><input type="radio" name="non_smoking" value="0" <?= !$store['non_smoking']?'checked':'' ?>>喫煙可</label>
</div>
</div>
<div class="form-group"><label>ホームページ：</label><input type="text" name="homepage_url" value="<?= htmlspecialchars($store['homepage_url']) ?>"></div>
<div class="form-group"><label>オープン日：</label><input type="date" name="open_date" value="<?= htmlspecialchars($store['open_date']) ?>"></div>

<!-- 既存画像 -->
<div class="form-group">
<label>既存画像：</label>
<div class="existing-photos">
<?php foreach($photos as $photo): ?>
    <div class="photo-container" data-id="<?= $photo['store_photo_id'] ?>">
        <img src="<?= htmlspecialchars($photo['store_photo_path']) ?>" class="preview-img">
        <span class="delete-photo">✕</span>
    </div>
<?php endforeach; ?>
</div>
</div>

<!-- 画像追加 -->
<div class="form-group">
<label>画像追加：</label>
<input type="file" name="new_photos[]" multiple>
</div>

<div class="btn-center"><button class="edit-btn" type="submit">更新</button></div>
</form>

<!-- フッター -->
<footer class="footer">
    <div class="footer-content">
        &copy; <?= date('Y') ?> KANPO 管理者
    </div>
</footer>

<script>
// ジャンルドロップダウン
const dropdown = document.querySelector('.dropdown');
const displayInput = document.getElementById('genreDisplay');
const dropdownList = document.querySelector('.dropdown-content');
const hiddenInput = document.getElementById('category_id');
const genreInput = document.getElementById('genre_name');

displayInput.addEventListener('click', ()=>{ dropdownList.style.display = dropdownList.style.display==='block'?'none':'block'; });
dropdownList.querySelectorAll('div').forEach(item=>{
    item.addEventListener('click', ()=>{
        displayInput.value=item.textContent;
        hiddenInput.value=item.dataset.id;
        genreInput.value=item.textContent;
        dropdownList.style.display='none';
    });
});
document.addEventListener('click', e=>{if(!dropdown.contains(e.target)){dropdownList.style.display='none';}});

// 画像削除（Ajax）
document.querySelectorAll('.delete-photo').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const container = btn.parentElement;
        const photo_id = container.dataset.id;
        if(confirm('この画像を削除しますか？')){
            fetch('admin_store_delete_photo.php?id='+photo_id)
            .then(res=>res.json())
            .then(data=>{
                if(data.success) container.remove();
                else alert('削除失敗: '+data.message);
            })
            .catch(err=>alert('通信エラー'));
        }
    });
});
</script>

</body>
</html>
