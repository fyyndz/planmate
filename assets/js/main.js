/**
 * PlanMate JavaScript Helpers
 */

document.addEventListener('DOMContentLoaded', () => {
  // Auto dismiss alert messages after 5 seconds
  const alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });

  // Confirm delete dialog helper
  const deleteLinks = document.querySelectorAll('.confirm-delete');
  deleteLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      const message = link.getAttribute('data-confirm') || 'Apakah Anda yakin ingin menghapus data ini?';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });
});
