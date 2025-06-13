<?php

$servername = "localhost:3307";
$username = "root";        // change if different
$password = "";            // change if you have a DB password
$database = "dooars_tutors"; // change to your actual DB name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$search = $_POST['search'] ?? '';
$sql = "SELECT * FROM tutors WHERE 
           subjects LIKE ? OR 
           classes LIKE ? OR 
           boards LIKE ?";

$stmt = $conn->prepare($sql);
$searchParam = "%{$search}%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DooarsTutors - Find Your Perfect Tutor</title>  
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/style_1.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .tutors-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        /* background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); */
        min-height: 100vh;
    }

    .tutors-section h2 {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 700;
        color:rgb(255, 255, 255);
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(255, 255, 255, 0.1);
    }

    .section-subtitle {
        text-align: center;
        font-size: 1.1rem;
        color:rgb(255, 255, 255);
        margin-bottom: 50px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .tutor-carousel {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .tutor-card-container {
        display: flex;
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform;
    }

    .tutor-card {
        min-width: 100%;
        display: flex;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 500px;
    }

    .tutor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .tutor-image-section {
        flex: 0 0 40%;
        position: relative;
        background: linear-gradient(135deg, #38a3a5 0%, #22577a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
    }

    .tutor-img {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 6px solid white;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }

    .tutor-img:hover {
        transform: scale(1.05);
    }

    .tutor-info {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .tutor-header {
        margin-bottom: 30px;
    }

    .tutor-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .tutor-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .tutor-details {
        flex: 1;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        padding: 12px 0;
        border-bottom: 1px solid #ecf0f1;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #34495e;
        min-width: 100px;
        margin-right: 15px;
        font-size: 0.95rem;
    }

    .detail-value {
        color: #7f8c8d;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .map-container {
        margin: 20px 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .map-container iframe {
        width: 100%;
        height: 180px;
        border: none;
        transition: transform 0.3s ease;
    }

    .map-container:hover iframe {
        transform: scale(1.02);
    }

    .map-link {
        display: block;
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .map-link:hover {
        background: #667eea;
        color: white;
    }

    .carousel-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
        gap: 20px;
    }

    .carousel-btn {
        background: linear-gradient(135deg, #38A3A5 0%, #57cc99 100%);
        border: none;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .carousel-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .carousel-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .carousel-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 0 20px;
    }

    .carousel-counter {
        background: rgba(102, 126, 234, 0.1);
        color: #57cc99;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        min-width: 80px;
        text-align: center;
    }

    .carousel-indicators {
        display: flex;
        gap: 6px;
    }

    .indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #bdc3c7;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .indicator.active {
        background: #57cc99;
        transform: scale(1.3);
    }

    .progress-bar {
        width: 120px;
        height: 4px;
        background: #ecf0f1;
        border-radius: 2px;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
        transition: width 0.4s ease;
    }

    .swipe-hint {
        text-align: center;
        color:rgb(255, 255, 255);
        font-size: 0.9rem;
        margin-top: 15px;
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .tutor-card {
            flex-direction: column;
            height: auto;
        }

        .tutor-image-section {
            flex: none;
            padding: 20px;
        }

        .tutor-img {
            width: 150px;
            height: 150px;
        }

        .tutor-info {
            padding: 20px;
        }

        .tutor-info h3 {
            font-size: 1.5rem;
        }

        .tutors-section h2 {
            font-size: 2rem;
            color:white;
        }
    }



    .rating-section {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding: 12px 0;
    }

    .stars-container {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .star {
        color: #ffd700;
        font-size: 1.2rem;
        transition: all 0.2s ease;
    }

    .star.empty {
        color: #e0e0e0;
    }

    .rating-text {
        color: #34495e;
        font-weight: 600;
        font-size: 1rem;
        margin-left: 5px;
    }

    .rating-count {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-left: 5px;
    }

    .no-rating {
        color: #95a5a6;
        font-style: italic;
        font-size: 0.9rem;
    }



.stats-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.views-section, .teachers-section {
    background: transparent;
    padding: 0;
}

.views-container, .teachers-container {
    width: 100%;
    max-width: none;
}

.views-card, .teachers-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    padding: 3rem 2rem;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    transition: all 0.4s ease;
    text-align: center;
    height: 100%;
}

.views-card:hover, .teachers-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 30px 60px -12px rgba(34, 87, 122, 0.25);
}

.views-title, .teachers-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.views-label, .teachers-label {
    font-size: 1rem;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
}

.php-counter {
    font-size: 3rem !important;
    font-weight: 700 !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
    margin: 1rem 0 !important;
    line-height: 1.2 !important;
    display: block !important;
}

.teachers-counter {
    font-size: 3rem !important;
    font-weight: 700 !important;
    background: linear-gradient(135deg, #38a3a5 0%, #57cc99 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
    margin: 1rem 0 !important;
    line-height: 1.2 !important;
    display: block !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 2rem 1rem;
    }
    
    .views-card, .teachers-card {
        padding: 2rem 1.5rem;
    }
    
    .php-counter, .teachers-counter {
        font-size: 2.5rem !important;
    }
}





    </style>
</head>
<body>  
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">DooarsTutors</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li class="dropdown">
                    <a href="#skills">Skills</a>
                    <ul class="dropdown-menu">
                        <li><a href="#music">Music</a></li>
                        <li><a href="#dance">Dance</a></li>
                        <li><a href="#arts">Arts</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#sports&health">Sports & Health</a>
                    <ul class="dropdown-menu">
                        <li><a href="#sports">Sports</a></li>
                        <li><a href="#yoga">Yoga</a></li>
                        <li><a href="#gym">Gym</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#register">Become a Tutor</a>
                    <ul class="dropdown-menu">
                        <li><a href="#individual">As an Individual</a></li>
                        <li><a href="#organization">As Organization</a></li>
                    </ul>
                </li>
                <!-- <li><a href="#become-tutor">Become a Tutor</a></li> -->
                <li><a href="#login">Login</a></li>
            </ul>
            <div class="mobile-menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="mobile-overlay"></div>
    <nav class="mobile-nav">
        <div class="mobile-nav-header">
            <h3>DooarsTutors</h3>
        </div>
        <div class="mobile-nav-content">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li class="dropdown">
                    <a href="#skills">Skills</a>
                    <ul class="dropdown-menu">
                        <li><a href="#music">Music</a></li>
                        <li><a href="#dance">Dance</a></li>
                        <li><a href="#arts">Arts</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#sports&health">Sports & Health</a>
                    <ul class="dropdown-menu">
                        <li><a href="#sports">Sports</a></li>
                        <li><a href="#yoga">Yoga</a></li>
                        <li><a href="#gym">Gym</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#register">Become a Tutor</a>
                    <ul class="dropdown-menu">
                        <li><a href="#individual">As an Individual</a></li>
                        <li><a href="#organization">As Organization</a></li>
                    </ul>
                </li>
                <!-- <li><a href="#become-tutor">Become a Tutor</a></li> -->
                <li><a href="#login">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <h1>Find Your Perfect Tutor</h1>
            <p>Connect with qualified teachers across all boards and subjects. Excellence in education, delivered personally.</p>
            
            <!-- Search Section -->
            <div class="search-section">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search by teacher name, subject, or class...">
                    <select class="location-select">
                        <option value="">Select Place</option>
                        <option value="Alipurduar">Alipurduar</option>
                        <option value="Coochbehar">Coochbehar</option>
                    </select>
                    <button class="search-btn">Search</button>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <div class="filter-label" onclick="toggleDropdown('class')">
                            Class 
                            <span class="selected-count" id="class-count">0 selected</span>
                        </div>
                        <div class="filter-dropdown" id="class-dropdown">
                            <div class="filter-option" onclick="toggleFilter('class', '1')">
                                <div class="filter-checkbox" id="class-1"></div>
                                <span class="filter-option-text">Class 1</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '2')">
                                <div class="filter-checkbox" id="class-2"></div>
                                <span class="filter-option-text">Class 2</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '3')">
                                <div class="filter-checkbox" id="class-3"></div>
                                <span class="filter-option-text">Class 3</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '4')">
                                <div class="filter-checkbox" id="class-4"></div>
                                <span class="filter-option-text">Class 4</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '5')">
                                <div class="filter-checkbox" id="class-5"></div>
                                <span class="filter-option-text">Class 5</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '6')">
                                <div class="filter-checkbox" id="class-6"></div>
                                <span class="filter-option-text">Class 6</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '7')">
                                <div class="filter-checkbox" id="class-7"></div>
                                <span class="filter-option-text">Class 7</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '8')">
                                <div class="filter-checkbox" id="class-8"></div>
                                <span class="filter-option-text">Class 8</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '9')">
                                <div class="filter-checkbox" id="class-9"></div>
                                <span class="filter-option-text">Class 9</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '10')">
                                <div class="filter-checkbox" id="class-10"></div>
                                <span class="filter-option-text">Class 10</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '11')">
                                <div class="filter-checkbox" id="class-11"></div>
                                <span class="filter-option-text">Class 11</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('class', '12')">
                                <div class="filter-checkbox" id="class-12"></div>
                                <span class="filter-option-text">Class 12</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <div class="filter-label" onclick="toggleDropdown('board')">
                            Board 
                            <span class="selected-count" id="board-count">0 selected</span>
                        </div>
                        <div class="filter-dropdown" id="board-dropdown">
                            <div class="filter-option" onclick="toggleFilter('board', 'wbbse')">
                                <div class="filter-checkbox" id="board-wbbse"></div>
                                <span class="filter-option-text">WBBSE</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('board', 'cbse')">
                                <div class="filter-checkbox" id="board-cbse"></div>
                                <span class="filter-option-text">CBSE</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('board', 'icse')">
                                <div class="filter-checkbox" id="board-icse"></div>
                                <span class="filter-option-text">ICSE</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('board', 'ise')">
                                <div class="filter-checkbox" id="board-ise"></div>
                                <span class="filter-option-text">ISE</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('board', 'wbcshe')">
                                <div class="filter-checkbox" id="board-wbcshe"></div>
                                <span class="filter-option-text">WBCSHE</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <div class="filter-label" onclick="toggleDropdown('subject')">
                            Subject 
                            <span class="selected-count" id="subject-count">0 selected</span>
                        </div>
                        <div class="filter-dropdown" id="subject-dropdown">
                            <div class="filter-option" onclick="toggleFilter('subject', 'english')">
                                <div class="filter-checkbox" id="subject-english"></div>
                                <span class="filter-option-text">English</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'bengali')">
                                <div class="filter-checkbox" id="subject-bengali"></div>
                                <span class="filter-option-text">Bengali</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'mathematics')">
                                <div class="filter-checkbox" id="subject-mathematics"></div>
                                <span class="filter-option-text">Mathematics</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'physics')">
                                <div class="filter-checkbox" id="subject-physics"></div>
                                <span class="filter-option-text">Physics</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'chemistry')">
                                <div class="filter-checkbox" id="subject-chemistry"></div>
                                <span class="filter-option-text">Chemistry</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'biology')">
                                <div class="filter-checkbox" id="subject-biology"></div>
                                <span class="filter-option-text">Biology</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'history')">
                                <div class="filter-checkbox" id="subject-history"></div>
                                <span class="filter-option-text">History</span>
                            </div>
                            <div class="filter-option" onclick="toggleFilter('subject', 'geography')">
                                <div class="filter-checkbox" id="subject-geography"></div>
                                <span class="filter-option-text">Geography</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Connect to database
        $conn = new mysqli("localhost:3307", "root", "", "dooars_tutors");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get all tutors
        $sql = "SELECT * FROM tutors WHERE status = 'active'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0): 
    ?>
    <section class="tutors-section">
    <h2>Our Expert Tutors</h2>
    <p class="section-subtitle">Meet our qualified and experienced tutors who are passionate about helping students achieve their academic goals.</p>
    
    <div class="tutor-carousel">
        <div class="tutor-card-container" id="tutorContainer">
            <?php 
            $tutors = [];
            while ($row = $result->fetch_assoc()) {
                $tutors[] = $row;
            }
            
            foreach ($tutors as $index => $row): 
                $img = (!empty($row['profile_pic']) && file_exists('uploads/' . $row['profile_pic']))
                    ? $row['profile_pic'] : 'default.png';
            ?>
            <div class="tutor-card">
                <div class="tutor-image-section">
                    <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="tutor-img" loading="lazy">
                </div>
                <div class="tutor-info">
                    <div class="tutor-header">
    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
    <div class="tutor-badge"><?php echo htmlspecialchars($row['experience']); ?> Years Experience</div>
    
    <?php 
    // Rating display - add these columns to your database: rating DECIMAL(2,1), rating_count INT
    $rating = isset($row['rating']) ? floatval($row['rating']) : 0;
    $ratingCount = isset($row['rating_count']) ? intval($row['rating_count']) : 0;
    ?>
    
    <div class="rating-section">
        <?php if ($rating > 0): ?>
            <div class="stars-container">
                <?php 
                $fullStars = floor($rating);
                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                
                // Full stars
                for ($i = 1; $i <= $fullStars; $i++): ?>
                    <span class="star">★</span>
                <?php endfor; 
                
                // Half star
                if ($hasHalfStar): ?>
                    <span class="star">☆</span>
                <?php endif;
                
                // Empty stars
                $remainingStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                for ($i = 1; $i <= $remainingStars; $i++): ?>
                    <span class="star empty">☆</span>
                <?php endfor; ?>
            </div>
            <span class="rating-text"><?php echo number_format($rating, 1); ?></span>
            <span class="rating-count">(<?php echo $ratingCount; ?> <?php echo $ratingCount == 1 ? 'review' : 'reviews'; ?>)</span>
        <?php else: ?>
            <div class="no-rating">No ratings yet</div>
        <?php endif; ?>
    </div>
