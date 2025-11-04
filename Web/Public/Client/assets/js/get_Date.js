function getToday() {
    let today = new Date();
    let tie = new Time();
    let formatted = today.toLocaleDateString('vi-VN');
    return formatted;
}