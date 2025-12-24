<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Bella Pizza</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-cream">
    <nav class="navbar">
        <div class="logo">BELLA <span>PIZZA</span></div>
        <a href="index.php" class="btn-secondary">← Back to Menu</a>
    </nav>

    <div class="container checkout-container">
        <div class="checkout-grid">
            <div class="card">
                <h3>Order Summary</h3>
                <hr>
                <div id="summary-items">
                    </div>
                <div class="total-section">
                    <h4>Total: $<span id="summary-total">0.00</span></h4>
                </div>
            </div>

            <div class="card">
                <h3>Delivery Details</h3>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <p class="error">You must <a href="login.php">Login</a> to place an order.</p>
                <?php else: ?>
                    <form id="checkout-form">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo $_SESSION['user_name']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Delivery Address</label>
                            <textarea id="address" required placeholder="Enter full address..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="phone" required placeholder="e.g. +1 234 567 890">
                        </div>
                        <button type="submit" class="btn-primary w-100">Place My Order</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Load summary from the session cart
        async function loadSummary() {
            const res = await fetch('api/cart_handler.php?action=get');
            const data = await res.json();
            
            if (data.items.length === 0) {
                window.location.href = 'index.php'; // Redirect if cart is empty
            }

            document.getElementById('summary-items').innerHTML = data.items.map(i => `
                <div class="summary-item">
                    <span>${i.name} (${i.size})</span>
                    <span>$${i.price}</span>
                </div>
            `).join('');
            document.getElementById('summary-total').innerText = data.total.toFixed(2);
        }

        // Handle Form Submission
        document.getElementById('checkout-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const orderData = {
                address: document.getElementById('address').value,
                phone: document.getElementById('phone').value
            };

            const res = await fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });

            const result = await res.json();
            if(result.status === 'success') {
                window.location.href = `success.php?id=${result.order_id}`;
            } else {
                alert(result.message);
            }
        });

        loadSummary();
    </script>
</body>
</html>