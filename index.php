<?php
if (!ob_get_level()) {
    ob_start();
}

require_once 'includes/config.php';
$base_url = '/Library_Dashboard';

$stats = [
    'students' => $pdo->query("SELECT COUNT(*) FROM student_information")->fetchColumn(),
    'books' => $pdo->query("SELECT COUNT(*) FROM booklist")->fetchColumn(),
    'available_books' => $pdo->query("SELECT COUNT(*) FROM booklist WHERE id NOT IN (SELECT book_id FROM borrow_book WHERE return_date IS NULL)")->fetchColumn(),
    'borrowed_books' => $pdo->query("SELECT COUNT(*) FROM borrow_book WHERE return_date IS NULL")->fetchColumn(),
    'overdue_books' => $pdo->query("SELECT COUNT(*) FROM borrow_book WHERE return_date IS NULL AND borrow_date < DATE_SUB(NOW(), INTERVAL 14 DAY)")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Library Dashboard</title>

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
  <main class="flex-1 md:ml-64 min-h-screen pt-16 md:pt-0">
    <div class="container mx-auto px-4 py-8">
      <!-- Dashboard Content -->
      <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue rounded-xl p-6 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-6">Library Dashboard</h1>
        <div class="flex flex-wrap items-center gap-4">
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
            <span class="font-semibold"><?= $stats['students'] ?> Students</span>
          </div>
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
            <span class="font-semibold"><?= $stats['books'] ?> Total Books</span>
          </div>
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
            <span class="font-semibold"><?= $stats['available_books'] ?> Available</span>
          </div>
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
            <span class="font-semibold"><?= $stats['borrowed_books'] ?> Borrowed</span>
          </div>
          <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
            <span class="font-semibold"><?= $stats['overdue_books'] ?> Overdue</span>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="bg-white card-hover rounded-xl overflow-hidden">
          <div class="bg-romantic-deepblue px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
          </div>
          <div class="p-6 space-y-4">
            <a href="students/add.php" class="block w-full px-4 py-3 bg-romantic-pale text-romantic-deepblue rounded-lg hover:bg-romantic-lightblue transition-colors flex items-center">
              <i class="fas fa-user-plus w-5 h-5 mr-2"></i>
              Add New Student
            </a>
            <a href="books/add.php" class="block w-full px-4 py-3 bg-romantic-pale text-romantic-deepblue rounded-lg hover:bg-romantic-lightblue transition-colors flex items-center">
              <i class="fas fa-book-medical w-5 h-5 mr-2"></i>
              Add New Book
            </a>
            <a href="transactions/borrow.php" class="block w-full px-4 py-3 bg-romantic-pale text-romantic-deepblue rounded-lg hover:bg-romantic-lightblue transition-colors flex items-center">
              <i class="fas fa-exchange-alt w-5 h-5 mr-2"></i>
              Borrow a Book
            </a>
          </div>
        </div>

        <!-- Overdue Books -->
        <div class="bg-white card-hover rounded-xl overflow-hidden lg:col-span-2">
          <div class="bg-gradient-to-r from-romantic-pink to-romantic-deepblue px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Overdue Books</h2>
          </div>
          <div class="p-6">
            <?php
            $overdue = $pdo->query("
              SELECT br.*, s.firstname, s.lastname, b.bookname, b.bookcode,
                     DATEDIFF(NOW(), br.borrow_date) as days_overdue
              FROM borrow_book br
              JOIN student_information s ON br.student_id = s.id
              JOIN booklist b ON br.book_id = b.id
              WHERE br.return_date IS NULL 
              AND br.borrow_date < DATE_SUB(NOW(), INTERVAL 14 DAY)
              ORDER BY days_overdue DESC
              LIMIT 5
            ")->fetchAll();

            if (count($overdue) > 0): ?>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-romantic-pale">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-romantic-deepblue uppercase tracking-wider">Student</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-romantic-deepblue uppercase tracking-wider">Book</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-romantic-deepblue uppercase tracking-wider">Code</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-romantic-deepblue uppercase tracking-wider">Status</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($overdue as $item): ?>
                    <tr class="hover:bg-romantic-pale/50">
                      <td class="px-4 py-3 whitespace-nowrap">
                        <div class="font-medium text-gray-900"><?= htmlspecialchars($item['firstname']) ?> <?= htmlspecialchars($item['lastname']) ?></div>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-gray-600"><?= htmlspecialchars($item['bookname']) ?></td>
                      <td class="px-4 py-3 whitespace-nowrap text-gray-600"><?= htmlspecialchars($item['bookcode']) ?></td>
                      <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                          <?= $item['days_overdue'] ?> days overdue
                        </span>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-8">
                <i class="fas fa-check-circle mx-auto text-5xl text-romantic-lightblue mb-4"></i>
                <h3 class="mt-2 text-lg font-medium text-romantic-deepblue">No overdue books</h3>
                <p class="mt-1 text-sm text-romantic-lightblue">All books have been returned on time.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Activity Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Books -->
        <div class="bg-white card-hover rounded-xl overflow-hidden">
          <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Recent Book Additions</h2>
          </div>
          <div class="p-6">
            <?php
            $recentBooks = $pdo->query("
              SELECT * FROM booklist 
              ORDER BY created_at DESC 
              LIMIT 5
            ")->fetchAll();
            ?>
            <ul class="divide-y divide-gray-200">
              <?php foreach ($recentBooks as $book): ?>
              <li class="py-4 flex items-center justify-between hover:bg-romantic-pale/30 px-2 rounded transition-colors">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 bg-romantic-pale rounded-full flex items-center justify-center">
                    <i class="fas fa-book text-romantic-deepblue"></i>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($book['bookname']) ?></p>
                    <p class="text-sm text-romantic-lightblue">Added on <?= date('M d, Y', strtotime($book['created_at'])) ?></p>
                  </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-romantic-pale text-romantic-deepblue">
                  <?= htmlspecialchars($book['bookcode']) ?>
                </span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <!-- Recent Students -->
        <div class="bg-white card-hover rounded-xl overflow-hidden">
          <div class="bg-gradient-to-r from-romantic-pink to-romantic-lightblue px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Recent Student Registrations</h2>
          </div>
          <div class="p-6">
            <?php
            $recentStudents = $pdo->query("
              SELECT * FROM student_information 
              ORDER BY registerdate DESC 
              LIMIT 5
            ")->fetchAll();
            ?>
            <ul class="divide-y divide-gray-200">
              <?php foreach ($recentStudents as $student): ?>
              <li class="py-4 hover:bg-romantic-pale/30 px-2 rounded transition-colors">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-romantic-pale flex items-center justify-center">
                      <i class="fas fa-user text-romantic-deepblue"></i>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($student['firstname']) ?> <?= htmlspecialchars($student['lastname']) ?></p>
                    <p class="text-sm text-romantic-lightblue truncate"><?= htmlspecialchars($student['email']) ?></p>
                  </div>
                  <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-romantic-pale text-romantic-deepblue">
                      <?= date('M d', strtotime($student['registerdate'])) ?>
                    </span>
                  </div>
                </div>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menu-btn');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');

      // Toggle sidebar on mobile
      menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
      });

      // Close sidebar when clicking overlay
      overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 && 
            !sidebar.contains(e.target) && 
            e.target !== menuBtn) {
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      });

      // Close sidebar when resizing to desktop
      window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('-translate-x-full');
          overlay.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      });
    });
  </script>
</body>
</html>