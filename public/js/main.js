// Fashion Paris — JS partagé (navigation, modales, confirm, abandon, mdp)
document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('burger');
  const navLinks = document.getElementById('nav-links');

  if (burger && navLinks) {
    const labelOuvert = burger.dataset.labelOpen || 'Ouvrir le menu de navigation';
    const labelFerme = burger.dataset.labelClose || 'Fermer le menu de navigation';

    function menuOuvert() {
      return navLinks.classList.contains('is-open');
    }

    function setMenuState(ouvert) {
      navLinks.classList.toggle('is-open', ouvert);
      burger.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
      burger.setAttribute('aria-label', ouvert ? labelFerme : labelOuvert);
    }

    burger.addEventListener('click', () => setMenuState(!menuOuvert()));

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menuOuvert()) {
        setMenuState(false);
        burger.focus();
      }
    });

    navLinks.querySelectorAll('a').forEach((lien) => {
      lien.addEventListener('click', () => setMenuState(false));
    });

    document.addEventListener('click', (e) => {
      if (menuOuvert() && !burger.contains(e.target) && !navLinks.contains(e.target)) {
        setMenuState(false);
      }
    });
  }

  function formulaireSale() {
    return !!document.querySelector(
      'form[data-abandon-guard][data-abandon-dirty="1"], form[data-abandon-watch][data-abandon-dirty="1"]'
    );
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-navigate]');
    if (!btn) return;
    if (formulaireSale()) return;
    const url = btn.getAttribute('data-navigate');
    if (url) window.location.href = url;
  });

  function fermerModaleConfirm() {
    const overlay = document.getElementById('modal-confirm-site');
    if (overlay) overlay.remove();
    document.body.classList.remove('modal-open');
  }

  function ouvrirModaleConfirm(message, onConfirm, options) {
    fermerModaleConfirm();
    options = options || {};
    const isBlock = !!options.block;
    const titre = options.titre || (isBlock ? 'Information' : 'Confirmation');

    const overlay = document.createElement('div');
    overlay.id = 'modal-confirm-site';
    overlay.className = 'modal-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.innerHTML =
      '<div class="modal-dialog">' +
      '<h2 class="modal-title">' + titre + '</h2>' +
      '<p class="modal-message"></p>' +
      (options.alert ? '<p class="modal-alert"></p>' : '') +
      '<div class="modal-actions"></div>' +
      '</div>';
    overlay.querySelector('.modal-message').textContent = message || '';
    if (options.alert) {
      overlay.querySelector('.modal-alert').textContent = options.alert;
    }

    const actions = overlay.querySelector('.modal-actions');
    if (isBlock) {
      const ok = document.createElement('button');
      ok.type = 'button';
      ok.className = 'btn btn-primary';
      ok.textContent = 'OK';
      ok.addEventListener('click', () => {
        fermerModaleConfirm();
        if (typeof onConfirm === 'function') onConfirm();
      });
      actions.appendChild(ok);
    } else {
      const cancel = document.createElement('button');
      cancel.type = 'button';
      cancel.className = 'btn btn-ghost';
      cancel.textContent = 'Annuler';
      cancel.addEventListener('click', fermerModaleConfirm);
      const ok = document.createElement('button');
      ok.type = 'button';
      ok.className = 'btn btn-primary';
      ok.textContent = 'Confirmer';
      ok.addEventListener('click', () => {
        fermerModaleConfirm();
        if (typeof onConfirm === 'function') onConfirm();
      });
      actions.appendChild(cancel);
      actions.appendChild(ok);
    }

    document.body.appendChild(overlay);
    document.body.classList.add('modal-open');

    const onEsc = (e) => {
      if (e.key === 'Escape') {
        fermerModaleConfirm();
        document.removeEventListener('keydown', onEsc);
      }
    };
    document.addEventListener('keydown', onEsc);
  }

  window.FashionModal = { ouvrir: ouvrirModaleConfirm, fermer: fermerModaleConfirm };

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (form.dataset.confirmReady === '1') {
        delete form.dataset.confirmReady;
        return;
      }
      e.preventDefault();
      const msg = form.getAttribute('data-confirm') || 'Confirmer cette action ?';
      const alertMsg = form.getAttribute('data-confirm-alert') || '';
      const block = form.getAttribute('data-confirm-block') === '1';
      ouvrirModaleConfirm(msg, () => {
        form.dataset.confirmReady = '1';
        if (typeof form.requestSubmit === 'function') form.requestSubmit();
        else form.submit();
      }, {
        block,
        alert: alertMsg || undefined,
        titre: block ? 'Information' : 'Confirmation',
      });
    });
  });

  const iconOeilOuvert = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
  const iconOeilFerme = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

  document.querySelectorAll('input[type="password"]').forEach((input) => {
    if (input.closest('.password-field')) return;
    const wrap = document.createElement('div');
    wrap.className = 'password-field';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'password-toggle';
    btn.innerHTML = iconOeilOuvert;
    btn.setAttribute('aria-label', 'Afficher le mot de passe');
    btn.setAttribute('aria-pressed', 'false');
    wrap.appendChild(btn);

    btn.addEventListener('click', () => {
      const visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      btn.innerHTML = visible ? iconOeilOuvert : iconOeilFerme;
      btn.setAttribute('aria-pressed', visible ? 'false' : 'true');
      btn.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
    });
  });

  document.querySelectorAll('form[data-abandon-guard], form[data-abandon-watch]').forEach((form) => {
    const markDirty = () => {
      form.dataset.abandonDirty = '1';
    };
    form.addEventListener('input', markDirty);
    form.addEventListener('change', markDirty);
    form.addEventListener('submit', () => {
      delete form.dataset.abandonDirty;
    });
  });

  window.addEventListener('beforeunload', (e) => {
    if (formulaireSale()) {
      e.preventDefault();
      e.returnValue = '';
    }
  });
});
