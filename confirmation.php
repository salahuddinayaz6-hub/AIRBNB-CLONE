<?php
require_once 'db.php';
 
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
$stmt = $pdo->prepare("SELECT b.*, p.title, p.location, p.image_url FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();
 
if (!$booking) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed! | Airbnb Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF385C;
            --text-dark: #222222;
            --text-light: #717171;
            --white: #FFFFFF;
            --border: #DDDDDD;
            --success: #4CAF50;
        }
 
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f9f9f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
 
        .confirmation-card {
            background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 600px; width: 100%; padding: 40px; text-align: center;
        }
 
        .success-icon {
            font-size: 60px; color: var(--success); margin-bottom: 20px;
        }
 
        h1 { font-size: 28px; margin-bottom: 10px; }
        p { color: var(--text-light); margin-bottom: 30px; }
 
        .booking-details {
            border: 1px solid var(--border); border-radius: 12px; padding: 20px;
            text-align: left; margin-bottom: 30px; display: flex; gap: 20px;
        }
 
        .booking-details img { width: 120px; height: 100px; object-fit: cover; border-radius: 8px; }
        .details-text h3 { margin-bottom: 5px; }
        .details-text div { font-size: 14px; color: var(--text-light); margin-bottom: 3px; }
 
        .btn-home {
            background: var(--primary); color: white; border: none; padding: 14px 30px;
            border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none;
            display: inline-block; transition: background 0.2s;
        }
 
        .btn-home:hover { background: #E31C5F; }
    </style>
</head>
<body>
 
<div class="confirmation-card">
    <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
    <h1>Booking Confirmed!</h1>
    <p>Your stay at <?= htmlspecialchars($booking['title']) ?> has been reserved successfully.</p>
 
    <div class="booking-details">
        <img src="<?= $booking['image_url'] ?>" alt="Property">
        <div class="details-text">
            <h3><?= htmlspecialchars($booking['location']) ?></h3>
            <div><strong>Reservation ID:</strong> #<?= $booking['id'] ?></div>
            <div><strong>Dates:</strong> <?= date('M j, Y', strtotime($booking['check_in'])) ?> - <?= date('M j, Y', strtotime($booking['check_out'])) ?></div>
            <div><strong>Total Paid:</strong> $<?= number_format($booking['total_price']) ?></div>
        </div>
    </div>
 
    <a href="index.php" class="btn-home">Return Home</a>
</div>
 
</body>
</html>
 
