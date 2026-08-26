/**
 * Backend shared entry point.
 *
 * Keep this bundle isolated from the frontend/Vue app. The legacy backend
 * layout already loads jQuery and panel plugins globally; importing
 * resources/js/app here would replace window.jQuery and detach plugins such
 * as Select2, DataTables, Datepicker, Slick, FullCalendar, and custom scrollbars.
 */

const RICHTEXT_SELECTOR = [
  'body.sidebar-light .main-container textarea:not([data-backend-richtext="false"])',
  'body.sidebar-light .modal textarea:not([data-backend-richtext="false"])',
  'textarea.textarea_editor',
  'textarea[data-backend-richtext="true"]',
].join(', ');

const BACKEND_DATE_PICKER_SELECTOR = '[data-backend-picker="date"]';
const BACKEND_STATUS_TOGGLE_SELECTOR = '[data-backend-status-toggle]';
const BACKEND_MONEY_INPUT_SELECTOR = 'input:not([type="hidden"]):not([type="submit"]):not([type="button"])';
const BACKEND_MONEY_UNIT_BY_NAME = Object.freeze({
  additional_guest_rate: 'USD',
  additional_service_price: 'USD',
  agent_rate: 'USD',
  arrangement_price: 'USD',
  basic_price: 'USD',
  contract_rate: 'IDR',
  contract_rate_idr: 'IDR',
  fee: 'USD',
  holiday_price: 'USD',
  kick_back: 'USD',
  markup: 'USD',
  price: 'USD',
  public_rate: 'USD',
  publish_rate: 'USD',
  rate: 'USD',
  tax: '%',
  week_day_price: 'USD',
});
const BACKEND_REQUIRED_CONTROL_SELECTOR = [
  'input[required]:not([type="hidden"])',
  'select[required]',
  'textarea[required]',
].join(', ');
const BACKEND_MUTATION_FORM_SELECTOR = 'form:not([data-backend-submit-guard="false"])';

function showBackendModal(modal) {
  if (!(modal instanceof Element)) {
    return false;
  }

  const bootstrapModal = window.bootstrap?.Modal;

  if (typeof bootstrapModal?.getOrCreateInstance === 'function') {
    bootstrapModal.getOrCreateInstance(modal).show();
    return true;
  }

  if (window.jQuery?.fn?.modal) {
    window.jQuery(modal).modal('show');
    return true;
  }

  return false;
}

function closeBackendModal(modal) {
  if (!(modal instanceof Element)) {
    return false;
  }

  const bootstrapModal = window.bootstrap?.Modal;
  const bootstrapInstance = bootstrapModal?.getInstance?.(modal);

  if (bootstrapInstance) {
    bootstrapInstance.hide();
    return true;
  }

  if (window.jQuery?.fn?.modal) {
    window.jQuery(modal).modal('hide');
    return true;
  }

  modal.classList.remove('show');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');

  if (!document.querySelector('.modal.show')) {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
  }

  return true;
}

function handleBackendModalClose(event) {
  if (!(event.target instanceof Element)) {
    return;
  }

  const closeControl = event.target.closest('[data-backend-modal-close]');

  if (!closeControl) {
    return;
  }

  event.preventDefault();
  closeBackendModal(closeControl.closest('.modal'));
}

function backendFormSubmitControls(form) {
  return Array.from(document.querySelectorAll([
    'button:not([type])',
    'button[type="submit"]',
    'input[type="submit"]',
    'input[type="image"]',
  ].join(', ')))
    .filter((control) => control.form === form);
}

