<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

function getAreaIdFromAddress($pdo, $address) {
    $sql = "SELECT area_id, area_name FROM area";
    $stmt = $pdo->query($sql);
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($areas as $area) {
        if (strpos($address, $area['area_name']) !== false) {
            return $area['area_id'];
        }
    }
    return null;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $store_name = trim($_POST['store_name'] ?? '');
    $contact_info = trim($_POST['contact_info'] ?? '');
    $reservation_available = trim($_POST['reservation_available'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $store_address = trim($_POST['store_address'] ?? '');
    $access = trim($_POST['access'] ?? '');
    $opening_hours = trim($_POST['business_hours'] ?? '');
    $budget = trim($_POST['budget_review'] ?? '');

    // ▼ 支払い方法（大分類・細分類）
    $payment_methods = $_POST['payment_method'] ?? [];
    $payment_details = $_POST['payment_details'] ?? [];

    // JSON 形式に変換
    $payment_methods_json = json_encode($payment_methods, JSON_UNESCAPED_UNICODE);
    $payment_details_json = json_encode($payment_details, JSON_UNESCAPED_UNICODE);

    $private_available = trim($_POST['private_available'] ?? '');
    $non_smoking = trim($_POST['non_smoking'] ?? '');
    $homepage_url = trim($_POST['homepage'] ?? '');
    $open_date = trim($_POST['open_date'] ?? '');

    $area_id = getAreaIdFromAddress($pdo, $store_address);

    if ($store_name === '' || $category_id === '' || $genre === '' || $store_address === '' || 
        $access === '' || $opening_hours === '' || $budget === '' || empty($payment_methods) || 
        $homepage_url === '' || $open_date === '') {
        $error = "必須項目をすべて入力してください。";
    } elseif ($area_id === null) {
        $error = "住所に対応するエリアが見つかりません。";
    } else {
        try {
            $sql = "INSERT INTO store (
                        store_name, category_id, genre, contact_info, reservation_available,
                        store_address, access, opening_hours, budget,
                        payment_methods, payment_details, private_available, non_smoking,
                        homepage_url, open_date, area_id
                    ) VALUES (
                        :store_name, :category_id, :genre, :contact_info, :reservation_available,
                        :store_address, :access, :opening_hours, :budget,
                        :payment_methods, :payment_details, :private_available, :non_smoking,
                        :homepage_url, :open_date, :area_id
                    )";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':store_name', $store_name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':contact_info', $contact_info);
            $stmt->bindParam(':reservation_available', $reservation_available);
            $stmt->bindParam(':store_address', $store_address);
            $stmt->bindParam(':access', $access);
            $stmt->bindParam(':opening_hours', $opening_hours);
            $stmt->bindParam(':budget', $budget);

            // ▼ 支払い方法 JSON
            $stmt->bindParam(':payment_methods', $payment_methods_json);
            $stmt->bindParam(':payment_details', $payment_details_json);

            $stmt->bindParam(':private_available', $private_available);
            $stmt->bindParam(':non_smoking', $non_smoking);
            $stmt->bindParam(':homepage_url', $homepage_url);
            $stmt->bindParam(':open_date', $open_date);
            $stmt->bindParam(':area_id', $area_id);

            $stmt->execute();
            $store_id = $pdo->lastInsertId();

            // ▼ 画像登録
            if (!empty($_FILES['store_images']['name'][0])) {
                $upload_dir = 'uploads/store_photos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                foreach ($_FILES['store_images']['tmp_name'] as $index => $tmp_path) {
                    $original_name = basename($_FILES['store_images']['name'][$index]);
                    $target_path = $upload_dir . time() . '_' . $original_name;

                    if (move_uploaded_file($tmp_path, $target_path)) {
                        $sql_photo = "INSERT INTO store_photo (store_id, store_photo_path, uploaded_at)
                                      VALUES (:store_id, :path, NOW())";
                        $stmt_photo = $pdo->prepare($sql_photo);
                        $stmt_photo->bindParam(':store_id', $store_id);
                        $stmt_photo->bindParam(':path', $target_path);
                        $stmt_photo->execute();
                    }
                }
            }

            $success = "店舗情報と画像が正常に登録されました。";
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
    <title>店舗追加画面</title>
    <link rel="stylesheet" href="css/store_add.css">
</head>
<body>
    <div class="header-bar">
        <a href="admin_home.php" class="logo-link">
            <img src="../images/Akanpo.png" alt="サイトロゴ">
        </a>
        <div class="page-title">店舗追加</div>
    </div>

    <!-- 戻るボタン -->
    <div class="back-btn-container">
        <a href="javascript:history.back();" class="back-link">
            <img src="../images/back.png" alt="戻る" class="back-icon">
        </a>
    </div>
    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form class="store-add-form" method="post" enctype="multipart/form-data">
        <!-- 各フォーム項目（省略せず記載） -->
        <div class="form-group">
            <label>店名：</label>
            <input type="text" name="store_name" value="<?= htmlspecialchars($store_name ?? '', ENT_QUOTES) ?>"><br>
        </div>
          <div class="form-group">
            <label>お問い合わせ：</label>
            <input type="text" name="contact_info" value="<?= htmlspecialchars($contact_info ?? '', ENT_QUOTES) ?>"><br>
        </div>

        <div class="form-group">
            <label>予約可否：</label>
            <div class="radio-group">
                <label><input type="radio" name="reservation_available" value="1" required>可</label>
                <label><input type="radio" name="reservation_available" value="0">不可</label>
            </div>
        </div>

        <div class="form-group">
        <label>ジャンル：</label>
        <div class="dropdown">
            <input type="text" id="genreDisplay" placeholder="選択してください" readonly>
            <input type="hidden" name="category_id" id="category_id">
            <input type="hidden" name="genre" id="genre_name">
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

        <div class="form-group">
            <label>住所：</label>
            <input type="text" name="store_address" value="<?= htmlspecialchars($store_address ?? '', ENT_QUOTES) ?>"><br>
        </div>

        <div class="form-group">
            <label>交通手段：</label>
            <input type="text" name="access">
        </div>

        <div class="form-group">
            <label>営業時間：</label>
            <input type="text" name="business_hours">
        </div>

        <div class="form-group">
            <label>予算口コミ：</label>
            <input type="text" name="budget_review">
        </div>

        <div class="form-group">
            <label>支払い方法 <span class="required">*</span></label>

        <!-- 大分類 -->
        <div class="payment-main">
            <label><input type="checkbox" name="payment_method[]" value="現金"> 現金</label>
            <label><input type="checkbox" name="payment_method[]" value="クレジットカード" onclick="toggleDetail('card_detail')"> クレジットカード</label>
            <label><input type="checkbox" name="payment_method[]" value="電子マネー" onclick="toggleDetail('emoney_detail')"> 電子マネー</label>
            <label><input type="checkbox" name="payment_method[]" value="QR決済" onclick="toggleDetail('qr_detail')"> QR決済</label>
        </div>

        <!-- クレカ詳細 -->
        <div id="card_detail" class="payment-detail" style="display:none;">
            <label><input type="checkbox" name="payment_details[]" value="VISA"> VISA</label>
            <label><input type="checkbox" name="payment_details[]" value="MasterCard"> MasterCard</label>
            <label><input type="checkbox" name="payment_details[]" value="JCB"> JCB</label>
            <label><input type="checkbox" name="payment_details[]" value="AMEX"> AMEX</label>
            <label><input type="checkbox" name="payment_details[]" value="Diners"> Diners</label>
        </div>

        <!-- 電子マネー詳細 -->
        <div id="emoney_detail" class="payment-detail" style="display:none;">
            <label><input type="checkbox" name="payment_details[]" value="Suica"> Suica</label>
            <label><input type="checkbox" name="payment_details[]" value="PASMO"> PASMO</label>
            <label><input type="checkbox" name="payment_details[]" value="iD"> iD</label>
            <label><input type="checkbox" name="payment_details[]" value="QUICPay"> QUICPay</label>
        </div>

        <!-- QR決済詳細 -->
        <div id="qr_detail" class="payment-detail" style="display:none;">
            <label><input type="checkbox" name="payment_details[]" value="PayPay"> PayPay</label>
            <label><input type="checkbox" name="payment_details[]" value="楽天Pay"> 楽天Pay</label>
            <label><input type="checkbox" name="payment_details[]" value="d払い"> d払い</label>
        </div>
    </div>

        <div class="form-group">
            <label>貸切：</label>
            <div class="radio-group">
                <label><input type="radio" name="private_available" value="1">可</label>
                <label><input type="radio" name="private_available" value="0">不可</label>
            </div>
        </div>

        <div class="form-group">
            <label>たばこ：</label>
            <div class="radio-group">
                <label><input type="radio" name="non_smoking" value="1">禁煙</label>
                <label><input type="radio" name="non_smoking" value="0">喫煙可</label>
            </div>
        </div>

        <div class="form-group">
            <label>ホームページ：</label>
            <input type="text" name="homepage">
        </div>

        <div class="form-group">
            <label>オープン日：</label>
            <input type="date" name="open_date">
        </div>

        <div class="form-group">
            <label>店舗画像追加：</label>
            <input type="file" name="store_images[]" multiple>
        </div>

        <div class="btn-center">
            <button class="add-btn" type="submit">登録</button>
        </div>
    </form>
        <!-- フッター -->
        <footer class="footer">
            <div class="footer-content">
                &copy; <?= date('Y') ?> KANPO 管理者
            </div>
    </footer>

</body>
</html>
<script>
  // JavaScriptでジャンル名とIDをセット
const dropdown = document.querySelector('.dropdown');
const displayInput = document.getElementById('genreDisplay');
const dropdownList = document.querySelector('.dropdown-content');
const hiddenInput = document.getElementById('category_id');
const genreInput = document.getElementById('genre_name');

displayInput.addEventListener('click', () => {
  dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
});

dropdownList.querySelectorAll('div').forEach(item => {
  item.addEventListener('click', () => {
    displayInput.value = item.textContent;
    hiddenInput.value = item.dataset.id;
    genreInput.value = item.textContent;
    dropdownList.style.display = 'none';
  });
});

document.addEventListener('click', e => {
  if (!dropdown.contains(e.target)) {
    dropdownList.style.display = 'none';
  }
});

function toggleDetail(id) {
    let elem = document.getElementById(id);
    elem.style.display = elem.style.display === "none" ? "block" : "none";
}
</script>