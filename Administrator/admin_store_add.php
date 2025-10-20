<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kanpo;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "DB接続エラー: " . $e->getMessage();
    exit;
}

// 初期設定
$error = '';
$success = '';

// フォーム送信処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $store_name = trim($_POST['store_name'] ?? '');
    $contact_info = trim($_POST['contact_info'] ?? '');
    $reservation_available = trim($_POST['reservation_available'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $store_address = trim($_POST['store_address'] ?? '');
    $access = trim($_POST['access'] ?? '');
    $opening_hours = trim($_POST['business_hours'] ?? '');
    $budget = trim($_POST['budget_review'] ?? '');
    $payment_methods = trim($_POST['payment_method'] ?? '');
    $private_available = trim($_POST['private_available'] ?? '');
    $non_smoking = trim($_POST['non_smoking'] ?? '');
    $homepage_url = trim($_POST['homepage'] ?? '');
    $open_date = trim($_POST['open_date'] ?? '');

    // 未入力チェック（最低限）
    if ($store_name === '' || $category_id === '' || $store_address === '' || $access === '' || $opening_hours === '' || $budget === '' || $payment_methods === '' || $homepage_url === '' || $open_date === '') {
        $error = "必須項目（店名・ジャンル・住所・交通手段・営業時間・予算・支払い方法・ホームページ・オープン日）を入力してください。";
    } else {
        try {
            // 店舗情報登録
            $sql = "INSERT INTO store (
                        store_name, category_id, contact_info, reservation_available,
                        store_address, access, opening_hours, budget,
                        payment_methods, private_available, non_smoking,
                        homepage_url, open_date
                    ) VALUES (
                        :store_name, :category_id, :contact_info, :reservation_available,
                        :store_address, :access, :opening_hours, :budget,
                        :payment_methods, :private_available, :non_smoking,
                        :homepage_url, :open_date
                    )";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':store_name', $store_name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':contact_info', $contact_info);
            $stmt->bindParam(':reservation_available', $reservation_available);
            $stmt->bindParam(':store_address', $store_address);
            $stmt->bindParam(':access', $access);
            $stmt->bindParam(':opening_hours', $opening_hours);
            $stmt->bindParam(':budget', $budget);
            $stmt->bindParam(':payment_methods', $payment_methods);
            $stmt->bindParam(':private_available', $private_available);
            $stmt->bindParam(':non_smoking', $non_smoking);
            $stmt->bindParam(':homepage_url', $homepage_url);
            $stmt->bindParam(':open_date', $open_date);

            $stmt->execute();

            $success = "店舗情報が正常に登録されました。";
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

    <form class="store-add-form" method="post">
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
                <input type="text" id="genreDisplay" placeholder="選択してください" value="<?= htmlspecialchars($genre ?? '', ENT_QUOTES) ?>" readonly>
                <input type="hidden" name="category_id" id="category_id">
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
            <label>支払い方法：</label>
            <input type="text" name="payment_method">
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
            <input type="file" name="store_image">
        </div>

        <div class="btn-center">
            <button class="add-btn" type="submit">登録</button>
        </div>
    </form>
</body>
</html>

<script>
  const dropdown = document.querySelector('.dropdown');
  const displayInput = document.getElementById('genreDisplay');
  const dropdownList = document.querySelector('.dropdown-content');
  const hiddenInput = document.getElementById('category_id');

  displayInput.addEventListener('click', () => {
    dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
  });

  dropdownList.querySelectorAll('div').forEach(item => {
    item.addEventListener('click', () => {
      displayInput.value = item.textContent;
      hiddenInput.value = item.dataset.id;
      dropdownList.style.display = 'none';
    });
  });

  document.addEventListener('click', e => {
    if (!dropdown.contains(e.target)) {
      dropdownList.style.display = 'none';
    }
  });
</script>
