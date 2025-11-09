document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formEditEmployee");
    const MaNVInput = document.getElementById("MaNV");
    const oldPw = document.getElementById("oldPw");
    const newPw = document.getElementById("newPw");
    const confirmPw = document.getElementById("confirmPw");

    const notifyOverlay = document.getElementById("notifyOverlay");
    const notifyMessage = document.getElementById("notifyMessage");
    const closeNotify = document.getElementById("closeNotify");

    // Load MaNV từ URL
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    if (!id) { alert("Không tìm thấy mã nhân viên!"); return; }
    MaNVInput.value = id;

    // Popup
    closeNotify.addEventListener("click", () => { notifyOverlay.style.display = "none"; });
    function showNotify(message) {
        notifyMessage.textContent = message;
        notifyOverlay.style.display = "flex";
    }

    // Toggle show/hide mật khẩu
    document.querySelectorAll(".togglePw").forEach(icon => {
        icon.addEventListener("click", () => {
            const input = icon.previousElementSibling;
            const eye = icon.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                eye.classList.remove("fa-eye");
                eye.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                eye.classList.remove("fa-eye-slash");
                eye.classList.add("fa-eye");
            }
        });
    });

    // Submit form
    form.addEventListener("submit", e => {
        e.preventDefault();
        const oldPassword = oldPw.value.trim();
        const newPassword = newPw.value.trim();
        const confirmPassword = confirmPw.value.trim();

        if (newPassword !== confirmPassword) {
            showNotify("Mật khẩu mới và xác nhận mật khẩu không khớp!");
            return;
        }
        if (newPassword.length < 6) {
            showNotify("Mật khẩu mới phải ít nhất 6 ký tự!");
            return;
        }

        fetch("../../API/admin/auth_api.php?action=change_password", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                MaNV: MaNVInput.value,
                oldPassword: oldPassword,
                newPassword: newPassword
            })
        })
        .then(res => res.json())
        .then(data => {
            showNotify(data.message);
            if (data.status === "success") {
                oldPw.value = "";
                newPw.value = "";
                confirmPw.value = "";
            }
        })
        .catch(err => { console.error(err); showNotify("Có lỗi xảy ra!"); });
    });
});
