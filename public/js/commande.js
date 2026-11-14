// Fashion Paris — checkout live (frais / franco) + anti double-submit
document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.commande-page');
  const form = document.getElementById('commande-form');
  const recap = document.getElementById('recap-content');
  if (!page || !form || !recap) return;

  const baseUrl = page.dataset.baseUrl || '';
  const sousTotal = parseFloat(recap.dataset.sousTotal || '0') || 0;
  const btn = document.getElementById('btn-confirmer-commande');

  function formatEuro(n) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(n);
  }

  function majRecap() {
    fetch(baseUrl + '/api/calcul-frais?sous_total=' + encodeURIComponent(String(sousTotal)))
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) return;
        const fraisEl = document.getElementById('recap-frais');
        const totalEl = document.getElementById('recap-total');
        const hint = document.getElementById('recap-franco-hint');
        const reste = document.getElementById('recap-reste-franco');
        if (fraisEl) fraisEl.textContent = data.franco ? 'Offerte' : formatEuro(data.frais);
        if (totalEl) totalEl.textContent = formatEuro(data.total);
        if (hint) {
          if (data.franco || data.frais <= 0) {
            hint.hidden = true;
          } else {
            hint.hidden = false;
            if (reste) reste.textContent = formatEuro(data.reste_franco);
          }
        }
      })
      .catch((err) => console.error('Erreur calcul frais:', err));
  }

  majRecap();

  form.addEventListener('submit', (e) => {
    if (form.dataset.submitting === '1') {
      e.preventDefault();
      return;
    }
    form.dataset.submitting = '1';
    if (btn) {
      btn.dataset.labelOrigine = btn.textContent;
      btn.textContent = 'Envoi…';
      btn.disabled = true;
      btn.classList.add('is-submitting');
    }
  });
});
