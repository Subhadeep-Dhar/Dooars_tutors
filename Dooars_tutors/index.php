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
    <style>
        .tutors-section {
    padding: 40px;
    background: #f2f2f2;
}
.tutor-card-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: flex-start;
}
.tutor-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 20px;
    width: 260px;
    text-align: center;
    transition: 0.3s;
}
.tutor-card:hover {
    transform: translateY(-5px);
}
.tutor-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
    border: 2px solid #eee;
}
.tutor-info h3 {
    margin-bottom: 8px;
    font-size: 18px;
}
.tutor-info p {
    font-size: 14px;
    color: #555;
    margin: 4px 0;
}

.map-container {
    margin-top: 10px;
    border-radius: 6px;
    overflow: hidden;
}
.map-container iframe {
    border-radius: 6px;
}
.map-container small {
    display: block;
    margin-top: 5px;
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

    <!-- <div class="search-results">
        <php if ($result->num_rows > 0): ?>
            <php while ($row = $result->fetch_assoc()): ?>
                <div class="tutor-card">
                    <h3><  = htmlspecialchars($row['name']) ?></h3>
                    <p><strong>Subjects:</strong> <  = htmlspecialchars($row['subjects']) ?></p>
                    <p><strong>Classes:</strong> <  = htmlspecialchars($row['classes']) ?></p>
                    <p><strong>Boards:</strong> <  = htmlspecialchars($row['boards']) ?></p>
                    <p><strong>Experience:</strong> <  = htmlspecialchars($row['experience']) ?> years</p>
                    <p><strong>Location:</strong> <  = htmlspecialchars($row['location']) ?></p>
                    <!-- Add more fields as needed --
                </div>
            <  php endwhile; ?>
        <  php else: ?>
            <p>No tuto  s found for "<= htmlspecialchars($search) ?>".</p>
        <  php endif; ?>
    </div> -->

    <?php
// Connect to database
$conn = new mysqli("localhost:3307", "root", "", "dooars_tutors");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all tutors
$sql = "SELECT * FROM tutors WHERE status = 'active'";
$result = $conn->query($sql);

if ($result->num_rows > 0): ?>
<section class="tutors-section">
    <h2>Our Tutors</h2>
    <div class="tutor-card-container">
        <?php while ($row = $result->fetch_assoc()): 
            $img = !empty($row['profile_pic']) ? $row['profile_pic'] : 'C:\Users\Subhadeep Dhar\Downloads\contact-icon-illustration-isolated\11539820.png';
        ?>
            <div class="tutor-card">
                <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="Profile" class="tutor-img">
                <div class="tutor-info">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Subjects:</strong> <?php echo htmlspecialchars($row['subjects']); ?></p>
                    <p><strong>Classes:</strong> <?php echo htmlspecialchars($row['classes']); ?></p>
                    <p><strong>Boards:</strong> <?php echo htmlspecialchars($row['boards']); ?></p>
                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                    <div class="map-container">
    <iframe
        src="https://www.openstreetmap.org/export/embed.html?bbox=<?php 
            echo $row['longitude'] - 0.01; ?>,<?php 
            echo $row['latitude'] - 0.01; ?>,<?php 
            echo $row['longitude'] + 0.01; ?>,<?php 
            echo $row['latitude'] + 0.01; ?>&layer=mapnik&marker=<?php 
            echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>"
        style="border:1px solid #ccc;" 
        width="100%" 
        height="200" 
        loading="lazy">
    </iframe>
    <small><a href="https://www.openstreetmap.org/?mlat=<?php echo $row['latitude']; ?>&mlon=<?php echo $row['longitude']; ?>#map=15/<?php echo $row['latitude']; ?>/<?php echo $row['longitude']; ?>" target="_blank">
        View on OpenStreetMap</a></small>
</div>

                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['teaching_preferences']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<?php else: ?>
    <p>No tutors found.</p>
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

    <!-- views-section.php -->
<section class="views-section" style="padding: 20px; text-align: center; background: #f9f9f9;">
    <div style="display: inline-block; padding: 20px; border: 2px solid #ddd; border-radius: 10px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>How many has turned to us</h2>

        <?php
        // Database connection
        $host = "localhost:3307";
        $user = "root";
        $pass = "";
        $dbname = "dooars_tutors";

        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Increment view count
        $conn->query("UPDATE view_counter SET total_views = total_views + 1");

        // Get updated count
        $result = $conn->query("SELECT total_views FROM view_counter LIMIT 1");
        $row = $result->fetch_assoc();
        echo "<p style='font-size: 28px; color: #007bff; font-weight: bold;'>{$row['total_views']} views</p>";

        $conn->close();
        ?>
    </div>
</section>


    <section class="all-tutors-map">
        <h2>All Tutors on Map</h2>
        <div id="tutorMap" style="height: 500px; width: 100%;"></div>
    </section>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <?php
        $conn = new mysqli("localhost:3307", "root", "", "dooars_tutors");
        if ($conn->connect_error) die("DB connection failed");

        $sql = "SELECT name, latitude, `longitude`, address FROM tutors WHERE status = 'active' AND latitude IS NOT NULL AND `longitude` IS NOT NULL";
        $result = $conn->query($sql);

        $tutor_locations = [];
        while ($row = $result->fetch_assoc()) {
            $tutor_locations[] = $row;
        }
    ?>

    <script> const tutorLocations = <?php echo json_encode($tutor_locations); ?>; </script>

    <script> const tutorLocations = <?php echo json_encode($tutor_locations); ?>; </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('tutorMap').setView([26.4922, 89.5320], 10); // Default center (adjust as needed)

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add markers
            tutorLocations.forEach(tutor => {
                const marker = L.marker([tutor.latitude, tutor.longitude]).addTo(map);
                marker.bindPopup(`<strong>${tutor.name}</strong><br>${tutor.address}`);
            });
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