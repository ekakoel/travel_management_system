/******/ (() => { // webpackBootstrap
/*!*****************************************************!*\
  !*** ./resources/backend/js/admin/reviews/index.js ***!
  \*****************************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-review-tab]').forEach(function (tab) {
    tab.addEventListener('click', function (event) {
      event.preventDefault();
      var targetId = tab.getAttribute('href');
      if (!targetId) return;
      document.querySelectorAll('[data-review-tab]').forEach(function (item) {
        item.classList.toggle('is-active', item === tab);
      });
      document.querySelectorAll('[data-review-tab-pane]').forEach(function (pane) {
        pane.classList.toggle('is-active', '#' + pane.id === targetId);
      });
      var url = new URL(window.location.href);
      url.searchParams.set('tab', targetId.replace('#', ''));
      window.history.replaceState(null, '', url.toString());
    });
  });
  document.querySelectorAll('[data-tour-review-action]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      var button = form.querySelector('button[type="submit"]');
      var message = button ? button.getAttribute('data-confirm') : null;
      if (message && !window.confirm(message)) {
        event.preventDefault();
        return;
      }
    });
  });
  document.querySelectorAll('[data-review-group-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      var targetId = button.getAttribute('aria-controls');
      var target = targetId ? document.getElementById(targetId) : null;
      var expanded = button.getAttribute('aria-expanded') === 'true';
      if (!target) return;
      target.hidden = expanded;
      button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      button.classList.toggle('is-open', !expanded);
      var icon = button.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-chevron-down', expanded);
        icon.classList.toggle('fa-chevron-up', !expanded);
      }
    });
  });
  function clearPrintState() {
    document.body.classList.remove('is-tour-review-printing');
    document.querySelectorAll('[data-review-print-sheet]').forEach(function (sheet) {
      sheet.classList.remove('is-printing');
    });
    var printRoot = document.querySelector('[data-review-print-root]');
    if (printRoot) {
      printRoot.remove();
    }
    document.querySelectorAll('[data-review-print-frame]').forEach(function (frame) {
      frame.remove();
    });
  }
  function buildPrintDocument(sheetHtml) {
    return '<!doctype html><html><head><meta charset="utf-8">' + '<title>Tour Review Brief</title>' + '<style>' + '@page{size:A4 portrait;margin:6mm;}' + '*{box-sizing:border-box;}' + 'html,body{background:#fff;color:#111827;font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;width:100%;}' + '.tour-review-print-sheet{display:block!important;width:100%;max-width:none;margin:0;padding:0;font-size:12px;line-height:1.4;}' + '.tour-review-print-header{align-items:start;border-bottom:2px solid #111827;display:grid;gap:12px;grid-template-columns:minmax(0,1fr) 104px;padding-bottom:8px;}' + '.tour-review-print-header span,.tour-review-print-meta dt,.tour-review-print-team dt{color:#0f766e;display:block;font-size:9px;font-weight:800;text-transform:uppercase;}' + '.tour-review-print-header h1{color:#111827;font-size:26px;font-weight:800;line-height:1;margin:3px 0 4px;}' + '.tour-review-print-header p{color:#475569;font-size:11px;margin:0;}' + '.tour-review-print-header div:last-child{text-align:right;}' + '.tour-review-print-header div:last-child strong{color:#047857;display:block;font-size:32px;line-height:1;}' + '.tour-review-print-meta{border-bottom:1px solid #d1d5db;display:grid;gap:10px;grid-template-columns:repeat(5,minmax(0,1fr));margin:9px 0 0;padding:0 0 9px;}' + '.tour-review-print-meta div,.tour-review-print-team dl div{min-width:0;}' + '.tour-review-print-meta dd,.tour-review-print-team dd{color:#111827;font-size:12px;font-weight:800;margin:2px 0 0;overflow-wrap:anywhere;}' + '.tour-review-print-section{border-bottom:1px solid #d1d5db;padding:10px 0;}' + '.tour-review-print-section h2{color:#111827;font-size:15px;font-weight:800;margin:0 0 7px;}' + '.tour-review-print-ratings{display:grid;gap:6px;}' + '.tour-review-print-ratings div{align-items:start;display:grid;gap:12px;grid-template-columns:138px minmax(0,1fr);}' + '.tour-review-print-ratings strong{color:#0f766e;font-size:11px;text-transform:uppercase;}' + '.tour-review-print-ratings p{display:flex;flex-wrap:wrap;gap:5px 14px;margin:0;}' + '.tour-review-print-ratings span{color:#111827;font-size:11px;white-space:nowrap;}' + '.tour-review-print-team dl{display:grid;gap:10px;grid-template-columns:repeat(2,minmax(0,1fr));margin:0;}' + '.tour-review-print-notes{display:grid;gap:7px;}' + '.tour-review-print-notes article{border-top:1px solid #e5e7eb;padding-top:7px;}' + '.tour-review-print-notes article:first-child{border-top:0;padding-top:0;}' + '.tour-review-print-notes strong{color:#111827;display:block;font-size:12px;margin-bottom:3px;}' + '.tour-review-print-notes p{color:#374151;font-size:11px;line-height:1.35;margin:0;}' + '</style></head><body>' + sheetHtml + '</body></html>';
  }
  document.querySelectorAll('[data-review-print-trigger]').forEach(function (button) {
    button.addEventListener('click', function () {
      var targetId = button.getAttribute('aria-controls');
      var target = targetId ? document.getElementById(targetId) : null;
      if (!target) return;
      clearPrintState();
      var printSheet = target.cloneNode(true);
      var frame = document.createElement('iframe');
      printSheet.classList.add('is-printing');
      frame.setAttribute('data-review-print-frame', '');
      frame.style.border = '0';
      frame.style.height = '1px';
      frame.style.opacity = '0';
      frame.style.position = 'fixed';
      frame.style.right = '0';
      frame.style.bottom = '0';
      frame.style.width = '1px';
      document.body.appendChild(frame);
      var frameWindow = frame.contentWindow;
      var frameDocument = frameWindow.document;
      frameDocument.open();
      frameDocument.write(buildPrintDocument(printSheet.outerHTML));
      frameDocument.close();
      frameWindow.onafterprint = function () {
        window.setTimeout(clearPrintState, 100);
      };
      frameWindow.focus();
      window.setTimeout(function () {
        frameWindow.print();
      }, 250);
    });
  });
  window.addEventListener('afterprint', clearPrintState);
  document.querySelectorAll('[data-uppercase-input]').forEach(function (input) {
    input.addEventListener('input', function () {
      input.value = input.value.toUpperCase();
    });
  });
  var filterInputs = document.querySelectorAll('[data-review-link-filter]');
  function filterReviewLinks() {
    var agent = (document.querySelector('[data-review-link-filter="agent"]') || {}).value || '';
    var booking = (document.querySelector('[data-review-link-filter="booking"]') || {}).value || '';
    agent = agent.toLowerCase();
    booking = booking.toLowerCase();
    document.querySelectorAll('[data-review-link-card]').forEach(function (card) {
      var matchesAgent = !agent || (card.getAttribute('data-agent') || '').indexOf(agent) !== -1;
      var matchesBooking = !booking || (card.getAttribute('data-booking') || '').indexOf(booking) !== -1;
      card.hidden = !(matchesAgent && matchesBooking);
    });
  }
  filterInputs.forEach(function (input) {
    input.addEventListener('input', filterReviewLinks);
  });
  document.querySelectorAll('[data-copy-text]').forEach(function (button) {
    button.addEventListener('click', function () {
      var text = button.getAttribute('data-copy-text') || '';
      function markCopied() {
        var originalText = button.innerHTML;
        button.innerHTML = '<i class="fa fa-check"></i> Copied';
        window.setTimeout(function () {
          button.innerHTML = originalText;
        }, 1600);
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(markCopied);
        return;
      }
      var textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.setAttribute('readonly', 'readonly');
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
      markCopied();
    });
  });
});
/******/ })()
;