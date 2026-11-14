// Fashion Paris — filtres catalogue AJAX (parité menus.js traiteur)
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('filter-form');
  const list = document.getElementById('catalogue-list');
  const countEl = document.getElementById('catalogue-count');
  const applyBtn = document.getElementById('apply-filters');
  const resetBtn = document.getElementById('reset-filters');
  if (!form || !list) return;

  const baseUrl = form.dataset.baseUrl || '';

  function appliquerFiltres() {
    const params = new URLSearchParams(new FormData(form));
    fetch(baseUrl + '/api/catalogue?' + params.toString())
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) return;
        list.innerHTML = data.html;
        if (countEl) countEl.textContent = String(data.count);
        lierResetVide();
      })
      .catch((err) => console.error('Erreur filtres catalogue:', err));
  }

  function reinitialiser() {
    form.reset();
    window.location.href = baseUrl + '/catalogue';
  }

  function lierResetVide() {
    const btn = document.getElementById('reset-filters-empty');
    if (btn) btn.addEventListener('click', reinitialiser);
  }

  if (applyBtn) applyBtn.addEventListener('click', appliquerFiltres);
  if (resetBtn) resetBtn.addEventListener('click', reinitialiser);
  lierResetVide();

  form.querySelectorAll('select').forEach((sel) => {
    sel.addEventListener('change', appliquerFiltres);
  });
});
