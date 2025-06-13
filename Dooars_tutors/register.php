<!DOCTYPE html>
<html lang="en">

<head>
    <title>DooarsTutors - Become a tutor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/reg_style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>DooarsTutors</h1>
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
                                <input type="checkbox" name="subjects[]" value="Sanskrit" id="sanskrit">
                                <label for="sanskrit">Sanskrit</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Mathematics" id="mathematics">
                                <label for="mathematics">Mathematics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Physics" id="physics">
                                <label for="physics">Physics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Chemistry" id="chemistry">
                                <label for="chemistry">Chemistry</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Biology" id="biology">
                                <label for="biology">Biology</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="General Science" id="general-science">
                                <label for="general-science">General Science</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="EVS" id="evs">
                                <label for="evs">EVS</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Computer Science" id="computer-science">
                                <label for="computer-science">Computer Science</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="IT" id="it">
                                <label for="it">IT</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="AI" id="ai">
                                <label for="ai">AI</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="History" id="history">
                                <label for="history">History</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Geography" id="geography">
                                <label for="geography">Geography</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Political Science"
                                    id="political-science">
                                <label for="political-science">Political Science</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Civics" id="civics">
                                <label for="civics">Civics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Economics" id="economics">
                                <label for="economics">Economics</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Social Studies" id="social-studies">
                                <label for="social-studies">Social Studies</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Accountancy" id="accountancy">
                                <label for="accountancy">Accountancy</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Business Studies" id="business-studies">
                                <label for="business-studies">Business Studies</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Philosophy" id="philosophy">
                                <label for="philosophy">Philosophy</label>
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

                        <button type="button" class="btn btn-secondary" onclick="searchLocation()">📍 Search on
                            Map</button>

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

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-photon"></script>
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