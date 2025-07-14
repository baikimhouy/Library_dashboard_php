<?php
$base_url = '/Library_Dashboard'; // Change to match your project root
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Library Management System</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
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
</head>
<body class="bg-romantic-pale min-h-screen flex flex-col">

<!-- Tailwind Navbar -->
<header class="bg-gradient-to-r from-romantic-deepblue to-romantic-lightblue text-white shadow-lg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo / Brand -->
      <div class="flex items-center">
        <a href="<?= $base_url ?>/index.php" class="text-xl font-bold"></a>
      </div>

      <!-- Menu (desktop) -->
      <div class="hidden md:flex space-x-4">
        <a href="<?= $base_url ?>/index.php" class="hover:bg-white/20 px-3 py-2 rounded">Dashboard</a>
        <a href="<?= $base_url ?>/students/index.php" class="hover:bg-white/20 px-3 py-2 rounded">Students</a>
        <a href="<?= $base_url ?>/books/index.php" class="hover:bg-white/20 px-3 py-2 rounded">Books</a>
        <a href="<?= $base_url ?>/transactions/index.php" class="hover:bg-white/20 px-3 py-2 rounded">Transactions</a>
      </div>

      <!-- Mobile menu button -->
      <div class="md:hidden">
        <button id="menu-btn" class="text-white focus:outline-none">
          ☰
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="md:hidden hidden px-4 pb-4">
    <a href="<?= $base_url ?>/index.php" class="block px-3 py-2 rounded hover:bg-white/20">Dashboard</a>
    <a href="<?= $base_url ?>/students/index.php" class="block px-3 py-2 rounded hover:bg-white/20">Students</a>
    <a href="<?= $base_url ?>/books/index.php" class="block px-3 py-2 rounded hover:bg-white/20">Books</a>
    <a href="<?= $base_url ?>/transactions/index.php" class="block px-3 py-2 rounded hover:bg-white/20">Transactions</a>
  </div>
</header>

<!-- JavaScript to toggle mobile menu -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  });
</script>

<!-- Page content starts -->
<main class="flex-grow container mx-auto px-4 py-8">
