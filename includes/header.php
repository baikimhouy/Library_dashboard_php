<?php
if (!ob_get_level()) {
    ob_start();
}

require_once 'config.php';
$base_url = '/Library_Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Library Management System</title>

  <script src="https://cdn.tailwindcss.com"></script>
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
        },
      },
    }
  </script>
  <style>
    /* Custom styles */
    .sidebar-transition {
      transition: transform 0.3s ease-in-out;
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
  </style>
</head>
<body class="bg-romantic-pale min-h-screen flex flex-col md:flex-row">

  <header class="md:hidden bg-gradient-to-r from-romantic-deepblue to-romantic-lightblue text-white p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Library System</h1>
    <button id="menu-btn" class="text-white focus:outline-none text-2xl">
      <i class="fas fa-bars"></i>
    </button>
  </header>

  <!-- Sidebar -->
  <aside id="sidebar" class="w-64 bg-gradient-to-b from-romantic-deepblue to-romantic-lightblue text-white shadow-lg fixed h-full sidebar-transition transform -translate-x-full md:translate-x-0 z-50">
    <div class="p-4 h-full flex flex-col">
      <!-- Logo/Brand -->
      <div class="text-xl font-bold mb-8 pl-2 flex items-center">
        <i class="fas fa-book-reader mr-2"></i>
        <span>Library System</span>
      </div>
      
      <!-- Navigation Links -->
      <nav class="space-y-2 flex-1">
        <a href="<?= $base_url ?>/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-home w-5 h-5 mr-3 text-center"></i>
          <span>Dashboard</span>
        </a>
        
        <a href="<?= $base_url ?>/students/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-user-graduate w-5 h-5 mr-3 text-center"></i>
          <span>Students</span>
        </a>
        
        <a href="<?= $base_url ?>/books/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-book w-5 h-5 mr-3 text-center"></i>
          <span>Books</span>
        </a>
        
        <a href="<?= $base_url ?>/transactions/index.php" class="nav-item flex items-center p-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-center"></i>
          <span>Transactions</span>
        </a>
      </nav>

      <div class="pt-4 border-t border-white/20 mt-auto">
        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-cog w-5 h-5 mr-3 text-center"></i>
          <span>Settings</span>
        </a>
      </div>
    </div>
  </aside>

  <div id="overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden"></div>

  <!-- Main Content -->
