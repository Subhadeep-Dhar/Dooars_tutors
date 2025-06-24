// Unified filter state management using Set
const filterState = {
    class: new Set(),
    board: new Set(),
    subject: new Set()
};

// Toggle dropdown visibility
function toggleDropdown(filterType) {
    const dropdown = document.getElementById(`${filterType}-dropdown`);
    const label = dropdown.previousElementSibling;

    // Close other dropdowns
    document.querySelectorAll('.filter-dropdown').forEach(dd => {
        if (dd !== dropdown) {
            dd.classList.remove('active');
            dd.previousElementSibling.classList.remove('active');
        }
    });

    // Toggle current
    dropdown.classList.toggle('active');
    label.classList.toggle('active');
}

// Toggle filter selection
function toggleFilter(filterType, value) {
    const checkbox = document.getElementById(`${filterType}-${value}`);
    const countElement = document.getElementById(`${filterType}-count`);

    if (filterState[filterType].has(value)) {
        filterState[filterType].delete(value);
        checkbox.classList.remove('checked');
    } else {
        filterState[filterType].add(value);
        checkbox.classList.add('checked');
    }

    const count = filterState[filterType].size;
    countElement.textContent = count === 0 ? '0 selected' : `${count} selected`;

    if (event) event.stopPropagation();

    performSearch();
}

// Search logic
function performSearch() {
    const searchInput = document.querySelector('.search-input')?.value.trim() || '';
    const location = document.querySelector('.location-select')?.value || '';

    const searchInputField = document.getElementById('searchInput');
    const locationSelect = document.getElementById('locationSelect');

    if (searchInputField) searchInputField.value = searchInput;
    if (locationSelect) locationSelect.value = location;

    if (typeof selectedFilters !== 'undefined') {
        selectedFilters.class = Array.from(filterState.class);
        selectedFilters.board = Array.from(filterState.board);
        selectedFilters.subject = Array.from(filterState.subject);
    }

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

// Explore board
function exploreBoard(board) {
    window.location.href = `search.php?board=${encodeURIComponent(board)}`;
}

// Mobile menu toggle
function toggleMobileMenu() {
    document.querySelector('.mobile-menu-btn')?.classList.toggle('active');
    document.querySelector('.mobile-nav')?.classList.toggle('active');
    document.querySelector('.mobile-overlay')?.classList.toggle('active');
    document.body.style.overflow = document.querySelector('.mobile-nav')?.classList.contains('active') ? 'hidden' : '';
}

// Close dropdowns if clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('.filter-group')) {
        document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
            dropdown.classList.remove('active');
            dropdown.previousElementSibling.classList.remove('active');
        });
    }
});

// Prevent dropdown from closing when clicked inside
document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', e => e.stopPropagation());
});

document.addEventListener('DOMContentLoaded', function () {
    const isHashLink = href => href && href.startsWith('#');

    // Desktop and mobile nav links
    document.querySelectorAll('.nav-links a, .mobile-nav a').forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // ✅ INTERNAL LINK: Smooth scroll
            if (isHashLink(href)) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }

            // ✅ Close mobile menu on any click
            document.querySelector('.mobile-menu-btn')?.classList.remove('active');
            document.querySelector('.mobile-nav')?.classList.remove('active');
            document.querySelector('.mobile-overlay')?.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Mobile toggle button
    document.querySelector('.mobile-menu-btn')?.addEventListener('click', () => {
        document.querySelector('.mobile-menu-btn').classList.toggle('active');
        document.querySelector('.mobile-nav').classList.toggle('active');
        document.querySelector('.mobile-overlay').classList.toggle('active');
        document.body.style.overflow = document.querySelector('.mobile-nav').classList.contains('active') ? 'hidden' : '';
    });

    // Mobile overlay click closes menu
    document.querySelector('.mobile-overlay')?.addEventListener('click', () => {
        document.querySelector('.mobile-menu-btn')?.classList.remove('active');
        document.querySelector('.mobile-nav')?.classList.remove('active');
        document.querySelector('.mobile-overlay')?.classList.remove('active');
        document.body.style.overflow = '';
    });
});




// DOM Ready
document.addEventListener('DOMContentLoaded', function () {
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');

    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') performSearch();
        });
    }

    // Toggle mobile menu
    document.querySelector('.mobile-menu-btn')?.addEventListener('click', toggleMobileMenu);
    document.querySelector('.mobile-overlay')?.addEventListener('click', toggleMobileMenu);

    // Link click (mobile): smooth scroll for # links, allow normal for external
    document.querySelectorAll('.mobile-nav a, .nav-links a').forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
                toggleMobileMenu(); // close mobile nav
            } else {
                // external .php or .html: allow navigation, just close mobile menu if open
                if (document.querySelector('.mobile-nav')?.classList.contains('active')) {
                    toggleMobileMenu();
                }
            }
        });
    });

    // Card hover effects
    document.querySelectorAll('.board-card').forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
