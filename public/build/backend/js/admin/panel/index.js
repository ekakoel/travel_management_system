/******/ (() => { // webpackBootstrap
/*!***************************************************!*\
  !*** ./resources/backend/js/admin/panel/index.js ***!
  \***************************************************/
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
document.addEventListener('DOMContentLoaded', function () {
  var page = document.querySelector('[data-admin-panel]');
  if (!page) {
    return;
  }
  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      var submitter = event.submitter && event.submitter.getAttribute('data-confirm') ? event.submitter : form;
      var message = submitter.getAttribute('data-confirm');
      if (!message) {
        return;
      }
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });
  var analytics = document.querySelector('[data-traffic-analytics]');
  if (!analytics) {
    return;
  }
  var periods = {};
  try {
    periods = JSON.parse(analytics.getAttribute('data-traffic-analytics') || '{}');
  } catch (error) {
    return;
  }
  var numberFormat = new Intl.NumberFormat('en-US');
  var summaryTarget = analytics.querySelector('[data-analytics-summary]');
  var chartBars = analytics.querySelector('[data-analytics-chart-bars]');
  var chartLabels = analytics.querySelector('[data-analytics-chart-labels]');
  var chartLabel = analytics.querySelector('[data-analytics-chart-label]');
  var chartTotal = analytics.querySelector('[data-analytics-chart-total]');
  var chartRange = analytics.querySelector('[data-analytics-chart-range]');
  var insightTitle = analytics.querySelector('[data-analytics-insight-title]');
  var insightCopy = analytics.querySelector('[data-analytics-insight-copy]');
  var clearChildren = function clearChildren(element) {
    while (element && element.firstChild) {
      element.removeChild(element.firstChild);
    }
  };
  var createElement = function createElement(tag, className, text) {
    var element = document.createElement(tag);
    if (className) {
      element.className = className;
    }
    if (text !== undefined) {
      element.textContent = text;
    }
    return element;
  };
  var renderSummary = function renderSummary(periodData) {
    clearChildren(summaryTarget);
    (periodData.summary || []).forEach(function (item) {
      var card = createElement('article', "admin-analytics-kpi admin-analytics-kpi--".concat(item.tone || 'neutral'));
      card.appendChild(createElement('span', '', item.label));
      card.appendChild(createElement('strong', '', numberFormat.format(item.value)));
      card.appendChild(createElement('small', '', item.meta));
      summaryTarget.appendChild(card);
    });
  };
  var renderChart = function renderChart(periodData) {
    var series = periodData.series || {};
    var labels = series.labels || [];
    var values = series.values || [];
    var max = Math.max(1, Number(series.max || 1));
    clearChildren(chartBars);
    clearChildren(chartLabels);
    labels.forEach(function (label, index) {
      var value = Number(values[index] || 0);
      var bar = createElement('button', 'admin-analytics-chart__bar');
      var height = Math.max(4, Math.round(value / max * 100));
      bar.type = 'button';
      bar.style.setProperty('--bar-height', "".concat(height, "%"));
      bar.setAttribute('aria-label', "".concat(label, ": ").concat(numberFormat.format(value), " visits"));
      bar.title = "".concat(label, ": ").concat(numberFormat.format(value), " visits");
      var valueLabel = createElement('span', 'admin-analytics-chart__value', numberFormat.format(value));
      bar.appendChild(valueLabel);
      chartBars.appendChild(bar);
    });
    if (labels.length > 0) {
      chartLabels.appendChild(createElement('small', '', labels[0]));
      chartLabels.appendChild(createElement('small', '', labels[Math.floor(labels.length / 2)] || labels[0]));
      chartLabels.appendChild(createElement('small', '', labels[labels.length - 1]));
    }
    chartLabel.textContent = "".concat(periodData.label, " Traffic");
    chartTotal.textContent = "".concat(numberFormat.format(series.total || 0), " visits");
    chartRange.textContent = periodData.range || '';
  };
  var renderBreakdown = function renderBreakdown(periodData) {
    Object.entries(periodData.breakdowns || {}).forEach(function (_ref) {
      var _ref2 = _slicedToArray(_ref, 2),
        key = _ref2[0],
        rows = _ref2[1];
      var container = analytics.querySelector("[data-analytics-breakdown=\"".concat(key, "\"]"));
      var heading = container ? container.querySelector('h3') : null;
      if (!container || !heading) {
        return;
      }
      Array.from(container.children).forEach(function (child) {
        if (child !== heading) {
          child.remove();
        }
      });
      var total = Math.max(1, Number((periodData.series || {}).total || 0));
      if (!rows || rows.length === 0) {
        container.appendChild(createElement('p', 'admin-panel-empty', 'No tracked data for this period.'));
        return;
      }
      rows.forEach(function (row) {
        var count = Number(row.total || 0);
        var percent = Math.round(count / total * 100);
        var item = createElement('div', 'admin-analytics-row');
        item.appendChild(createElement('span', '', row.label || 'Unknown'));
        item.appendChild(createElement('strong', '', numberFormat.format(count)));
        var progress = createElement('i');
        progress.style.setProperty('--progress-width', "".concat(Math.max(2, percent), "%"));
        item.appendChild(progress);
        container.appendChild(item);
      });
    });
  };
  var renderInsight = function renderInsight(periodData) {
    var summary = periodData.summary || [];
    var visits = summary[0] || {
      value: 0,
      meta: 'No traffic'
    };
    var average = summary[2] || {
      value: 0,
      meta: 'No average'
    };
    var peak = summary[3] || {
      value: 0,
      meta: '-'
    };
    insightTitle.textContent = "".concat(periodData.label, " visibility");
    insightCopy.textContent = "".concat(numberFormat.format(visits.value), " visits in this range. Peak traffic reached ").concat(numberFormat.format(peak.value), " visits on ").concat(peak.meta, ". Average volume is ").concat(numberFormat.format(average.value), " ").concat(average.meta.toLowerCase(), ". ").concat(visits.meta, ".");
  };
  var setActivePeriod = function setActivePeriod(period) {
    var periodData = periods[period] || periods.day || Object.values(periods)[0];
    if (!periodData) {
      return;
    }
    analytics.querySelectorAll('[data-analytics-period]').forEach(function (button) {
      var isActive = button.getAttribute('data-analytics-period') === period;
      button.classList.toggle('is-active', isActive);
      button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
    renderSummary(periodData);
    renderChart(periodData);
    renderBreakdown(periodData);
    renderInsight(periodData);
  };
  analytics.querySelectorAll('[data-analytics-period]').forEach(function (button) {
    button.addEventListener('click', function () {
      setActivePeriod(button.getAttribute('data-analytics-period'));
    });
  });
  setActivePeriod('day');
});
/******/ })()
;