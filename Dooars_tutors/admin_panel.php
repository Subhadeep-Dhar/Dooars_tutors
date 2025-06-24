<?php
// Enhanced error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection configuration
$config = [
    'host' => 'localhost:3307',
    'username' => 'root',
    'password' => '',
    'database' => 'dooars_tutors',
    'charset' => 'utf8mb4'
];

$tutors = [];
$error = null;
$success = null;

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    
    // Handle AJAX requests
    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        switch ($_POST['action']) {
            case 'search':
                $tutors = searchTutors($pdo, $_POST);
                echo json_encode(['success' => true, 'data' => $tutors]);
                exit;
                
            case 'update_status':
                $result = updateTutorStatus($pdo, $_POST['id'], $_POST['status']);
                echo json_encode($result);
                exit;
                
            case 'delete_tutor':
                $result = deleteTutor($pdo, $_POST['id']);
                echo json_encode($result);
                exit;
                
            case 'get_stats':
                $stats = getTutorStats($pdo);
                echo json_encode(['success' => true, 'data' => $stats]);
                exit;
        }
    }
    
    // Fetch all tutors for initial load
    $stmt = $pdo->prepare("
        SELECT id, name, email, phone, city, experience, address, 
               latitude, longitude, rating, rating_count, plan, status, 
               type, profession, payment_status, referred_by, created_at
        FROM tutors 
        ORDER BY created_at DESC 
        LIMIT 100
    ");
    $stmt->execute();
    $tutors = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Database connection failed: " . $e->getMessage();
    error_log("Database Error: " . $e->getMessage());
}

