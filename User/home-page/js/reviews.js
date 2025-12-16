function openImageLightbox(src) {
  document.getElementById("lightboxImg").src = src;
  document.getElementById("imageLightbox").style.display = "block";
}
function closeImageLightbox() {
  document.getElementById("imageLightbox").style.display = "none";
}
const swiper = new Swiper('.mySwiper', {
  loop: true,
  slidesPerView: 3,
  spaceBetween: 20,
  pagination: { el: '.swiper-pagination', clickable: true },
  navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
  breakpoints: {
    1024: { slidesPerView: 3 },
    768: { slidesPerView: 2 },
    480: { slidesPerView: 1 }
  }
});

function openReviewGallery(id) {
  document.getElementById("reviewGalleryModal" + id).style.display = "block";
}
function closeReviewGallery(id) {
  document.getElementById("reviewGalleryModal" + id).style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".sort-toggle");
  const menu = document.querySelector(".sort-menu");

  toggle.addEventListener("click", () => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
  });

  // メニュー外をクリックしたら閉じる
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".sort-dropdown")) {
      menu.style.display = "none";
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".filter-toggle");
  const menu = document.querySelector(".filter-menu");

  toggle.addEventListener("click", () => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
  });

  // メニュー外クリックで閉じる
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".filter-dropdown")) {
      menu.style.display = "none";
    }
  });
});

