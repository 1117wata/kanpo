<?php
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');

$where = [];
$params = [];

if (!empty($_GET['keyword'])) {
  $where[] = "(store_name LIKE :keyword OR genre LIKE :keyword)";
  $params[':keyword'] = '%' . $_GET['keyword'] . '%';
}

if (!empty($_GET['category_id'])) {
  $where[] = "category_id = :category_id";
  $params[':category_id'] = $_GET['category_id'];
}

if (!empty($_GET['area_id'])) {
  $where[] = "area_id = :area_id";
  $params[':area_id'] = $_GET['area_id'];
}

$sql = "SELECT * FROM store";
if ($where) {
  $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>検索結果</title>
  <link rel="stylesheet" href="css/search_result.css">
</head>
<body>

<header>
  <a href="home.php">← 戻る</a>
</header>

<h2>検索結果</h2>

<!-- 検索フォーム -->
<form action="search_results.php" method="GET" class="search-box">
  <input type="text" name="keyword" placeholder="店舗名・キーワードで検索" />
  <button type="submit">検索</button>
</form>


<?php if (count($stores) === 0): ?>
  <p>該当する店舗は見つかりませんでした。</p>
<?php else: ?>
  <ul class="store-list">
    <?php foreach ($stores as $store): ?>
      <li>
        <h3><?= htmlspecialchars($store['store_name']) ?></h3>
        <p><?= htmlspecialchars($store['genre']) ?> / <?= htmlspecialchars($store['store_address']) ?></p>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

</body>
</html>
