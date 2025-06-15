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
    <link rel="stylesheet" href="./css/style_2.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                    <a href="#skills">Arts & Culture</a>
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
                    <a href="#skills">Arts & Culture</a>
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

    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1 class="main-title">Find Your Ideal Teacher</h1>
                <p class="subtitle">Discover qualified educators for personalized learning</p>
            </div>
        </div>

        <div class="search-content">         

            <!-- Advanced Filters -->
            <div class="advanced-filters">
                <!-- <h3 class="filters-title">Advanced Search Filters</h3> -->
                <form id="searchForm">
                    <div class="form-group">
                        <label for="teacherName" class="form-label">Teacher Name</label>
                        <input 
                            type="text" 
                            id="teacherName" 
                            name="teacherName" 
                            class="form-input" 
                            placeholder="Enter teacher's full name"
                        >
                    </div><br>
                    <div class="form-grid">

                        <div class="form-group">
                            <label for="board" class="form-label">Educational Board</label>
                            <select id="board" name="board" class="form-select">
                                <option value="">Select Board</option>
                                <option value="WB">West Bengal Board</option>
                                <option value="CBSE">CBSE</option>
                                <option value="ICSE">ICSE</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">Location</label>
                            <select id="city" name="city" class="form-select">
                                <option value="">Select Location</option>
                                <option value="Alipurduar">Alipurduar</option>
                                <option value="Coochbehar">Coochbehar</option>
                                <option value="Falakata">Falakata</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="classGrade" class="form-label">Class/Grade</label>
                            <input 
                                type="text" 
                                id="classGrade" 
                                name="classGrade" 
                                class="form-input" 
                                placeholder="Class 5, Class 10, Class 12, Madhyamik"
                            >
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                class="form-input" 
                                placeholder="Mathematics, Physics, English, Bengali"
                            >
                        </div>
                    </div>

                    <div class="search-button-container">
                        <button type="submit" class="search-button">
                            Search Teachers
                        </button>
                    </div>
                </form>
            </div>          
        </div>
    </div>

    <div id="results"></div>
             

    <script>

        // Advanced search form functionality
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultsDiv = document.getElementById('results');
            
            // Show loading state
            resultsDiv.innerHTML = `
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Searching with advanced filters...</div>
                </div>
            `;
            
            // Send AJAX request
            fetch('search_teachers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                displayResults(data, 'Advanced Search Results');
            })
            .catch(error => {
                resultsDiv.innerHTML = `<div class="error-state">Search Error: ${error.message}</div>`;
            });
        });
        
        // Replace your displayResults function with this version
        function displayResults(teachers, searchType = 'Search Results') {
            const resultsDiv = document.getElementById('results');
            
            if (teachers.length === 0) {
                resultsDiv.innerHTML = `
                    <div class="no-results">
                        <div class="no-results-icon">🔍</div>
                        <div class="no-results-title">No Teachers Found</div>
                        <div class="no-results-text">Try adjusting your search criteria or use different keywords</div>
                    </div>
                `;
                return;
            }
            
            let html = `
                <div class="results-section">
                    <div class="results-header">
                        <div class="results-title">
                            ${searchType}
                            <span class="results-count">${teachers.length}</span>
                        </div>
                    </div>
                    <div class="teachers-container">
                        <div class="teachers-scroll" id="teachersScroll">
            `;
            
            teachers.forEach(teacher => {
                const stars = '★'.repeat(Math.floor(teacher.rating)) + '☆'.repeat(5 - Math.floor(teacher.rating));
                const initials = teacher.name.split(' ').map(n => n[0]).join('').toUpperCase();
                
                html += `
                    <div class="teacher-profile">
                        <div class="profile-picture">${initials}</div>
                        <div class="teacher-name">${teacher.name}</div>

                        <div class="rating-section">
                            <div class="rating-stars">
                                <span class="star">${stars}</span>
                            </div>
                            <span class="rating-value">${teacher.rating}/5</span>
                            <span class="rating-reviews">(${teacher.rating_count})</span>
                        </div>

                        <div class="profile-details">
                            <div class="detail-item">
                                <span class="detail-label">Subjects</span>
                                <span class="detail-content" title="${teacher.subjects}">${teacher.subjects}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Classes</span>
                                <span class="detail-content">${teacher.classes}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span class="detail-content">${teacher.city}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Experience</span>
                                <span class="detail-content">${teacher.experience} Years</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Mode</span>
                                <span class="detail-content" title="${teacher.teaching_preferences}">${teacher.teaching_preferences}</span>
                            </div>
                        </div>

                        <!-- Fixed Call Now Button -->
                        <div class="contact-wrapper">
                            <a href="tel:${teacher.phone}" class="contact-button">Call now</a>
                        </div>
                    </div>
                `;
            });
                
            html += `
                        </div>
                        
                    </div>
                </div>
            `;
            
            resultsDiv.innerHTML = html;
        }

        // Scroll navigation functions
        function scrollTeachers(direction) {
            const container = document.getElementById('teachersScroll');
            const scrollAmount = 300;
            
            if (direction === 'left') {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }
    </script>

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
                                
                                <div class="rating-section" style ="justify-content: left;">
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

    <script> const tutorLocations = <?php echo json_encode($tutor_locations); ?>; </script>

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