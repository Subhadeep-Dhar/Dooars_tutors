<?php
        // Connect to database
        $conn = new mysqli("localhost:3307", "root", "", "dooars_tutors");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query for organizations and events
        $sql = "SELECT * FROM gallery 
        WHERE status = 'active' 
        ORDER BY date_added DESC, priority DESC 
        LIMIT 20";

        $result = $conn->query($sql);

        if ($result->num_rows > 0): 
    ?>

    <section class="gallery-section">
        <div class="section-header">
            <h2 style="color:#12181e">Our Organizations & Events</h2>
            <p class="section-subtitle" style="color:#12181e">Discover the vibrant community of educational organizations and memorable events that shape our learning environment.</p>
        </div>
        
        <div class="gallery-tabs">
            <button class="tab-btn active" data-filter="all">All</button>
            <button class="tab-btn" data-filter="organizations">Organizations</button>
            <button class="tab-btn" data-filter="events">Events</button>
            <button class="tab-btn" data-filter="workshops">Workshops</button>
            <button class="tab-btn" data-filter="competitions">Competitions</button>
        </div>

        <div class="gallery-grid" id="galleryGrid">
            <?php 
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            
            foreach ($items as $index => $item): 
                $img = (!empty($item['image']) && file_exists('uploads/gallery/' . $item['image']))
                    ? $item['image'] : 'default-gallery.jpg';
                $category = strtolower($item['category'] ?? 'general');
            ?>
            <div class="gallery-item" data-category="<?php echo htmlspecialchars($category); ?>" data-index="<?php echo $index; ?>">
                <div class="gallery-card">
                    <div class="image-container">
                        <img src="uploads/gallery/<?php echo htmlspecialchars($img); ?>" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                             class="gallery-img" 
                             loading="lazy">
                        <div class="image-overlay">
                            <div class="overlay-content">
                                <button class="view-btn" onclick="openLightbox(<?php echo $index; ?>)">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    View
                                </button>
                                <?php if (!empty($item['link'])): ?>
                                <a href="<?php echo htmlspecialchars($item['link']); ?>" 
                                   target="_blank" 
                                   class="external-link-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15,3 21,3 21,9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="category-badge"><?php echo htmlspecialchars(ucfirst($item['category'] ?? 'General')); ?></div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <?php if (!empty($item['description'])): ?>
                        <p class="card-description"><?php echo htmlspecialchars(substr($item['description'], 0, 120)); ?><?php echo strlen($item['description']) > 120 ? '...' : ''; ?></p>
                        <?php endif; ?>
                        
                        <div class="card-meta">
                            <?php if (!empty($item['organization_name'])): ?>
                            <div class="meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                </svg>
                                <span><?php echo htmlspecialchars($item['organization_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['event_date'])): ?>
                            <div class="meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <span><?php echo date('M d, Y', strtotime($item['event_date'])); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['location'])): ?>
                            <div class="meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span><?php echo htmlspecialchars($item['location']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="load-more-container">
            <button class="load-more-btn" id="loadMoreBtn">Load More</button>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="lightbox-overlay" id="lightboxOverlay">
        <div class="lightbox-container">
            <button class="lightbox-close" onclick="closeLightbox()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="lightbox-content">
                <div class="lightbox-image-container">
                    <img id="lightboxImage" src="" alt="">
                    <div class="lightbox-nav">
                        <button class="nav-btn prev-btn" onclick="previousImage()">‹</button>
                        <button class="nav-btn next-btn" onclick="nextImage()">›</button>
                    </div>
                </div>
                <div class="lightbox-info">
                    <h3 id="lightboxTitle"></h3>
                    <p id="lightboxDescription"></p>
                    <div class="lightbox-meta" id="lightboxMeta"></div>
                </div>
            </div>
            <div class="lightbox-counter">
                <span id="lightboxCounter"></span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = <?php echo json_encode($items); ?>;
            const tabBtns = document.querySelectorAll('.tab-btn');
            const galleryGrid = document.getElementById('galleryGrid');
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            let currentFilter = 'all';
            let currentLightboxIndex = 0;
            let filteredItems = galleryItems;
            let visibleItems = 8; // Show 8 items initially

            // Tab filtering
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    
                    // Update active tab
                    tabBtns.forEach(tab => tab.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter items
                    filterGallery(filter);
                });
            });

            function filterGallery(filter) {
                currentFilter = filter;
                const items = document.querySelectorAll('.gallery-item');
                
                items.forEach(item => {
                    const category = item.dataset.category;
                    if (filter === 'all' || category === filter) {
                        item.style.display = 'block';
                        item.classList.add('fade-in');
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('fade-in');
                    }
                });

                // Update filtered items for lightbox
                filteredItems = galleryItems.filter(item => 
                    filter === 'all' || (item.category && item.category.toLowerCase() === filter)
                );

                // Update load more button visibility
                updateLoadMoreButton();
            }

            function updateLoadMoreButton() {
                const visibleFilteredItems = document.querySelectorAll(
                    `.gallery-item[data-category${currentFilter === 'all' ? '' : `="${currentFilter}"`}]:not([style*="display: none"])`
                );
                
                if (visibleFilteredItems.length >= filteredItems.length) {
                    loadMoreBtn.style.display = 'none';
                } else {
                    loadMoreBtn.style.display = 'block';
                }
            }

            // Load more functionality
            loadMoreBtn.addEventListener('click', function() {
                visibleItems += 4;
                // Add logic here to load more items from database if needed
                updateLoadMoreButton();
            });

            // Lightbox functionality
            window.openLightbox = function(index) {
                currentLightboxIndex = index;
                updateLightbox();
                document.getElementById('lightboxOverlay').classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            window.closeLightbox = function() {
                document.getElementById('lightboxOverlay').classList.remove('active');
                document.body.style.overflow = 'auto';
            };

            window.nextImage = function() {
                currentLightboxIndex = (currentLightboxIndex + 1) % filteredItems.length;
                updateLightbox();
            };

            window.previousImage = function() {
                currentLightboxIndex = currentLightboxIndex === 0 ? filteredItems.length - 1 : currentLightboxIndex - 1;
                updateLightbox();
            };

            function updateLightbox() {
                const item = filteredItems[currentLightboxIndex];
                if (!item) return;

                const img = item.image && item.image !== '' ? item.image : 'default-gallery.jpg';
                
                document.getElementById('lightboxImage').src = `uploads/gallery/${img}`;
                document.getElementById('lightboxTitle').textContent = item.title || '';
                document.getElementById('lightboxDescription').textContent = item.description || '';
                document.getElementById('lightboxCounter').textContent = `${currentLightboxIndex + 1} / ${filteredItems.length}`;
                
                // Update meta info
                let metaHTML = '';
                if (item.organization_name) {
                    metaHTML += `<div class="meta-item"><strong>Organization:</strong> ${item.organization_name}</div>`;
                }
                if (item.event_date) {
                    metaHTML += `<div class="meta-item"><strong>Date:</strong> ${new Date(item.event_date).toLocaleDateString()}</div>`;
                }
                if (item.location) {
                    metaHTML += `<div class="meta-item"><strong>Location:</strong> ${item.location}</div>`;
                }
                document.getElementById('lightboxMeta').innerHTML = metaHTML;
            }

            // Keyboard navigation for lightbox
            document.addEventListener('keydown', function(e) {
                if (document.getElementById('lightboxOverlay').classList.contains('active')) {
                    if (e.key === 'Escape') closeLightbox();
                    if (e.key === 'ArrowLeft') previousImage();
                    if (e.key === 'ArrowRight') nextImage();
                }
            });

            // Close lightbox on overlay click
            document.getElementById('lightboxOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeLightbox();
            });

            // Initialize
            updateLoadMoreButton();
        });
    </script>

    <style>
        .gallery-section {
            padding: 80px 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .gallery-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 12px 24px;
            border: 2px solid #e9ecef;
            background: white;
            color: #6c757d;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .tab-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-item {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .gallery-item.fade-in {
            animation: fadeInUp 0.6s ease forwards;
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

        .gallery-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .gallery-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .image-container {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-card:hover .gallery-img {
            transform: scale(1.05);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-card:hover .image-overlay {
            opacity: 1;
        }

        .overlay-content {
            display: flex;
            gap: 15px;
        }

        .view-btn, .external-link-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .view-btn:hover, .external-link-btn:hover {
            background: white;
            transform: scale(1.05);
        }

        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .card-content {
            padding: 25px;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .card-description {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .meta-item svg {
            color: #667eea;
        }

        .load-more-container {
            text-align: center;
            margin-top: 50px;
        }

        .load-more-btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .load-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Lightbox Styles */
        .lightbox-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .lightbox-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .lightbox-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .lightbox-content {
            max-width: 90%;
            max-height: 90%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .lightbox-image-container {
            position: relative;
            flex: 1;
            min-height: 400px;
        }

        .lightbox-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            transform: translateY(-50%);
        }

        .nav-btn {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: white;
            transform: scale(1.1);
        }

        .lightbox-info {
            width: 350px;
            padding: 30px;
            background: white;
        }

        .lightbox-info h3 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .lightbox-info p {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .lightbox-meta .meta-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f3f4;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            transition: all 0.3s ease;
        }

        .lightbox-close:hover {
            background: white;
            transform: scale(1.1);
        }

        .lightbox-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            color: #333;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
            
            .lightbox-content {
                flex-direction: column;
                max-width: 95%;
                max-height: 95%;
            }
            
            .lightbox-info {
                width: 100%;
            }
            
            .gallery-tabs {
                gap: 5px;
            }
            
            .tab-btn {
                padding: 8px 16px;
                font-size: 0.8rem;
            }
        }
    </style>

    <?php endif;

    $conn->close();
    ?>