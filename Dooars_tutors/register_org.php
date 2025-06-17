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
                    <h2 class="section-title">Organization Information</h2>

                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name">
                    </div>

                    <div class="two-column">
                        <div class="form-group">
                            <label for="phone">Contact Number *</label>
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
                    <h2 class="section-title">Organization Credentials</h2>

                    <div class="form-group">
                        <label>Coaching / Arts & Culture</label>
                        <div class="checkbox-grid">
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Cooching Centre" id="cooching centre">
                                <label for="cooching centre">Cooching Centre (Educational)</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Abacus" id="abacus">
                                <label for="abacus">Abacus</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Dance School" id="dance school">
                                <label for="dance school">Dance School</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Art School" id="art school">
                                <label for="art school">Art School</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Singing School" id="singing school">
                                <label for="singing school">Singing School</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Computer Centre" id="computer center">
                                <label for="computer center">Computer Centre</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Abriti School" id="abriti">
                                <label for="abriti">Abriti School</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="others" id="class-others">
                                <label for="class-others">Others</label>
                            </div>
                        </div>
                        <div class="other-specification" id="other-specification" style="display: none;">
                            <label for="other-subject">Please specify others:</label>
                            <input type="text" name="other_subject" id="other-subject" placeholder="Enter subject name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Sports & Health</label>
                        <div class="checkbox-grid">
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="GYM" id="gym">
                                <label for="gym">Gym</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Cricket" id="cricket">
                                <label for="cricket">Cricket</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Football" id="football">
                                <label for="football">Football</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Badminton" id="badminton">
                                <label for="badminton">Badminton</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Table Tennis" id="tt">
                                <label for="tt">Table Tennis</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Chess" id="chess">
                                <label for="chess">Chess</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Running" id="running">
                                <label for="running">Running</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Swimming" id="swimming">
                                <label for="swimming">Swimming</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Skating" id="skating">
                                <label for="skating">Skating</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="Karate" id="karate">
                                <label for="karate">Karate</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="subjects[]" value="others" id="class-others">
                                <label for="class-others">Others</label>
                            </div>
                        </div>
                        <div class="other-specification" id="other-specification" style="display: none;">
                            <label for="other-subject">Please specify others:</label>
                            <input type="text" name="other_subject" id="other-subject" placeholder="Enter subject name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Coaching days in a week</label>
                        <div class="radio-grid">
                            <div class="radio-item" required>
                                <input type="radio" name="classes[]" value="1" id="1">
                                <label for="1">1</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="classes[]" value="2" id="2">
                                <label for="2">2</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="classes[]" value="3" id="3">
                                <label for="3">3</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="classes[]" value="4" id="4">
                                <label for="4">4</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="classes[]" value="5" id="5">
                                <label for="5">5</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="classes[]" value="6" id="6">
                                <label for="6">6</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="classes[]" value="7" id="7">
                                <label for="7">7</label>
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
                                <input type="text" id="addressInput" name="address" required readonly placeholder="Select location on map">
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