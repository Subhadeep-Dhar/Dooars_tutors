<!DOCTYPE html>
<html lang="en">

<head>
    <title>DooarsTutors - Become a tutor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./favicon_io/favicon.ico">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="./css/reg_style.css"> -->
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
            
            background: #8aafdf;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: var(--glassmorphism);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.8s ease 0.2s both;
        }

        .header {
            background: #003153;
            backdrop-filter: blur(10px);
            color: white;
            padding: 40px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease 0.4s both;
        }

        .header p {
            font-size: 18px;
            opacity: 0.9;
            font-weight: 300;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .form-container {
            padding: 50px 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .form-section {
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease both;
        }

        .form-section:nth-child(1) { animation-delay: 0.1s; }
        .form-section:nth-child(2) { animation-delay: 0.2s; }
        .form-section:nth-child(3) { animation-delay: 0.3s; }
        .form-section:nth-child(4) { animation-delay: 0.4s; }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            background: #003153;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid transparent;
            border-image: linear-gradient(135deg, var(--primary-light), var(--primary-lightest)) 1;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-light));
            animation: expandLine 0.8s ease 0.5s both;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            position: relative;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(87, 204, 153, 0.15);
            transform: translateY(-2px);
            background: var(--white);
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 14px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .checkbox-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .checkbox-item:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-lightest));
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(87, 204, 153, 0.3);
        }

        .checkbox-item:hover::before {
            left: 100%;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-dark);
            cursor: pointer;
        }

        .checkbox-item.checked {
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-light));
            color: var(--white);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(56, 163, 165, 0.4);
        }

        .radio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 12px;
}

.radio-item {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 2px solid transparent;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 14px;
    font-weight: 500;
    position: relative;
    overflow: hidden;
}

.radio-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s ease;
}

.radio-item:hover {
    background: linear-gradient(135deg, var(--primary-light), var(--primary-lightest));
    color: var(--white);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(87, 204, 153, 0.3);
}

.radio-item:hover::before {
    left: 100%;
}

.radio-item input[type="radio"] {
    margin-right: 12px;
    width: 18px;
    height: 18px;
    accent-color: var(--primary-dark);
    cursor: pointer;
}

