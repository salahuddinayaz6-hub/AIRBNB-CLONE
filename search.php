<?php
require_once 'db.php';
 
$location = isset($_GET['location']) ? $_GET['location'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;
$amenity_filter = isset($_GET['amenity']) ? $_GET['amenity'] : '';
 
// Build Query
$query = "SELECT * FROM properties WHERE price_per_night >= ? AND price_per_night <= ?";
$params = [$min_price, $max_price];
 
if (!empty($location)) {
    $query .= " AND (location LIKE ? OR title LIKE ?)";
    $params[] = "%$location%";
    $params[] = "%$location%";
}
 
if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}
 
if (!empty($amenity_filter)) {
    $query .= " AND amenities LIKE ?";
    $params[] = "%$amenity_filter%";
}
 
if ($sort == 'price_low') {
    $query .= " ORDER BY price_per_night ASC";
} elseif ($sort == 'price_high') {
    $query .= " ORDER BY price_per_night DESC";
} elseif ($sort == 'rating') {
    $query .= " ORDER BY rating DESC";
} else {
    $query .= " ORDER BY id DESC";
}
 
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | Airbnb Clone</title>
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
            margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif;
        }
 
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
 
        .search-summary {
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 8px 24px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
 
        .main-container { padding: 30px 80px; }
 
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
 
        .sort-select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 14px;
            outline: none;
            cursor: pointer;
        }
 
        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
 
        .property-card { cursor: pointer; text-decoration: none; color: inherit; }
        .property-image {
            width: 100%; aspect-ratio: 1/1; border-radius: 12px; overflow: hidden;
            position: relative; margin-bottom: 12px;
        }
        .property-image img {
            width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;
        }
        .property-card:hover .property-image img { transform: scale(1.05); }
 
        .property-info { display: flex; flex-direction: column; gap: 4px; }
        .property-header { display: flex; justify-content: space-between; }
        .property-title { font-weight: 600; font-size: 15px; }
        .property-rating { display: flex; align-items: center; gap: 4px; font-size: 14px; }
        .property-price { margin-top: 4px; font-weight: 600; font-size: 15px; }
 
        .no-results {
            text-align: center;
            padding: 100px 0;
            grid-column: 1 / -1;
        }
 
        .no-results i { font-size: 48px; color: var(--text-light); margin-bottom: 20px; }
        .no-results h2 { margin-bottom: 10px; }
 
        @media (max-width: 768px) {
            header, .main-container { padding: 15px 20px; }
            .search-summary { display: none; }
        }
    </style>
</head>
<body>
 
<header>
    <a href="index.php" class="logo">
        <i class="fa-brands fa-airbnb"></i>
    </a>
 
    <div class="search-summary">
        <?= htmlspecialchars($location ?: 'Anywhere') ?> · 
        <?= !empty($check_in) ? date('M j', strtotime($check_in)) : 'Any dates' ?> - 
        <?= !empty($check_out) ? date('M j', strtotime($check_out)) : '' ?>
    </div>
 
    <div style="display:flex; align-items:center; gap:20px">
        <i class="fa-solid fa-bars"></i>
        <div style="background:var(--text-light); color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center">
            <i class="fa-solid fa-user"></i>
        </div>
    </div>
</header>
 
<main class="main-container">
    <div style="display: grid; grid-template-columns: 250px 1fr; gap: 40px;">
        <!-- Filter Sidebar -->
        <aside class="sidebar" style="border-right: 1px solid var(--border); padding-right: 20px;">
            <h3 style="margin-bottom: 20px; font-size: 18px;">Filters</h3>
 
            <form id="filterForm" action="search.php" method="GET">
                <input type="hidden" name="location" value="<?= htmlspecialchars($location) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
 
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 10px;">Price Range</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="number" name="min_price" placeholder="Min" value="<?= $min_price ?>" style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Max" value="<?= $max_price ?>" style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
                    </div>
                </div>
 
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 10px;">Amenities</label>
                    <select name="amenity" style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
                        <option value="">Any</option>
                        <option value="Wifi" <?= $amenity_filter == 'Wifi' ? 'selected' : '' ?>>Wifi</option>
                        <option value="Pool" <?= $amenity_filter == 'Pool' ? 'selected' : '' ?>>Pool</option>
                        <option value="AC" <?= $amenity_filter == 'AC' ? 'selected' : '' ?>>AC</option>
                        <option value="Kitchen" <?= $amenity_filter == 'Kitchen' ? 'selected' : '' ?>>Kitchen</option>
                    </select>
                </div>
 
                <button type="submit" style="width: 100%; background: var(--text-dark); color: white; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: 600;">Apply Filters</button>
            </form>
        </aside>
 
        <div>
            <div class="controls">
                <div>
                    <h1 style="font-size: 20px; font-weight: 600;">
                        Showing <?= count($properties) ?> spots
                    </h1>
                </div>
                <div>
                    <select class="sort-select" onchange="handleSort(this.value)">
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating" <?= $sort == 'rating' ? 'selected' : '' ?>>Top Rated</option>
                    </select>
                </div>
            </div>
 
            <div class="property-grid">
        <?php if (count($properties) > 0): ?>
            <?php foreach ($properties as $prop): ?>
            <a href="details.php?id=<?= $prop['id'] ?>&check_in=<?= $check_in ?>&check_out=<?= $check_out ?>" class="property-card">
                <div class="property-image">
                    <img src="<?= $prop['image_url'] ?>" alt="<?= $prop['title'] ?>">
                </div>
                <div class="property-info">
                    <div class="property-header">
                        <span class="property-title"><?= htmlspecialchars($prop['location']) ?></span>
                        <span class="property-rating">
                            <i class="fa-solid fa-star"></i>
                            <?= $prop['rating'] ?>
                        </span>
                    </div>
                    <span style="color:var(--text-light); font-size:15px;"><?= htmlspecialchars($prop['title']) ?></span>
                    <span class="property-price">$<?= number_format($prop['price_per_night']) ?> <span style="font-weight:400">night</span></span>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h2>No results found</h2>
                <p>Try adjusting your filters or search area.</p>
                <button onclick="window.location.href='index.php'" style="margin-top:20px; padding:10px 20px; border-radius:8px; border:1px solid var(--text-dark); background:white; cursor:pointer; font-weight:600;">Clear all filters</button>
            </div>
        <?php endif; ?>
        </div>
    </div>
</div>
</main>
 
<script>
    function handleSort(value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('sort', value);
        window.location.href = 'search.php?' + urlParams.toString();
    }
</script>
 
</body>
</html>
 
