
<?php
// Replace the existing PHP query section at the top of temp.txt with this:

$servername = "localhost:3307";
$username = "root";        
$password = "";            
$database = "dooars_tutors"; 

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = $_POST['search'] ?? '';
$sql = "SELECT *, profession_details FROM tutors WHERE 
           profession_details IS NOT NULL AND profession_details != '' AND (
           name LIKE ? OR
           JSON_EXTRACT(profession_details, '$.tutor.subjects') LIKE ? OR 
           JSON_EXTRACT(profession_details, '$.tutor.classes') LIKE ? OR 
           JSON_EXTRACT(profession_details, '$.tutor.boards') LIKE ?
           )";

$stmt = $conn->prepare($sql);
$searchParam = "%{$search}%";
$stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DooarsTutors - Find Your Perfect Tutor</title>  
    <link rel="icon" type="image/x-icon" href="./favicon_io/favicon.ico">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="./css/style_1.css">
    <!-- <link rel="stylesheet" href="./css/style_2.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            
        }

        button {
    -webkit-tap-highlight-color: transparent;
}

button:focus,
button:focus-visible,
button:active {
    outline: none;
    box-shadow: none;
}
.mobile-menu-btn,
.mobile-menu-btn span {
    -webkit-tap-highlight-color: transparent;
    outline: none;
}

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            /* background: linear-gradient(135deg, #22577a 0%, #38a3a5 50%, #57cc99 100%); */
            background: #8aafdf;
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
            background: black;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 20px;
        }

        .nav-links li {
            position: relative;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            display: block;
        }

        .nav-links a:hover {
            background: #7B3F00;
            color: white;
            transform: translateY(-2px);
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            min-width: 180px;
            z-index: 1001;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            list-style: none;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .dropdown-menu a:hover {
            background: #7B3F00;
            color: white;
            transform: none;
        }

        .dropdown-menu li:first-child a {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .dropdown-menu li:last-child a {
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Mobile Menu Styles */
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
        }

        .mobile-menu-btn span {
            width: 25px;
            height: 3px;
            background: #333;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-nav {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background: white;
            z-index: 1999;
            transition: all 0.3s ease;
            box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100vh; 
        }

        .mobile-nav-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: #7b3f00;
            color: white;
            flex-shrink: 0;
        }

        .mobile-nav-content {
            padding: 20px 0;
            flex-grow: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch; /* smooth scroll on iOS */
        }

        .mobile-nav ul {
            list-style: none;
        }

        .mobile-nav li {
            margin-bottom: 10px;
        }

        .mobile-nav a {
            display: block;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .mobile-nav a:hover {
            background: #f5f5f5;
            color: #7B3F00;
        }

        /* Mobile Dropdown Styles */
        .mobile-nav .dropdown-menu {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            background: #f9f9f9;
            margin-left: 20px;
            border-radius: 5px;
            margin-top: 5px;
        }

        .mobile-nav .dropdown-menu a {
            padding: 10px 15px;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .mobile-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .mobile-nav.active {
                right: 0;
            }

            .mobile-menu-btn.active span:nth-child(1) {
                transform: rotate(-45deg) translate(-5px, 6px);
            }

            .mobile-menu-btn.active span:nth-child(2) {
                opacity: 0;
            }

            .mobile-menu-btn.active span:nth-child(3) {
                transform: rotate(45deg) translate(-5px, -6px);
            }
        }

        /* Hero Section */
        .hero {
            padding: 150px 2rem 100px;
            text-align: center;
            color: white;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        /* Search Section */
        .search-section {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 2rem auto;
            max-width: 900px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease 0.4s both;
        }

        .search-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            outline: none;
            transition: all 0.3s ease;
            min-width: 300px;
        }

        .search-input:focus {
            background: white;
            box-shadow: 0 0 20px rgba(56, 163, 165, 0.3);
            transform: translateY(-2px);
        }

        .location-select, .search-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
        }

        .location-select {
            background: rgba(255, 255, 255, 0.9);
            color: #22577a;
            min-width: 150px;
        }

        .search-btn {
            background: linear-gradient(135deg, #38a3a5, #57cc99);
            color: white;
            box-shadow: 0 4px 15px rgba(56, 163, 165, 0.4);
        }

        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(56, 163, 165, 0.6);
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-width: 200px;
            position: relative;
        }

        .filter-label {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-label:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .filter-label::after {
            content: '▼';
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }

        .filter-label.active::after {
            transform: rotate(180deg);
        }

        .filter-dropdown {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(34, 87, 122, 0.2);
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin-bottom: 0.25rem;
        }

        .filter-option:hover {
            background: rgba(87, 204, 153, 0.1);
            padding-left: 0.75rem;
        }

        .filter-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #38a3a5;
            border-radius: 4px;
            position: relative;
            background: white;
            transition: all 0.2s ease;
        }

        .filter-checkbox.checked {
            background: linear-gradient(135deg, #38a3a5, #57cc99);
            border-color: #57cc99;
        }

        .filter-checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .filter-option-text {
            color: #22577a;
            font-weight: 500;
            font-size: 0.9rem;
            user-select: none;
        }

        .selected-count {
            background: linear-gradient(135deg, #57cc99, #80ed99);
            color: #22577a;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: center;
            }
            
            .search-container {
                flex-direction: column;
            }
            
            .search-input {
                min-width: auto;
            }
        }

        /* Boards Section */
        .boards-section {
            padding: 5rem 2rem;
            background: #7B3F00;
        }

        .boards-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease;
        }

        .boards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .board-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeInUp 1s ease;
        }

        .board-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .board-icon {
            width: 80px;
            height: 80px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: #7B3F00;
            font-weight: bold;
        }

        .board-card h3 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .board-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            background: #7B3F00;
            backdrop-filter: blur(20px);
            padding: 3rem 2rem 2rem;
            color: white;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #ffffff;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: #8da4dd;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 2rem;
            padding-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .search-container {
                flex-direction: column;
            }
            
            .search-input {
                min-width: auto;
            }
            
            .filters {
                flex-direction: column;
                align-items: center;
            }
        }







        .tutors-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        /* background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); */
        min-height: 100vh;
    }

    button {
    -webkit-tap-highlight-color: transparent;
}