</div>
                    
                    <div class="tutor-details">
                        <div class="detail-item">
                            <span class="detail-label">Subjects:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($row['subjects']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Classes:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($row['classes']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Boards:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($row['boards']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($row['teaching_preferences']); ?></span>
                        </div>
                    </div>

                    <div class="map-container">
                        <iframe
                            src="https://www.openstreetmap.org/export/embed.html?bbox=<?php 
                                echo $row['longitude'] - 0.01; ?>,<?php 
                                echo $row['latitude'] - 0.01; ?>,<?php 
                                echo $row['longitude'] + 0.01; ?>,<?php 
                                echo $row['latitude'] + 0.01; ?>&layer=mapnik&marker=<?php 
                                echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>"
                            width="100%" 
                            height="180" 
                            loading="lazy">
                        </iframe>
                        <a href="https://www.openstreetmap.org/?mlat=<?php echo $row['latitude']; ?>&mlon=<?php echo $row['longitude']; ?>#map=15/<?php echo $row['latitude']; ?>/<?php echo $row['longitude']; ?>" 
                           target="_blank" class="map-link">
                            📍 View Location on Map
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="carousel-controls">
        <button class="carousel-btn" id="prevBtn">‹</button>
        <div class="carousel-info" id="carouselInfo">
            <div class="carousel-counter" id="counter">1 / <?php echo count($tutors); ?></div>
            <?php if (count($tutors) <= 10): ?>
                <div class="carousel-indicators" id="indicators">
                    <?php for ($i = 0; $i < count($tutors); $i++): ?>
                        <div class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></div>
                    <?php endfor; ?>
                </div>
            <?php else: ?>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: <?php echo (1 / count($tutors)) * 100; ?>%"></div>
                </div>
            <?php endif; ?>
        </div>
        <button class="carousel-btn" id="nextBtn">›</button>
    </div>
    
    <div class="swipe-hint">← Swipe or use arrow keys to navigate →</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('tutorContainer');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const counter = document.getElementById('counter');
    const indicators = document.querySelectorAll('.indicator');
    const progressFill = document.getElementById('progressFill');
    const totalCards = <?php echo count($tutors); ?>;
    const useIndicators = totalCards <= 10;
    let currentIndex = 0;
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    function updateCarousel() {
        const translateX = -currentIndex * 100;
        container.style.transform = `translateX(${translateX}%)`;
        
        // Update counter
        counter.textContent = `${currentIndex + 1} / ${totalCards}`;
        
        if (useIndicators) {
            // Update indicators for small numbers
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentIndex);
            });
        } else {
            // Update progress bar for large numbers
            const progressPercent = ((currentIndex + 1) / totalCards) * 100;
            progressFill.style.width = `${progressPercent}%`;
        }
        
        // Update buttons
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex === totalCards - 1;
    }

    function nextSlide() {
        if (currentIndex < totalCards - 1) {
            currentIndex++;
            updateCarousel();
        }
    }

    function prevSlide() {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    }

    function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
    }

    // Button controls
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);

    // Indicator controls (only if using indicators)
    if (useIndicators) {
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => goToSlide(index));
        });
    }

    // Keyboard controls
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });

    // Touch/Mouse swipe controls
    container.addEventListener('mousedown', handleStart);
    container.addEventListener('touchstart', handleStart);
    container.addEventListener('mousemove', handleMove);
    container.addEventListener('touchmove', handleMove);
    container.addEventListener('mouseup', handleEnd);
    container.addEventListener('touchend', handleEnd);
    container.addEventListener('mouseleave', handleEnd);

    function handleStart(e) {
        isDragging = true;
        startX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
        container.style.transition = 'none';
    }

    function handleMove(e) {
        if (!isDragging) return;
        
        e.preventDefault();
        currentX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
        const diffX = currentX - startX;
        const translateX = -currentIndex * 100 + (diffX / container.offsetWidth) * 100;
        container.style.transform = `translateX(${translateX}%)`;
    }

    function handleEnd(e) {
        if (!isDragging) return;
        
        isDragging = false;
        container.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        
        const diffX = currentX - startX;
        const threshold = container.offsetWidth * 0.2;
        
        if (Math.abs(diffX) > threshold) {
            if (diffX > 0) {
                prevSlide();
            } else {
                nextSlide();
            }
        } else {
            updateCarousel();
        }
    }

    // Auto-play (optional)
    let autoPlayInterval;
    
    function startAutoPlay() {
        autoPlayInterval = setInterval(() => {
            if (currentIndex < totalCards - 1) {
                nextSlide();
            } else {
                currentIndex = 0;
                updateCarousel();
            }
        }, 5000);
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
    }

    // Start auto-play
    startAutoPlay();

    // Pause auto-play on hover
    container.addEventListener('mouseenter', stopAutoPlay);
    container.addEventListener('mouseleave', startAutoPlay);

    // Initialize
    updateCarousel();
});
</script>

