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