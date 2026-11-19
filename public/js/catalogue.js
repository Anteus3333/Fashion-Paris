// Fashion Paris — filtres catalogue AJAX (recherche fluide + filtres)
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('filter-form');
  const list = document.getElementById('catalogue-list');
  const searchInput = document.getElementById('q');
  const applyBtn = document.getElementById('apply-filters');
  const resetBtn = document.getElementById('reset-filters');
  if (!form || !list) return;

  const baseUrl = form.dataset.baseUrl || '';
  let debounceTimer = null;
  let requeteEnCours = null;

  function majLibelleCompte(n) {
    const parent = document.querySelector('.catalogue-filters__count');
    if (!parent) return;
    parent.innerHTML = '<span id="catalogue-count">' + n + '</span> article' + (n > 1 ? 's' : '');
  }

  function appliquerFiltres() {
    if (debounceTimer) {
      clearTimeout(debounceTimer);
      debounceTimer = null;
    }
    if (requeteEnCours) {
      requeteEnCours.abort();
    }
    const controller = new AbortController();
    requeteEnCours = controller;

    const params = new URLSearchParams(new FormData(form));
    fetch(baseUrl + '/api/catalogue?' + params.toString(), { signal: controller.signal })
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) return;
        list.innerHTML = data.html;
        majLibelleCompte(data.count);
        lierResetVide();
      })
      .catch((err) => {
        if (err.name !== 'AbortError') {
          console.error('Erreur filtres catalogue:', err);
        }
      })
      .finally(() => {
        if (requeteEnCours === controller) {
          requeteEnCours = null;
        }
      });
  }

  function rechercherAvecDebounce() {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(appliquerFiltres, 320);
  }

  function reinitialiser() {
    form.reset();
    window.location.href = baseUrl + '/catalogue';
  }

  function lierResetVide() {
    const btn = document.getElementById('reset-filters-empty');
    if (btn) btn.addEventListener('click', reinitialiser);
  }

  // Entrée / bouton Filtrer → recherche immédiate (tous les critères)
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    appliquerFiltres();
  });

  if (applyBtn) {
    applyBtn.addEventListener('click', (e) => {
      e.preventDefault();
      appliquerFiltres();
    });
  }
  if (resetBtn) resetBtn.addEventListener('click', reinitialiser);

  // Champ recherche : saisie live (debounce) + Entrée immédiate
  if (searchInput) {
    searchInput.addEventListener('input', rechercherAvecDebounce);
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        appliquerFiltres();
      }
    });
    // Croix native type=search → rafraîchir la liste
    searchInput.addEventListener('search', appliquerFiltres);
  }

  // Catégorie : applique tout de suite (recherche + budget inclus)
  form.querySelectorAll('select').forEach((sel) => {
    sel.addEventListener('change', appliquerFiltres);
  });

  // Budget : Entrée dans Min/Max = filtrer
  form.querySelectorAll('input[type="number"]').forEach((input) => {
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        appliquerFiltres();
      }
    });
  });

  lierResetVide();
});
