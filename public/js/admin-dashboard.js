(function () {
  'use strict';

  function init() {
    if (typeof Chart === 'undefined') {
      window.setTimeout(init, 50);
      return;
    }

    var dataEl = document.getElementById('admin-stats-data');
    if (!dataEl) return;

    var data;
    try {
      data = JSON.parse(dataEl.textContent);
    } catch (e) {
      return;
    }

    if (!data.labels || !data.labels.length) return;

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var palette = ['#8b2942', '#1a1520', '#5c5566', '#6a1e32', '#2c3340', '#6b3e2e', '#c4a4b0', '#4a5568'];

    function buildOptions(titleY) {
      return {
        responsive: true,
        maintainAspectRatio: false,
        animation: reducedMotion ? false : undefined,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                var val = ctx.parsed.y;
                if (titleY === '€') {
                  return val.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
                }
                return val + ' commande(s)';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                if (titleY === '€') {
                  return value.toLocaleString('fr-FR') + ' €';
                }
                return value;
              }
            }
          }
        }
      };
    }

    function makeDataset(values) {
      return {
        data: values,
        backgroundColor: data.labels.map(function (_, i) {
          return palette[i % palette.length];
        }),
        borderRadius: 4
      };
    }

    var ctxCommandes = document.getElementById('chart-commandes');
    if (ctxCommandes) {
      new Chart(ctxCommandes, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [Object.assign({ label: 'Commandes' }, makeDataset(data.commandes))]
        },
        options: buildOptions('')
      });
    }

    var ctxCa = document.getElementById('chart-ca');
    if (ctxCa) {
      new Chart(ctxCa, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [Object.assign({ label: "Chiffre d'affaires" }, makeDataset(data.ca))]
        },
        options: buildOptions('€')
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
