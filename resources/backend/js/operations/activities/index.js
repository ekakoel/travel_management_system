document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-activity-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const activityName = button.dataset.activityDelete || 'this activity';
      const confirmed = window.confirm(`Are you sure you want to remove ${activityName} from the activity list?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
