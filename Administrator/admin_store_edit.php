<?php 
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

$store_id = $_GET['id'] ?? $_POST['store_id'] ?? null;
if(!$store_id){ echo "店舗IDが不正です"; exit; }

// 店舗情報取得
$stmt = $pdo->prepare("SELECT * FROM store WHERE store_id=?");
$stmt->execute([$store_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$store){ echo "店舗情報が存在しません"; exit; }

// 支払い方法・詳細を配列化
$store_payment_methods = json_decode($store['payment_methods'], true) ?? [];
$store_payment_details = json_decode($store['payment_details'], true) ?? [];

// 既存画像取得
$stmt = $pdo->prepare("SELECT * FROM store_photo WHERE store_id=?");
$stmt->execute([$store_id]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $payment_methods = $_POST['payment_method'] ?? [];
    $payment_details = $_POST['payment_details'] ?? [];

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
        'payment_methods'=>json_encode($payment_methods, JSON_UNESCAPED_UNICODE),
        'payment_details'=>json_encode($payment_details, JSON_UNESCAPED_UNICODE),
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
                budget=:budget, payment_methods=:payment_methods, payment_details=:payment_details,
                private_available=:private_available, non_smoking=:non_smoking, homepage_url=:homepage_url, open_date=:open_date
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

<div class="header-bar">
    <a href="admin_home.php" class="logo-link"><img src="../images/Akanpo.png" alt="サイトロゴ"></a>
    <div class="page-title">店舗編集</div>
</div>

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

<!-- お問い合わせ -->
<div class="form-group">
<label>お問い合わせ：</label>
<input type="text" name="contact_info" value="<?= htmlspecialchars($store['contact_info']) ?>">
</div>

<!-- 予約可否 -->
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

<!-- 支払い方法 -->
<div class="form-group">
<label>支払い方法 <span class="required">*</span></label>

<div class="payment-main">
<label><input type="checkbox" name="payment_method[]" value="現金" <?= in_array('現金',$store_payment_methods)?'checked':'' ?>> 現金</label>
<label><input type="checkbox" name="payment_method[]" value="クレジットカード" onclick="toggleDetail('card_detail')" <?= in_array('クレジットカード',$store_payment_methods)?'checked':'' ?>> クレジットカード</label>
<label><input type="checkbox" name="payment_method[]" value="電子マネー" onclick="toggleDetail('emoney_detail')" <?= in_array('電子マネー',$store_payment_methods)?'checked':'' ?>> 電子マネー</label>
<label><input type="checkbox" name="payment_method[]" value="QR決済" onclick="toggleDetail('qr_detail')" <?= in_array('QR決済',$store_payment_methods)?'checked':'' ?>> QR決済</label>
</div>

<div id="card_detail" class="payment-detail" style="display:<?= in_array('クレジットカード',$store_payment_methods)?'block':'none' ?>;">
<label><input type="checkbox" name="payment_details[]" value="VISA" <?= in_array('VISA',$store_payment_details)?'checked':'' ?>> VISA</label>
<label><input type="checkbox" name="payment_details[]" value="MasterCard" <?= in_array('MasterCard',$store_payment_details)?'checked':'' ?>> MasterCard</label>
<label><input type="checkbox" name="payment_details[]" value="JCB" <?= in_array('JCB',$store_payment_details)?'checked':'' ?>> JCB</label>
<label><input type="checkbox" name="payment_details[]" value="AMEX" <?= in_array('AMEX',$store_payment_details)?'checked':'' ?>> AMEX</label>
<label><input type="checkbox" name="payment_details[]" value="Diners" <?= in_array('Diners',$store_payment_details)?'checked':'' ?>> Diners</label>
</div>

<div id="emoney_detail" class="payment-detail" style="display:<?= in_array('電子マネー',$store_payment_methods)?'block':'none' ?>;">
<label><input type="checkbox" name="payment_details[]" value="Suica" <?= in_array('Suica',$store_payment_details)?'checked':'' ?>> Suica</label>
<label><input type="checkbox" name="payment_details[]" value="PASMO" <?= in_array('PASMO',$store_payment_details)?'checked':'' ?>> PASMO</label>
<label><input type="checkbox" name="payment_details[]" value="iD" <?= in_array('iD',$store_payment_details)?'checked':'' ?>> iD</label>
<label><input type="checkbox" name="payment_details[]" value="QUICPay" <?= in_array('QUICPay',$store_payment_details)?'checked':'' ?>> QUICPay</label>
</div>

<div id="qr_detail" class="payment-detail" style="display:<?= in_array('QR決済',$store_payment_methods)?'block':'none' ?>;">
<label><input type="checkbox" name="payment_details[]" value="PayPay" <?= in_array('PayPay',$store_payment_details)?'checked':'' ?>> PayPay</label>
<label><input type="checkbox" name="payment_details[]" value="楽天Pay" <?= in_array('楽天Pay',$store_payment_details)?'checked':'' ?>> 楽天Pay</label>
<label><input type="checkbox" name="payment_details[]" value="d払い" <?= in_array('d払い',$store_payment_details)?'checked':'' ?>> d払い</label>
</div>

</div>

<!-- 貸切 -->
<div class="form-group">
<label>貸切：</label>
<div class="radio-group">
<label><input type="radio" name="private_available" value="1" <?= $store['private_available']?'checked':'' ?>>可</label>
<label><input type="radio" name="private_available" value="0" <?= !$store['private_available']?'checked':'' ?>>不可</label>
</div>
</div>

<!-- タバコ -->
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

// 支払い詳細表示切替
function toggleDetail(id){
    let elem = document.getElementById(id);
    elem.style.display = elem.style.display==="none"?"block":"none";
}

// 画像削除
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