<?php else: ?>
    <div class="tutors-section">
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <h3 style="color: #7f8c8d; font-size: 1.5rem; margin-bottom: 10px;">No Tutors Available</h3>
            <p style="color: #bdc3c7;">Please check back later for available tutors.</p>
        </div>
    </div>
<?php endif;

$conn->close();
?>



    <!-- Boards Section -->
    <section class="boards-section">
        <div class="boards-container">
            <h2 class="section-title">Explore Teachers by Board</h2>
            
            <div class="boards-grid">
                <div class="board-card" onclick="exploreBoard('wbbse')">
                    <div class="board-icon">WB</div>
                    <h3>WBBSE</h3>
                    <p>West Bengal Board of Secondary Education - Find expert teachers for Bengali medium and English medium schools following WBBSE curriculum.</p>
                </div>
                
                <div class="board-card" onclick="exploreBoard('cbse')">
                    <div class="board-icon">CB</div>
                    <h3>CBSE</h3>
                    <p>Central Board of Secondary Education - Connect with qualified tutors specializing in CBSE syllabus and NCERT textbooks.</p>
                </div>
                
                <div class="board-card" onclick="exploreBoard('icse')">
                    <div class="board-icon">IC</div>
                    <h3>ICSE</h3>
                    <p>Indian Certificate of Secondary Education - Discover teachers experienced in ICSE curriculum and comprehensive subject knowledge.</p>
                </div>
                
                <div class="board-card" onclick="exploreBoard('ise')">
                    <div class="board-icon">IS</div>
                    <h3>ISE</h3>
                    <p>Indian School Examination - Find dedicated educators familiar with ISE board requirements and assessment patterns.</p>
                </div>
                
                <div class="board-card" onclick="exploreBoard('wbcshe')">
                    <div class="board-icon">WC</div>
                    <h3>WBCSHE</h3>
                    <p>West Bengal Council of Secondary and Higher Education - Connect with teachers for higher secondary education in West Bengal.</p>
                </div>
                
                <div class="board-card" onclick="exploreBoard('others')">
                    <div class="board-icon">+</div>
                    <h3>Other Boards</h3>
                    <p>State boards, international curricula, and specialized programs - Find tutors for various educational systems and competitive exams.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- views -->
    <div class="stats-container">
    <section class="views-section">
        <div class="views-container">
            <div class="views-card">
                <h2 class="views-title">Community Reach</h2>
                <p class="views-label">Total Welcomes</p>
                <?php
                    // Database connection
                    require_once 'db.php';

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Increment view count
                    $conn->query("UPDATE view_counter SET total_views = total_views + 1");

                    // Get updated count
                    $result = $conn->query("SELECT total_views FROM view_counter LIMIT 1");
                    $row = $result->fetch_assoc();
                    echo "<p class='php-counter'>" . number_format($row['total_views']) . "</p>";

                    // Don't close connection yet - we need it for the next section
                ?>
            </div>
        </div>
    </section>

    <section class="teachers-section">
        <div class="teachers-container">
            <div class="teachers-card">
                <h2 class="teachers-title">Our Educators</h2>
                <p class="teachers-label">Active Teachers</p>
                <?php
                    // Use the same connection from above
                    // Get teacher count
                    $result = $conn->query("SELECT COUNT(*) as teacher_count FROM tutors WHERE status = 'active'");
                    $row = $result->fetch_assoc();
                    echo "<p class='teachers-counter'>" . number_format($row['teacher_count']) . "</p>";

                    // Close connection after all queries are done
                    $conn->close();
                ?>
            </div>
        </div>
    </section>
