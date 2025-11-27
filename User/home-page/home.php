<?php
session_start();
require_once '../../DB/db_connect.php';

$pdo = getDB();

$user = null;

if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

?>

<!DOCTYPE html>

<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>利用者ホーム画面</title>
  <script src="js/home.js" defer></script>
  <link rel="stylesheet" href="css/home.css">
</head>
<body>

<div class="header-bar">

    <div class="profile-box">
      <a href="profile.php" class="icon-link">
        <img src="<?= !empty($user['icon_path']) ? htmlspecialchars($user['icon_path'], ENT_QUOTES) : '../../images/aikon.png' ?>" class="user-icon" alt="プロフ画像">
      </a>

      <?php if ($user): ?>
        <span class="nickname-label"><?= htmlspecialchars($user['nickname'], ENT_QUOTES) ?></span>
      <?php endif; ?>
    </div>


    <div class="page-title"></div>
</div>


<!-- ロゴ表示 -->
<div class="logo">
  <img src="../../images/Kanpo.png" alt="ロゴ" />
</div>

<!-- 検索フォーム -->
<form action="search_results.php" method="GET" class="search-box">
  <input type="text" name="keyword" placeholder="店舗名・キーワードで検索" />
  <button type="submit">検索</button>
</form>

<?php
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');
$categories = $pdo->query("SELECT category_id, category_name FROM category")->fetchAll(PDO::FETCH_ASSOC);
$areas = $pdo->query("SELECT area_id, area_name FROM area")->fetchAll(PDO::FETCH_ASSOC);

// カテゴリと画像パスを取得（JOIN）
$categories = $pdo->query("
  SELECT c.category_id, c.category_name, ci.image_path
  FROM category c
  LEFT JOIN category_image ci ON c.category_id = ci.category_id
")->fetchAll(PDO::FETCH_ASSOC);

$areas = $pdo->query("
  SELECT a.area_id, a.area_name, ai.image_path
  FROM area a
  LEFT JOIN area_image ai ON a.area_id = ai.area_id
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- ジャンル選択 -->
<section>
  <h2 style="padding: 0 16px;">ジャンルから探す</h2>
  <div class="filter-grid" id="category-grid">
    <?php foreach ($categories as $index => $category): ?>
      <?php
        $bgPath = $category['image_path'] ?? "images/category/{$category['category_id']}.jpg";
      ?>
      <a href="search_results.php?category_id=<?= $category['category_id'] ?>" class="filter-card <?= $index >= 6 ? 'hidden' : '' ?>">
        <div class="card-bg" style="background-image: url('<?= htmlspecialchars($bgPath) ?>');"></div>
        <span class="card-label"><?= htmlspecialchars($category['category_name']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <button class="toggle-btn" onclick="toggleCategory()" id="toggle-category-btn">＋もっと見る</button>
</section>

<!-- エリア選択 -->
<section>
  <h2 style="padding: 0 16px;">エリアから探す</h2>
  <div class="filter-grid" id="area-grid">
    <?php foreach ($areas as $index => $area): ?>
      <?php
        $areaBg = $area['image_path'] ?? "images/area/{$area['area_id']}.jpg";
      ?>
      <a href="search_results.php?area_id=<?= $area['area_id'] ?>" class="filter-card <?= $index >= 6 ? 'hidden' : '' ?>">
        <div class="card-bg" style="background-image: url('<?= htmlspecialchars($areaBg) ?>');"></div>
        <span class="card-label"><?= htmlspecialchars($area['area_name']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <button class="toggle-btn" onclick="toggleArea()" id="toggle-area-btn">＋もっと見る</button>
</section>


<?php
/*// DB接続設定
$host = 'localhost';
$dbname = 'kanpo';
$user = 'root';
$pass = ''; // XAMPPのデフォルトなら空欄

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("DB接続失敗: " . $e->getMessage());
}

// アップロード処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'uploads/';
    
    // フォルダがなければ作成
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // ファイル名を安全に生成（日本語対策＋重複防止）
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $safeName = uniqid('img_') . '.' . $ext;
    $targetPath = $uploadDir . $safeName;

    // ファイル保存
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $categoryId = $_POST['category_id'];

        // DBにパスを保存
        $stmt = $pdo->prepare("INSERT INTO category_image (category_id, image_path) VALUES (?, ?)");
        $stmt->execute([$categoryId, $targetPath]);

        echo "<p style='color:green;'>✅ 画像アップロード＆DB登録完了！</p>";
    } else {
        echo "<p style='color:red;'>❌ 画像の保存に失敗しました。</p>";
    }
}

// エリア画像アップロード処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['area_image'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($_FILES['area_image']['name'], PATHINFO_EXTENSION);
    $safeName = uniqid('area_') . '.' . $ext;
    $targetPath = $uploadDir . $safeName;

    if (move_uploaded_file($_FILES['area_image']['tmp_name'], $targetPath)) {
        $areaId = $_POST['area_id'];
        $stmt = $pdo->prepare("INSERT INTO area_image (area_id, image_path) VALUES (?, ?)");
        $stmt->execute([$areaId, $targetPath]);
        echo "<p style='color:green;'>✅ エリア画像アップロード＆DB登録完了！</p>";
    } else {
        echo "<p style='color:red;'>❌ エリア画像の保存に失敗しました。</p>";
    }
}

?>

<!-- HTMLフォーム -->
<h2>画像アップロードフォーム</h2>
<form method="POST" enctype="multipart/form-data">
  <label>カテゴリID:</label><br>
  <input type="number" name="category_id" required><br><br>

  <label>画像ファイル:</label><br>
  <input type="file" name="image" accept="image/*" required><br><br>

  <button type="submit">アップロード</button>
</form>

<section style="padding: 16px;">
  <h2>エリア画像アップロードフォーム</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>エリアID:</label><br>
    <input type="number" name="area_id" required><br><br>

    <label>画像ファイル:</label><br>
    <input type="file" name="area_image" accept="image/*" required><br><br>

    <button type="submit">アップロード</button>
  </form>
</section>

*/
?>
</body>
</html>
