document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".store-photos").forEach(container => {
    const photoData = JSON.parse(container.dataset.photos);
    let index = 0;

    const prevBtn = container.parentElement.querySelector(".photo-prev");
    const nextBtn = container.parentElement.querySelector(".photo-next");

    const renderPhotos = () => {
      container.innerHTML = "";
      const slice = photoData.slice(index, index + 3);
      slice.forEach((src, i) => {
        const img = document.createElement("img");
        img.src = "../../Administrator/" + src;
        img.className = "store-photo";
        if (i === 0 || i === 2) img.style.marginTop = "30px";
        img.onclick = () => openModal(img.src);
        container.appendChild(img);
      });

      prevBtn.style.display = index > 0 ? "inline-block" : "none";
      nextBtn.style.display = index + 3 < photoData.length ? "inline-block" : "none";
    };

    prevBtn.addEventListener("click", () => {
      if (index > 0) {
        index -= 1;
        renderPhotos();
      }
    });

    nextBtn.addEventListener("click", () => {
      if (index + 3 < photoData.length) {
        index += 1;
        renderPhotos();
      }
    });

    // ⏱ 自動スライド（3秒ごとに1枚進む）
    if (photoData.length > 3) {
      setInterval(() => {
        if (index + 3 < photoData.length) {
          index += 1;
        } else {
          index = 0; // ループ再開
        }
        renderPhotos();
      }, 5000); // ← ここで速度調整（3000ms = 3秒）
    }

    renderPhotos();
  });
});

// モーダル表示
function openModal(src) {
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImage");
  modal.style.display = "block";
  modalImg.src = src;
}

function closeModal() {
  document.getElementById("imageModal").style.display = "none";
}
