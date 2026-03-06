<?php
require_once 'db.php';
 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
 
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$prop = $stmt->fetch();
 
if (!$prop) {
    echo "Property not found.";
    exit;
}
 
// Basic price calc logic for display
$days = 1;
if (!empty($check_in) && !empty($check_out)) {
    $diff = strtotime($check_out) - strtotime($check_in);
    $days = max(1, floor($diff / (60 * 60 * 24)));
}
$total_price = $prop['price_per_night'] * $days;
$cleaning_fee = 50;
$service_fee = round($total_price * 0.12);
$grand_total = $total_price + $cleaning_fee + $service_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($prop['title']) ?> | Airbnb Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF385C;
            --text-dark: #222222;
            --text-light: #717171;
            --bg-light: #F7F7F7;
            --white: #FFFFFF;
            --border: #DDDDDD;
            --shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
 
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { color: var(--text-dark); background: var(--white); }
 
        header {
            border-bottom: 1px solid var(--border);
            padding: 15px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--white);
            position: sticky; top: 0; z-index: 1000;
        }
 
        .logo { color: var(--primary); font-size: 24px; font-weight: 700; text-decoration: none; }
 
        .main-container { padding: 40px 80px; max-width: 1280px; margin: 0 auto; }
 
        .detail-header h1 { font-size: 26px; font-weight: 600; margin-bottom: 10px; }
        .detail-meta { display: flex; gap: 20px; font-size: 14px; font-weight: 600; text-decoration: underline; margin-bottom: 20px; }
 
        .gallery {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-rows: repeat(2, 200px);
            gap: 8px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 40px;
        }
 
        .gallery-main { grid-row: span 2; }
        .gallery img { width: 100%; height: 100%; object-fit: cover; transition: brightness 0.2s; cursor: pointer; }
        .gallery img:hover { filter: brightness(0.9); }
 
        .grid-layout { display: grid; grid-template-columns: 1.8fr 1fr; gap: 80px; }
 
        .host-info { font-size: 22px; font-weight: 600; padding: 20px 0; border-bottom: 1px solid var(--border); margin-bottom: 30px; }
        .amenities-list { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
        .amenity-item { display: flex; align-items: center; gap: 10px; color: var(--text-dark); }
 
        /* Booking Sidebar */
        .booking-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 120px;
            background: white;
        }
 
        .price-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
        .price-header .price { font-size: 22px; font-weight: 600; }
 
        .booking-form-box { border: 1px solid #999; border-radius: 8px; margin-bottom: 16px; }
        .date-inputs { display: grid; grid-template-columns: 1fr 1fr; }
        .date-box { padding: 10px; border-bottom: 1px solid #999; }
        .date-box:first-child { border-right: 1px solid #999; }
        .date-box label { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .date-box input { border: none; outline: none; width: 100%; font-size: 14px; margin-top: 2px; }
 
        .guest-box { padding: 10px; }
        .guest-box label { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .guest-box select { border: none; width: 100%; font-size: 14px; outline: none; }
 
        .book-btn {
            background: var(--primary);
            color: white; border: none; width: 100%; border-radius: 8px;
            padding: 14px; font-size: 16px; font-weight: 600; cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, background 0.2s;
        }
        .book-btn:hover { background: #E31C5F; transform: scale(1.01); }
        .book-btn:active { transform: scale(0.98); }
 
        .total-summary { margin-top: 24px; display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--border); padding-top: 24px; }
        .summary-row { display: flex; justify-content: space-between; font-size: 16px; }
 
        @media (max-width: 1024px) {
            .grid-layout { grid-template-columns: 1fr; }
            header, .main-container { padding: 15px 40px; }
        }
 
        @media (max-width: 768px) {
            header, .main-container { padding: 15px 20px; }
            .gallery { grid-template-columns: 1fr; grid-template-rows: 300px; }
            .gallery img:not(.gallery-main img) { display: none; }
        }
    </style>
</head>
<body>
 
<header>
    <a href="index.php" class="logo">
        <i class="fa-brands fa-airbnb"></i>
        <span>airbnb</span>
    </a>
</header>
 
<main class="main-container">
    <div class="detail-header">
        <h1><?= htmlspecialchars($prop['title']) ?></h1>
        <div class="detail-meta">
            <span><i class="fa-solid fa-star"></i> <?= $prop['rating'] ?></span>
            <span>· 12 reviews</span>
            <span>· <?= htmlspecialchars($prop['location']) ?></span>
        </div>
    </div>
 
    <div class="gallery">
        <div class="gallery-main">
            <img src="<?= $prop['image_url'] ?>" alt="Property Image">
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1512918766775-d260021c41b5?auto=format&fit=crop&w=600&q=80" alt="Detail 1">
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=600&q=80" alt="Detail 2">
        </div>
    </div>
 
    <div class="grid-layout">
        <div class="info-section">
            <div class="host-info">
                Entire home hosted by Sarah
                <p style="font-weight:400; font-size:16px; color:var(--text-light); margin-top:5px;">4 guests · 2 bedrooms · 2 beds · 1 bath</p>
            </div>
 
            <div style="padding-bottom:30px; border-bottom:1px solid var(--border);">
                <h3 style="margin-bottom:15px;">What this place offers</h3>
                <div class="amenities-list">
                    <?php 
                    $amenities = explode(',', $prop['amenities']);
                    foreach ($amenities as $item): 
                    ?>
                    <div class="amenity-item">
                        <i class="fa-solid fa-check"></i>
                        <span><?= trim($item) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
 
            <div style="padding: 30px 0;">
                <h3 style="margin-bottom:15px;">Description</h3>
                <p style="line-height:1.6; color:var(--text-dark);"><?= htmlspecialchars($prop['description']) ?> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            </div>
        </div>
 
        <div class="booking-section">
            <div class="booking-card">
                <div class="price-header">
                    <div class="price">$<?= number_format($prop['price_per_night']) ?> <span style="font-weight:400; font-size:16px;">night</span></div>
                    <div style="font-size:14px; text-decoration:underline;">12 reviews</div>
                </div>
 
                <form id="bookingForm" action="book.php" method="POST">
                    <input type="hidden" name="property_id" value="<?= $prop['id'] ?>">
                    <input type="hidden" name="price_per_night" value="<?= $prop['price_per_night'] ?>">
 
                    <div class="booking-form-box">
                        <div class="date-inputs">
                            <div class="date-box">
                                <label>Check-in</label>
                                <input type="date" name="check_in" value="<?= $check_in ?>" required onchange="calculateTotal()">
                            </div>
                            <div class="date-box">
                                <label>Checkout</label>
                                <input type="date" name="check_out" value="<?= $check_out ?>" required onchange="calculateTotal()">
                            </div>
                        </div>
                        <div class="guest-box">
                            <label>Guests</label>
                            <select name="guests">
                                <option>1 guest</option>
                                <option selected>2 guests</option>
                                <option>3 guests</option>
                                <option>4 guests</option>
                            </select>
                        </div>
                    </div>
 
                    <div style="padding: 15px 0;">
                        <input type="text" name="user_name" placeholder="Your Name" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; margin-bottom:10px;" required>
                        <input type="email" name="user_email" placeholder="Your Email" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px;" required>
                    </div>
 
                    <button type="submit" class="book-btn">Reserve</button>
                    <p style="text-align:center; font-size:14px; color:var(--text-light); margin-top:15px;">You won't be charged yet</p>
                </form>
 
                <div class="total-summary" id="priceSummary">
                    <div class="summary-row">
                        <span id="nightlyText">$<?= number_format($prop['price_per_night']) ?> x <?= $days ?> nights</span>
                        <span id="baseTotal">$<?= number_format($total_price) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Cleaning fee</span>
                        <span>$50</span>
                    </div>
                    <div class="summary-row">
                        <span>Airbnb service fee</span>
                        <span id="serviceFee">$<?= number_format($service_fee) ?></span>
                    </div>
                    <div class="summary-row" style="font-weight:700; border-top:1px solid var(--border); padding-top:10px; margin-top:5px; font-size:18px;">
                        <span>Total</span>
                        <span id="grandTotal">$<?= number_format($grand_total) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
 
<script>
    function calculateTotal() {
        const checkIn = document.getElementsByName('check_in')[0].value;
        const checkOut = document.getElementsByName('check_out')[0].value;
        const pricePerNight = <?= $prop['price_per_night'] ?>;
 
        if (checkIn && checkOut) {
            const start = new Date(checkIn);
            const end = new Date(checkOut);
            const diff = end - start;
            const days = Math.max(1, Math.floor(diff / (1000 * 60 * 60 * 24)));
 
            const baseTotal = pricePerNight * days;
            const cleaningFee = 50;
            const serviceFee = Math.round(baseTotal * 0.12);
            const grandTotal = baseTotal + cleaningFee + serviceFee;
 
            document.getElementById('nightlyText').innerText = '$' + pricePerNight + ' x ' + days + ' nights';
            document.getElementById('baseTotal').innerText = '$' + baseTotal.toLocaleString();
            document.getElementById('serviceFee').innerText = '$' + serviceFee.toLocaleString();
            document.getElementById('grandTotal').innerText = '$' + grandTotal.toLocaleString();
        }
    }
 
    // Initial calc
    calculateTotal();
</script>
 
</body>
</html>
 
