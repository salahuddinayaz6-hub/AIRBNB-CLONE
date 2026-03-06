<?php
require_once 'db.php';
 
// Fetch properties for the home page
$stmt = $pdo->query("SELECT * FROM properties LIMIT 12");
$properties = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airbnb Clone | Holiday Rentals, Cabins, Beach Houses & More</title>
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
 
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
 
        body {
            color: var(--text-dark);
            background-color: var(--white);
        }
 
        /* Header Styles */
        header {
            border-bottom: 1px solid var(--border);
            padding: 15px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
        }
 
        .logo {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }
 
        .search-bar-container {
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 8px 10px 8px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s;
            cursor: pointer;
        }
 
        .search-bar-container:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.18);
        }
 
        .search-item {
            font-size: 14px;
            font-weight: 500;
        }
 
        .search-btn {
            background: var(--primary);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
        }
 
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid var(--border);
            padding: 5px 5px 5px 12px;
            border-radius: 25px;
            cursor: pointer;
        }
 
        /* Filter Section */
        .filters {
            display: flex;
            align-items: center;
            padding: 20px 80px;
            gap: 40px;
            overflow-x: auto;
            border-bottom: 1px solid var(--border);
            background: var(--white);
            position: sticky;
            top: 80px;
            z-index: 999;
        }
 
        .filter-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            cursor: pointer;
            min-width: fit-content;
            transition: color 0.2s, border-bottom 0.2s, transform 0.2s;
            padding-bottom: 10px;
            border-bottom: 2px solid transparent;
            text-decoration: none;
        }
 
        .filter-item:hover {
            color: var(--text-dark);
            border-bottom: 2px solid var(--border);
            transform: translateY(-2px);
        }
 
        .filter-item.active {
            color: var(--text-dark);
            border-bottom: 2px solid var(--text-dark);
        }
 
        .filter-item i {
            font-size: 24px;
        }
 
        .filter-item span {
            font-size: 12px;
            font-weight: 500;
        }
 
        /* Property Grid */
        .main-container {
            padding: 30px 80px;
        }
 
        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
 
        .property-card {
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
 
        .property-image {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            margin-bottom: 12px;
        }
 
        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
 
        .property-card:hover .property-image img {
            transform: scale(1.05);
        }
 
        .heart-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            font-size: 20px;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
 
        .property-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
 
        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
 
        .property-title {
            font-weight: 600;
            font-size: 15px;
        }
 
        .property-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
        }
 
        .property-location {
            color: var(--text-light);
            font-size: 15px;
        }
 
        .property-dates {
            color: var(--text-light);
            font-size: 15px;
        }
 
        .property-price {
            margin-top: 4px;
            font-weight: 600;
            font-size: 15px;
        }
 
        /* Modal / Search Overlay */
        #search-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 2000;
            justify-content: center;
            padding-top: 100px;
        }
 
        .search-modal {
            background: white;
            width: 850px;
            border-radius: 32px;
            padding: 30px;
            box-shadow: var(--shadow);
            animation: slideDown 0.3s ease-out;
        }
 
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
 
        .search-form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            background: #ebebeb;
            border-radius: 40px;
            padding: 0;
            overflow: hidden;
        }
 
        .search-input-group {
            padding: 14px 24px;
            border-radius: 40px;
            transition: background 0.2s;
            cursor: pointer;
        }
 
        .search-input-group:hover {
            background: #dddddd;
        }
 
        .search-input-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 2px;
        }
 
        .search-input-group input {
            background: transparent;
            border: none;
            width: 100%;
            outline: none;
            font-size: 14px;
            color: #222;
        }
 
        .search-submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 10px;
        }
 
        /* Responsive */
        @media (max-width: 1024px) {
            header, .filters, .main-container {
                padding: 15px 40px;
            }
        }
 
        @media (max-width: 768px) {
            header, .filters, .main-container {
                padding: 15px 20px;
            }
            .search-bar-container {
                display: none;
            }
        }
    </style>
</head>
<body>
 
