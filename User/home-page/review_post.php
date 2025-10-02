<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>口コミ登録画面</title>
    <style>
    body {
      font-family: "Arial", sans-serif;
      background: #fff;
      margin: 0;
      padding: 0;
    }
    header {
      background: #ffeb66;
      padding: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header img {
      height: 40px;
    }
    header .mypage {
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    main {
      padding: 15px;
    }
    h2 {
      font-size: 18px;
      margin: 10px 0;
    }
    .user {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 10px 0;
    }
    .stars {
      display: flex;
      gap: 5px;
      font-size: 28px;
      cursor: pointer;
    }
    .star {
      color: #ccc;
      transition: 0.2s;
    }
    .star.active {
      color: gold;
    }
    textarea {
      width: 100%;
      height: 80px;
      margin: 10px 0;
      padding: 8px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: none;
    }
    .photo-upload {
      background: #e6f3ff;
      padding: 12px;
      text-align: center;
      border-radius: 6px;
      cursor: pointer;
      margin-bottom: 15px;
    }
    .photo-upload input {
      display: none;
    }
    .uploaded-photos {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    .uploaded-photos img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .cost-box {
      background: #e6f3ff;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .cost-box h3 {
      font-size: 14px;
      margin-bottom: 10px;
    }
    .cost-options {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
    .cost-options button {
      padding: 8px;
      border: 1px solid #aaa;
      border-radius: 6px;
      background: #fff;
      cursor: pointer;
      transition: 0.2s;
    }
    .cost-options button.active {
      background: #66aaff;
      color: #fff;
      border-color: #66aaff;
    }
    .submit {
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
</head>
<body>
      <header>
    <img src="https://via.placeholder.com/100x40?text=KANPO" alt="KANPO Logo">
    <div class="mypage">マイページ</div>
  </header>

  <main>
    <h2>バーガーキング 博多駅筑紫口店</h2>
    <div class="user">
      <span>ジョーで～す</span>
    </div>
        <div class="stars">
      <span class="star" data-value="1">★</span>
      <span class="star" data-value="2">★</span>
      <span class="star" data-value="3">★</span>
      <span class="star" data-value="4">★</span>
      <span class="star" data-value="5">★</span>
    </div>

    <!-- コメント入力 -->
    <textarea placeholder="この場所での自分の体験や感想を共有しましょう"></textarea>

    <!-- 写真アップロード -->
    <label class="photo-upload">
      📷 写真を追加
      <input type="file" id="photoInput" accept="image/*" multiple>
    </label>
    <div class="uploaded-photos" id="photoPreview"></div>

    <!-- 費用選択 -->
    <div class="cost-box">
      <h3>1人当たりの費用はいくらでしたか？</h3>
      <div class="cost-options">
        <button>¥1〜1,000</button>
        <button>¥1,000〜2,000</button>
        <button>¥2,000〜3,000</button>
        <button>¥3,000〜4,000</button>
        <button>¥3,000〜4,000</button>
        <button>¥4,000〜5,000</button>
        <button>¥5,000〜6,000</button>
        <button>¥6,000〜7,000</button>
        <button>¥7,000〜8,000</button>
        <button>¥8,000〜9,000</button>
        <button>¥10,000以上</button>
      </div>
    </div>

    <button class="submit">投稿</button>
  </main>

  <script>
    // 星のクリックで評価
    const stars = document.querySelectorAll(".star");
    stars.forEach(star => {
      star.addEventListener("click", () => {
        const value = star.getAttribute("data-value");
        stars.forEach(s => {
          if (s.getAttribute("data-value") <= value) {
            s.classList.add("active");
          } else {
            s.classList.remove("active");
          }
        });
      });
    });

    // 写真プレビュー
    const photoInput = document.getElementById("photoInput");
    const photoPreview = document.getElementById("photoPreview");
    photoInput.addEventListener("change", () => {
      photoPreview.innerHTML = "";
      Array.from(photoInput.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
          const img = document.createElement("img");
          img.src = e.target.result;
          photoPreview.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    });

    // 費用ボタン選択
    const costButtons = document.querySelectorAll(".cost-options button");
    costButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        costButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
      });
    });
  </script>
</body>
</html>