function setBackendActionLoading(control, loading = true) {
  if (!control) {
    return;
  }

  control.classList.toggle('is-submitting', loading);

  if (!loading) {
    control.removeAttribute('aria-disabled');
    control.removeAttribute('aria-busy');

    const spinner = control.querySelector
      ? control.querySelector('[data-backend-action-spinner]')
      : null;

    if (spinner) {
      spinner.remove();
    }

    control.style.removeProperty('--backend-action-spinner-color');

    if (control.tagName === 'INPUT' && control.dataset.backendOriginalValue !== undefined) {
      control.value = control.dataset.backendOriginalValue;
      delete control.dataset.backendOriginalValue;
    }

    return;
  }

  control.setAttribute('aria-disabled', 'true');
  control.setAttribute('aria-busy', 'true');

  if (window.getComputedStyle) {
    control.style.setProperty(
      '--backend-action-spinner-color',
      window.getComputedStyle(control).color
    );
  }

  if (control.tagName === 'INPUT') {
    if (control.dataset.backendOriginalValue === undefined) {
      control.dataset.backendOriginalValue = control.value;
    }
    control.value = control.dataset.loadingLabel || 'Processing...';
    return;
  }

  if (!control.querySelector('[data-backend-action-spinner]')) {
    const spinner = document.createElement('span');
    spinner.className = 'backend-action-spinner';
    spinner.setAttribute('aria-hidden', 'true');
    spinner.setAttribute('data-backend-action-spinner', 'true');
    control.append(spinner);
  }
}

function resetBackendSubmittingState(form) {
  if (!form) {
    return;
  }

  if (form.backendSubmitDisableTimer) {
    window.clearTimeout(form.backendSubmitDisableTimer);
    form.backendSubmitDisableTimer = null;
  }

  if (form.backendSubmitValidationTimer) {
    window.clearTimeout(form.backendSubmitValidationTimer);
    form.backendSubmitValidationTimer = null;
  }

  delete form.dataset.backendSubmitting;
  delete form.dataset.backendSubmitPending;
  form.backendActiveSubmitter = null;
  form.removeAttribute('aria-busy');

  backendFormSubmitControls(form).forEach((control) => {
    control.disabled = control.dataset.backendOriginalDisabled === 'true';
    delete control.dataset.backendOriginalDisabled;
    setBackendActionLoading(control, false);
  });
}

function showBackendFormLoading(form, submitter) {
  form.setAttribute('aria-busy', 'true');
  form.backendActiveSubmitter = submitter || form.backendActiveSubmitter || null;

  backendFormSubmitControls(form).forEach((control) => {
    if (control.dataset.backendOriginalDisabled === undefined) {
      control.dataset.backendOriginalDisabled = control.disabled ? 'true' : 'false';
    }
    control.setAttribute('aria-disabled', 'true');
  });

  setBackendActionLoading(form.backendActiveSubmitter, true);
}

function primeBackendSubmitting(form, submitter) {
  form.dataset.backendSubmitPending = 'true';
  showBackendFormLoading(form, submitter);

  form.backendSubmitValidationTimer = window.setTimeout(() => {
    if (form.dataset.backendSubmitting !== 'true') {
      resetBackendSubmittingState(form);
    }
  }, 0);
}

function commitBackendSubmitting(form, submitter) {
  delete form.dataset.backendSubmitPending;
  form.dataset.backendSubmitting = 'true';
  showBackendFormLoading(form, submitter);

  // Defer the native disabled state until the browser has captured the
  // successful submitter, while the dataset guard blocks repeated submits now.
  form.backendSubmitDisableTimer = window.setTimeout(() => {
    backendFormSubmitControls(form).forEach((control) => {
      control.disabled = true;
    });
  }, 0);
}

function bindBackendSubmitGuard(form) {
  const method = (form.getAttribute('method') || 'get').toLowerCase();

  if (form.dataset.backendSubmitGuardReady === 'true' || method === 'get' || method === 'dialog') {
    return;
  }

  form.dataset.backendSubmitGuardReady = 'true';
  form.addEventListener('submit', (event) => {
    if (form.dataset.backendSubmitting === 'true') {
      event.preventDefault();
      event.stopImmediatePropagation();
      return;
    }

    if (event.defaultPrevented) {
      resetBackendSubmittingState(form);
      return;
    }

    commitBackendSubmitting(form, event.submitter || form.backendActiveSubmitter);

    window.queueMicrotask(() => {
      if (event.defaultPrevented) {
        resetBackendSubmittingState(form);
      }
    });
  });
}