// Helper functions
function searchTutors($pdo, $filters) {
    $sql = "SELECT id, name, email, phone, city, experience, address, 
                   latitude, longitude, rating, rating_count, plan, status, 
                   type, profession, payment_status, referred_by, created_at 
            FROM tutors WHERE 1=1";
    $params = [];
    
    if (!empty($filters['name'])) {
        $sql .= " AND name LIKE :name";
        $params['name'] = '%' . $filters['name'] . '%';
    }
    
    if (!empty($filters['type'])) {
        $sql .= " AND type = :type";
        $params['type'] = $filters['type'];
    }
    
    if (!empty($filters['profession'])) {
        $sql .= " AND profession LIKE :profession";
        $params['profession'] = '%' . $filters['profession'] . '%';
    }
    
    if (!empty($filters['payment_status'])) {
        $sql .= " AND payment_status = :payment_status";
        $params['payment_status'] = $filters['payment_status'];
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND status = :status";
        $params['status'] = $filters['status'];
    }
    
    if (!empty($filters['city'])) {
        $sql .= " AND city LIKE :city";
        $params['city'] = '%' . $filters['city'] . '%';
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT 100";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function updateTutorStatus($pdo, $id, $status) {
    try {
        $stmt = $pdo->prepare("UPDATE tutors SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);
        return ['success' => true, 'message' => 'Status updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to update status'];
    }
}

function deleteTutor($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM tutors WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return ['success' => true, 'message' => 'Tutor deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to delete tutor'];
    }
}

function getTutorStats($pdo) {
    $stats = [];
    
    // Total tutors
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tutors");
    $stats['total'] = $stmt->fetch()['count'];
    
    // Active tutors
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tutors WHERE status = 'active'");
    $stats['active'] = $stmt->fetch()['count'];
    
    // Premium tutors
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tutors WHERE plan IN ('Premium', 'premium')");
    $stats['premium'] = $stmt->fetch()['count'];
    
    // Average rating
    $stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM tutors WHERE rating IS NOT NULL AND rating > 0");
    $result = $stmt->fetch();
    $stats['avg_rating'] = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
    
    // Total referrals
    $stmt = $pdo->query("SELECT SUM(referred_by) as total FROM tutors WHERE referred_by IS NOT NULL");
    $result = $stmt->fetch();
    $stats['referrals'] = $result['total'] ?? 0;
    
    // Paid tutors
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tutors WHERE payment_status = 'paid'");
    $stats['paid'] = $stmt->fetch()['count'];
    
    return $stats;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dooars Tutors - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --success-color: #10b981;
            --success-hover: #059669;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
            --warning-color: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary-color);
        }

        .header h1 {
            color: var(--gray-900);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            color: var(--gray-300);
        }

        .search-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .search-section h3 {
            color: var(--gray-900);
            font-size: 1.25rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-grid input,
        .search-grid select {
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: white;
        }

        .search-grid input:focus,
        .search-grid select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--gray-500);
            color: white;
        }

        .btn-secondary:hover {
            background: var(--gray-600);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: var(--success-hover);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: var(--danger-hover);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .tutors-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 25px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h3 {
            color: var(--gray-900);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .table-container {
            overflow-x: auto;
        }

        .tutors-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tutors-table th {
            background: var(--gray-50);
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .tutors-table td {
            padding: 16px 12px;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.9rem;
        }

        .tutors-table tr:hover {
            background: var(--gray-50);
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status.active {
            background: #dcfce7;
            color: #166534;
        }

        .status.inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        .status.paid {
            background: #dcfce7;
            color: #166534;
        }

        .status.unpaid {
            background: #fef3c7;
            color: #92400e;
        }

        .status.pending {
            background: #dbeafe;
            color: #1e40af;
        }

        .rating {
            color: var(--warning-color);
            font-weight: 500;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            width: 90%;
            max-width: 800px;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            color: var(--gray-900);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close {
            color: var(--gray-400);
            font-size: 1.75rem;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s ease;
            border: none;
            background: none;
        }

        .close:hover {
            color: var(--gray-600);
        }

        .modal-body {
            padding: 25px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: var(--gray-500);
        }

        .loading i {
            font-size: 2rem;
            margin-bottom: 15px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: var(--gray-500);
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--gray-300);
        }

        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }
            
            .search-grid {
                grid-template-columns: 1fr;
            }
            
            .search-actions {
                justify-content: center;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }

        .logo-blend2{
    height: 70px;
  margin-top: 10px;
  mix-blend-mode: multiply;
  /* margin-left:150px;  */
}

@media (min-width: 770px) {
    .logo-blend2{
    height: 70px;
  margin-top: 10px;
  mix-blend-mode: multiply;
  /* margin-left:50px;  */
}
}
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="logo_black.jpg" class="logo-blend2"></img>
            <p>Comprehensive tutor management and analytics dashboard</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number" id="totalTutors">-</div>
                <div class="stat-label">Total Tutors</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check stat-icon"></i>
                <div class="stat-number" id="activeTutors">-</div>
                <div class="stat-label">Active Tutors</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-crown stat-icon"></i>
                <div class="stat-number" id="premiumTutors">-</div>
                <div class="stat-label">Premium Plans</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-number" id="avgRating">-</div>
                <div class="stat-label">Average Rating</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-share-alt stat-icon"></i>
                <div class="stat-number" id="totalReferrals">-</div>
                <div class="stat-label">Total Referrals</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-credit-card stat-icon"></i>
                <div class="stat-number" id="paidTutors">-</div>
                <div class="stat-label">Paid Status</div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-section">
            <h3><i class="fas fa-search"></i> Search & Filter</h3>
            <div class="search-grid">
                <input type="text" id="searchName" placeholder="Search by name...">
                <select id="filterType">
                    <option value="">All Types</option>
                    <option value="individual">Individual</option>
                    <option value="institution">Institution</option>
                </select>
                <input type="text" id="searchProfession" placeholder="Search by profession...">
                <select id="filterPaymentStatus">
                    <option value="">All Payment Status</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="pending">Pending</option>
                </select>
                <select id="filterStatus">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <input type="text" id="searchCity" placeholder="Search by city...">
            </div>
            <div class="search-actions">
                <button class="btn btn-primary" onclick="searchTutors()">
                    <i class="fas fa-search"></i> Search
                </button>
                <button class="btn btn-secondary" onclick="resetSearch()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button class="btn btn-success" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Tutors Table Section -->
        <div class="tutors-section">
            <div class="section-header">
                <h3><i class="fas fa-table"></i> Tutors Management</h3>
                <div class="actions">
                    <span id="tutorCount" class="stat-label">Loading...</span>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading" class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading tutors...</p>
            </div>

            <!-- Error State -->
            <div id="error" class="alert alert-danger" style="display: none;"></div>

            <!-- Success State -->
            <div id="success" class="alert alert-success" style="display: none;"></div>

            <!-- Table Container -->
            <div class="table-container">
                <table class="tutors-table" id="tutorsTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Experience</th>
                            <th>Rating</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Type</th>
                            <th>Referrals</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tutorsTableBody">
                    </tbody>
                </table>
            </div>

            <!-- No Data State -->
            <div id="noData" class="no-data" style="display: none;">
                <i class="fas fa-user-slash"></i>
                <p>No tutors found matching your criteria</p>
            </div>
        </div>
    </div>

    <!-- Map Modal -->
    <div id="mapModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="mapTitle"><i class="fas fa-map-marker-alt"></i> Tutor Location</h3>
                <button class="close" onclick="closeMapModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; width: 100%; background: var(--gray-100); display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid var(--gray-200);">
                    <div style="text-align: center; color: var(--gray-500);">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <p>Map integration ready</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">Connect with Google Maps, OpenStreetMap, or preferred mapping service</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        class TutorAdmin {
            constructor() {
                this.allTutors = [];
                this.filteredTutors = [];
                this.currentPage = 1;
                this.pageSize = 50;
            }

            async init() {
                try {
                    // Load initial data from PHP
                    this.allTutors = <?php echo json_encode($tutors); ?>;
                    this.filteredTutors = [...this.allTutors];
                    
                    this.hideLoading();
                    
                    <?php if ($error): ?>
                        this.showError('<?php echo addslashes($error); ?>');
                    <?php else: ?>
                        this.showTable();
                        await this.updateStatistics();
                        this.displayTutors(this.allTutors);
                    <?php endif; ?>
                } catch (error) {
                    console.error('Initialization error:', error);
                    this.showError('Failed to initialize admin panel');
                }
            }

            async updateStatistics() {
                try {
                    const response = await fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=get_stats'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        const stats = result.data;
                        document.getElementById('totalTutors').textContent = stats.total || 0;
                        document.getElementById('activeTutors').textContent = stats.active || 0;
                        document.getElementById('premiumTutors').textContent = stats.premium || 0;
                        document.getElementById('avgRating').textContent = stats.avg_rating || '0.0';
                        document.getElementById('totalReferrals').textContent = stats.referrals || 0;
                        document.getElementById('paidTutors').textContent = stats.paid || 0;
                    }
                } catch (error) {
                    console.error('Failed to update statistics:', error);
                }
            }

            displayTutors(tutors) {
                const tbody = document.getElementById('tutorsTableBody');
                const tutorCount = document.getElementById('tutorCount');
                
                tbody.innerHTML = '';
                tutorCount.textContent = `Showing ${tutors.length} tutors`;

                if (tutors.length === 0) {
                    this.showNoData();
                    return;
                }

                this.hideNoData();

                tutors.forEach(tutor => {
                    const row = this.createTutorRow(tutor);
                    tbody.appendChild(row);
                });
            }

            createTutorRow(tutor) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div style="font-weight: 500;">${tutor.name || 'N/A'}</div>
                        <div style="font-size: 0.8rem; color: var(--gray-500);">${tutor.profession || 'N/A'}</div>
                    </td>
                    <td>
                        <div>${tutor.email || 'N/A'}</div>
                        <div style="font-size: 0.8rem; color: var(--gray-500);">${tutor.phone || 'N/A'}</div>
                    </td>
                    <td>
                        <div>${tutor.city || 'N/A'}</div>
                        ${tutor.latitude && tutor.longitude ? 
                            `<button class="btn btn-success btn-sm" onclick="tutorAdmin.showMap('${tutor.name}', ${tutor.latitude}, ${tutor.longitude})">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>` : 
                            '<span style="color: var(--gray-400); font-size: 0.8rem;">No location</span>'}
                    </td>
                    <td>${tutor.experience || 'N/A'}</td>
                    <td>
                        <div class="rating">
                            <i class="fas fa-star"></i> ${tutor.rating || '0.0'}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--gray-500);">(${tutor.rating_count || 0} reviews)</div>
                    </td>
                    <td>
                        <span class="status ${tutor.plan && tutor.plan.toLowerCase() === 'premium' ? 'paid' : 'unpaid'}">
                            ${tutor.plan || 'Basic'}
                        </span>
                    </td>
                    <td>
                        <select class="status ${tutor.status}" onchange="tutorAdmin.updateStatus(${tutor.id}, this.value)" style="border: none; background: transparent; font-weight: 500;">
                            <option value="active" ${tutor.status === 'active' ? 'selected' : ''}>Active</option>
                            <option value="inactive" ${tutor.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </td>
                    <td>
                        <span class="status ${tutor.payment_status || 'unpaid'}">${tutor.payment_status || 'Unpaid'}</span>
                    </td>
                    <td>
                        <span class="status ${tutor.type === 'institution' ? 'paid' : 'unpaid'}">
                            ${tutor.type || 'Individual'}
                        </span>
                    </td>
                    <td>${tutor.referred_by || 0}</td>
                    <td>
                        <div>${tutor.created_at ? new Date(tutor.created_at).toLocaleDateString() : 'N/A'}</div>
                        <div style="font-size: 0.8rem; color: var(--gray-500);">
                            ${tutor.created_at ? new Date(tutor.created_at).toLocaleTimeString() : ''}
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-danger btn-sm" onclick="tutorAdmin.deleteTutor(${tutor.id})" title="Delete Tutor">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                return row;
            }

            async searchTutors() {
                try {
                    this.showLoading();
                    
                    const filters = {
                        name: document.getElementById('searchName').value,
                        type: document.getElementById('filterType').value,
                        profession: document.getElementById('searchProfession').value,
                        payment_status: document.getElementById('filterPaymentStatus').value,
                        status: document.getElementById('filterStatus').value,
                        city: document.getElementById('searchCity').value
                    };

                    const formData = new URLSearchParams();
                    formData.append('action', 'search');
                    Object.keys(filters).forEach(key => {
                        if (filters[key]) formData.append(key, filters[key]);
                    });

                    const response = await fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: formData.toString()
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.filteredTutors = result.data;
                        this.hideLoading();
                        this.showTable();
                        this.displayTutors(this.filteredTutors);
                    } else {
                        throw new Error('Search failed');
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    this.hideLoading();
                    this.showError('Failed to search tutors. Please try again.');
                }
            }

            resetSearch() {
                document.getElementById('searchName').value = '';
                document.getElementById('filterType').value = '';
                document.getElementById('searchProfession').value = '';
                document.getElementById('filterPaymentStatus').value = '';
                document.getElementById('filterStatus').value = '';
                document.getElementById('searchCity').value = '';
                
                this.filteredTutors = [...this.allTutors];
                this.displayTutors(this.filteredTutors);
                this.hideError();
            }

            async refreshData() {
                try {
                    this.showLoading();
                    location.reload(); // Simple refresh for now
                } catch (error) {
                    console.error('Refresh error:', error);
                    this.hideLoading();
                    this.showError('Failed to refresh data');
                }
            }

            async updateStatus(tutorId, newStatus) {
                try {
                    const response = await fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=update_status&id=${tutorId}&status=${newStatus}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showSuccess(result.message);
                        // Update local data
                        const tutor = this.allTutors.find(t => t.id == tutorId);
                        if (tutor) tutor.status = newStatus;
                        await this.updateStatistics();
                    } else {
                        this.showError(result.message || 'Failed to update status');
                    }
                } catch (error) {
                    console.error('Update status error:', error);
                    this.showError('Failed to update tutor status');
                }
            }

            async deleteTutor(tutorId) {
                if (!confirm('Are you sure you want to delete this tutor? This action cannot be undone.')) {
                    return;
                }

                try {
                    const response = await fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=delete_tutor&id=${tutorId}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showSuccess(result.message);
                        // Remove from local data
                        this.allTutors = this.allTutors.filter(t => t.id != tutorId);
                        this.filteredTutors = this.filteredTutors.filter(t => t.id != tutorId);
                        this.displayTutors(this.filteredTutors);
                        await this.updateStatistics();
                    } else {
                        this.showError(result.message || 'Failed to delete tutor');
                    }
                } catch (error) {
                    console.error('Delete tutor error:', error);
                    this.showError('Failed to delete tutor');
                }
            }

            viewTutor(tutorId) {
                const tutor = this.allTutors.find(t => t.id == tutorId);
                if (!tutor) return;

                // Create a detailed view modal (you can enhance this)
                alert(`Tutor Details:\n\nName: ${tutor.name}\nEmail: ${tutor.email}\nPhone: ${tutor.phone}\nCity: ${tutor.city}\nExperience: ${tutor.experience}\nRating: ${tutor.rating}\nPlan: ${tutor.plan}\nStatus: ${tutor.status}`);
            }

            showMap(name, lat, lng) {
                document.getElementById('mapTitle').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${name}'s Location`;
                document.getElementById('map').innerHTML = `
                    <div style="text-align: center; padding: 30px;">
                        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                            <h4 style="color: var(--gray-900); margin-bottom: 15px;">
                                <i class="fas fa-map-pin" style="color: var(--danger-color);"></i> 
                                ${name}
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div>
                                    <strong>Latitude:</strong><br>
                                    <code style="background: var(--gray-100); padding: 4px 8px; border-radius: 4px;">${lat}</code>
                                </div>
                                <div>
                                    <strong>Longitude:</strong><br>
                                    <code style="background: var(--gray-100); padding: 4px 8px; border-radius: 4px;">${lng}</code>
                                </div>
                            </div>
                            <div style="margin-top: 20px;">
                                <a href="https://www.google.com/maps?q=${lat},${lng}" 
                                   target="_blank" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Open in Google Maps
                                </a>
                                <a href="https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}&zoom=15" 
                                   target="_blank" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-map"></i> Open in OpenStreetMap
                                </a>
                            </div>
                        </div>
                        <div style="color: var(--gray-500); font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i>
                            Integrate with your preferred mapping service for interactive maps
                        </div>
                    </div>
                `;
                document.getElementById('mapModal').style.display = 'block';
            }

            closeMapModal() {
                document.getElementById('mapModal').style.display = 'none';
            }

            showLoading() {
                document.getElementById('loading').style.display = 'block';
                document.getElementById('tutorsTable').style.display = 'none';
                document.getElementById('noData').style.display = 'none';
            }

            hideLoading() {
                document.getElementById('loading').style.display = 'none';
            }

            showTable() {
                document.getElementById('tutorsTable').style.display = 'table';
                document.getElementById('noData').style.display = 'none';
            }

            showNoData() {
                document.getElementById('tutorsTable').style.display = 'none';
                document.getElementById('noData').style.display = 'block';
            }

            hideNoData() {
                document.getElementById('noData').style.display = 'none';
            }

            showError(message) {
                const errorDiv = document.getElementById('error');
                errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
                errorDiv.style.display = 'block';
                setTimeout(() => this.hideError(), 5000);
            }

            hideError() {
                document.getElementById('error').style.display = 'none';
            }

            showSuccess(message) {
                const successDiv = document.getElementById('success');
                successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
                successDiv.style.display = 'block';
                setTimeout(() => this.hideSuccess(), 3000);
            }

            hideSuccess() {
                document.getElementById('success').style.display = 'none';
            }
        }

        // Initialize the admin panel
        const tutorAdmin = new TutorAdmin();

        // Global functions for backward compatibility
        function searchTutors() {
            tutorAdmin.searchTutors();
        }

        function resetSearch() {
            tutorAdmin.resetSearch();
        }

        function refreshData() {
            tutorAdmin.refreshData();
        }

        function showMap(name, lat, lng) {
            tutorAdmin.showMap(name, lat, lng);
        }

        function closeMapModal() {
            tutorAdmin.closeMapModal();
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            tutorAdmin.init();
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('mapModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Handle Enter key in search inputs
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                const searchInputs = ['searchName', 'searchProfession', 'searchCity'];
                if (searchInputs.includes(event.target.id)) {
                    searchTutors();
                }
            }
        });

        // Auto-refresh every 5 minutes
        setInterval(() => {
            tutorAdmin.updateStatistics();
        }, 300000);

        console.log('Dooars Tutors Admin Panel v2.0 loaded successfully');
    </script>
</body>
</html>