<?php
    // Database configuration
    $host = 'localhost:3307';
    $dbname = 'dooars_tutors';
    $username = 'root';
    $pass = '';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Set content type to JSON
    header('Content-Type: application/json');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        function generateReferralCode($name, $id) {
            $prefix = strtoupper(substr(preg_replace("/[^A-Za-z]/", '', $name), 0, 3));
            $suffix = str_pad($id, 4, '0', STR_PAD_LEFT);
            return $prefix . $suffix;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Debug: Log received data
            error_log('POST data received: ' . print_r($_POST, true));
            error_log('FILES data received: ' . print_r($_FILES, true));

            // Collect basic data
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            // $password = password_hash($password, PASSWORD_DEFAULT);
            $profession = isset($_POST['profession']) ? implode(',', $_POST['profession']) : '';

            // Validate required fields
            if (empty($name) || empty($phone) || empty($profession)) {
                throw new Exception('Required fields are missing: Name, Phone, and Profession are mandatory.');
            }

            // Validate email if provided
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }

            // Check if email already exists (only if email is provided)
            if (!empty($email)) {
                $check_email = "SELECT id FROM tutors WHERE email = ?";
                $stmt = $pdo->prepare($check_email);
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    throw new Exception('Email already registered');
                }
            }

            // Initialize profession details array
            $profession_details = [];

            // After collecting the tutor data, set main columns to empty since we're using JSON only
            $boards = ''; // Keep empty - data will be in JSON
            $classes = ''; // Keep empty - data will be in JSON  
            $subjects = ''; // Keep empty - data will be in JSON

            // Handle class-subject mapping (for individual tutors) - keep this logic for JSON storage
            $subjectList = array_keys($_POST['classes_for_subject'] ?? []);
            $subjectString = implode(',', $subjectList);

            $classList = [];
            foreach ($_POST['classes_for_subject'] ?? [] as $subject => $classesForSubj) {
                foreach ($classesForSubj as $cls) {
                    $classList[] = $cls;
                }
            }
            $classString = implode(',', array_unique($classList));

            $boardsString = isset($_POST['boards']) ? implode(',', $_POST['boards']) : '';

            // Other subject handling
            $finalSubjectString = $subjectString;
            if (isset($_POST['classes_for_subject']['Others']) && !empty($_POST['other_subjects'])) {
                $finalSubjectString = str_replace('Others', trim($_POST['other_subjects']), $subjectString);
            }

            // Store tutor data in profession_details JSON
            if (in_array('Tutor', $_POST['profession'] ?? [])) {
                $profession_details['tutor'] = [
                    'boards' => $boardsString,
                    'classes' => $classString,
                    'subjects' => $finalSubjectString,
                    'class_subject_mapping' => $_POST['classes_for_subject'] ?? []
                ];
            }

            // Individual tutor professions
            if (in_array('Sports Coach', $_POST['profession'] ?? [])) {
                $profession_details['sports_coach'] = [
                    'sports_type' => trim($_POST['sports_type'] ?? ''),
                    'gender' => trim($_POST['sports_gender'] ?? ''),
                    'days_per_week' => trim($_POST['sports_days'] ?? '')
                ];
            }

            if (in_array('Trainer', $_POST['profession'] ?? [])) {
                $profession_details['trainer'] = [
                    'training_type' => trim($_POST['training_type'] ?? ''),
                    'gender' => trim($_POST['training_gender'] ?? ''),
                    'days_per_week' => trim($_POST['training_days'] ?? '')
                ];
            }

            if (in_array('Dance Teacher', $_POST['profession'] ?? [])) {
                $profession_details['dance_teacher'] = [
                    'dance_type' => trim($_POST['dance_type'] ?? ''),
                    'gender' => trim($_POST['dance_gender'] ?? ''),
                    'days_per_week' => trim($_POST['dance_days'] ?? '')
                ];
            }

            if (in_array('Music Teacher', $_POST['profession'] ?? [])) {
                $profession_details['music_teacher'] = [
                    'music_type' => trim($_POST['music_type'] ?? ''),
                    'instruments' => trim($_POST['music_instruments'] ?? ''),
                    'days_per_week' => trim($_POST['music_days'] ?? '')
                ];
            }

            if (in_array('Singing Teacher', $_POST['profession'] ?? [])) {
                $profession_details['singing_teacher'] = [
                    'singing_type' => trim($_POST['singing_type'] ?? ''),
                    'gender' => trim($_POST['singing_gender'] ?? ''),
                    'days_per_week' => trim($_POST['singing_days'] ?? '')
                ];
            }

            if (in_array('Art Teacher', $_POST['profession'] ?? [])) {
                $profession_details['art_teacher'] = [
                    'days_per_week' => trim($_POST['art_days'] ?? '')
                ];
            }

            if (in_array('others', $_POST['profession'] ?? [])) {
                $profession_details['others'] = [
                    'profession_name' => trim($_POST['other_profession_name'] ?? ''),
                    'gender' => trim($_POST['other_gender'] ?? ''),
                    'days_per_week' => trim($_POST['other_days'] ?? '')
                ];
            }

            // Organization-specific professions
            if (in_array('Educational Coaching Centre', $_POST['profession'] ?? [])) {
                $profession_details['educational_coaching_centre'] = [
                    'course_type' => trim($_POST['edu_course_type'] ?? ''),
                    'days_per_week' => trim($_POST['edu_course_days'] ?? '')
                ];
            }

            if (in_array('Computer Centre', $_POST['profession'] ?? [])) {
                $profession_details['computer_centre'] = [
                    'course_type' => trim($_POST['comp_course_type'] ?? ''),
                    'days_per_week' => trim($_POST['comp_course_days'] ?? '')
                ];
            }

            if (in_array('Sports Coaching Centre', $_POST['profession'] ?? [])) {
                $profession_details['sports_coaching_centre'] = [
                    'sports_type' => trim($_POST['sports_cc_type'] ?? ''),
                    'gender' => trim($_POST['sports_cc_gender'] ?? ''),
                    'days_per_week' => trim($_POST['sports_cc_days'] ?? '')
                ];
            }

            if (in_array('Gym & Yoga', $_POST['profession'] ?? [])) {
                $profession_details['gym_yoga'] = [
                    'training_type' => trim($_POST['gym_yoga_type'] ?? ''),
                    'gender' => trim($_POST['gym_yoga_gender'] ?? ''),
                    'days_per_week' => trim($_POST['gym_yoga_days'] ?? '')
                ];
            }

            if (in_array('Dance School', $_POST['profession'] ?? [])) {
                $profession_details['dance_school'] = [
                    'dance_type' => trim($_POST['dance__school_type'] ?? ''),
                    'gender' => trim($_POST['dance__school_gender'] ?? ''),
                    'days_per_week' => trim($_POST['dance__school_days'] ?? '')
                ];
            }

            if (in_array('Music School', $_POST['profession'] ?? [])) {
                $profession_details['music_school'] = [
                    'music_type' => trim($_POST['music__school_type'] ?? ''),
                    'instrument' => trim($_POST['music__school_instrument'] ?? ''),
                    'days_per_week' => trim($_POST['music__school_days'] ?? '')
                ];
            }

            if (in_array('Singing School', $_POST['profession'] ?? [])) {
                $profession_details['singing_school'] = [
                    'singing_type' => trim($_POST['singing__school_type'] ?? ''),
                    'gender' => trim($_POST['singing__school_gender'] ?? ''),
                    'days_per_week' => trim($_POST['singing__school_days'] ?? '')
                ];
            }

            if (in_array('Abriti School', $_POST['profession'] ?? [])) {
                $profession_details['abriti_school'] = [
                    'days_per_week' => trim($_POST['abriti__school_days'] ?? '')
                ];
            }

            if (in_array('Visual Arts', $_POST['profession'] ?? [])) {
                $profession_details['visual_arts'] = [
                    'type' => trim($_POST['VS_school_type'] ?? ''),
                    'gender' => trim($_POST['VS_school_gender'] ?? ''),
                    'days_per_week' => trim($_POST['VS_school_days'] ?? '')
                ];
            }

            if (in_array('Abacus Centre', $_POST['profession'] ?? [])) {
                $profession_details['abacus_centre'] = [
                    'course_type' => trim($_POST['abacus_org_section_name'] ?? ''),
                    'gender' => trim($_POST['abacus_org_section_gender'] ?? ''),
                    'days_per_week' => trim($_POST['abacus_org_section_days'] ?? '')
                ];
            }

            $profession_details_json = json_encode($profession_details);

            // Teaching preferences
            $teaching_mode = isset($_POST['mode']) ? implode(',', $_POST['mode']) : '';
            $preferred_location = isset($_POST['preferred_location']) ? implode(',', $_POST['preferred_location']) : '';
            $teaching_preferences = $teaching_mode . '|' . $preferred_location;

            $city = trim($_POST['city'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $latitude = floatval($_POST['latitude'] ?? 0);
            $longitude = floatval($_POST['longitude'] ?? 0);
            $experience = trim($_POST['experience'] ?? '');
            $plan = 'basic';
            $status = 'active';
            $type = strtolower(trim($_POST['user_type'] ?? 'individual'));
            if (!in_array($type, ['individual', 'organisation'])) {
                $type = 'individual';
            }

            // Additional validation
            if (empty($address) || empty($password)) {
                throw new Exception('Required fields are missing: Address and Password are mandatory.');
            }

            // Enhanced validation for both individual and organization professions
            foreach ($_POST['profession'] as $prof) {
                switch ($prof) {
                    // Individual profession validations
                    case 'Sports Coach':
                        if (empty($_POST['sports_type']) || empty($_POST['sports_gender']) || empty($_POST['sports_days'])) {
                            throw new Exception('Sports Coach requires: Sports Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Trainer':
                        if (empty($_POST['training_type']) || empty($_POST['training_gender']) || empty($_POST['training_days'])) {
                            throw new Exception('Trainer requires: Training Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Dance Teacher':
                        if (empty($_POST['dance_type']) || empty($_POST['dance_gender']) || empty($_POST['dance_days'])) {
                            throw new Exception('Dance Teacher requires: Dance Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Music Teacher':
                        if (empty($_POST['music_type']) || empty($_POST['music_instruments']) || empty($_POST['music_days'])) {
                            throw new Exception('Music Teacher requires: Music Type, Instruments, and Days per week.');
                        }
                        break;
                    case 'Singing Teacher':
                        if (empty($_POST['singing_type']) || empty($_POST['singing_gender']) || empty($_POST['singing_days'])) {
                            throw new Exception('Singing Teacher requires: Singing Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Art Teacher':
                        if (empty($_POST['art_days'])) {
                            throw new Exception('Art Teacher requires: Days per week.');
                        }
                        break;
                    case 'others':
                        if (empty($_POST['other_profession_name']) || empty($_POST['other_gender']) || empty($_POST['other_days'])) {
                            throw new Exception('Other profession requires: Profession Name, Gender, and Days per week.');
                        }
                        break;

                    // Organization profession validations
                    case 'Educational Coaching Centre':
                        if (empty($_POST['edu_course_type']) || empty($_POST['edu_course_days'])) {
                            throw new Exception('Educational Coaching Centre requires: Course Type and Days per week.');
                        }
                        break;
                    case 'Computer Centre':
                        if (empty($_POST['comp_course_type']) || empty($_POST['comp_course_days'])) {
                            throw new Exception('Computer Centre requires: Course Type and Days per week.');
                        }
                        break;
                    case 'Sports Coaching Centre':
                        if (empty($_POST['sports_cc_type']) || empty($_POST['sports_cc_gender']) || empty($_POST['sports_cc_days'])) {
                            throw new Exception('Sports Coaching Centre requires: Sports Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Gym & Yoga':
                        if (empty($_POST['gym_yoga_type']) || empty($_POST['gym_yoga_gender']) || empty($_POST['gym_yoga_days'])) {
                            throw new Exception('Gym & Yoga requires: Training Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Dance School':
                        if (empty($_POST['dance__school_type']) || empty($_POST['dance__school_gender']) || empty($_POST['dance__school_days'])) {
                            throw new Exception('Dance School requires: Dance Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Music School':
                        if (empty($_POST['music__school_type']) || empty($_POST['music__school_instrument']) || empty($_POST['music__school_days'])) {
                            throw new Exception('Music School requires: Music Type, Instrument, and Days per week.');
                        }
                        break;
                    case 'Singing School':
                        if (empty($_POST['singing__school_type']) || empty($_POST['singing__school_gender']) || empty($_POST['singing__school_days'])) {
                            throw new Exception('Singing School requires: Singing Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Abriti School':
                        if (empty($_POST['abriti__school_days'])) {
                            throw new Exception('Abriti School requires: Days per week.');
                        }
                        break;
                    case 'Visual Arts':
                        if (empty($_POST['VS_school_type']) || empty($_POST['VS_school_gender']) || empty($_POST['VS_school_days'])) {
                            throw new Exception('Visual Art School requires: Type, Gender, and Days per week.');
                        }
                        break;
                    case 'Abacus Centre':
                        if (empty($_POST['abacus_org_section_name']) || empty($_POST['abacus_org_section_gender']) || empty($_POST['abacus_org_section_days'])) {
                            throw new Exception('Abacus Centre requires: Course Type, Gender, and Days per week.');
                        }
                        break;
                }
            }

            // Handle referral code
            $referralCodeUsed = trim($_POST['referral_code'] ?? '');
            $referredBy = null;

            if (!empty($referralCodeUsed)) {
                // Look up referring tutor
                $refStmt = $pdo->prepare("SELECT id, referral_code_created_at FROM tutors WHERE referral_code = :code");
                $refStmt->execute([':code' => $referralCodeUsed]);
                $referrer = $refStmt->fetch(PDO::FETCH_ASSOC);

                if ($referrer) {
                    // Check if referral code expired (valid for 3 months)
                    $createdAt = new DateTime($referrer['referral_code_created_at']);
                    $now = new DateTime();
                    
                    // Check if under 3 months
                    if ($createdAt->modify('+3 months') > new DateTime()) {
                        $referredBy = $referrer['id'];
                    } else {
                        throw new Exception("Referral code has expired.");
                    }
                } else {
                    throw new Exception("Invalid referral code.");
                }
            }

            // Insert into main tutors table
            $sql = "INSERT INTO tutors (
                name, phone, email, experience, profession, profession_details, boards, classes, subjects,
                teaching_preferences, city, address, latitude, longitude, password,
                plan, status, type, created_at
            ) VALUES (
                :name, :phone, :email, :experience, :profession, :profession_details, :boards, :classes, :subjects,
                :teaching_preferences, :city, :address, :latitude, :longitude, :password,
                :plan, :status, :type, NOW()
            )";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':experience', $experience);
            $stmt->bindParam(':profession', $profession);
            $stmt->bindParam(':profession_details', $profession_details_json);
            $stmt->bindParam(':boards', $boards);
            $stmt->bindParam(':classes', $classes);
            $stmt->bindParam(':subjects', $subjects);
            $stmt->bindParam(':teaching_preferences', $teaching_preferences);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':latitude', $latitude);
            $stmt->bindParam(':longitude', $longitude);
            $stmt->bindParam(':plan', $plan);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':type', $type);

            if ($stmt->execute()) {
                $tutor_id = $pdo->lastInsertId();

                // 1. Generate a new referral code for the newly registered tutor
                $newReferralCode = generateReferralCode($name, $tutor_id);
                $now = date('Y-m-d H:i:s');

                $updateReferral = $pdo->prepare("UPDATE tutors SET referral_code = :code, referred_by = :referred_by, referral_code_created_at = :created_at WHERE id = :id");
                $updateReferral->execute([
                    ':code' => $newReferralCode,
                    ':referred_by' => $referredBy,
                    ':created_at' => $now,
                    ':id' => $tutor_id
                ]);

                // 2. If the tutor was referred by someone
                if (!empty($referredBy)) {
                    // Prevent double rewards by checking if this referee has already been logged
                    $checkReferral = $pdo->prepare("SELECT COUNT(*) FROM referrals WHERE referee_id = :referee");
                    $checkReferral->execute([':referee' => $tutor_id]);

                    if ($checkReferral->fetchColumn() == 0) {
                        // Log the referral
                        $logReferral = $pdo->prepare("INSERT INTO referrals (referrer_id, referee_id, coupon_code, discount_applied, reward_given, created_at) VALUES (:referrer, :referee, :code, 1, 1, :created_at)");
                        $logReferral->execute([
                            ':referrer' => $referredBy,
                            ':referee' => $tutor_id,
                            ':code' => $referralCodeUsed,
                            ':created_at' => $now
                        ]);

                        // Add ₹48.82 to both referrer and referee wallets
                        $pdo->prepare("UPDATE tutors SET wallet_balance = wallet_balance + 48.82 WHERE id = :id")
                            ->execute([':id' => $referredBy]);

                        $pdo->prepare("UPDATE tutors SET wallet_balance = wallet_balance + 48.82 WHERE id = :id")
                            ->execute([':id' => $tutor_id]);
                    }
                }

                // Insert into tutor_subjects mapping table (only for individual tutors with class-subject mapping)
                if (isset($_POST['classes_for_subject']) && is_array($_POST['classes_for_subject'])) {
                    $insertMap = $pdo->prepare("INSERT INTO tutor_subjects (tutor_id, subject, class) VALUES (:tutor_id, :subject, :class)");

                    foreach ($_POST['classes_for_subject'] as $subject => $classList) {
                        foreach ($classList as $class) {
                            $insertMap->execute([
                                ':tutor_id' => $tutor_id,
                                ':subject' => $subject,
                                ':class' => $class
                            ]);
                        }
                    }
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to DooarsTutors.',
                    'tutor_id' => $tutor_id,
                    'redirect' => 'index.php'
                ]);
            } else {
                throw new Exception('Failed to save registration data.');
            }
        } else {
            throw new Exception('Invalid request method.');
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $pdo = null;
?>