function backendSubmitControlFromEvent(event) {
  if (!(event.target instanceof Element)) {
    return null;
  }

  return event.target.closest([
    'button:not([type])',
    'button[type="submit"]',
    'input[type="submit"]',
    'input[type="image"]',
  ].join(', '));
}

function handleBackendActionClick(event) {
  const submitter = backendSubmitControlFromEvent(event);

  if (submitter && submitter.form) {
    const form = submitter.form;
    const method = (form.getAttribute('method') || 'get').toLowerCase();

    if (method !== 'get' && method !== 'dialog' && form.matches(BACKEND_MUTATION_FORM_SELECTOR)) {
      if (form.dataset.backendSubmitting === 'true' || form.dataset.backendSubmitPending === 'true') {
        event.preventDefault();
        event.stopImmediatePropagation();
        return;
      }

      if (!event.defaultPrevented) {
        primeBackendSubmitting(form, submitter);
        window.queueMicrotask(() => {
          if (event.defaultPrevented) {
            resetBackendSubmittingState(form);
          }
        });
      }

      return;
    }
  }

  const standaloneAction = event.target instanceof Element
    ? event.target.closest('[data-backend-action-loading]')
    : null;

  if (!standaloneAction || event.defaultPrevented) {
    return;
  }

  if (standaloneAction.classList.contains('is-submitting')) {
    event.preventDefault();
    event.stopImmediatePropagation();
    return;
  }

  setBackendActionLoading(standaloneAction, true);
  window.queueMicrotask(() => {
    if (event.defaultPrevented) {
      setBackendActionLoading(standaloneAction, false);
    }
  });
}

function backendCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function updateBackendStatusBadge(badge, status, tone) {
  if (!badge) {
    return;
  }

  Array.from(badge.classList)
    .filter((className) => className.startsWith('backend-status-badge--'))
    .forEach((className) => badge.classList.remove(className));

  badge.classList.add(`backend-status-badge--${tone || status.toLowerCase()}`);
  badge.textContent = status;
}

function updateBackendStatusToggle(toggle, status, nextStatus, tone) {
  const isActive = status === 'Active';
  const label = toggle.querySelector('[data-backend-status-toggle-label]');
  const activeLabel = toggle.dataset.backendStatusLabelActive || 'Active';
  const draftLabel = toggle.dataset.backendStatusLabelDraft || 'Draft';

  toggle.dataset.backendStatusCurrent = status;
  toggle.dataset.backendStatusNext = nextStatus || (isActive ? 'Draft' : 'Active');
  toggle.classList.toggle('is-active', isActive);
  toggle.setAttribute('aria-pressed', isActive ? 'true' : 'false');
  toggle.setAttribute('title', isActive ? activeLabel : draftLabel);

  if (tone) {
    toggle.dataset.backendStatusTone = tone;
  }

  if (label) {
    label.textContent = isActive ? activeLabel : draftLabel;
  }
}

