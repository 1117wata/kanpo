document.addEventListener("DOMContentLoaded", () => {
  let markers = [];

  const map = L.map('map').setView([33.5902, 130.4017], 13);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; Carto',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map);

  // 📌 福岡市中心部の範囲制限
  const bounds = L.latLngBounds(
    [33.565, 130.375], // 南西
    [33.615, 130.435]  // 北東
  );
  map.fitBounds(bounds);
  map.setMaxBounds(bounds);
  map.setMinZoom(13);

  // 📌 カテゴリごとのカラーコード
  const categoryColors = {
    1: "#e53935", // 中華料理 赤
    2: "#1e88e5", // フランス料理 青
    3: "#8e24aa", // 多国籍料理 紫
    4: "#43a047", // イタリア料理 緑
    5: "#fb8c00", // 居酒屋 オレンジ
    6: "#6d4c41", // 和食 茶
    7: "#b71c1c", // 懐石料理 濃赤
    8: "#0d47a1", // 韓国料理 濃青
    9: "#424242"  // 炉端焼き 黒
  };

  // 📌 SVGでカスタムアイコンを生成
  function getCustomIcon(hexColor) {
    return L.divIcon({
      className: "custom-marker",
      html: `
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="41">
          <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 9.4 12.5 28.5 12.5 28.5S25 21.9 25 12.5C25 5.6 19.4 0 12.5 0z"
                fill="${hexColor}" stroke="black" stroke-width="2"/>
        </svg>
      `,
      iconAnchor: [12, 41],
      popupAnchor: [1, -34]
    });
  }

  // 📌 店舗マーカー描画
  function renderMarkers(categoryId = "") {
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    stores.forEach(store => {
      if (store.latitude && store.longitude) {
        if (categoryId === "" || store.category_id == categoryId) {
          const color = categoryColors[store.category_id] || "#e53935";
          const marker = L.marker([store.latitude, store.longitude], { icon: getCustomIcon(color) })
            .addTo(map)
            .bindTooltip(
              `<b>${store.store_name}</b><br>${store.store_address}<br>${store.category_name}`,
              { direction: "top", offset: [0, -10], sticky: true }
            );
          marker.on('click', () => {
            window.location.href = `store_detail.php?store_id=${store.store_id}`;
          });
          markers.push(marker);
        }
      }
    });
  }

  renderMarkers();

  // 📌 カテゴリボタンイベント
  document.querySelectorAll(".category-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".category-btn").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      renderMarkers(btn.dataset.id);
    });
  });

  // 📌 トグル開閉イベント
  const filterBar = document.getElementById("filterBar");
  const toggleBtn = document.getElementById("toggleFilter");
  toggleBtn.addEventListener("click", () => {
    if (filterBar.style.display === "none") {
      filterBar.style.display = "flex";
      toggleBtn.textContent = "カテゴリ ▼";
    } else {
      filterBar.style.display = "none";
      toggleBtn.textContent = "カテゴリ ▲";
    }
  });

  // 📌 現在地表示
  map.locate({ setView: true, maxZoom: 16 });
  map.on('locationfound', e => {
    const radius = e.accuracy / 2;
    L.marker(e.latlng).addTo(map).bindPopup("あなたの現在地").openPopup();
    L.circle(e.latlng, radius).addTo(map);
  });
  map.on('locationerror', e => {
    alert("現在地を取得できませんでした: " + e.message);
  });
});
