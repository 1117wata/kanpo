// 星クリックで評価を反映（0.5刻み対応）
const stars = document.querySelectorAll(".star");
stars.forEach(star => {
  star.addEventListener("click", e => {
    const rect = star.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const half = clickX < rect.width / 2 ? 0.5 : 1;

    let value = parseInt(star.getAttribute("data-value"));
    if (half === 0.5) value -= 0.5;

    document.getElementById("ratingInput").value = value;

    // 表示更新
    stars.forEach(s => {
      s.classList.remove("active", "half");
      const sValue = parseInt(s.getAttribute("data-value"));
      if (sValue <= Math.floor(value)) {
        s.classList.add("active");
      } else if (sValue === Math.ceil(value) && value % 1 !== 0) {
        s.classList.add("half");
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

// 費用選択
const costButtons = document.querySelectorAll(".cost-options button");
costButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    costButtons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    document.getElementById("priceRangeInput").value = btn.getAttribute("data-value");
  });
});