async function handleBackendStatusToggleClick(event) {
  const toggle = event.target instanceof Element
    ? event.target.closest(BACKEND_STATUS_TOGGLE_SELECTOR)
    : null;

  if (!toggle) {
    return;
  }

  event.preventDefault();

  if (toggle.classList.contains('is-submitting')) {
    return;
  }

  const url = toggle.dataset.backendStatusUrl;
  const nextStatus = toggle.dataset.backendStatusNext;

  if (!url || !nextStatus) {
    return;
  }

  setBackendActionLoading(toggle, true);

  try {
    const response = await window.fetch(url, {
      method: 'PATCH',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': backendCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ status: nextStatus }),
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
      throw new Error(payload.message || 'Status could not be updated.');
    }

    updateBackendStatusToggle(toggle, payload.status, payload.next_status, payload.tone);

    const badgeTarget = toggle.dataset.backendStatusBadgeTarget;

    if (badgeTarget) {
      document.querySelectorAll(badgeTarget).forEach((badge) => {
        updateBackendStatusBadge(
          badge,
          payload.status,
          payload.tone
        );
      });
    }
  } catch (error) {
    window.alert(error.message || 'Status could not be updated.');
  } finally {
    setBackendActionLoading(toggle, false);
  }
}

function initBackendSubmitGuards(root = document) {
  const forms = [];

  if (root instanceof HTMLFormElement && root.matches(BACKEND_MUTATION_FORM_SELECTOR)) {
    forms.push(root);
  }

  if (typeof root.querySelectorAll === 'function') {
    forms.push(...root.querySelectorAll(BACKEND_MUTATION_FORM_SELECTOR));
  }

  forms.forEach(bindBackendSubmitGuard);
}

function backendControlLabel(control) {
  if (control.labels && control.labels.length) {
    return control.labels[0];
  }

  const field = control.closest('.backend-form-field, .form-group');

  return field ? field.querySelector('label') : null;
}

function ensureBackendRequiredMarker(control) {
  const label = backendControlLabel(control);

  if (!label) {
    return;
  }

  const existingMarker = Array.from(label.querySelectorAll('span, b')).find(
    (candidate) => candidate.textContent.trim() === '*'
  );

  if (existingMarker) {
    existingMarker.classList.add('backend-required-marker');
    existingMarker.setAttribute('aria-hidden', 'true');
    return;
  }

  const marker = document.createElement('span');
  marker.className = 'backend-required-marker';
  marker.setAttribute('aria-hidden', 'true');
  marker.setAttribute('data-backend-required-generated', 'true');
  marker.textContent = '*';
  label.append(document.createTextNode(' '), marker);
}

function initBackendRequiredMarkers(root = document) {
  const controls = [];

  if (root instanceof Element && root.matches(BACKEND_REQUIRED_CONTROL_SELECTOR)) {
    controls.push(root);
  }

  if (typeof root.querySelectorAll === 'function') {
    controls.push(...root.querySelectorAll(BACKEND_REQUIRED_CONTROL_SELECTOR));
  }

  controls.forEach(ensureBackendRequiredMarker);
}

function backendMoneyFieldName(control) {
  return (control.getAttribute('name') || '')
    .replace(/\[[^\]]*\]/g, '')
    .trim();
}

function backendMoneyUnitMap(control) {
  return (control.dataset.backendMoneyUnitMap || '')
    .split('|')
    .reduce((units, entry) => {
      const separator = entry.indexOf(':');

      if (separator > 0) {
        units[entry.slice(0, separator)] = entry.slice(separator + 1);
      }

      return units;
    }, {});
}

function backendMoneyUnit(control) {
  if (control.dataset.backendMoneyUnitSource) {
    const scope = control.closest('form') || document;
    const source = scope.querySelector(control.dataset.backendMoneyUnitSource);
    const mappedUnit = source ? backendMoneyUnitMap(control)[source.value] : null;

    if (mappedUnit) {
      return mappedUnit;
    }
  }

  return control.dataset.backendMoneyUnit
    || BACKEND_MONEY_UNIT_BY_NAME[backendMoneyFieldName(control)]
    || '';
}

function backendMoneyHelpText(unit) {
  const template = document.body?.dataset.backendMoneyHint || ':unit';

  return template.replace(':unit', unit);
}