</div>

    <section class="all-tutors-map">
  <h2>All Tutors on Map</h2>
  <div id="tutorMap" style="height: 500px; width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>
  <!-- <div id="mapStats" style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px; color: #666;">
    Loading tutors...
  </div> -->
</section>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php
$conn = new mysqli("localhost:3307", "root", "", "dooars_tutors");
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

$sql = "SELECT name, latitude, longitude, address FROM tutors WHERE status = 'active' AND latitude IS NOT NULL AND longitude IS NOT NULL";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$tutor_locations = [];
while ($row = $result->fetch_assoc()) {
    $tutor_locations[] = $row;
}
$conn->close();
?>

<script>
  const tutorLocations = <?php echo json_encode($tutor_locations); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('tutorMap');
    const statsElement = document.getElementById('mapStats');
    
    // Check if map element exists
    if (!mapElement) {
        console.error("Map element not found");
        return;
    }
    
    // Check if tutor data is available
    if (!tutorLocations || tutorLocations.length === 0) {
        statsElement.innerHTML = '<span style="color: #dc3545;">No active tutors with locations found.</span>';
        
        // Still initialize map for empty state
        const map = L.map('tutorMap').setView([26.48, 89.53], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        return;
    }
    
    // Initialize map
    const map = L.map('tutorMap').setView([26.48, 89.53], 10);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    // Store all markers for bounds calculation
    const markers = [];
    let validTutors = 0;
    let invalidTutors = 0;
    
    // Add markers for each tutor
    tutorLocations.forEach((tutor) => {
        if (tutor.latitude && tutor.longitude) {
            try {
                const lat = parseFloat(tutor.latitude);
                const lng = parseFloat(tutor.longitude);
                
                // Validate coordinates
                if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                    invalidTutors++;
                    return;
                }
                
                const marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup(`
                    <div style="font-family: Arial, sans-serif;">
                        <strong style="color: #2c3e50; font-size: 16px;">${tutor.name}</strong><br>
                        <span style="color: #666; font-size: 14px;">📍 ${tutor.address || 'No address'}</span>
                    </div>
                `);
                
                markers.push(marker);
                validTutors++;
                
            } catch (error) {
                console.error(`Error adding marker for ${tutor.name}:`, error);
                invalidTutors++;
            }
        } else {
            invalidTutors++;
        }
    });
    
    // Update stats
    // statsElement.innerHTML = `
    //     <strong>${validTutors}</strong> active tutors displayed on map
    //     ${invalidTutors > 0 ? 
    //         `<span style="color: #dc3545;"> (${invalidTutors} tutors have invalid/missing coordinates)</span>` : 
    //         '<span style="color: #28a745;"> ✓</span>'
    //     }
    // `;
    
    // Auto-fit map to show all markers
    if (markers.length > 0) {
        try {
            const group = new L.featureGroup(markers);
            const bounds = group.getBounds();
            
            if (bounds.isValid()) {
                map.fitBounds(bounds, { 
                    padding: [20, 20],
                    maxZoom: 15 
                });
            }
        } catch (error) {
            console.error("Error fitting bounds:", error);
        }
    }
});
</script>




    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>DooarsTutors</h4>
                <p>Connecting students with qualified teachers across India. Excellence in education, delivered personally.</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#become-tutor">Become a Tutor</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Boards</h4>
                <ul>
                    <li><a href="#wbbse">WBBSE</a></li>
                    <li><a href="#cbse">CBSE</a></li>
                    <li><a href="#icse">ICSE</a></li>
                    <li><a href="#ise">ISE</a></li>
                    <li><a href="#wbcshe">WBCSHE</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="#help">Help Center</a></li>
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#terms">Terms of Service</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 DooarsTutors. All rights reserved. Made by DooarsTutors.</p>
        </div>
    </footer>

    <script src="./js/script.js"></script>
</body>
</html>