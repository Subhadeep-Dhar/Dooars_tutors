<!DOCTYPE html>
<html lang="en">

<head>
    <title>DooarsTutors - Become a tutor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                    <div class="form-group">
                        <label for="experience">Experience </label>
                        <input type="text" id="experience" name="experience" required placeholder="Enter teaching experience">
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="section-title">Teaching Credentials</h2>

                    <div class="form-group">
                        <label>Education Boards</label>
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

                    <div class="form-group">
                        <label>Classes</label>
                        <div class="checkbox-grid">
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="1" id="class-1">
                                <label for="class-1">Class 1</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="2" id="class-2">
                                <label for="class-2">Class 2</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="3" id="class-3">
                                <label for="class-3">Class 3</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="4" id="class-4">
                                <label for="class-4">Class 4</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="5" id="class-5">
                                <label for="class-5">Class 5</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="6" id="class-6">
                                <label for="class-6">Class 6</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="7" id="class-7">
                                <label for="class-7">Class 7</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="8" id="class-8">
                                <label for="class-8">Class 8</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="9" id="class-9">
                                <label for="class-9">Class 9</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="10" id="class-10">
                                <label for="class-10">Class 10</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="11" id="class-11">
                                <label for="class-11">Class 11</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="12" id="class-12">
                                <label for="class-12">Class 12</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="classes[]" value="others" id="class-others">
                                <label for="class-others">Others</label>
                            </div>
                        </div>
                        <div class="form-group" id="other-class-group" style="display: none; margin-top: 16px;">
                            <label for="other-class">Please specify:</label>
                            <input type="text" id="other-class" name="other_class"
                                placeholder="Enter other class details">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Subjects</label>
                        <div class="checkbox-grid">
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" English " id=" english ">
                                <label for=" english ">English</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Hindi " id=" hindi ">
                                <label for=" hindi ">Hindi</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Bengali " id=" bengali ">
                                <label for=" bengali ">Bengali</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Sanskrit " id=" sanskrit ">
                                <label for=" sanskrit ">Sanskrit</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Mathematics " id=" mathematics ">
                                <label for=" mathematics ">Mathematics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Physics " id=" physics ">
                                <label for=" physics ">Physics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Chemistry " id=" chemistry ">
                                <label for=" chemistry ">Chemistry</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Biology " id=" biology ">
                                <label for=" biology ">Biology</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" General Science " id=" general-science ">
                                <label for=" general-science ">General Science</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="EVS" id="evs">
                                <label for="evs">EVS</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value ="Computer Science " id=" computer-science ">
                                <label for=" computer-science ">Computer Science</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" IT " id=" it ">
                                <label for=" it ">IT</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" AI " id=" ai ">
                                <label for=" ai ">AI</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" History " id=" history ">
                                <label for=" history ">History</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Geography " id=" geography ">
                                <label for=" geography ">Geography</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Political Science "
                                    id=" political-science ">
                                <label for=" political-science ">Political Science</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Civics " id=" civics ">
                                <label for=" civics ">Civics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Economics " id=" economics ">
                                <label for=" economics ">Economics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Social Studies " id=" social-studies ">
                                <label for=" social-studies ">Social Studies</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Accountancy " id=" accountancy ">
                                <label for=" accountancy ">Accountancy</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Business Studies " id=" business-studies ">
                                <label for=" business-studies ">Business Studies</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value=" Philosophy " id=" philosophy ">
                                <label for=" philosophy ">Philosophy</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Others" id="others">
                                <label for="others">Others</label>
                            </div>
                        </div>
                        <div class="other-specification" id="other-specification" style="display: none;">
                            <label for="other-subject">Please specify other subject:</label>
                            <input type="text" name="other_subject" id="other-subject" placeholder="Enter subject name">
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
                                <input type="text" id="addressInput" name="address" required readonly placeholder="Enter your address or select location on map">
                            </div>
                        </div>

                        <!-- <button type="button" class="btn btn-secondary" onclick="searchLocation()">📍 Search on
                            Map</button> -->

                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

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

    </script>

</body>

</html>