function normalizeBackendMoneyValue(value, unit, displayValue = false) {
  let normalized = String(value ?? '')
    .trim()
    .replace(/\s+/g, '')
    .replace(/[^\d.,-]/g, '');

  if (!normalized) {
    return '';
  }

  if (displayValue && unit === 'IDR') {
    normalized = normalized.replace(/\./g, '').replace(',', '.');
  } else {
    normalized = normalized.replace(/,/g, '');
  }

  const negative = normalized.startsWith('-');
  const unsigned = normalized.replace(/-/g, '');
  const decimalPosition = unsigned.indexOf('.');
  let integer = decimalPosition >= 0 ? unsigned.slice(0, decimalPosition) : unsigned;
  const fraction = decimalPosition >= 0
    ? unsigned.slice(decimalPosition + 1).replace(/\./g, '')
    : null;

  integer = integer.replace(/^0+(?=\d)/, '') || '0';

  return `${negative ? '-' : ''}${integer}${fraction !== null ? `.${fraction}` : ''}`;
}

function formatBackendMoneyValue(rawValue, unit) {
  if (rawValue === '') {
    return '';
  }

  const negative = rawValue.startsWith('-');
  const unsigned = negative ? rawValue.slice(1) : rawValue;
  const [integer = '0', fraction] = unsigned.split('.', 2);
  const groupingSeparator = unit === 'IDR' ? '.' : ',';
  const decimalSeparator = unit === 'IDR' ? ',' : '.';
  const groupedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, groupingSeparator);

  return `${negative ? '-' : ''}${groupedInteger}${fraction !== undefined ? `${decimalSeparator}${fraction}` : ''}`;
}

function backendMoneyRawValue(control, unit = backendMoneyUnit(control)) {
  const isFormatted = control.dataset.backendMoneyFormatted === 'true';

  return normalizeBackendMoneyValue(control.value, unit, isFormatted);
}

function setBackendMoneyCaret(control, digitOffset, wasAtEnd) {
  if (wasAtEnd) {
    control.setSelectionRange(control.value.length, control.value.length);
    return;
  }

  if (digitOffset <= 0) {
    control.setSelectionRange(0, 0);
    return;
  }

  let digitsSeen = 0;
  let caret = control.value.length;

  for (let index = 0; index < control.value.length; index += 1) {
    if (/\d/.test(control.value[index])) {
      digitsSeen += 1;
    }

    if (digitsSeen === digitOffset) {
      caret = index + 1;
      break;
    }
  }

  control.setSelectionRange(caret, caret);
}

function formatBackendMoneyInput(control, unit = backendMoneyUnit(control), preserveCaret = false) {
  const caretPosition = preserveCaret ? control.selectionStart : null;
  const wasAtEnd = preserveCaret && caretPosition === control.value.length;
  const digitOffset = preserveCaret
    ? (control.value.slice(0, caretPosition ?? 0).match(/\d/g) || []).length
    : 0;
  const rawValue = backendMoneyRawValue(control, unit);

  control.dataset.backendMoneyRawValue = rawValue;
  control.dataset.backendMoneyCurrentUnit = unit;
  control.dataset.backendMoneyFormatted = 'true';
  control.setAttribute('type', 'text');
  control.setAttribute('inputmode', 'decimal');
  control.value = formatBackendMoneyValue(rawValue, unit);

  if (preserveCaret && document.activeElement === control) {
    setBackendMoneyCaret(control, digitOffset, wasAtEnd);
  }
}

function restoreBackendMoneyInput(control) {
  const unit = control.dataset.backendMoneyCurrentUnit || backendMoneyUnit(control);
  const rawValue = backendMoneyRawValue(control, unit).replace(/\.$/, '');

  control.dataset.backendMoneyRawValue = rawValue;
  control.dataset.backendMoneyFormatted = 'false';
  control.setAttribute('type', control.dataset.backendMoneyOriginalType || 'number');
  control.value = rawValue;
}

function bindBackendMoneyInput(control) {
  if (control.dataset.backendMoneyFormattingReady === 'true') {
    return;
  }

  control.dataset.backendMoneyFormattingReady = 'true';
  control.dataset.backendMoneyOriginalType = control.getAttribute('type') || 'text';

  control.addEventListener('input', () => {
    formatBackendMoneyInput(control, backendMoneyUnit(control), true);
  });

  control.addEventListener('blur', () => {
    formatBackendMoneyInput(control);
  });
}

