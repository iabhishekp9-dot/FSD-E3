function updateCartCount() {
    fetch('cart.php?action=get_count')
        .then(r => r.json())
        .then(data => {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = data.count;
            });
        });
}

function addToCart(productId) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add&product_id=' + productId
    }).then(() => {
        updateCartCount();
        showNotification('Added to cart!');
    });
}

function showNotification(msg) {
    let notif = document.createElement('div');
    notif.textContent = msg;
    notif.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:white;padding:12px 20px;border-radius:8px;z-index:9999;box-shadow:0 2px 10px rgba(0,0,0,0.2);';
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 2000);
}

document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            addToCart(btn.dataset.id);
        });
    });
});