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

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initBackendRichText(document);
  });
} else {
  initBackendRichText(document);
}

document.addEventListener('shown.bs.modal', (event) => {
  initBackendRichText(event.target);
});
