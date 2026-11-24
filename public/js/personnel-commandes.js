// Fashion Paris — recherche live commandes par nom client (espace personnel / admin)
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('search-client');
  const clearBtn = document.getElementById('search-client-clear');
  const tbody = document.getElementById('commandes-tbody');
  const cards = document.getElementById('commandes-cards');
  const emptyMsg = document.getElementById('commandes-search-empty');
  const tableWrap = document.querySelector('#commandes-listing .commandes-table-wrap');

  if (!input || (!tbody && !cards)) return;

  function normaliser(texte) {
    return String(texte || '')
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .trim();
  }

  function majBoutonEffacer() {
    if (!clearBtn) return;
    clearBtn.hidden = input.value.trim() === '';
  }

  function filtrerElements(elements) {
    const q = normaliser(input.value);
    let visibles = 0;
    elements.forEach((el) => {
      const client = normaliser(el.getAttribute('data-client') || '');
      const match = q === '' || client.includes(q);
      el.hidden = !match;
      if (match) visibles += 1;
    });
    return visibles;
  }

  function filtrer() {
    let visibles = 0;
    if (tbody) {
      visibles = filtrerElements(tbody.querySelectorAll('tr'));
    } else if (cards) {
      visibles = filtrerElements(cards.querySelectorAll('[data-client]'));
    }
    if (tbody && cards) {
      filtrerElements(cards.querySelectorAll('[data-client]'));
    }

    const aucun = visibles === 0;
    if (tableWrap) tableWrap.hidden = aucun;
    if (cards) cards.hidden = aucun;
    if (emptyMsg) emptyMsg.hidden = !aucun;
    majBoutonEffacer();
  }

  input.addEventListener('input', filtrer);
  clearBtn?.addEventListener('click', () => {
    input.value = '';
    filtrer();
    input.focus();
  });

  majBoutonEffacer();
});
