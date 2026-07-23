document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-activity-gallery-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const activityName = button.dataset.activityGalleryDelete || 'this activity';
      const confirmed = window.confirm(`Delete this gallery image from ${activityName}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('[data-activity-file-input]').forEach((input) => {
    const target = document.querySelector(input.dataset.activityFileInputTarget || '');

    if (!target) {
      return;
    }

    input.addEventListener('change', () => {
      const files = Array.from(input.files || []);

      target.textContent = files.length > 0
        ? `${files.length} file${files.length === 1 ? '' : 's'} selected`
        : target.dataset.activityFileInputDefault || 'No file selected';
    });
  });
});