function updateBackendMoneyInput(control) {
  const unit = backendMoneyUnit(control);

  if (!unit) {
    return;
  }

  const previousUnit = control.dataset.backendMoneyCurrentUnit;
  if (
    previousUnit
    && previousUnit !== unit
    && control.dataset.backendMoneyFormatted === 'true'
  ) {
    control.value = backendMoneyRawValue(control, previousUnit);
    control.dataset.backendMoneyFormatted = 'false';
  }

  let shell = control.closest('[data-backend-money-shell]');

  if (!shell) {
    shell = document.createElement('div');
    shell.className = 'backend-money-control';
    shell.dataset.backendMoneyShell = 'true';
    control.parentNode.insertBefore(shell, control);
    shell.append(control);
  }

  let unitLabel = shell.querySelector('[data-backend-money-unit-label]');

  if (!unitLabel) {
    unitLabel = document.createElement('span');
    unitLabel.className = 'backend-money-control__unit';
    unitLabel.dataset.backendMoneyUnitLabel = 'true';
    shell.prepend(unitLabel);
  }

  unitLabel.textContent = unit;
  unitLabel.setAttribute('aria-label', document.body?.dataset.backendMoneyLabel || unit);
  control.dataset.backendMoneyReady = 'true';

  const legacyWrapper = shell.parentElement;
  if (legacyWrapper?.classList.contains('btn-icon')) {
    Array.from(legacyWrapper.children).forEach((child) => {
      if (child !== shell && child.tagName === 'SPAN') {
        child.hidden = true;
      }
    });
  }

  let help = shell.nextElementSibling;
  if (!help?.matches('[data-backend-money-help]')) {
    help = document.createElement('small');
    help.className = 'backend-form-help backend-money-help';
    help.dataset.backendMoneyHelp = 'true';
    shell.after(help);
  }

  if (!help.id) {
    help.id = `backendMoneyHelp${Math.random().toString(36).slice(2, 10)}`;
  }

  help.textContent = backendMoneyHelpText(unit);
  const describedBy = new Set((control.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
  describedBy.add(help.id);
  control.setAttribute('aria-describedby', Array.from(describedBy).join(' '));

  bindBackendMoneyInput(control);
  formatBackendMoneyInput(control, unit, document.activeElement === control);
}

function initBackendMoneyInputs(root = document) {
  const controls = [];

  if (root instanceof HTMLInputElement && root.matches(BACKEND_MONEY_INPUT_SELECTOR)) {
    controls.push(root);
  }

  if (typeof root.querySelectorAll === 'function') {
    controls.push(...root.querySelectorAll(BACKEND_MONEY_INPUT_SELECTOR));
  }

  controls.forEach(updateBackendMoneyInput);
}

function initBackendDatePickers(root = document) {
  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.datepicker) {
    return;
  }

  window.jQuery(root)
    .find(BACKEND_DATE_PICKER_SELECTOR)
    .addBack(BACKEND_DATE_PICKER_SELECTOR)
    .each(function initDatePicker() {
      const input = window.jQuery(this);

      if (input.data('backend-date-picker-ready') || input.data('datepicker')) {
        return;
      }

      input
        .data('backend-date-picker-ready', true)
        .datepicker({
          language: 'en',
          autoClose: true,
          dateFormat: input.data('backend-picker-format') || 'yyyy-mm-dd',
        });
    });
}

function initBackendRichText(root = document) {
  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.summernote) {
    return;
  }

  window.jQuery(root).find(RICHTEXT_SELECTOR).addBack(RICHTEXT_SELECTOR).each(function initEditor() {
    const textarea = window.jQuery(this);

    if (textarea.data('backend-richtext-ready') || textarea.next('.note-editor').length) {
      return;
    }

    textarea
      .addClass('backend-richtext-control')
      .attr('data-backend-richtext', 'true')
      .data('backend-richtext-ready', true)
      .summernote({
        height: Number(textarea.data('backend-richtext-height')) || 180,
        toolbar: [
          ['style', ['bold', 'italic', 'underline', 'clear']],
          ['font', ['fontsize']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link']],
          ['view', ['codeview']],
        ],
        fontSizes: ['10', '11', '12', '14', '16', '18', '20', '24', '28', '32'],
        dialogsInBody: true,
      });
  });
}

function setBackendRichTextValue(element, value = '') {
  if (!element) {
    return;
  }

  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.summernote) {
    element.value = value;
    return;
  }

  const textarea = window.jQuery(element);

  if (textarea.next('.note-editor').length) {
    textarea.summernote('code', value);
    return;
  }

  element.value = value;
}

