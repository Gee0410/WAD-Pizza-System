<?php
require_once 'db.php';
require_once 'auth.php';

// Protect the page - redirect to login if not authenticated
requireLogin();

// Fetch all available pizzas from the database
$query = "SELECT * FROM pizzas WHERE status = 'available' ORDER BY category DESC, name ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Menu | Pizza Delight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">PIZZA <span>DELIGHT</span></h1>
            <div class="nav-links">
                <a href="index.php" class="active">Menu</a>
                <a href="order_history.php">History</a>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> 
                    Cart <span id="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>
                </a>
                <a href="profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                  <i class="fas fa-user-circle"></i> Profile
                </a>
                <span class="user-greet">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <main class="menu-container">
        <header class="menu-header">
            <h2>Our Gourmet Menu</h2>
            <p>Hand-tossed dough, premium ingredients, baked to perfection.</p>
        </header>

        <div class="menu-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="menuSearch" placeholder="Search for your favorite pizza..." onkeyup="filterMenu()">
            </div>
            
            <div class="filter-tabs">
                <button class="tab-btn active" onclick="filterCategory('all', this)">All</button>
                <button class="tab-btn" onclick="filterCategory('Classic', this)">Classic</button>
                <button class="tab-btn" onclick="filterCategory('Premium', this)">Premium</button>
                <button class="tab-btn" onclick="filterCategory('Vegetarian', this)">Vegetarian</button>
            </div>
        </div>

        <div class="menu-grid" id="pizzaGrid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($pizza = $result->fetch_assoc()): ?>
                <div class="pizza-card" 
                     data-category="<?php echo htmlspecialchars($pizza['category']); ?>" 
                     data-name="<?php echo strtolower(htmlspecialchars($pizza['name'])); ?>">
                    
                    <div class="pizza-image">
                        <img src="images/<?php echo $pizza['image']; ?>" alt="<?php echo htmlspecialchars($pizza['name']); ?>" 
                             onerror="this.src='https://via.placeholder.com/300x200?text=Delicious+Pizza'">
                        <span class="category-tag"><?php echo htmlspecialchars($pizza['category']); ?></span>
                    </div>

                    <div class="pizza-details">
                        <h3><?php echo htmlspecialchars($pizza['name']); ?></h3>
                        <p><?php echo htmlspecialchars($pizza['description']); ?></p>
                        
                        <div class="pizza-price-row">
                            <span class="price">RM <?php echo number_format($pizza['price'], 2); ?></span>
                            <div class="add-controls">
                                <input type="number" value="1" min="1" max="10" id="qty-<?php echo $pizza['id']; ?>" class="qty-input-small">
                                <button class="btn-add" onclick="addToCart(<?php echo $pizza['id']; ?>)">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No pizzas available at the moment. Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    /**
     * AJAX: Add to Cart functionality
     */
    async function addToCart(pizzaId) {
        const qtyInput = document.getElementById('qty-' + pizzaId);
        const qty = qtyInput.value;

        const formData = new FormData();
        formData.append('pizza_id', pizzaId);
        formData.append('quantity', qty);

        try {
            const response = await fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.status === 'success') {
                // Update the navbar cart count badge
                document.getElementById('cart-count').innerText = data.new_count;
                
                // Visual feedback (optional: you could replace alert with a toast)
                alert('Success! Added to your selection.');
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Request failed:', error);
            alert('Could not add to cart. Please try again.');
        }
    }

    /**
     * Search functionality (Real-time)
     */
    function filterMenu() {
        const searchValue = document.getElementById('menuSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.pizza-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            // Show card if search matches, otherwise hide
            card.style.display = name.includes(searchValue) ? "block" : "none";
        });
    }

    /**
     * Category Filter functionality
     */
    function filterCategory(category, button) {
        // Update active button styling
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        const cards = document.querySelectorAll('.pizza-card');
        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            // Show if 'all' is selected or if the category matches
            if (category === 'all' || cardCat === category) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
        
        // Clear search input when switching categories for a better UX
        document.getElementById('menuSearch').value = "";
    }
    </script>

</body>
</html>