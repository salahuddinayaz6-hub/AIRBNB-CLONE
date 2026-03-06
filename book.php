<?php
require_once 'db.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = (int)$_POST['property_id'];
    $user_name   = $_POST['user_name'];
    $user_email  = $_POST['user_email'];
    $check_in    = $_POST['check_in'];
    $check_out   = $_POST['check_out'];
    $price_per_night = (float)$_POST['price_per_night'];
 
    // Calculate total price on server
    $diff = strtotime($check_out) - strtotime($check_in);
    $days = max(1, floor($diff / (60 * 60 * 24)));
 
    $base_total = $price_per_night * $days;
    $cleaning_fee = 50;
    $service_fee = round($base_total * 0.12);
    $grand_total = $base_total + $cleaning_fee + $service_fee;
 
    try {
        $sql = "INSERT INTO bookings (property_id, user_name, user_email, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$property_id, $user_name, $user_email, $check_in, $check_out, $grand_total]);
        $booking_id = $pdo->lastInsertId();
 
        // JS Redirection as requested
        echo "<script>window.location.href = 'confirmation.php?id=$booking_id';</script>";
        exit;
    } catch (PDOException $e) {
        die("Error saving booking: " . $e->getMessage());
    }
} else {
    echo "<script>window.location.href = 'index.php';</script>";
}
?>
