// Unified filter state management using Set for better performance
const filterState = {
    class: new Set(),
    board: new Set(),
    subject: new Set()
};

// Toggle dropdown visibility
function toggleDropdown(filterType) {
    const dropdown = document.getElementById(`${filterType}-dropdown`);
    const label = dropdown.previousElementSibling;
    
    // Close all other dropdowns first
    document.querySelectorAll('.filter-dropdown').forEach(dd => {
        if (dd !== dropdown) {
            dd.classList.remove('active');
            dd.previousElementSibling.classList.remove('active');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('active');
    label.classList.toggle('active');
}

// Toggle individual filter selection
function toggleFilter(filterType, value) {
    const checkboxId = `${filterType}-${value}`;
    const checkbox = document.getElementById(checkboxId);
    const countElement = document.getElementById(`${filterType}-count`);

    if (filterState[filterType].has(value)) {
        filterState[filterType].delete(value);
        checkbox.classList.remove('checked');
    } else {
        filterState[filterType].add(value);
        checkbox.classList.add('checked');
    }

    // Update count display
    const count = filterState[filterType].size;
    countElement.textContent = count === 0 ? '0 selected' : `${count} selected`;
    
    // Prevent event from bubbling up to close dropdown
    if (event) event.stopPropagation();
    
    // Trigger search after filter change
    performSearch();
}

// Search functionality - Updated to work with database search
function performSearch() {
    const searchInput = document.querySelector('.search-input')?.value.trim() || '';
    const location = document.querySelector('.location-select')?.value || '';

    // Update the form inputs if they exist (for search page)
    const searchInputField = document.getElementById('searchInput');
    const locationSelect = document.getElementById('locationSelect');
    
    if (searchInputField) searchInputField.value = searchInput;
    if (locationSelect) locationSelect.value = location;

    // Update global selectedFilters for database search
    if (typeof selectedFilters !== 'undefined') {
        selectedFilters.class = Array.from(filterState.class);
        selectedFilters.board = Array.from(filterState.board);
        selectedFilters.subject = Array.from(filterState.subject);
    }

    // Call the database search if on search page
    if (typeof window.performDatabaseSearch === 'function') {
        window.performDatabaseSearch();
    } else {
        console.log('Search:', {
            query: searchInput,
            location: location,
            filters: {
                classes: Array.from(filterState.class),
                boards: Array.from(filterState.board),
                subjects: Array.from(filterState.subject)
            }
        });
    }
}

// Board exploration
function exploreBoard(board) {
    console.log('Exploring board:', board);
    // Redirect to search page with board filter
    window.location.href = `search.php?board=${encodeURIComponent(board)}`;
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const filterGroups = document.querySelectorAll('.filter-group');
    let clickedInside = false;
    
    filterGroups.forEach(group => {
        if (group.contains(event.target)) {
            clickedInside = true;
        }
    });
    
    if (!clickedInside) {
        document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
            dropdown.classList.remove('active');
            dropdown.previousElementSibling.classList.remove('active');
        });
    }
});

// Prevent dropdown from closing when clicking inside it
document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});

// Mobile menu functionality
function toggleMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    if (mobileMenuBtn && mobileNav && mobileOverlay) {
        mobileMenuBtn.classList.toggle('active');
        mobileNav.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        
        // Prevent body scroll when menu is open
        document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
    }
}

// Event listeners for search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    // Mobile menu event listeners
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', toggleMobileMenu);
    }

    // Close mobile menu when clicking on links
    document.querySelectorAll('.mobile-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            console.log('Mobile navigation clicked:', targetId);
            
            // Close mobile menu
            toggleMobileMenu();
            
            if (targetId === 'home') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // alert(`Navigating to ${targetId} page`);
            }
        });
    });

    // Smooth scrolling for desktop navigation links
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            console.log('Navigation clicked:', targetId);
            
            if (targetId === 'home') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // alert(`Navigating to ${targetId} page`);
            }
        });
    });

    // Add interactive hover effects for board cards
    document.querySelectorAll('.board-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});