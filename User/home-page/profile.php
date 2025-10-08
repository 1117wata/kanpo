<?php
session_start();

// 開発中はログインなしで動作確認できるようにする
$dev_mode = true;
if (!isset($_SESSION['user_id'])) {
    if ($dev_mode) {
        $_SESSION['user_id'] = 1; // テストユーザーID
    } else {
        header('Location: ../login.php');
        exit;
    }
}

$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8', 'root', '');

// ユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 投稿（口コミ）を取得
$stmt = $pdo->prepare("SELECT * FROM review WHERE user_id = ? ORDER BY review_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="css/profile.css">

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プロフィール</title>
  <link rel="stylesheet" href="profile.css">
</head>
<body>
  <header>
    <div class="logo">KANPO</div>
    <h1>プロフィール</h1>
  </header>

  <main>
    <section class="user-info">
      <img src="<?php echo htmlspecialchars($user['icon_path'] ?? '../default-icon.png'); ?>" alt="アイコン" class="icon">
      <div class="user-details">
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        <p><strong><?php echo count($reviews); ?></strong> 件の口コミ</p>
        <button class="edit-btn">プロフィール編集</button>
      </div>
    </section>

    <section class="review-list">
      <?php if (count($reviews) === 0): ?>
        <p class="no-review">まだ口コミがありません。</p>
      <?php else: ?>
        <?php foreach ($reviews as $review): ?>
          <article class="review">
            <h3><?php echo htmlspecialchars($review['store_name']); ?></h3>
            <div class="meta">
              <span class="date"><?php echo htmlspecialchars($review['review_date']); ?></span>
              <span class="rating">★<?php echo htmlspecialchars($review['rating']); ?></span>
            </div>
            <p class="detail"><?php echo nl2br(htmlspecialchars($review['store_detail'])); ?></p>
            <p class="comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
            <div class="images">
              <?php for ($i = 1; $i <= 3; $i++): ?>
                <?php if (!empty($review["image$i"])): ?>
                  <img src="<?php echo htmlspecialchars($review["image$i"]); ?>" alt="口コミ画像" class="review-img">
                <?php endif; ?>
              <?php endfor; ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
