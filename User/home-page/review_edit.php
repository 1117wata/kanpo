<?php
session_start();

$_SESSION['user_id'] = 1; // テスト用にユーザーIDをセット
$user_id = $_SESSION['user_id'];

$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$review_id = $_GET['id'] ?? null;

$sql = "SELECT * FROM review INNER JOIN store 
ON review.store_id = store.store_id 
WHERE review.review_id = ? 
ORDER BY review.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$review_id]);
$reviews = $stmt->fetch();

$user_sql = "SELECT username FROM user WHERE user_id = ?";
$user_stmt = $pdo->prepare($user_sql);
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>口コミ編集画面</title>
    <link rel="stylesheet" href="css/review_edit.css">
</head>
<body>
    <header class="header">
      <img src="../../images/Kinpo.png" alt="KANPO Logo" class="logo">
      <form action="profile.php" method="get">
        <button id="mypageBtn" class="mypage">マイページ</button>
      </form>
    </header>

    <h3><?= $reviews['store_name'] ?></h3>

    <div class="user-info">
      <span><?= $user['username'] ?></span>
    </div>

    <!-- 星評価 -->
    <div class="stars">
      <span class="star" data-value="1">★</span>
      <span class="star" data-value="2">★</span>
      <span class="star" data-value="3">★</span>
      <span class="star" data-value="4">★</span>
      <span class="star" data-value="5">★</span>
    </div>

    <!-- 価格選択 -->
    <div class="price-select">
      <select>
        <option>¥1〜1,000</option>
        <option>¥1,000〜2,000</option>
        <option>¥2,000〜3,000</option>
        <option>¥3,000〜4,000</option>
        <option>¥4,000〜5,000</option>
        <option>¥5,000〜6,000</option>
        <option>¥6,000〜7,000</option>
        <option>¥7,000〜8,000</option>
        <option>¥8,000〜9,000</option>
        <option>¥10,000以上</option>
      </select>
    </div>

    <!-- コメント -->
    <div class="comment-section">
      <h4>コメント</h4>
      <textarea placeholder="この場所での自分の体験や感想を共有しましょう"><?= $reviews['comment'] ?></textarea>
    </div>

    <!-- 写真セクション -->
    <div class="photo-section">
      <h4>写真</h4>
      <div class="photo-preview" id="photoPreview"></div>

      <div class="menu-wrapper">
        <button class="menu-btn">⋯</button>
        <div class="delete-menu" id="deleteMenu">
          <button class="select-all">すべて選択</button>
          <button class="delete">削除</button>
        </div>
      </div>

      <label for="photoInput" class="add-photo-btn">📷 写真を追加</label>
      <input type="file" id="photoInput" accept="image/*" multiple>
    </div>

    <!-- 更新ボタン -->
    <button class="submit-btn">更新</button>

    <script>
    // 星評価
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
      star.addEventListener('click', () => {
        stars.forEach((s, i) => s.classList.toggle('active', i <= index));
      });
    });

    // 写真追加
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');

    photoInput.addEventListener('change', () => {
      [...photoInput.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
          const container = document.createElement('div');
          container.classList.add('photo-container');
          container.innerHTML = `
            <input type="checkbox" class="photo-check">
            <img src="${e.target.result}">
          `;
          photoPreview.appendChild(container);
        };
        reader.readAsDataURL(file);
      });
    });

    // ・・・メニュー開閉
    const menuBtn = document.querySelector('.menu-btn');
    const deleteMenu = document.getElementById('deleteMenu');

    menuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      deleteMenu.style.display = deleteMenu.style.display === 'flex' ? 'none' : 'flex';
    });

    // 外をクリックしたら閉じる
    document.addEventListener('click', (e) => {
      if (!deleteMenu.contains(e.target) && !menuBtn.contains(e.target)) {
        deleteMenu.style.display = 'none';
      }
    });

    // 削除メニュー動作
    document.querySelector('.select-all').addEventListener('click', () => {
      document.querySelectorAll('.photo-check').forEach(c => c.checked = true);
    });

    document.querySelector('.delete').addEventListener('click', () => {
      document.querySelectorAll('.photo-check:checked').forEach(c => c.parentElement.remove());
    });
    </script>
  </body>
</html>