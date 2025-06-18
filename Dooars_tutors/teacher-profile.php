<?php
// Database connection (adjust credentials as needed)
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "dooars_tutors";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get teacher ID from URL parameter
$teacher_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$teacher_id) {
    header("Location: index.php");
    exit();
}

// Fetch teacher details from database
$stmt = $pdo->prepare("SELECT * FROM tutors WHERE id = ? OR name = ?");
$stmt->execute([$teacher_id, urldecode($teacher_id)]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    header("Location: index.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $student_name = $_POST['student_name'];
    $rating = (int)$_POST['rating'];
    $review_text = $_POST['review_text'];
    
    $stmt = $pdo->prepare("INSERT INTO reviews (teacher_id, student_name, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$teacher['id'], $student_name, $rating, $review_text]);
    
    // Update teacher's average rating
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE teacher_id = ?");
    $stmt->execute([$teacher['id']]);
    $rating_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("UPDATE tutors SET rating = ?, rating_count = ? WHERE id = ?");
    $stmt->execute([round($rating_data['avg_rating'], 1), $rating_data['count'], $teacher['id']]);
    
    header("Location: teacher-profile.php?id=" . $teacher['id']);
    exit();
}

// Fetch reviews
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE teacher_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$teacher['id']]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate initials for profile picture
$initials = '';
$name_parts = explode(' ', $teacher['name']);
foreach ($name_parts as $part) {
    $initials .= strtoupper($part[0]);
}

// Generate star rating
$full_stars = floor($teacher['rating']);
$empty_stars = 5 - $full_stars;
$stars_html = str_repeat('★', $full_stars) . str_repeat('☆', $empty_stars);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($teacher['name']); ?> - Teacher Profile</title>
    <link rel="icon" type="image/x-icon" href="./favicon_io/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e2e8f0;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .header {
            background: #003153;
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .back-button {
            position: absolute;
            left: 30px;
            top: 30px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: 600;
            margin: 0 auto 24px;
            border: 3px solid rgba(255,255,255,0.2);
        }

        .teacher-name {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .teacher-title {
            font-size: 18px;
            opacity: 0.8;
            margin-bottom: 24px;
            font-weight: 400;
        }

        .rating-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .rating-stars {
            font-size: 24px;
            color: #fbbf24;
        }

        .rating-value {
            font-size: 20px;
            font-weight: 600;
        }

        .rating-reviews {
            font-size: 16px;
            opacity: 0.7;
        }

        .content {
            padding: 48px 30px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 48px;
        }

        .left-column {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .right-column {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .info-card h3 {
            color: #1e293b;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card p {
            color: #475569;
            line-height: 1.6;
            font-size: 15px;
        }

        .icon {
            font-size: 18px;
            color: #003153;
        }

        .about-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 32px;
        }

        .about-section h3 {
            color: #1e293b;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .about-section p {
            color: #475569;
            line-height: 1.7;
            font-size: 16px;
        }

        .contact-section {
            background: #003153;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
        }

        .contact-section h3 {
            color: white;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .contact-section p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 24px;
        }

        .contact-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .contact-btn {
            background: white;
            color: #003153;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .contact-btn.primary {
            background: #0ea5e9;
            color: white;
        }

        .contact-btn.primary:hover {
            background: #0284c7;
        }

        .subjects-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .subject-tag {
            background: #003153;
            color: white;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 500;
        }

        .map-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            height: 300px;
        }

        .map-section h3 {
            color: #1e293b;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #map {
            width: 100%;
            height: 220px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .reviews-section {
            margin-top: 48px;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .reviews-header h3 {
            color: #1e293b;
            font-size: 24px;
            font-weight: 700;
        }

        .add-review-btn {
            background: #003153;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-review-btn:hover {
            background: #002844;
        }

        .review-form {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            display: none;
        }

        .review-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #003153;
            box-shadow: 0 0 0 3px rgba(0, 49, 83, 0.1);
        }

        .star-rating {
    display: flex;
    gap: 4px;
    font-size: 24px;
    margin-bottom: 8px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.2s ease;
}

.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #fbbf24;
}

        .form-buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #003153;
            color: white;
        }

        .btn-primary:hover {
            background: #002844;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .reviews-container {
    max-height: 500px; /* Adjust this height as needed */
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    padding: 8px;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding-right: 4px; /* Small padding to account for scrollbar */
}

.review-item {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    flex-shrink: 0; /* Prevent items from shrinking */
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.review-author {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
}

.review-rating {
    color: #fbbf24;
    font-size: 16px;
}

.review-date {
    color: #64748b;
    font-size: 13px;
}

.review-text {
    color: #475569;
    line-height: 1.6;
}

/* Custom scrollbar styling */
.reviews-container::-webkit-scrollbar {
    width: 6px;
}

.reviews-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.reviews-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.reviews-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* For better mobile experience */
@media (max-width: 768px) {
    .reviews-container {
        max-height: 400px;
    }
}

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .container {
                margin: 10px;
                border-radius: 12px;
            }
            
            .header {
                padding: 32px 20px;
            }
            
            .content {
                padding: 32px 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .contact-btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }

            .reviews-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
        }

        @media (max-width: 768px) {
            .back-button {
                position: static;
                margin-bottom: 20px;
                align-self: flex-start;
            }

            .header {
                text-align: left;
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Search
            </a>
            
            <div class="profile-avatar">
                <?php echo $initials; ?>
            </div>
            
            <h1 class="teacher-name"><?php echo htmlspecialchars($teacher['name']); ?></h1>
            <p class="teacher-title">Professional Educator</p>
            
            <div class="rating-section">
                <div class="rating-stars"><?php echo $stars_html; ?></div>
                <span class="rating-value"><?php echo $teacher['rating']; ?>/5</span>
                <span class="rating-reviews">(<?php echo $teacher['rating_count']; ?> ratings)</span>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content">
            <div class="main-grid">
                <!-- Left Column -->
                <div class="left-column">
                    <!-- Basic Information Grid -->
                    <div class="info-grid">
                        <div class="info-card">
                            <h3><i class="fas fa-book icon"></i> Subjects</h3>
                            <div class="subjects-list">
                                <?php 
                                $subjects = explode(',', $teacher['subjects']);
                                foreach ($subjects as $subject) {
                                    echo '<span class="subject-tag">' . htmlspecialchars(trim($subject)) . '</span>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="info-card">
                            <h3><i class="fas fa-graduation-cap icon"></i> Classes</h3>
                            <p><?php echo htmlspecialchars($teacher['classes']); ?></p>
                        </div>

                        <div class="info-card">
                            <h3><i class="fas fa-map-marker-alt icon"></i> City / Town</h3>
                            <p><?php echo htmlspecialchars($teacher['city']); ?></p>
                        </div>

                        <div class="info-card">
                            <h3><i class="fas fa-clock icon"></i> Experience</h3>
                            <p><?php echo $teacher['experience']; ?> Years of Teaching Experience</p>
                        </div>

                        <div class="info-card">
                            <h3><i class="fas fa-laptop icon"></i> Teaching Mode</h3>
                            <p><?php echo htmlspecialchars($teacher['teaching_preferences']); ?></p>
                        </div>

                        <div class="info-card">
                            <h3><i class="fas fa-rupee-sign icon"></i> Fees</h3>
                            <p>₹<?php echo isset($teacher['fees']) ? $teacher['fees'] : 'Contact for details'; ?> per session</p>
                        </div>
                    </div>

                    <!-- About Section -->
                    <?php if (!empty($teacher['about']) || !empty($teacher['description'])): ?>
                    <div class="about-section">
                        <h3>About <?php echo htmlspecialchars(explode(' ', $teacher['name'])[0]); ?></h3>
                        <p><?php echo htmlspecialchars($teacher['about'] ?? $teacher['description'] ?? 'Experienced teacher dedicated to helping students achieve their academic goals.'); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                    <!-- Contact Section -->
                    <div class="contact-section">
                        <h3>Ready to Start Learning?</h3>
                        <p>Contact <?php echo htmlspecialchars(explode(' ', $teacher['name'])[0]); ?> to schedule your first lesson</p>
                        
                        <div class="contact-buttons">
                            <a href="tel:<?php echo $teacher['phone']; ?>" class="contact-btn primary">
                                <i class="fas fa-phone"></i> Call Now
                            </a>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $teacher['phone']); ?>" class="contact-btn" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <?php if (!empty($teacher['email'])): ?>
                            <a href="mailto:<?php echo $teacher['email']; ?>" class="contact-btn">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="map-section">
                        <h3><i class="fas fa-map-marked-alt icon"></i> Location</h3>
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="reviews-section">
                <div class="reviews-header">
                    <h3>Student Reviews</h3>
                    <button class="add-review-btn" onclick="toggleReviewForm()">
                        <i class="fas fa-plus"></i> Add Review
                    </button>
                </div>

                <!-- Review Form -->
                <div class="review-form" id="reviewForm">
                    <form method="POST">
                        <div class="form-group">
                            <label for="student_name">Your Name</label>
                            <input type="text" id="student_name" name="student_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Rating</label>
                            <div class="star-rating">
                                <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                                <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                                <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                                <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                                <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
                            </div>

                        </div>
                        
                        <div class="form-group">
                            <label for="review_text">Your Review</label>
                            <textarea id="review_text" name="review_text" rows="4" placeholder="Share your experience with this teacher..." required></textarea>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                            <button type="button" class="btn btn-secondary" onclick="toggleReviewForm()">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Reviews List with Scrollable Container -->
                <div class="reviews-container">
                    <div class="reviews-list">
                        <?php if (empty($reviews)): ?>
                            <div class="review-item">
                                <p style="text-align: center; color: #64748b;">No reviews yet. Be the first to review this teacher!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <div>
                                        <div class="review-author"><?php echo htmlspecialchars($review['student_name']); ?></div>
                                        <div class="review-rating">
                                            <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                        </div>
                                    </div>
                                    <div class="review-date">
                                        <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="review-text">
                                    <?php echo htmlspecialchars($review['review_text']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTy16l_Zhg8IgEWj2nu_MnBJjCRg_SrB8&callback=initTutorMap" async defer></script>
    
    <script>
        // Initialize Google Map
        function initTutorMap() {
            const teacherLocation = '<?php echo htmlspecialchars($teacher['city']); ?>';
            const geocoder = new google.maps.Geocoder();
            
            geocoder.geocode({ address: teacherLocation }, function(results, status) {
                if (status === 'OK') {
                    const map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 13,
                        center: results[0].geometry.location,
                        styles: [
                            {
                                featureType: 'all',
                                elementType: 'geometry.fill',
                                stylers: [{ color: '#f8fafc' }]
                            },
                            {
                                featureType: 'water',
                                elementType: 'geometry',
                                stylers: [{ color: '#e2e8f0' }]
                            }
                        ]
                    });
                    
                    const marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        title: '<?php echo htmlspecialchars($teacher['name']); ?>'
                    });
                } else {
                    document.getElementById('map').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #64748b;">Map could not be loaded</div>';
                }
            });
        }

        // Toggle review form
        function toggleReviewForm() {
            const form = document.getElementById('reviewForm');
            form.classList.toggle('active');
        }

        // Star rating interaction
        document.addEventListener('DOMContentLoaded', function() {
            const starInputs = document.querySelectorAll('.star-rating input');
            const starLabels = document.querySelectorAll('.star-rating label');

            starLabels.forEach((label, index) => {
                label.addEventListener('mouseenter', function() {
                    // Light up from first star to current star
                    starLabels.forEach((l, i) => {
                        l.style.color = i <= index ? '#fbbf24' : '#d1d5db';
                    });
                });

                label.addEventListener('mouseleave', function() {
                    const checkedInput = document.querySelector('.star-rating input:checked');
                    if (checkedInput) {
                        const checkedIndex = Array.from(starInputs).indexOf(checkedInput);
                        starLabels.forEach((l, i) => {
                            l.style.color = i <= checkedIndex ? '#fbbf24' : '#d1d5db';
                        });
                    } else {
                        starLabels.forEach(l => l.style.color = '#d1d5db');
                    }
                });

                // Add click handler to properly set the rating
                label.addEventListener('click', function() {
                    const input = document.getElementById(label.getAttribute('for'));
                    input.checked = true;
                    
                    // Update visual state immediately
                    starLabels.forEach((l, i) => {
                        l.style.color = i <= index ? '#fbbf24' : '#d1d5db';
                    });
                });
            });
        });


        // Contact button analytics
        document.querySelectorAll('.contact-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('Contact button clicked:', this.textContent.trim());
            });
        });
    </script>
</body>
</html>