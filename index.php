<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bella Pizza | Premium Italian Pizza</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="logo">BELLA <span>PIZZA</span></div>
        <ul class="nav-links">
            <li><a href="#menu">Menu</a></li>
            <li><a href="#" onclick="toggleCart()">Cart (<span id="cart-count">0</span>)</a></li>
            <li id="auth-links"><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Handcrafted <br><span class="highlight">Pizza Perfection</span></h1>
            <p>From our stone oven straight to your doorstep.</p>
            <a href="#menu" class="btn-primary">Order Now</a>
        </div>
    </header>

    <main id="menu" class="container">
        <h2 class="section-title">Our Signature Pizzas</h2>
        <div id="pizza-grid" class="grid">
            </div>
    </main>

    <div id="cart-drawer" class="cart-drawer">
        <div class="cart-header">
            <h3>Your Selection</h3>
            <button onclick="toggleCart()">×</button>
        </div>
        <div id="cart-items"></div>
        <div class="cart-footer">
            <div class="total-row">Total: $<span id="cart-total">0.00</span></div>
            <button class="btn-checkout" onclick="goToCheckout()">Proceed to Checkout</button>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>