// Fashion Paris — JS partagé (navigation, modales Fashion, confirm, abandon, mdp)
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
    const okLabel = options.okLabel || (isBlock ? 'OK' : 'Confirmer');
    const cancelLabel = options.cancelLabel || 'Annuler';

    const overlay = document.createElement('div');
    overlay.id = 'modal-confirm-site';
    overlay.className = 'modal-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-labelledby', 'modal-confirm-titre');
    overlay.innerHTML =
      '<div class="modal-dialog">' +
      '<h2 class="modal-title" id="modal-confirm-titre"></h2>' +
      '<p class="modal-message"></p>' +
      (options.alert ? '<p class="modal-alert"></p>' : '') +
      '<div class="modal-actions"></div>' +
      '</div>';
    overlay.querySelector('.modal-title').textContent = titre;
    overlay.querySelector('.modal-message').textContent = message || '';
    if (options.alert) {
      overlay.querySelector('.modal-alert').textContent = options.alert;
    }

    const actions = overlay.querySelector('.modal-actions');
    let focusEl = null;

    if (isBlock) {
      const ok = document.createElement('button');
      ok.type = 'button';
      ok.className = 'btn btn-primary';
      ok.textContent = okLabel;
      ok.addEventListener('click', () => {
        fermerModaleConfirm();
        if (typeof onConfirm === 'function') onConfirm();
      });
      actions.appendChild(ok);
      focusEl = ok;
    } else {
      const cancel = document.createElement('button');
      cancel.type = 'button';
      cancel.className = 'btn btn-secondary';
      cancel.textContent = cancelLabel;
      cancel.addEventListener('click', fermerModaleConfirm);

      const ok = document.createElement('button');
      ok.type = 'button';
      ok.className = 'btn btn-primary';
      ok.textContent = okLabel;
      ok.addEventListener('click', () => {
        fermerModaleConfirm();
        if (typeof onConfirm === 'function') onConfirm();
      });

      actions.appendChild(cancel);
      actions.appendChild(ok);
      focusEl = options.focusCancel ? cancel : ok;
    }

    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) fermerModaleConfirm();
    });

    document.body.appendChild(overlay);
    document.body.classList.add('modal-open');
    if (focusEl) focusEl.focus();

    const onEsc = (e) => {
      if (e.key === 'Escape') {
        fermerModaleConfirm();
        document.removeEventListener('keydown', onEsc);
      }
    };
    document.addEventListener('keydown', onEsc);
  }

  window.FashionModal = { ouvrir: ouvrirModaleConfirm, fermer: fermerModaleConfirm };

  // Boutons ViewHelper::btnNav (data-navigate) — navigation hors garde d’abandon
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-navigate]');
    if (!btn) return;
    // Si un formulaire sale existe, la garde d’abandon (capture) gère déjà le clic
    if (document.querySelector(
      'form[data-abandon-guard][data-abandon-dirty="1"], form[data-abandon-watch][data-abandon-dirty="1"]'
    )) {
      return;
    }
    const url = btn.getAttribute('data-navigate');
    if (url) window.location.href = url;
  });

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
        titre: form.getAttribute('data-confirm-titre') || (block ? 'Information' : 'Confirmation'),
        okLabel: form.getAttribute('data-confirm-ok') || (block ? 'OK' : 'Confirmer'),
        cancelLabel: form.getAttribute('data-confirm-cancel') || 'Annuler',
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

  // ——— Garde d’abandon : modale Fashion pour la navigation interne ———
  (function initAbandonGuard() {
    const formGuards = Array.from(document.querySelectorAll('form[data-abandon-guard], form[data-abandon-watch]'));
    if (!formGuards.length) return;

    function snapshotForm(form) {
      const data = new FormData(form);
      const parts = [];
      data.forEach((value, key) => {
        parts.push(key + '=' + String(value));
      });
      return parts.sort().join('&');
    }

    function markDirty(form) {
      form.dataset.abandonDirty = '1';
    }

    function markClean(form) {
      delete form.dataset.abandonDirty;
    }

    function isFormDirty(form) {
      return form.dataset.abandonDirty === '1';
    }

    function anyDirty() {
      return formGuards.some(isFormDirty);
    }

    function dirtySource() {
      return formGuards.find(isFormDirty) || null;
    }

    formGuards.forEach((form) => {
      form.dataset.abandonSnapshot = snapshotForm(form);
      const onChange = () => {
        if (snapshotForm(form) !== form.dataset.abandonSnapshot) {
          markDirty(form);
        } else {
          markClean(form);
        }
      };
      form.addEventListener('input', onChange);
      form.addEventListener('change', onChange);
      form.addEventListener('submit', () => markClean(form));
    });

    function optionsForLeave() {
      const source = dirtySource();
      return {
        titre: (source && source.getAttribute('data-abandon-titre')) || 'Quitter cette page ?',
        okLabel: (source && source.getAttribute('data-abandon-ok')) || 'Quitter',
        cancelLabel: (source && source.getAttribute('data-abandon-cancel')) || 'Rester',
        focusCancel: true,
      };
    }

    function messageForLeave() {
      const source = dirtySource();
      return (source && source.getAttribute('data-abandon-message'))
        || 'Des modifications non enregistrées seront perdues. Voulez-vous vraiment quitter ?';
    }

    function confirmerDepart(url) {
      ouvrirModaleConfirm(messageForLeave(), () => {
        formGuards.forEach(markClean);
        window.location.href = url;
      }, optionsForLeave());
    }

    // F5 / fermeture d’onglet : alerte navigateur (non stylable, contrainte navigateur)
    window.addEventListener('beforeunload', (e) => {
      if (!anyDirty()) return;
      e.preventDefault();
      e.returnValue = '';
    });

    document.addEventListener('click', (e) => {
      if (!anyDirty()) return;

      const navBtn = e.target.closest('[data-navigate]');
      if (navBtn) {
        const url = navBtn.getAttribute('data-navigate') || '';
        if (!url) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        confirmerDepart(url);
        return;
      }

      const link = e.target.closest('a[href]');
      if (!link) return;

      const href = link.getAttribute('href') || '';
      if (
        href === ''
        || href.startsWith('#')
        || href.startsWith('javascript:')
        || link.target === '_blank'
        || link.hasAttribute('download')
      ) {
        return;
      }

      // Même URL (ancre seule)
      try {
        const dest = new URL(link.href, window.location.href);
        if (dest.pathname === window.location.pathname && dest.search === window.location.search) {
          return;
        }
      } catch (err) {
        return;
      }

      e.preventDefault();
      confirmerDepart(link.href);
    }, true);
  })();
});
