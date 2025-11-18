function toggleCategory() {
  const hiddenCards = document.querySelectorAll('#category-grid .hidden');
  const btn = document.getElementById('toggle-category-btn');

  if (hiddenCards.length > 0) {
    hiddenCards.forEach(card => card.classList.remove('hidden'));
    btn.textContent = '−閉じる';
  } else {
    const allCards = document.querySelectorAll('#category-grid .filter-card');
    allCards.forEach((card, index) => {
      if (index >= 6) card.classList.add('hidden');
    });
    btn.textContent = '＋もっと見る';
  }
}

function toggleArea() {
  const hiddenCards = document.querySelectorAll('#area-grid .hidden');
  const btn = document.getElementById('toggle-area-btn');

  if (hiddenCards.length > 0) {
    hiddenCards.forEach(card => card.classList.remove('hidden'));
    btn.textContent = '−閉じる';
  } else {
    const allCards = document.querySelectorAll('#area-grid .filter-card');
    allCards.forEach((card, index) => {
      if (index >= 6) card.classList.add('hidden');
    });
    btn.textContent = '＋もっと見る';
  }
}