window.initBackendRichText = initBackendRichText;
window.setBackendRichTextValue = setBackendRichTextValue;
window.initBackendDatePickers = initBackendDatePickers;
window.initBackendRequiredMarkers = initBackendRequiredMarkers;
window.initBackendMoneyInputs = initBackendMoneyInputs;
window.setBackendActionLoading = setBackendActionLoading;
window.showBackendModal = showBackendModal;
window.closeBackendModal = closeBackendModal;

function initBackendSharedForms(root = document) {
  initBackendSubmitGuards(root);
  initBackendRequiredMarkers(root);
  initBackendMoneyInputs(root);
  initBackendRichText(root);
  initBackendDatePickers(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initBackendSharedForms(document);
  });
} else {
  initBackendSharedForms(document);
}

document.addEventListener('shown.bs.modal', (event) => {
  initBackendSharedForms(event.target);
});

document.addEventListener('click', handleBackendActionClick);
document.addEventListener('click', handleBackendStatusToggleClick);
document.addEventListener('click', handleBackendModalClose);
document.addEventListener('change', (event) => {
  if (event.target instanceof Element && event.target.matches('[data-backend-money-unit-source-target]')) {
    initBackendMoneyInputs(event.target.closest('form') || document);
  }
});

window.addEventListener('pageshow', () => {
  document.querySelectorAll('[data-backend-submitting="true"]').forEach(resetBackendSubmittingState);
  initBackendMoneyInputs(document);
});

document.addEventListener('submit', (event) => {
  if (!(event.target instanceof HTMLFormElement)) {
    return;
  }

  const controls = Array.from(event.target.querySelectorAll(BACKEND_MONEY_INPUT_SELECTOR))
    .filter((control) => backendMoneyUnit(control));

  controls.forEach(restoreBackendMoneyInput);

  if (!event.target.checkValidity()) {
    event.preventDefault();
  }

  queueMicrotask(() => {
    if (event.defaultPrevented) {
      controls.forEach((control) => formatBackendMoneyInput(control));
    }
  });
}, true);

document.addEventListener('formdata', (event) => {
  if (!(event.target instanceof HTMLFormElement)) {
    return;
  }

  const controlsByName = Array.from(event.target.querySelectorAll(BACKEND_MONEY_INPUT_SELECTOR))
    .filter((control) => control.name && backendMoneyUnit(control))
    .reduce((groups, control) => {
      groups[control.name] = [...(groups[control.name] || []), control];
      return groups;
    }, {});

  Object.entries(controlsByName).forEach(([name, controls]) => {
    event.formData.delete(name);
    controls.forEach((control) => {
      event.formData.append(name, backendMoneyRawValue(control));
    });
  });
});

const backendRequiredObserver = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.type === 'attributes') {
      initBackendRequiredMarkers(mutation.target);
      return;
    }

    mutation.addedNodes.forEach((node) => {
      if (node instanceof Element) {
        initBackendSubmitGuards(node);
        initBackendRequiredMarkers(node);
        initBackendMoneyInputs(node);
      }
    });
  });
});

backendRequiredObserver.observe(document.documentElement, {
  attributeFilter: ['required', 'data-backend-money-unit'],
  attributes: true,
  childList: true,
  subtree: true,
});
