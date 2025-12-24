document.addEventListener('DOMContentLoaded', () => {
    loadMenu();
    updateCartCount();
});

async function loadMenu() {
    const res = await fetch('api/get_menu.php');
    const pizzas = await res.json();
    const grid = document.getElementById('pizza-grid');
    
    grid.innerHTML = pizzas.map(p => `
        <div class="pizza-card">
            <div class="pizza-info">
                <h3>${p.name}</h3>
                <p>${p.description}</p>
                <div class="price">$${p.base_price}</div>
                <select id="size-${p.id}">
                    <option value="Small">Small</option>
                    <option value="Medium">Medium (+$3)</option>
                    <option value="Large">Large (+$6)</option>
                </select>
                <button class="btn-primary" onclick="addToCart(${p.id}, '${p.name}', ${p.base_price})">
                    Add to Cart
                </button>
            </div>
        </div>
    `).join('');
}

async function addToCart(id, name, price) {
    const size = document.getElementById(`size-${id}`).value;
    let finalPrice = parseFloat(price);
    if(size === 'Medium') finalPrice += 3;
    if(size === 'Large') finalPrice += 6;

    const response = await fetch('api/cart_handler.php?action=add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, name, size, price: finalPrice })
    });
    
    const result = await response.json();
    if(result.status === 'success') {
        document.getElementById('cart-count').innerText = result.cart_count;
        toggleCart(); // Show cart after adding
        fetchCartItems();
    }
}

function toggleCart() {
    document.getElementById('cart-drawer').classList.toggle('active');
    fetchCartItems();
}

async function fetchCartItems() {
    const res = await fetch('api/cart_handler.php?action=get');
    const data = await res.json();
    const container = document.getElementById('cart-items');
    
    container.innerHTML = data.items.map(i => `
        <div class="cart-item">
            <span>${i.name} (${i.size})</span>
            <span>$${i.price}</span>
        </div>
    `).join('');
    document.getElementById('cart-total').innerText = data.total.toFixed(2);
}

// In app.js
async function applyCoupon() {
    const code = document.getElementById('coupon-input').value;
    const res = await fetch(`api/apply_coupon.php?code=${code}`);
    const data = await res.json();

    if(data.valid) {
        let currentTotal = parseFloat(document.getElementById('cart-total').innerText);
        let discount = currentTotal * (data.discount / 100);
        document.getElementById('cart-total').innerText = (currentTotal - discount).toFixed(2);
        alert(`Coupon Applied! You saved $${discount.toFixed(2)}`);
    } else {
        alert("Invalid Coupon Code");
    }
}