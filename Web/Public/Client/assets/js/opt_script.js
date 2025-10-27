
function sendOTP() {
  const email = document.querySelector('input[name="username"]').value;
  if (!email) {
    alert("Vui lòng nhập email trước khi nhận OTP!");
    return;
  }

  const button = document.getElementById('btn-GetOtp');
  button.disabled = true;
  button.textContent = "Đang gửi...";

  fetch("send_otp.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "email=" + encodeURIComponent(email)
  })
  .then(res => res.text())
  .then(text => {
    alert(text);
    button.textContent = "Gửi lại OTP";
    setTimeout(() => button.disabled = false, 30000); // chờ 30 giây
  })
  .catch(() => {
    alert("Lỗi khi gửi OTP!");
    button.disabled = false;
    button.textContent = "Gửi lại OTP";
  });
}
