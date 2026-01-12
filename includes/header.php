<?php
// Absolute first thing - start output buffering
if (!ob_get_level()) {
    ob_start();
}

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title>Library Management System</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            romantic: {
              pink: '#BEA8DD',
              deepblue: '#5688C9',
              lightblue: '#8CCDE9',
              pale: '#EBFBFA',
            },
          },
          screens: {
            'xs': '475px',
          },
        },
      },
    }
  </script>
  <style>
    /* Custom styles */
    .sidebar-transition {
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .nav-item {
      transition: all 0.2s ease;
    }
    .nav-item:hover {
      transform: translateX(4px);
    }
    .card-hover {
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(86, 136, 201, 0.3);
    }
    
    /* Mobile responsive improvements */
    @media (max-width: 768px) {
      body {
        overflow-x: hidden;
      }
      #sidebar {
        width: 280px;
      }
      .mobile-tap-friendly {
        min-height: 44px;
        min-width: 44px;
      }
    }
    
    @media (max-width: 475px) {
      #sidebar {
        width: 100%;
      }
      .mobile-header {
        padding: 1rem;
      }
    }
    
    /* Improve touch targets on mobile */
    .touch-target {
      min-height: 44px;
      min-width: 44px;
      display: flex;
      align-items: center;
    }
    
    /* Responsive typography */
    .text-responsive {
      font-size: clamp(0.875rem, 2vw, 1rem);
    }
    
    /* Smooth scrolling */
    html {
      scroll-behavior: smooth;
    }
    
    /* Prevent horizontal overflow */
    .no-overflow {
      max-width: 100%;
      overflow-x: hidden;
    }
  </style>
</head>
<body class="bg-romantic-pale min-h-screen flex flex-col md:flex-row no-overflow">

  <!-- Mobile Header (hidden on desktop) -->
  <header class="md:hidden bg-gradient-to-r from-romantic-deepblue to-romantic-lightblue text-white p-4 flex justify-between items-center mobile-header sticky top-0 z-40">
    <h1 class="text-xl font-bold truncate pr-2">Library System</h1>
    <button id="menu-btn" class="text-white focus:outline-none touch-target" aria-label="Toggle menu" aria-expanded="false">
      <i class="fas fa-bars text-2xl"></i>
    </button>
  </header>

  <!-- Sidebar -->
  <aside id="sidebar" class="w-64 md:w-56 lg:w-64 bg-gradient-to-b from-romantic-deepblue to-romantic-lightblue text-white shadow-xl fixed h-full sidebar-transition transform -translate-x-full md:translate-x-0 z-50 md:z-30">
    <div class="p-4 h-full flex flex-col">
      <!-- Logo/Brand with close button for mobile -->
      <div class="flex items-center justify-between mb-8">
        <div class="text-xl font-bold flex items-center">
          <i class="fas fa-book-reader mr-2 text-2xl"></i>
          
        </div>
        
      </div>
      
      <!-- Navigation Links -->
      <nav class="space-y-1 flex-1 overflow-y-auto" aria-label="Main navigation">
        <a href="/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
          <i class="fas fa-home w-5 h-5 mr-3 text-center text-lg"></i>
          <span class="truncate">Dashboard</span>
          <span class="ml-auto text-xs bg-white/20 px-2 py-1 rounded-full hidden lg:inline">Home</span>
        </a>

        <a href="/modules/students/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
          <i class="fas fa-user-graduate w-5 h-5 mr-3 text-center text-lg"></i>
          <span class="truncate">Students</span>
          <span class="ml-auto text-xs bg-white/20 px-2 py-1 rounded-full hidden lg:inline">Manage</span>
        </a>
        
        <a href="/modules/books/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
          <i class="fas fa-book w-5 h-5 mr-3 text-center text-lg"></i>
          <span class="truncate">Books</span>
          <span class="ml-auto text-xs bg-white/20 px-2 py-1 rounded-full hidden lg:inline">Catalog</span>
        </a>

        <a href="/modules/transactions/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
          <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-center text-lg"></i>
          <span class="truncate">Transactions</span>
          <span class="ml-auto text-xs bg-white/20 px-2 py-1 rounded-full hidden lg:inline">History</span>
        </a>
        
        <!-- Additional navigation items for larger screens -->
        <div class="lg:space-y-1 hidden lg:block mt-4">
          <a href="#" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
            <i class="fas fa-chart-bar w-5 h-5 mr-3 text-center text-lg"></i>
            <span class="truncate">Reports</span>
          </a>
          <a href="#" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
            <i class="fas fa-users w-5 h-5 mr-3 text-center text-lg"></i>
            <span class="truncate">Staff</span>
          </a>
        </div>
      </nav>

      <!-- User/Settings at bottom -->
      <div class="pt-4 border-t border-white/20 mt-auto">
        <div class="flex items-center p-3 mb-2">
          <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center mr-3">
            <i class="fas fa-user text-sm"></i>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium truncate text-responsive">Admin User</p>
            <p class="text-xs text-white/80 truncate">admin@library.com</p>
          </div>
        </div>
        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive">
          <i class="fas fa-cog w-5 h-5 mr-3 text-center text-lg"></i>
          <span>Settings</span>
        </a>
        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/20 transition touch-target text-responsive mt-1">
          <i class="fas fa-sign-out-alt w-5 h-5 mr-3 text-center text-lg"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
  </aside>

  <!-- Overlay for mobile menu -->
  <div id="overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden backdrop-blur-sm transition-opacity duration-300"></div>

  <!-- Main Content Area -->
  

  <!-- JavaScript for responsive sidebar -->
  <script>
    // Mobile menu toggle
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const body = document.body;

    function toggleSidebar() {
      const isOpen = sidebar.classList.contains('translate-x-0');
      
      if (isOpen) {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        menuBtn.setAttribute('aria-expanded', 'false');
        body.style.overflow = 'auto';
      } else {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        menuBtn.setAttribute('aria-expanded', 'true');
        body.style.overflow = 'hidden';
      }
    }

    // Toggle menu
    menuBtn.addEventListener('click', toggleSidebar);
    closeBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking a link on mobile
    document.querySelectorAll('#sidebar a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 768) {
          toggleSidebar();
        }
      });
    });

    // Close sidebar on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && window.innerWidth < 768) {
        if (sidebar.classList.contains('translate-x-0')) {
          toggleSidebar();
        }
      }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.add('hidden');
        menuBtn.setAttribute('aria-expanded', 'false');
        body.style.overflow = 'auto';
      } else {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
      }
    });

    // Prevent body scroll when sidebar is open on mobile
    const observer = new MutationObserver(() => {
      if (sidebar.classList.contains('translate-x-0') && window.innerWidth < 768) {
        body.style.overflow = 'hidden';
      }
    });

    observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    
    // Add active state to current page link
    const currentPath = window.location.pathname;
    document.querySelectorAll('#sidebar a').forEach(link => {
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('bg-white/30', 'font-bold');
        link.classList.remove('hover:bg-white/20');
      }
    });
  </script>
</body>
</html>