.radio-item.selected {
    background: linear-gradient(135deg, var(--primary-medium), var(--primary-light));
    color: var(--white);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(56, 163, 165, 0.4);
}


        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .map-section {
            margin-top: 32px;
        }

        #map {
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px var(--shadow);
            margin-top: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        #map:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px var(--shadow);
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(34, 87, 122, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(34, 87, 122, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-lightest));
            color: var(--white);
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(87, 204, 153, 0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-light));
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(56, 163, 165, 0.5);
        }

        .btn-submit {
            width: 100%;
            padding: 20px;
            font-size: 18px;
            font-weight: 700;
            margin-top: 40px;
            background: #003153;
            color: white;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(34, 87, 122, 0.4);
            position: relative;
            z-index: 1;
        }

        .btn-submit:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(34, 87, 122, 0.6);
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-light));
            border-radius: 16px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .btn-submit:hover::after {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .container {
                margin: 0;
                border-radius: 0;
            }

            .header {
                padding: 32px 24px;
            }

            .form-container {
                padding: 32px 24px;
            }

            .two-column {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .checkbox-grid {
                grid-template-columns: 1fr;
            }

            #map {
                height: 300px;
            }
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 500;
            border: 1px solid transparent;
            backdrop-filter: blur(10px);
            animation: slideIn 0.5s ease;
        }

        .alert-error {
            background: rgba(255, 99, 99, 0.15);
            border-color: rgba(255, 99, 99, 0.3);
            color: #d63031;
        }

        .alert-success {
            background: rgba(87, 204, 153, 0.15);
            border-color: rgba(87, 204, 153, 0.3);
            color: #00b894;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        @keyframes expandLine {
            from { width: 0; }
            to { width: 60px; }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Additional modern touches */
        .form-group:hover label {
            color: var(--primary-medium);
            transform: translateX(4px);
        }

        input[type="text"]:not(:placeholder-shown),
        input[type="tel"]:not(:placeholder-shown),
        input[type="email"]:not(:placeholder-shown),
        select:not([value=""]),
        textarea:not(:placeholder-shown) {
            border-color: var(--primary-light);
            background: var(--white);
        }

        /* Floating label effect */
        .form-group.floating {
            position: relative;
        }

        .form-group.floating label {
            position: absolute;
            top: 18px;
            left: 20px;
            transition: all 0.3s ease;
            pointer-events: none;
            background: var(--white);
            padding: 0 4px;
            z-index: 1;
        }

        .form-group.floating input:focus + label,
        .form-group.floating input:not(:placeholder-shown) + label {
            top: -8px;
            left: 16px;
            font-size: 12px;
            color: var(--primary-medium);
            font-weight: 600;
        }


        .logo-blend {
  height: 100px;
  margin-top: 10px;
  mix-blend-mode: screen;
  filter: brightness(1) contrast(10);
}




.dropdown-section {
    margin-bottom: 20px;
    border: 2px solid #e0e6ed;
    border-radius: 12px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.dropdown-section:hover {
    /* border-color: #57cc99; */
    box-shadow: 0 4px 15px rgba(87, 204, 153, 0.1);
}

.dropdown-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    color: #003153;
    transition: all 0.3s ease;
    user-select: none;
}

.dropdown-header:hover {
    background: #003153;
    color: white;
}

.dropdown-arrow {
    transition: transform 0.3s ease;
    font-size: 14px;
}

.dropdown-section.active .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-content {
    padding: 0 20px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.dropdown-section.active .dropdown-content {
    max-height: 500px;
    padding: 20px;
}

.dropdown-section.active .dropdown-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkbox-item input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.checkbox-item label {
    margin: 0;
    cursor: pointer;
    font-weight: normal;
}

.selection-summary {
    margin-left: 10px;
    font-size: 12px;
    color: #003153;
    font-weight: normal;
}

@media (max-width: 768px) {
    .two-column {
        grid-template-columns: 1fr;
    }
    
    .checkbox-grid {
        grid-template-columns: 1fr;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .dropdown-content {
        padding: 0 16px;
    }
    
    .dropdown-section.active .dropdown-content {
        padding: 16px;
        max-height: 350px;
    }
}

.form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
        }

.form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

.conditional-section {
            display: none;
            margin-top: 20px;
            padding: 16px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        
        .conditional-section.active {
            display: block;
        }
     </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="DOOARS TUTORS (7)_page-0001 (1) (1).jpg" class="logo-blend" alt="Logo">
            <p>Join our community of dedicated educators and add value to the society</p>
        </div>

        <div class="form-container">
            <form action="save_teacher.php" method="POST" id="tutorForm">
                <div class="form-section">
                    <h2 class="section-title">Personal Information</h2>

                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name">
                    </div>

                    <div class="two-column">
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address </label>
                            <input type="email" id="email" name="email" placeholder="your.email@example.com">
                        </div>
                    </div>

                    <!-- Profession Dropdown -->
                    <div class="dropdown-section" id="profession-section">
                        <div class="dropdown-header" onclick="toggleDropdown('profession-section')">
                            <span>Profession <span class="selection-summary" id="profession-summary"></span></span>
                            <span class="dropdown-arrow">▼</span>
                        </div>
                        <div class="dropdown-content">
                            <div class="checkbox-grid">
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Tutor" id="tutor" onchange="handleProfessionChange()">
                                    <label for="tutor">Tutor</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Sports Coach" id="sports-coach" onchange="handleProfessionChange()">
                                    <label for="sports-coach">Sports Coach</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Trainer" id="trainer" onchange="handleProfessionChange()">
                                    <label for="trainer">Trainer</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Dance Teacher" id="dance-teacher" onchange="handleProfessionChange()">
                                    <label for="dance-teacher">Dance Teacher</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Music Teacher" id="music-teacher" onchange="handleProfessionChange()">
                                    <label for="music-teacher">Music Teacher</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Singing Teacher" id="singing-teacher" onchange="handleProfessionChange()">
                                    <label for="singing-teacher">Singing Teacher</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="Art Teacher" id="art-teacher" onchange="handleProfessionChange()">
                                    <label for="art-teacher">Art Teacher</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="profession[]" value="others" id="profession-others" onchange="handleProfessionChange()">
                                    <label for="profession-others">Others</label>
                                </div>
                            </div>
                        </div>
                    </div>
    

                    <div class="form-group">
                        <label for="experience">Experience </label>
                        <input type="text" id="experience" name="experience" required placeholder="Enter teaching experience">
                    </div>
                

                    <div class="conditional-section" id="tutor-section">
                        <h3>Teaching Credentials</h3>
                        <div class="dropdown-section" id="boards-section">
                            <div class="dropdown-header" onclick="toggleDropdown('boards-section')">
                                <span>Education Boards <span class="selection-summary" id="boards-summary"></span></span>
                                <span class="dropdown-arrow">▼</span>
                            </div>
                            <div class="dropdown-content">
                                <div class="checkbox-grid">
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="boards[]" value="WB" id="wb">
                                        <label for="wb">West Bengal</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="boards[]" value="CBSE" id="cbse">
                                        <label for="cbse">CBSE</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="boards[]" value="ICSE" id="icse">
                                        <label for="icse">ICSE</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Classes Dropdown -->
                        <div class="dropdown-section" id="classes-section">
                            <div class="dropdown-header" onclick="toggleDropdown('classes-section')">
                                <span>Classes <span class="selection-summary" id="classes-summary"></span></span>
                                <span class="dropdown-arrow">▼</span>
                            </div>
                            <div class="dropdown-content">
                                <div class="checkbox-grid">
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="1" id="class-1" onchange="updateSummary('classes')">
                                        <label for="class-1">Class 1</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="2" id="class-2" onchange="updateSummary('classes')">
                                        <label for="class-2">Class 2</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="3" id="class-3" onchange="updateSummary('classes')">
                                        <label for="class-3">Class 3</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="4" id="class-4" onchange="updateSummary('classes')">
                                        <label for="class-4">Class 4</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="5" id="class-5" onchange="updateSummary('classes')">
                                        <label for="class-5">Class 5</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="6" id="class-6" onchange="updateSummary('classes')">
                                        <label for="class-6">Class 6</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="7" id="class-7" onchange="updateSummary('classes')">
                                        <label for="class-7">Class 7</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="8" id="class-8" onchange="updateSummary('classes')">
                                        <label for="class-8">Class 8</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="9" id="class-9" onchange="updateSummary('classes')">
                                        <label for="class-9">Class 9</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="10" id="class-10" onchange="updateSummary('classes')">
                                        <label for="class-10">Class 10</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="11" id="class-11" onchange="updateSummary('classes')">
                                        <label for="class-11">Class 11</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="12" id="class-12" onchange="updateSummary('classes')">
                                        <label for="class-12">Class 12</label>
                                    </div>
                                
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="others" id="class-others" onchange="toggleOtherClass(); updateSummary('classes')">
                                        <label for="class-others">Others</label>
                                    </div>
                                </div>
                                <div class="form-group" id="other-class-group" style="display: none; margin-top: 16px;">
                                    <label for="other-class">Please specify:</label>
                                    <input type="text" id="other-class" name="other_class" placeholder="Enter other class details">
                                </div>
                            </div>
                        </div>
                            

                            
                        <div class="dropdown-section" id="subjects-section">
                            <div class="dropdown-header" onclick="toggleDropdown('subjects-section')">
                                <span>Subjects <span class="selection-summary" id="subjects-summary"></span></span>
                                <span class="dropdown-arrow">▼</span>
                            </div>
                            <div class="dropdown-content">
                                <div class="checkbox-grid">
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="English" id="english">
                                        <label for="english">English</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Hindi" id="hindi">
                                        <label for="hindi">Hindi</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Bengali" id="bengali">
                                        <label for="bengali">Bengali</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Mathematics" id="mathematics" onchange="updateSummary('subjects')">
                                        <label for="mathematics">Mathematics</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Physics" id="physics" onchange="updateSummary('subjects')">
                                        <label for="physics">Physics</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Chemistry" id="chemistry" onchange="updateSummary('subjects')">
                                        <label for="chemistry">Chemistry</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Biology" id="biology" onchange="updateSummary('subjects')">
                                        <label for="biology">Biology</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="General Science" id="general-science" onchange="updateSummary('subjects')">
                                        <label for="general-science">General Science</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="EVS" id="evs" onchange="updateSummary('subjects')">
                                        <label for="evs">EVS</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Computer Science" id="computer-science" onchange="updateSummary('subjects')">
                                        <label for="computer-science">Computer Science</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="History" id="history" onchange="updateSummary('subjects')">
                                        <label for="history">History</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Geography" id="geography" onchange="updateSummary('subjects')">
                                        <label for="geography">Geography</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Economics" id="economics" onchange="updateSummary('subjects')">
                                        <label for="economics">Economics</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Accountancy" id="accountancy" onchange="updateSummary('subjects')">
                                        <label for="accountancy">Accountancy</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Business Studies" id="business-studies" onchange="updateSummary('subjects')">
                                        <label for="business-studies">Business Studies</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Education" id="education" onchange="updateSummary('subjects')">
                                        <label for="education">Education</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Philosophy" id="philosophy" onchange="updateSummary('subjects')">
                                        <label for="philosophy">Philosophy</label>
                                    </div>
                
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="subjects[]" value="Others" id="others" onchange="toggleOtherSubject(); updateSummary('subjects')">
                                        <label for="others">Others</label>
                                    </div>
                                </div>
                                <div class="other-specification" id="other-specification" style="display: none; margin-top: 16px;">
                                    <label for="other-subject">Please specify other subject:</label>
                                    <input type="text" name="other_subject" id="other-subject" placeholder="Enter subject name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sports Coach Section -->
                    <div class="conditional-section" id="sports-coach-section">
                        <h3>Sports Coaching Details</h3>
                        <div class="form-group">
                            <label for="sports-type">Sports:</label>
                            <select id="sports-type" name="sports_type">
                                <option value="">Select Sport</option>
                                <option value="Football">Football</option>
                                <option value="Cricket">Cricket</option>
                                <option value="Basketball">Basketball</option>
                                <option value="Tennis">Tennis</option>
                                <option value="Badminton">Badminton</option>
                                <option value="Swimming">Swimming</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sports-gender">For which gender:</label>
                            <select id="sports-gender" name="sports_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sports-days">Coaching days in a week:</label>
                            <select id="sports-days" name="sports_days">
                                <option value="">Select Days</option>
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

                    <!-- Trainer Section -->
                    <div class="conditional-section" id="trainer-section">
                        <h3>Training Details</h3>
                        <div class="form-group">
                            <label for="training-type">Training Type:</label>
                            <select id="training-type" name="training_type">
                                <option value="">Select Type</option>
                                <option value="Gym">Gym</option>
                                <option value="Yoga">Yoga</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="training-gender">For which gender:</label>
                            <select id="training-gender" name="training_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="training-days">Training days in a week:</label>
                            <select id="training-days" name="training_days">
                                <option value="">Select Days</option>
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

                    <!-- Dance Teacher Section -->
                    <div class="conditional-section" id="dance-teacher-section">
                        <h3>Dance Teaching Details</h3>
                        <div class="form-group">
                            <label for="dance-type">Dance Type:</label>
                            <select id="dance-type" name="dance_type">
                                <option value="">Select Type</option>
                                <option value="Eastern/Classical">Eastern/Classical</option>
                                <option value="Western">Western</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dance-gender">For which gender:</label>
                            <select id="dance-gender" name="dance_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dance-days">Coaching days in a week:</label>
                            <select id="dance-days" name="dance_days">
                                <option value="">Select Days</option>
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

                    <!-- Music Teacher Section -->
                    <div class="conditional-section" id="music-teacher-section">
                        <h3>Music Teaching Details</h3>
                        <div class="form-group">
                            <label for="music-type">Music Type:</label>
                            <select id="music-type" name="music_type">
                                <option value="">Select Type</option>
                                <option value="Eastern/Classical">Eastern/Classical</option>
                                <option value="Western">Western</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="music-gender">For which gender:</label>
                            <select id="music-gender" name="music_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="music-days">Learning days in a week:</label>
                            <select id="music-days" name="music_days">
                                <option value="">Select Days</option>
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

                    <!-- Singing Teacher Section -->
                    <div class="conditional-section" id="singing-teacher-section">
                        <h3>Singing Teaching Details</h3>
                        <div class="form-group">
                            <label for="singing-type">Singing Type:</label>
                            <select id="singing-type" name="singing_type">
                                <option value="">Select Type</option>
                                <option value="Eastern/Classical">Eastern/Classical</option>
                                <option value="Western">Western</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="singing-gender">For which gender:</label>
                            <select id="singing-gender" name="singing_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="singing-days">Learning days in a week:</label>
                            <select id="singing-days" name="singing_days">
                                <option value="">Select Days</option>
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

                    <!-- Art Teacher Section -->
                    <div class="conditional-section" id="art-teacher-section">
                        <h3>Art Teaching Details</h3>
                        <div class="form-group">
                            <label for="art-days">Learning days in a week:</label>
                            <select id="art-days" name="art_days">
                                <option value="">Select Days</option>
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

                    <!-- Others Section -->
                    <div class="conditional-section" id="others-section">
                        <h3>Other Profession Details</h3>
                        <div class="form-group">
                            <label for="other-profession-name">Profession Name:</label>
                            <input type="text" id="other-profession-name" name="other_profession_name" placeholder="Enter your profession">
                        </div>
                        <div class="form-group">
                            <label for="other-gender">For which gender:</label>
                            <select id="other-gender" name="other_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="other-days">Coaching days in a week:</label>
                            <select id="other-days" name="other_days">
                                <option value="">Select Days</option>
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
                </div>

                <div class="form-section">
                    <h2 class="section-title">Teaching Preferences</h2>

                    <div class="two-column">
                        <div class="form-group">
                            <label>Teaching Mode</label>
                            <div class="checkbox-grid">
                                <div class="checkbox-item">
                                    <input type="checkbox" name="mode[]" value="Online" id="online">
                                    <label for="online">Online</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="mode[]" value="Offline" id="offline">
                                    <label for="offline">Offline</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Preferred Location</label>
                            <div class="checkbox-grid">
                                <div class="checkbox-item">
                                    <input type="checkbox" name="preferred_location[]" value="Home" id="home">
                                    <label for="home">At Home</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="preferred_location[]" value="Away" id="away">
                                    <label for="away">Travel to Student</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="section-title">Location Details</h2>

                    <div class="two-column">
                        <div class="form-group">
                            <label for="citySelect">Select City *</label>
                            <select id="citySelect" name="city" required>
                                <option value="">-- Select City --</option>
                                <option value="Alipurduar">Alipurduar</option>
                                <option value="Coochbehar">Coochbehar</option>
                                <option value="Falakata">Falakata</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="addressInput">Address *</label>
                            <input type="text" id="addressInput" name="address" required readonly placeholder="Kindly select your location on map">
                        </div>
                    </div>

                    <!-- <button type="button" class="btn btn-secondary" onclick="searchLocation()">📍 Search on
                        Map</button> -->

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="user_type" value="individual">

                    <div class="map-section">
                        <div id="map"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">Complete Registration</button>
            </form>
        </div>
    </div>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTy16l_Zhg8IgEWj2nu_MnBJjCRg_SrB8&libraries=geometry&callback=initializeMap">
    </script>
    <script src="./js/reg_script.js"></script>
    
    <script>

        function handleProfessionChange() {
    const checkboxes = document.querySelectorAll('input[name="profession[]"]');
    const selectedProfessions = [];
    
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedProfessions.push(checkbox.value);
        }
    });
    
    // Hide all conditional sections first
    const allSections = document.querySelectorAll('.conditional-section');
    allSections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Show sections based on selected professions
    selectedProfessions.forEach(profession => {
        let sectionId = '';
        
        switch(profession) {
            case 'Tutor':
                sectionId = 'tutor-section';
                break;
            case 'Sports Coach':
                sectionId = 'sports-coach-section';
                break;
            case 'Trainer':
                sectionId = 'trainer-section';
                break;
            case 'Dance Teacher':
                sectionId = 'dance-teacher-section';
                break;
            case 'Music Teacher':
                sectionId = 'music-teacher-section';
                break;
            case 'Singing Teacher':
                sectionId = 'singing-teacher-section';
                break;
            case 'Art Teacher':
                sectionId = 'art-teacher-section';
                break;
            case 'others':
                sectionId = 'others-section';
                break;
        }
        
        if (sectionId) {
            document.getElementById(sectionId).classList.add('active');
        }
    });
    
    // Update profession summary
    updateProfessionSummary(selectedProfessions);
}
function updateProfessionSummary(selectedProfessions) {
    const summary = document.getElementById('profession-summary');
    if (selectedProfessions.length > 0) {
        summary.textContent = `(${selectedProfessions.length} selected)`;
    } else {
        summary.textContent = '';
    }
}

        // Handle form submission
        document.getElementById('tutorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('save_teacher.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Reset form or redirect to success page
                        this.reset();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the form. Please try again.');
            });
        });

        // Show/hide other class specification
        document.getElementById('class-others').addEventListener('change', function() {
            const otherClassGroup = document.getElementById('other-class-group');
            otherClassGroup.style.display = this.checked ? 'block' : 'none';
        });

        // Show/hide other subject specification
        document.getElementById('others').addEventListener('change', function() {
            const otherSpecification = document.getElementById('other-specification');
            otherSpecification.style.display = this.checked ? 'block' : 'none';
        });

        function toggleOtherProfession() {
            const checkbox = document.getElementById('profession-others');
            const otherProfessionGroup = document.getElementById('other-profession-group');
            otherProfessionGroup.style.display = checkbox.checked ? 'block' : 'none';
        }



        function toggleDropdown(sectionId) {
            const section = document.getElementById(sectionId);
            section.classList.toggle('active');
        }

        function updateSummary(type) {
            const checkboxes = document.querySelectorAll(`input[name="${type}[]"]:checked`);
            const summaryElement = document.getElementById(`${type}-summary`);
            const count = checkboxes.length;
            
            if (count > 0) {
                summaryElement.textContent = `(${count} selected)`;
                summaryElement.style.color = '#57cc99';
            } else {
                summaryElement.textContent = '';
            }
        }

        function toggleOtherClass() {
            const otherCheckbox = document.getElementById('class-others');
            const otherGroup = document.getElementById('other-class-group');
            otherGroup.style.display = otherCheckbox.checked ? 'block' : 'none';
        }

        function toggleOtherSubject() {
            const otherCheckbox = document.getElementById('others');
            const otherSpecification = document.getElementById('other-specification');
            otherSpecification.style.display = otherCheckbox.checked ? 'block' : 'none';
        }

        // Initialize summaries on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary('boards');
            updateSummary('classes');
            updateSummary('subjects');
        });

    </script>

</body>

</html>