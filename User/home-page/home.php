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

<header>
  <button onclick="history.back()">←</button>
  <div>👤</div>
</header>

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
?>

<!-- ジャンル選択 -->
<section>
  <h2 style="padding: 0 16px;">ジャンル</h2>
  <div class="filter-grid" id="category-grid">
    <?php foreach ($categories as $index => $category): ?>
      <a href="search_results.php?category_id=<?= $category['category_id'] ?>" class="filter-card <?= $index >= 6 ? 'hidden' : '' ?>">
        <div class="card-bg" style="background-image: url('images/category/<?= $category['category_id'] ?>.jpg');"></div>
        <span class="card-label"><?= htmlspecialchars($category['category_name']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <button class="toggle-btn" onclick="toggleCategory()" id="toggle-category-btn">＋もっと見る</button>
</section>

<!-- エリア選択 -->
<section>
  <h2 style="padding: 0 16px;">エリア</h2>
  <div class="filter-grid" id="area-grid">
    <?php foreach ($areas as $index => $area): ?>
      <a href="search_results.php?area_id=<?= $area['area_id'] ?>" class="filter-card <?= $index >= 6 ? 'hidden' : '' ?>">
        <div class="card-bg" style="background-image: url('images/area/<?= $area['area_id'] ?>.jpg');"></div>
        <span class="card-label"><?= htmlspecialchars($area['area_name']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <button class="toggle-btn" onclick="toggleArea()" id="toggle-area-btn">＋もっと見る</button>
</section>

</body>
</html>
