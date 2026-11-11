// Fashion Paris — JS vanilla (menu burger + navigation boutons data-navigate)
document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('burger');
  const nav = document.getElementById('nav-links');

  if (burger && nav) {
    burger.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      const label = open
        ? burger.dataset.labelClose
        : burger.dataset.labelOpen;
      if (label) {
        burger.setAttribute('aria-label', label);
      }
    });
  }

  document.querySelectorAll('[data-navigate]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const url = btn.getAttribute('data-navigate');
      if (url) {
        window.location.href = url;
      }
    });
  });
});