<header>
    <a href="index.php" class="logo">
        <i class="fa-brands fa-airbnb"></i>
        <span>airbnb</span>
    </a>
 
    <div class="search-bar-container" onclick="toggleSearch()">
        <div class="search-item">Anywhere</div>
        <div style="width:1px; height:24px; background:var(--border)"></div>
        <div class="search-item">Any week</div>
        <div style="width:1px; height:24px; background:var(--border)"></div>
        <div class="search-item" style="color:var(--text-light); font-weight:400">Add guests</div>
        <div class="search-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>
 
    <div class="user-menu">
        <i class="fa-solid fa-bars"></i>
        <div style="background:var(--text-light); color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center">
            <i class="fa-solid fa-user"></i>
        </div>
    </div>
</header>
 
<div class="filters">
    <a href="index.php" class="filter-item <?= !isset($_GET['category']) ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i>
        <span>Trending</span>
    </a>
    <a href="search.php?category=Amazing views" class="filter-item">
        <i class="fa-solid fa-mountain-sun"></i>
        <span>Amazing views</span>
    </a>
    <a href="search.php?category=Beachfront" class="filter-item">
        <i class="fa-solid fa-water"></i>
        <span>Beachfront</span>
    </a>
    <a href="search.php?category=Cabins" class="filter-item">
        <i class="fa-solid fa-campground"></i>
        <span>Cabins</span>
    </a>
    <a href="search.php?category=Modern" class="filter-item">
        <i class="fa-solid fa-person-swimming"></i>
        <span>Amazing pools</span>
    </a>
    <a href="search.php?category=Tropical" class="filter-item">
        <i class="fa-solid fa-umbrella-beach"></i>
        <span>Tropical</span>
    </a>
    <a href="search.php?category=Desert" class="filter-item">
        <i class="fa-solid fa-snowflake"></i>
        <span>Desert</span>
    </a>
    <a href="search.php?category=Historic" class="filter-item">
        <i class="fa-solid fa-castle"></i>
        <span>Historic</span>
    </a>
</div>
 
<main class="main-container">
    <div class="property-grid">
        <?php foreach ($properties as $prop): ?>
        <a href="details.php?id=<?= $prop['id'] ?>" class="property-card">
            <div class="property-image">
                <img src="<?= $prop['image_url'] ?>" alt="<?= $prop['title'] ?>">
                <div class="heart-btn"><i class="fa-regular fa-heart"></i></div>
            </div>
            <div class="property-info">
                <div class="property-header">
                    <span class="property-title"><?= htmlspecialchars($prop['location']) ?></span>
                    <span class="property-rating">
                        <i class="fa-solid fa-star"></i>
                        <?= $prop['rating'] ?>
                    </span>
                </div>
                <span class="property-location"><?= htmlspecialchars($prop['title']) ?></span>
                <span class="property-dates">Feb 10 - 15</span>
                <span class="property-price">$<?= number_format($prop['price_per_night']) ?> <span style="font-weight:400">night</span></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</main>
 
<!-- Search Overlay -->
<div id="search-overlay" onclick="closeSearch(event)">
    <div class="search-modal" onclick="event.stopPropagation()">
        <form action="search.php" method="GET">
            <div class="search-form">
                <div class="search-input-group">
                    <label>Where</label>
                    <input type="text" name="location" placeholder="Search destinations" required>
                </div>
                <div class="search-input-group">
                    <label>Check in</label>
                    <input type="date" name="check_in" required>
                </div>
                <div class="search-input-group">
                    <label>Check out</label>
                    <input type="date" name="check_out" required>
                </div>
                <div style="display:flex; align-items:center; justify-content:flex-end">
                    <button type="submit" class="search-submit-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
 
<script>
    function toggleSearch() {
        document.getElementById('search-overlay').style.display = 'flex';
    }
 
    function closeSearch(event) {
        if (event.target.id === 'search-overlay') {
            document.getElementById('search-overlay').style.display = 'none';
        }
    }
 
    // Smooth redirection example as requested
    function navigateTo(url) {
        window.location.href = url;
    }
</script>
 
</body>
</html>
 
