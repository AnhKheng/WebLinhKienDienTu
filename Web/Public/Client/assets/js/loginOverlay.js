function openLoginForm() {
  document.getElementById('loginOverlay').classList.add('active');
}

function closeLoginForm() {
  document.getElementById('loginOverlay').classList.remove('active');
}

// Đóng overlay khi click ra ngoài form
window.addEventListener('click', (e) => {
  const overlay = document.getElementById('loginOverlay');
  if (e.target === overlay) {
    overlay.classList.remove('active');
  }
});
