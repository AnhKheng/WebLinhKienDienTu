function openLoginForm() {
  document.getElementById('loginOverlay').classList.add('active');
}

function closeLoginForm() {
  document.getElementById('loginOverlay').classList.remove('active');
}

function openRegisterForm() {
  document.getElementById('registerOverlay').classList.add('active');
}

function closeRegisterForm() {
  document.getElementById('registerOverlay').classList.remove('active');
}

function openPassWordForm() {
  document.getElementById('passwordOverlay').classList.add('active');
}

function closePassWordForm() {
  document.getElementById('passwordOverlay').classList.remove('active');
}

// Đóng overlay khi click ra ngoài form
window.addEventListener('click', (e) => {
  ['loginOverlay', 'registerOverlay', 'passwordOverlay'].forEach(id => {
    const overlay = document.getElementById(id);
    if (e.target === overlay) overlay.classList.remove('active');
  });
});