button:focus,
button:focus-visible,
button:active {
    outline: none;
    box-shadow: none;
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
        background:#7B3F00;
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
        background: #FFE001;
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
        margin-bottom: 0px;
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
        background: #7B3F00;
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0px;
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
        background: #7B3F00;
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
        color: #7B3F00;
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
        background: #7B3F00;
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
        background: #7B3F00;
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
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1600px;
    width: 100%;
    margin: 0 auto;
    padding: 4rem 2rem;
    background: rgba(123, 63, 0, 0.7); /* fallback color */
    align-items: center;
    overflow: hidden; /* prevent image overflow */
    z-index: 1; /* bring content to front */
}

.stats-container::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("https://images.pexels.com/photos/433452/pexels-photo-433452.jpeg");
    background-size: cover;
    background-position: center;
    opacity: 0.2; /* control opacity here */
    z-index: -1; /* behind the content */
}


.views-section, .teachers-section {
    background: transparent;
    padding: 0;
}

.t_c{
    background:#e2e8f05d;
}

.views-container, .teachers-container {
    width: 100%;
    /* max-width: none; */
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
    max-width:500px;
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

@media (min-width: 800px) {
    .views-container {
        margin-left:200px;
    }
}



        .section-wrapper {
        position: relative;
        max-width: 100%;
        margin-top: 8rem;
        z-index: 1;
        }

        .background-layer {
        position: absolute;
        top: -100px;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("mda.png");
        background-size: cover;
        background-position: center;
        opacity: 0.75; /* Adjust transparency */
        z-index: -1; /* Behind container */
        pointer-events: none;
        }

        .container {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(244, 234, 213, 0.35); /* <-- Semi-transparent white */
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            z-index: 2;
        }

        .header {
            /* background: #7B3F00; */
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            background: rgba(123, 63, 0, 0.72);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(4.4px);
            -webkit-backdrop-filter: blur(4.4px);
            border: 1px solid rgba(123, 63, 0, 0.3);
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>'); */
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .main-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .search-content {
            padding: 40px 30px;
        }

        .universal-search {
            margin-bottom: 30px;
        }

        .search-wrapper {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-input {
            width: 100%;
            padding: 18px 50px 18px 20px;
            font-size: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            outline: none;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .search-input:focus {
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 20px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 40px 0;
            gap: 20px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #d1d5db, transparent);
        }

        .divider-text {
            color: #6b7280;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .advanced-filters {
            /* background: #f8fafc; */
            border-radius: 16px;
            padding: 30px;
            /* border: 1px solid #e2e8f0; */
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(3.5px);
            -webkit-backdrop-filter: blur(3.5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .filters-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-input,
        .form-select {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #7B3F00;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-select {
            cursor: pointer;
        }

        .search-button-container {
            text-align: center;
            margin-top: 30px;
        }

        .search-button {
            background: #7B3F00;
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(123, 63, 0, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(123, 63, 0, 0.4);
        }

        .search-button:active {
            transform: translateY(0);
        }


        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 16px;
                margin-top: 8rem;
            }

            .header {
                padding: 30px 20px;
            }

            .main-title {
                font-size: 2rem;
            }

            .search-content {
                padding: 30px 20px;
            }

            .advanced-filters {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .quick-filter-tags {
                gap: 8px;
            }

            .quick-tag {
                font-size: 11px;
                padding: 6px 12px;
            }
        }

        .loading {
            display: none;
            text-align: center;
            color: #6b7280;
            margin-top: 20px;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            border-top-color: #4f46e5;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }











                /* Results Container */
        /* Results Container */
#results {
    max-width: 100%;
    margin: 20px auto;
    padding: 0 20px;
}

/* Loading, Error, No Results States */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top: 3px solid #7B3F00;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: #64748b;
    font-size: 16px;
    font-weight: 500;
}

.error-state {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.no-results-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.6;
}

.no-results-title {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
}

.no-results-text {
    color: #64748b;
    font-size: 16px;
}

/* Results Section */
.results-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.results-header {
    background: #7B3F00;
    border-bottom: 1px solid #e2e8f0;
    padding: 20px 24px;
}

.results-title {
    font-size: 20px;
    font-weight: 600;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 12px;
}

.results-count {
    background: #9E3D3F;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* Horizontal Swipeable Container */
.teachers-container {
    padding: 20px 0;
    position: relative;
}

.teachers-scroll {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 0 24px 20px 24px;
    -webkit-overflow-scrolling: touch;
}

.teachers-scroll::-webkit-scrollbar {
    height: 8px;
}

.teachers-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.teachers-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.teachers-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Teacher Card - Horizontal Layout */
.teacher-profile {
    flex: 0 0 320px; /* Increased width to accommodate longer text */
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    cursor: pointer;
}


.contact-wrapper {
    margin-top: 15px;
    text-align: right;
}

.contact-button {
    display: inline-block;
    padding: 10px 16px;
    text-align: center;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s;
}


.teacher-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    border-color: #3b82f6;
}

/* Profile Picture */
.profile-picture {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #7B3F00;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 20px;
    margin: 0 auto 12px auto;
    border: 3px solid #e2e8f0;
}

/* Teacher Info */
.teacher-name {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    text-align: center;
    margin-bottom: 8px;
    line-height: 1.2;
}

.rating-section {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.rating-stars .star {
    color: #fbbf24;
    font-size: 14px;
}

.rating-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
}

.rating-reviews {
    color: #64748b;
    font-size: 12px;
}

/* Compact Details */
.profile-details {
    display: flex;
    flex-direction: column;
    gap: 12px; /* Increased gap for better spacing */
}

.detail-item {
    display: flex;
    flex-direction: column; /* Changed to column layout */
    align-items: flex-start; /* Align to start */
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
    gap: 4px; /* Add gap between label and content */
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 11px;
    font-weight: 500;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-content {
    font-size: 12px;
    font-weight: 500;
    color: #1e293b;
    line-height: 1.4; /* Better line height for multiple lines */
    word-wrap: break-word; /* Allow word wrapping */
    width: 100%; /* Take full width */
    /* Removed text-overflow: ellipsis and white-space: nowrap */
}

/* Special styling for subjects and modes to handle multiple items better */
.detail-content[title*=","] {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

/* Contact Button */
.contact-button {
    margin-top: 16px;
    padding: 10px;
    background: #7B3F00;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.contact-button:hover {
    background:rgb(150, 78, 1);
    transform: translateY(-1px);
}

/* Scroll Navigation Buttons */
.scroll-nav {
    display: flex;
    justify-content: center;
    gap: 12px;
    padding: 0 24px 20px 24px;
}

.scroll-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #64748b;
}

.scroll-btn:hover {
    background: #f8fafc;
    border-color: #3b82f6;
    color: #3b82f6;
}

.scroll-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
    #results {
        padding: 0 12px;
        margin: 12px auto;
    }
    
    .teachers-scroll {
        padding: 0 12px 20px 12px;
    }
    
    .teacher-profile {
        flex: 0 0 300px; /* Adjusted for mobile */
        padding: 16px;
    }
    
    .results-header {
        padding: 16px;
    }
    
    .scroll-nav {
        padding: 0 12px 16px 12px;
    }
}

@media (max-width: 480px) {
    .teacher-profile {
        flex: 0 0 280px; /* Adjusted for small mobile */
        padding: 14px;
    }
    
    .profile-picture {
        width: 50px;
        height: 50px;
        font-size: 18px;
    }
    
    .teacher-name {
        font-size: 16px;
    }
}




.logo-blend {
  height: 70px;
  margin-top: 10px;
  mix-blend-mode: screen;
  filter: brightness(1) contrast(10);
}

.logo-blend2{
    height: 70px;
  margin-top: 10px;
  mix-blend-mode: multiply;
  /* margin-left:-150px;  */
}

@media (min-width: 770px) {
    .logo-blend2{
    height: 70px;
  margin-top: 10px;
  mix-blend-mode: multiply;
  margin-left:-50px; 
}
}

/* Music fields container - 2 column layout */
.music-fields-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

/* Music fields styling */
.music-fields {
    display: none;
}

.music-fields.show {
    display: block;
}

/* When music is selected, show fields in 2 columns */
.music-active .music-fields-container {
    display: grid;
}

.music-active .music-fields {
    display: block;
}

/* Singing fields - full width spanning 2 columns */
.singing-fields {
    display: none;
    grid-column: 1 / -1; /* Span full width */
}

.singing-fields.show {
    display: block;
}

/* When singing is selected, show field spanning full width */
.singing-active .singing-fields {
    display: block;
    grid-column: 1 / -1;
}

/* Form group styling */
.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-select,
.form-input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-select:focus,
.form-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

/* Responsive design */
@media (max-width: 768px) {
    .music-fields-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .singing-fields {
        grid-column: 1;
    }
}

/* Drawing Canvas Overlay */
        #drawingCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 1000;
            cursor: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #drawingCanvas.active {
            pointer-events: all;
            opacity: 1;
        }

        /* Floating Drawing Button */
        .drawing-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1001;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #003153;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: pulse 2s infinite;
        }

        .drawing-fab:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
        }

        @keyframes pulse {
            0% { box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 8px 25px rgba(102, 126, 234, 0.8), 0 0 0 10px rgba(102, 126, 234, 0.1); }
            100% { box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); }
        }

        /* Tooltip */
        .drawing-fab::before {
            content: "Try drawing on this page! 🎨";
            position: absolute;
            right: 80px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .drawing-fab:hover::before {
            opacity: 1;
        }

        /* Drawing Controls Panel */
        .drawing-controls {
            position: fixed;
            bottom: 120px;
            right: 30px;
            z-index: 1002;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: rgba(0, 0, 0, 0.25);
            padding: 25px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transform: scale(0) translateY(20px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: bottom right;
            min-width: 200px;
        }

        .drawing-controls.open {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .drawing-controls button {
            padding: 12px 16px;
            border: none;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.25);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .drawing-controls button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .drawing-controls button.active {
            background: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        /* Close button */
        .close-controls {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ff4757;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .close-controls:hover {
            background: #ff3742;
            transform: scale(1.1);
        }

        .color-palette {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: white;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .brush-size {
            margin-top: 10px;
        }

        .brush-size input {
            width: 100%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 5px;
            height: 30px;
        }

        .brush-size label {
            color: white;
            font-size: 12px;
            margin-bottom: 5px;
            display: block;
        }

        /* Custom Cursor */
        .custom-cursor {
            position: fixed;
            pointer-events: none;
            z-index: 1002;
            border-radius: 50%;
            border: 2px solid white;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .custom-cursor.visible {
            opacity: 1;
        }
         /* Responsive */
        @media (max-width: 768px) {
            .drawing-fab {
                width: 60px;
                height: 60px;
                font-size: 24px;
                bottom: 20px;
                right: 20px;
            }
            .drawing-fab::before {
                right: 70px;
                font-size: 12px;
            }
            .drawing-controls {
                bottom: 90px;
                right: 20px;
                padding: 20px;
                min-width: 180px;
            }
            .color-palette {
                gap: 5px;
            }
            .color-option {
                width: 25px;
                height: 25px;
            }
        }
    </style>

</head>
<body style="background:rgb(241, 228, 201);">  
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><img src="logo_black.jpg" class="logo-blend2"></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="centres.php">Centres</a></li>
                <li class="dropdown">
                    <a href="arts&culture.php">Arts & Culture</a>
                    <ul class="dropdown-menu">
                        <li><a href="music&singing.php">Music/Singing</a></li>
                        <li><a href="dance.php">Dance</a></li>
                        <li><a href="arts.php">Visual Arts</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="sports&health.php">Sports & Health</a>
                    <ul class="dropdown-menu">
                        <li><a href="sports.php">Sports</a></li>
                        <li><a href="gym&yoga.php">Gym / Yoga</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#register">Become a Tutor</a>
                    <ul class="dropdown-menu">
                        <li><a href="register.php">As an Individual</a></li>
                        <li><a href="register_org.php">As Organization</a></li>
                    </ul>
                </li>
                <!-- <li><a href="#become-tutor">Become a Tutor</a></li> -->
                <li><a href="login.php">Login</a></li>
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
        <img src="logo_white.jpg" class="logo-blend" alt="Logo">
        </div>

        <div class="mobile-nav-content">
            <ul class="mobile-nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="centres.php">Centres</a></li>
                <li class="dropdown">
                    <a href="arts&culture.php">Arts & Culture</a>
                    <ul class="dropdown-menu">
                        <li><a href="music&singing.php">Music/Singing</a></li>
                        <li><a href="dance.php">Dance</a></li>
                        <li><a href="arts.php">Visual Arts</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="sports&health.php">Sports & Health</a>
                    <ul class="dropdown-menu">
                        <li><a href="sports.php">Sports</a></li>
                        <li><a href="gym&yoga.php">Gym / Yoga</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#register">Become a Tutor</a>
                    <ul class="dropdown-menu">
                        <li><a href="register.php">As an Individual</a></li>
                        <li><a href="register_org.php">As Organization</a></li>
                    </ul>
                </li>
                <!-- <li><a href="#become-tutor">Become a Tutor</a></li> -->
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Drawing Canvas -->
    <canvas id="drawingCanvas"></canvas>
    
    <!-- Custom Cursor -->
    <div class="custom-cursor" id="customCursor"></div>

    <!-- Floating Drawing Button -->
    <button class="drawing-fab" id="drawingFab">🎨</button>

    <!-- Drawing Controls Panel -->
    <div class="drawing-controls" id="drawingControls">
        <button class="close-controls" id="closeControls">×</button>
        <button id="toggleDraw" class="toggle-btn">Start Drawing</button>
        <button id="clearCanvas">Clear</button>
        
        <div class="color-palette">
            <div class="color-option selected" data-color="#ff4757" style="background: #ff4757;"></div>
            <div class="color-option" data-color="#2ed573" style="background: #2ed573;"></div>
            <div class="color-option" data-color="#1e90ff" style="background: #1e90ff;"></div>
            <div class="color-option" data-color="#ffa502" style="background: #ffa502;"></div>
            <div class="color-option" data-color="#ff6348" style="background: #ff6348;"></div>
            <div class="color-option" data-color="#a55eea" style="background: #a55eea;"></div>
            <div class="color-option" data-color="#ffffff" style="background: #ffffff;"></div>
            <div class="color-option" data-color="#2c2c54" style="background: #2c2c54;"></div>
        </div>
        
        <div class="brush-size">
            <label>Brush Size</label>
            <input type="range" id="brushSize" min="2" max="50" value="15">
        </div>
    </div>

    <script>
        class InteractiveDrawing {
            constructor() {
                this.canvas = document.getElementById('drawingCanvas');
                this.ctx = this.canvas.getContext('2d');
                this.isDrawing = false;
                this.isDrawMode = false;
                this.currentColor = '#ff4757';
                this.currentSize = 15;
                this.lastX = 0;
                this.lastY = 0;
                
                this.init();
                this.setupEventListeners();
            }

            init() {
                this.resizeCanvas();
                window.addEventListener('resize', () => this.resizeCanvas());
                
                // Setup canvas drawing properties
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                this.ctx.globalCompositeOperation = 'source-over';
            }

            resizeCanvas() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
                
                // Re-apply drawing properties after resize
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                this.ctx.globalCompositeOperation = 'source-over';
            }

            setupEventListeners() {
                const drawingFab = document.getElementById('drawingFab');
                const drawingControls = document.getElementById('drawingControls');
                const closeControls = document.getElementById('closeControls');
                const toggleBtn = document.getElementById('toggleDraw');
                const clearBtn = document.getElementById('clearCanvas');
                const brushSize = document.getElementById('brushSize');
                const colorOptions = document.querySelectorAll('.color-option');
                const customCursor = document.getElementById('customCursor');

                // Show/hide controls
                drawingFab.addEventListener('click', () => {
                    drawingControls.classList.add('open');
                    drawingFab.style.transform = 'scale(0)';
                    setTimeout(() => {
                        drawingFab.style.display = 'none';
                    }, 200);
                });

                closeControls.addEventListener('click', () => {
                    drawingControls.classList.remove('open');
                    drawingFab.style.display = 'flex';
                    setTimeout(() => {
                        drawingFab.style.transform = 'scale(1)';
                    }, 100);
                    
                    // Also stop drawing if active
                    if (this.isDrawMode) {
                        this.isDrawMode = false;
                        this.canvas.classList.remove('active');
                        toggleBtn.textContent = 'Start Drawing';
                        toggleBtn.classList.remove('active');
                        customCursor.classList.remove('visible');
                    }
                });

                // Toggle drawing mode
                toggleBtn.addEventListener('click', () => {
                    this.isDrawMode = !this.isDrawMode;
                    this.canvas.classList.toggle('active', this.isDrawMode);
                    toggleBtn.textContent = this.isDrawMode ? 'Stop Drawing' : 'Start Drawing';
                    toggleBtn.classList.toggle('active', this.isDrawMode);
                    customCursor.classList.toggle('visible', this.isDrawMode);
                });

                // Clear canvas
                clearBtn.addEventListener('click', () => {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                });

                // Brush size
                brushSize.addEventListener('input', (e) => {
                    this.currentSize = e.target.value;
                    this.updateCursor();
                });

                // Color selection
                colorOptions.forEach(option => {
                    option.addEventListener('click', () => {
                        colorOptions.forEach(opt => opt.classList.remove('selected'));
                        option.classList.add('selected');
                        this.currentColor = option.dataset.color;
                    });
                });

                // Drawing events
                this.canvas.addEventListener('mousedown', this.startDrawing.bind(this));
                this.canvas.addEventListener('mousemove', this.draw.bind(this));
                this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this));
                this.canvas.addEventListener('mouseout', this.stopDrawing.bind(this));

                // Touch events for mobile
                this.canvas.addEventListener('touchstart', this.handleTouch.bind(this));
                this.canvas.addEventListener('touchmove', this.handleTouch.bind(this));
                this.canvas.addEventListener('touchend', this.stopDrawing.bind(this));

                // Custom cursor
                this.canvas.addEventListener('mousemove', (e) => {
                    if (this.isDrawMode) {
                        customCursor.style.left = e.clientX + 'px';
                        customCursor.style.top = e.clientY + 'px';
                    }
                });

                this.updateCursor();
            }

            updateCursor() {
                const customCursor = document.getElementById('customCursor');
                const size = this.currentSize;
                customCursor.style.width = size + 'px';
                customCursor.style.height = size + 'px';
                customCursor.style.backgroundColor = this.currentColor + '40';
                customCursor.style.borderColor = this.currentColor;
            }

            startDrawing(e) {
                if (!this.isDrawMode) return;
                
                this.isDrawing = true;
                [this.lastX, this.lastY] = this.getCoordinates(e);
            }

            draw(e) {
                if (!this.isDrawing || !this.isDrawMode) return;
                
                const [currentX, currentY] = this.getCoordinates(e);
                
                this.ctx.beginPath();
                this.ctx.moveTo(this.lastX, this.lastY);
                this.ctx.lineTo(currentX, currentY);
                this.ctx.strokeStyle = this.currentColor;
                this.ctx.lineWidth = this.currentSize;
                this.ctx.stroke();
                
                [this.lastX, this.lastY] = [currentX, currentY];
            }

            stopDrawing() {
                this.isDrawing = false;
            }

            handleTouch(e) {
                e.preventDefault();
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                    e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                this.canvas.dispatchEvent(mouseEvent);
            }

            getCoordinates(e) {
                const rect = this.canvas.getBoundingClientRect();
                return [
                    e.clientX - rect.left,
                    e.clientY - rect.top
                ];
            }
        }

        // Initialize the drawing application
        new InteractiveDrawing();

        // Add interactivity to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>

    <div class="section-wrapper">
    <div class="background-layer"></div>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1 class="main-title">Learn about the Culture</h1>
                <p class="subtitle">Discover qualified educators for personalized learning</p>
            </div>
        </div>
        <div class="search-content">         

            <!-- Advanced Filters -->
            <div class="advanced-filters">
                <form id="searchForm">
                    <div class="form-group">
                        <label for="name" class="form-label">Teacher Name / School name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            placeholder="Enter name and search"
                        >
                    </div><br>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="profession_type" class="form-label">Profession Type</label>
                            <select id="profession_type" name="profession_type" class="form-select">
                                <option value="">Select Profession Type</option>
                                <option value="art_teacher">Art Teacher</option>
                                <option value="visual_arts">Visual Arts Instructor</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">Location</label>
                            <select id="city" name="city" class="form-select">
                                <option value="">Select Location</option>
                                <option value="Alipurduar">Alipurduar</option>
                                <option value="Coochbehar">Coochbehar</option>
                                <option value="Falakata">Falakata</option>
                                <option value="Sonapur">Sonapur</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="art_type" class="form-label">Type of Art</label>
                            <select id="art_type" name="art_type" class="form-select">
                                <option value="">Select Art Type</option>
                                <option value="Drawing">Drawing</option>
                                <option value="Painting">Painting</option>
                                <option value="Sculpture">Sculpture</option>
                                <option value="Digital Art">Digital Art</option>
                                <option value="Watercolor">Watercolor</option>
                                <option value="Oil Painting">Oil Painting</option>
                                <option value="Sketching">Sketching</option>
                                <option value="Calligraphy">Calligraphy</option>
                                <option value="Mixed Media">Mixed Media</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="days_per_week" class="form-label">Days per Week</label>
                            <select id="days_per_week" name="days_per_week" class="form-select">
                                <option value="">Select Days per Week</option>
                                <option value="1">1 Day</option>
                                <option value="2">2 Days</option>
                                <option value="3">3 Days</option>
                                <option value="4">4 Days</option>
                                <option value="5">5 Days</option>
                                <option value="6">6 Days</option>
                                <option value="7">7 Days</option>
                            </select>
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
            fetch('search_arts.php', {
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
        
        // Updated displayResults function for only dance search results
function displayResults(teachers, searchType = 'Search Results') {
    const resultsDiv = document.getElementById('results');
    
    // Debug: Log the teachers data
    console.log('Teachers data:', teachers);
    console.log('Number of teachers:', teachers ? teachers.length : 'teachers is null/undefined');
    
    if (!teachers || teachers.length === 0) {
        resultsDiv.innerHTML = `
            <div class="no-results">
                <div class="no-results-title">No Dance Teachers Found</div>
                <div class="no-results-text">Try adjusting your search criteria for dance teachers and schools</div>
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
            <div class="teachers-container t_c">
                <div class="teachers-scroll" id="teachersScroll">
    `;
    
    teachers.forEach((teacher, index) => {
        // Debug: Log each teacher
        console.log(`Teacher ${index}:`, teacher);
        
        const rating = teacher.rating || 0;
        const stars = '★'.repeat(Math.floor(rating)) + '☆'.repeat(5 - Math.floor(rating));
        const initials = teacher.name ? teacher.name.split(' ').map(n => n[0]).join('').toUpperCase() : 'NA';
        
        // Handle professions more defensively
        const professions = teacher.professions || {};
        
        const professionTypes = [];
        const allDanceTypes = [];
        const genderPreferences = [];
        const daysPerWeek = [];
        
        // Process dance_teacher
        if (professions.dance_teacher) {
            professionTypes.push('Dance Teacher');
            if (professions.dance_teacher.dance_type) {
                allDanceTypes.push(professions.dance_teacher.dance_type);
            }
            if (professions.dance_teacher.gender) {
                genderPreferences.push(professions.dance_teacher.gender);
            }
            if (professions.dance_teacher.days_per_week) {
                daysPerWeek.push(professions.dance_teacher.days_per_week);
            }
        }
        
        // Process dance_school
        if (professions.dance_school) {
            professionTypes.push('Dance School');
            if (professions.dance_school.dance_type) {
                allDanceTypes.push(professions.dance_school.dance_type);
            }
            if (professions.dance_school.gender) {
                genderPreferences.push(professions.dance_school.gender);
            }
            if (professions.dance_school.days_per_week) {
                daysPerWeek.push(professions.dance_school.days_per_week);
            }
        }
        
        const uniqueDanceTypes = [...new Set(allDanceTypes)];
        const uniqueGenderPreferences = [...new Set(genderPreferences)];
        const uniqueDaysPerWeek = [...new Set(daysPerWeek)];
        
        const professionType = professionTypes.length > 0 ? professionTypes.join(', ') : 'Dance Professional';
        const availableServices = teacher.available_services || professionType;
        
        html += `
            <div class="teacher-profile">
                <div class="profile-picture">${initials}</div>
                <div class="teacher-name">
                    ${teacher.name || 'Unknown Teacher'}
                </div>

                <div class="rating-section">
                    <div class="rating-stars">
                        <span class="star">${stars}</span>
                    </div>
                    <span class="rating-value">${rating}/5</span>
                    <span class="rating-reviews">(${teacher.rating_count || 0})</span>
                </div>

                <div class="profile-details">
                    <div class="detail-item">
                        <span class="detail-label">Services</span>
                        <span class="detail-content" title="${availableServices}">${availableServices.length > 50 ? availableServices.substring(0, 50) + '...' : availableServices}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Experience</span>
                        <span class="detail-content">${teacher.experience || 'Not specified'}</span>
                    </div>
                    
                    ${uniqueGenderPreferences.length > 0 ? `
                    <div class="detail-item">
                        <span class="detail-label">Gender Preference</span>
                        <span class="detail-content">${uniqueGenderPreferences.join(', ')}</span>
                    </div>
                    ` : ''}
                    
                    ${uniqueDaysPerWeek.length > 0 ? `
                    <div class="detail-item">
                        <span class="detail-label">Days per Week</span>
                        <span class="detail-content">${uniqueDaysPerWeek.join(', ')} day${uniqueDaysPerWeek.length > 1 || uniqueDaysPerWeek[0] > 1 ? 's' : ''}</span>
                    </div>
                    ` : ''}
                    
                    <div class="detail-item">
                        <span class="detail-label">Location</span>
                        <span class="detail-content">${teacher.city || 'Not specified'}</span>
                    </div>
                </div>

                <div class="contact-wrapper">
                    <a href="teacher-profile.php?id=${teacher.id || teacher.teacher_id || encodeURIComponent(teacher.name || 'unknown')}" class="contact-button">Check Profile</a>
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
    console.log('Results displayed successfully');
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

        $sql = "SELECT * FROM tutors 
        WHERE status = 'active' 
        ORDER BY rating DESC, rating_count DESC 
        LIMIT 15";

        $result = $conn->query($sql);

        if ($result->num_rows > 0): 
    ?>

    <section class="tutors-section">
        <h2 style="color:#12181e">Our Expert Tutors</h2>
        <p class="section-subtitle" style="color:#12181e">Meet our qualified and experienced tutors who are passionate about helping students achieve their academic goals.</p>
        
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
                                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDTy16l_Zhg8IgEWj2nu_MnBJjCRg_SrB8&q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>&zoom=15"
                                width="100%"
                                height="180"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy">
                            </iframe>
                            <a href="https://www.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>&z=15"
                            target="_blank" class="map-link">
                                📍 View Location on Google Maps
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
        
        <!-- <div class="swipe-hint">← Swipe or use arrow keys to navigate →</div> -->
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
// Touch/Mouse swipe controls
let initialY = 0;
let isVerticalScroll = false;
let autoPlayTimeout; // Add this to track restart timeout

container.addEventListener('mousedown', handleStart);
container.addEventListener('touchstart', handleStart);
container.addEventListener('mousemove', handleMove);
container.addEventListener('touchmove', handleMove, { passive: false });
container.addEventListener('mouseup', handleEnd);
container.addEventListener('touchend', handleEnd);
container.addEventListener('mouseleave', handleEnd);

function handleStart(e) {
    isDragging = true;
    isVerticalScroll = false;
    startX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
    initialY = e.type === 'mousedown' ? e.clientY : e.touches[0].clientY;
    container.style.transition = 'none';
    
    // Stop auto-play and clear any pending restart
    stopAutoPlay();
    clearTimeout(autoPlayTimeout);
}

function handleMove(e) {
    if (!isDragging) return;
    
    currentX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
    const currentY = e.type === 'mousemove' ? e.clientY : e.touches[0].clientY;
    
    const diffX = currentX - startX;
    const diffY = currentY - initialY;
    
    // Determine if this is a vertical scroll
    if (!isVerticalScroll && Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 10) {
        isVerticalScroll = true;
        isDragging = false;
        container.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        updateCarousel();
        return;
    }
    
    // Only prevent default and handle horizontal drag if it's not vertical scroll
    if (!isVerticalScroll && Math.abs(diffX) > 10) {
        e.preventDefault();
        const translateX = -currentIndex * 100 + (diffX / container.offsetWidth) * 100;
        container.style.transform = `translateX(${translateX}%)`;
    }
}

function handleEnd(e) {
    if (!isDragging || isVerticalScroll) {
        isDragging = false;
        isVerticalScroll = false;
        return;
    }
    
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
    
    // Clear any existing timeout and restart auto-play after user interaction ends
    clearTimeout(autoPlayTimeout);
    autoPlayTimeout = setTimeout(() => {
        startAutoPlay();
    }, 3000); // 3 second delay before restarting auto-play
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
                    <p class="teachers-label">Active Members</p>
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
    <h2>Our Community on Map</h2>
    <div id="tutorMap" style="height: 500px; width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>
</section>

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
    
    let map;
    let infoWindow;
    
    function initTutorMap() {
        const mapElement = document.getElementById('tutorMap');
        const statsElement = document.getElementById('mapStats');
        
        // Check if map element exists
        if (!mapElement) {
            console.error("Map element not found");
            return;
        }
        
        // Check if tutor data is available
        if (!tutorLocations || tutorLocations.length === 0) {
            if (statsElement) {
                statsElement.innerHTML = '<span style="color: #dc3545;">No active tutors with locations found.</span>';
            }
            
            // Still initialize map for empty state
            map = new google.maps.Map(mapElement, {
                center: { lat: 26.48, lng: 89.53 },
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            return;
        }
        
        // Initialize map
        map = new google.maps.Map(mapElement, {
            center: { lat: 26.48, lng: 89.53 },
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        
        infoWindow = new google.maps.InfoWindow();
        
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
                    
                    const marker = new google.maps.Marker({
                        position: { lat: lat, lng: lng },
                        map: map,
                        title: tutor.name,
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                                <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 2 C10.5 2 6 6.5 6 12 C6 20 16 30 16 30 S26 20 26 12 C26 6.5 21.5 2 16 2 Z" 
                                        fill="#7B3F00" stroke="white" stroke-width="2"/>
                                    <circle cx="16" cy="12" r="4" fill="white"/>
                                </svg>
                            `),
                            scaledSize: new google.maps.Size(40, 40),
                            anchor: new google.maps.Point(16, 30)
                        }
                    });
                    
                    const contentString = `
                        <div style="font-family: Arial, sans-serif; min-width: 200px;">
                            <strong style="color: #2c3e50; font-size: 16px;">${tutor.name}</strong><br>
                            <span style="color: #666; font-size: 14px;">📍 ${tutor.address || 'No address'}</span>
                        </div>
                    `;
                    
                    marker.addListener('click', () => {
                        infoWindow.setContent(contentString);
                        infoWindow.open(map, marker);
                    });
                    
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
        
        // Update stats (uncomment if you have statsElement)
        // if (statsElement) {
        //     statsElement.innerHTML = `
        //         <strong>${validTutors}</strong> active tutors displayed on map
        //         ${invalidTutors > 0 ? 
        //             `<span style="color: #dc3545;"> (${invalidTutors} tutors have invalid/missing coordinates)</span>` : 
        //             '<span style="color: #28a745;"> ✓</span>'
        //         }
        //     `;
        // }
        
        // Auto-fit map to show all markers
        if (markers.length > 0) {
            try {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => {
                    bounds.extend(marker.getPosition());
                });
                
                map.fitBounds(bounds);
                
                // Set maximum zoom level
                google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                    if (map.getZoom() > 15) {
                        map.setZoom(15);
                    }
                });
                
            } catch (error) {
                console.error("Error fitting bounds:", error);
            }
        }
    }
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', initTutorMap);
</script>

<!-- Load Google Maps API -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTy16l_Zhg8IgEWj2nu_MnBJjCRg_SrB8&callback=initTutorMap">
</script>




    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <img src="logo_white.jpg" class="logo-blend" alt="Logo">
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.querySelector('.mobile-menu-btn');
        const mobileNav = document.querySelector('.mobile-nav');
        const overlay = document.querySelector('.mobile-overlay');

        const toggleMenu = () => {
            mobileNav.classList.toggle('active');
            overlay.classList.toggle('active');
            menuBtn.classList.toggle('active');
            document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
        };

        const closeMenu = () => {
            mobileNav.classList.remove('active');
            overlay.classList.remove('active');
            menuBtn.classList.remove('active');
            document.body.style.overflow = '';
        };

        // Toggle menu on button click
        menuBtn.addEventListener('click', toggleMenu);

        // Close menu on overlay click
        overlay.addEventListener('click', closeMenu);

        // Smooth scroll & close menu on link click
        const isHashLink = href => href && href.startsWith('#');
        document.querySelectorAll('.nav-links a, .mobile-nav a').forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');

                // If internal anchor link, scroll smoothly
                if (isHashLink(href)) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                }

                // Close menu after click
                closeMenu();
            });
        });
    });
</script>
</body>
</html>