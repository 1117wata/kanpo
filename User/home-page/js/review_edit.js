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