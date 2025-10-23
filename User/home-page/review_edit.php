<?php
$pdo = new PDO('mysql:host=localhost;dbname=kanpo;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>口コミ編集画面</title>

    
</head>
<body>
    <header class="header">
    <img src="../../images/Kinpo.png" alt="KANPO Logo">
    <button id="mypageBtn" class="mypage">マイページ</button>
    <style>
body {
  font-family: "Hiragino Kaku Gothic ProN", sans-serif;
  background: #fff;
  margin: 0;
  padding: 0;
  color: #333;
}

/* ヘッダー */
header {
  background-color: #f7d76b;
  padding: 10px 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
header img {
  height: 30px;
}
header button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

/* タイトル */
h3 {
  margin: 15px;
  font-size: 18px;
}
.user-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 16px;
  margin-bottom: 5px;
}

/* 星評価 */
.stars {
  display: flex;
  justify-content: center;
  margin: 5px 0 15px;
}
.star {
  font-size: 35px;
  color: #ddd;
  cursor: pointer;
  transition: color 0.2s;
}
.star.active {
  color: #f7d76b;
}

/* 費用セレクト */
.price-select {
  text-align: center;
  margin-bottom: 15px;
}
.price-select select {
  padding: 6px 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
}

/* コメント欄 */
.comment-section {
  width: 85%;
  margin: 0 auto 20px;
  text-align: left;
}
.comment-section h4 {
  margin-bottom: 5px;
}
.comment-section textarea {
  width: 100%;
  height: 120px;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 8px;
  resize: none;
  font-size: 14px;
}

/* 写真エリア */
.photo-section {
  width: 85%;
  margin: 0 auto;
  text-align: left;
}
.photo-section h4 {
  margin-bottom: 5px;
}
.photo-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 10px;
}
.photo-container {
  position: relative;
}
.photo-container img {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
}
.photo-container input[type="checkbox"] {
  position: absolute;
  top: 5px;
  left: 5px;
  transform: scale(1.3);
}

/* 写真追加ボタン */
#photoInput {
  display: none;
}
.add-photo-btn {
  display: block;
  width: 100%;
  background: #d7efff;
  color: #333;
  padding: 10px 25px;
  border-radius: 20px;
  text-align: center;
  margin: 10px auto;
  cursor: pointer;
  font-weight: bold;
}

/* ・・・メニュー */
.menu-wrapper {
  position: relative;
  display: flex;
  justify-content: flex-end;
  margin-right: 15px;
}
.menu-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  line-height: 1;
}
.delete-menu {
  position: absolute;
  top: 25px;
  right: 0;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 8px 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  display: none;
  flex-direction: column;
  gap: 5px;
  z-index: 10;
}
.delete-menu button {
  background: none;
  border: none;
  text-align: left;
  font-size: 14px;
  cursor: pointer;
}
.delete-menu .select-all {
  color: #000;
}
.delete-menu .delete {
  color: red;
}

/* 更新ボタン */
.submit-btn {
  width: 100%;
      padding: 12px;
      background: #4169e1;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
}
</style>
  </header>

<h3>バーガーキング 博多駅筑紫口店</h3>

<div class="user-info">
  <span>👤 ジョーで〜す</span>
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
  <textarea placeholder="この場所での自分の体験や感想を共有しましょう"></textarea>
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