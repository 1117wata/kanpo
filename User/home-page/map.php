<?php
session_start();

$pdo = new PDO('mysql:host=mysql322.phy.lolipop.lan;dbname=LAA1681943-watabe17;charset=utf8', 'LAA1681943', 'Watabe17', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$target_store_id = $_GET['store_id'] ?? null;

// カテゴリ一覧を取得
$categories = $pdo->query("SELECT category_id, category_name FROM category")->fetchAll();

// 店舗データ取得（緯度経度がある店舗のみ）
$stmt = $pdo->query("SELECT s.store_id, s.store_name, s.store_address, s.latitude, s.longitude, c.category_id, c.category_name
                     FROM store s
                     JOIN category c ON s.category_id = c.category_id
                     WHERE s.latitude IS NOT NULL AND s.longitude IS NOT NULL");
$stores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>店舗マップ</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <link rel="stylesheet" href="css/map.css">
  <script src="js/map.js" defer></script>
  <style>

  </style>
</head>
<body>
  <!-- ヘッダー -->
  <div class="header-bar">
      <a href="home.php" class="logo-link">
          <img src="../../images/Ukanpo.png" alt="サイトロゴ">
      </a>
      <div class="page-title">店舗マップ</div>
  </div>

  <!-- トグルボタン -->
  <button id="toggleFilter">カテゴリ ▼</button>

  <!-- カテゴリ選択ボタン -->
  <div class="filter-bar" id="filterBar">
    <button class="category-btn active" data-id="" style="background:#9e9e9e;">すべて</button>
    <?php foreach ($categories as $cat): 
      $colors = [
        1 => "#e53935", // 中華料理 赤
        2 => "#1e88e5", // フランス料理 青
        3 => "#8e24aa", // 多国籍料理 紫
        4 => "#43a047", // イタリア料理 緑
        5 => "#fb8c00", // 居酒屋 オレンジ
        6 => "#6d4c41", // 和食 茶
        7 => "#b71c1c", // 懐石料理 濃赤
        8 => "#0d47a1", // 韓国料理 濃青
        9 => "#424242"  // 炉端焼き 黒
      ];
      $color = $colors[$cat['category_id']] ?? "#9e9e9e";
    ?>
      <button class="category-btn" data-id="<?= $cat['category_id'] ?>" style="background:<?= $color ?>;">
        <?= htmlspecialchars($cat['category_name']) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- 地図 -->
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
 const stores = <?= json_encode($stores, JSON_UNESCAPED_UNICODE) ?>;
  const targetStoreId = <?= json_encode($target_store_id) ?>;
</script>

</body>
</html>
