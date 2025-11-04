document.getElementById('btn-add-to-cart').addEventListener('click', function (e) {
    e.preventDefault();

    const productId = this.getAttribute('data-id'); 

    fetch('../../API/client/Cart/add.php?id=' + encodeURIComponent(productId))
        .then(res => res.json())
        .then(data => {
            alert(data.message);
        })
        .catch(err => {
            console.error('Lỗi fetch:', err);
            alert('Có lỗi xảy ra khi thêm sản phẩm!');
        });
});
