<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Tracking Your Order</title>
</head>
<body class="bg-cream">
    <div class="container text-center">
        <div class="order-card">
            <h2>Order #<span id="order-id"><?php echo $_GET['id']; ?></span></h2>
            <p>Status: <strong id="status-text" class="highlight">Pending</strong></p>
            
            <div class="tracking-bar">
                <div id="progress" class="progress-fill" style="width: 20%"></div>
            </div>
            
            <div id="status-animation">
                <p>We've received your order and the chef is ready!</p>
            </div>
        </div>
    </div>

    <script>
        const orderId = new URLSearchParams(window.location.search).get('id');

        async function checkStatus() {
            const res = await fetch(`api/order_status.php?id=${orderId}`);
            const data = await res.json();
            
            const statusMap = {
                'Pending': '20%',
                'Preparing': '40%',
                'Baking': '60%',
                'Out for Delivery': '85%',
                'Delivered': '100%'
            };

            document.getElementById('status-text').innerText = data.status;
            document.getElementById('progress').style.width = statusMap[data.status];

            if(data.status === 'Delivered') {
                clearInterval(poll);
                alert("Enjoy your pizza!");
            }
        }

        // Poll every 5 seconds
        const poll = setInterval(checkStatus, 5000);
    </script>
</body>
</html>