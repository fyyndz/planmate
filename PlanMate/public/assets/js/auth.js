/**
 * PlanMate Authentication Guard & State Checker
 */

const Auth = {
  // Check if user is logged in for protected pages
  requireAuth() {
    const currentUser = Store.getCurrentUser();
    if (!currentUser) {
      window.location.href = 'login.php';
    } else {
      this.updateNavbarUser(currentUser);
    }
  },

  // Redirect logged in user away from Login / Register pages
  requireGuest() {
    const currentUser = Store.getCurrentUser();
    if (currentUser) {
      window.location.href = 'dashboard.php';
    }
  },

  // Dynamic user details in header
  updateNavbarUser(user) {
    document.addEventListener('DOMContentLoaded', () => {
      const nameElem = document.getElementById('nav-user-name');
      const avatarElem = document.getElementById('nav-user-avatar');
      if (nameElem) nameElem.textContent = user.name || user.username;
      if (avatarElem) avatarElem.textContent = (user.name || user.username).charAt(0).toUpperCase();
    });
  },

  logout() {
    Store.logout();
  }
};
