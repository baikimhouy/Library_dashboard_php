<?php
// Start session and handle all PHP logic at the top
session_start();
require_once '../../database/migrations/database.php';

// Handle student export
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_export_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email', 'Gender', 'Registration Date']);

    $stmt = $pdo->query("SELECT * FROM student_information WHERE deleted = 0 ORDER BY lastname, firstname");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

// Handle student deletion before any output
if (isset($_GET['delete_id'])) {
    $studentId = (int)$_GET['delete_id'];
    
    try {
        // Check if student has active book borrowings
        $borrowedCount = $pdo->query("SELECT COUNT(*) FROM borrow_book WHERE student_id = $studentId AND return_date IS NULL")->fetchColumn();
        
        if ($borrowedCount > 0) {
            $_SESSION['error'] = "Cannot delete student - they have active book borrowings";
        } else {
            // Mark student as deleted (soft delete)
            $stmt = $pdo->prepare("UPDATE student_information SET deleted = 1 WHERE id = ?");
            $stmt->execute([$studentId]);
            
            $_SESSION['success'] = "Student deleted successfully";
        }
        
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting student: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
}

// Pagination + Search setup
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;
$search = $_GET['search'] ?? '';
$where = "WHERE deleted = 0";
if (!empty($search)) {
    $where .= " AND (firstname LIKE :search OR lastname LIKE :search OR email LIKE :search)";
}

// Get total students
$total = $pdo->prepare("SELECT COUNT(*) FROM student_information $where");
if (!empty($search)) {
    $total->execute(['search' => "%$search%"]);
} else {
    $total->execute();
}
$totalStudents = $total->fetchColumn();
$pages = ceil($totalStudents / $perPage);

// Get students with pagination
$stmt = $pdo->prepare("
    SELECT * FROM student_information 
    $where
    ORDER BY lastname, firstname
    LIMIT $start, $perPage
");
if (!empty($search)) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$students = $stmt->fetchAll();

// Now include header after all PHP processing
require_once '../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
      <style>
    /* Custom styles */
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
    
    /* Transaction specific styles */
    .bg-romantic-gradient {
      background: linear-gradient(to right, #8CCDE9, #5688C9);
    }
  </style>
</head>
<body class="bg-romantic-pale min-h-screen">

    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden backdrop-blur-sm transition-opacity duration-300"></div>

      <!-- Main Content Area -->
  <main class="flex-1 md:ml-64 lg:ml-64 p-4 md:p-6 transition-all duration-300 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-romantic-deepblue">
                <i class="fas fa-user-graduate mr-2"></i> Student Management
            </h1>
            <p class="text-romantic-lightblue mt-1">Manage all student records</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <a href="add.php" class="flex items-center px-5 py-2.5 bg-romantic-pink text-white rounded-lg hover:bg-romantic-deepblue transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-user-plus mr-2"></i> Add Student
            </a>
            <a href="index.php?export=1" class="flex items-center px-4 py-2.5 bg-romantic-deepblue text-white rounded-lg hover:bg-romantic-lightblue transition-colors shadow-md">
                <i class="fas fa-file-export mr-2"></i> Export
            </a>
        </div>
    </div>

    <!-- Status Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <div>
                <p class="font-medium">Success!</p>
                <p><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <div>
                <p class="font-medium">Error!</p>
                <p><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="bg-romantic-gradient px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-search mr-2"></i> Search Students
            </h2>
        </div>
        
        <form method="get" class="p-6">
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-romantic-pink focus:border-transparent"
                    placeholder="Search by name or email..."
                    value="<?= htmlspecialchars($search) ?>"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-romantic-pink">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 flex flex-wrap justify-end gap-3">
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-romantic-deepblue text-white rounded-lg hover:bg-romantic-pink transition-colors flex items-center shadow-md"
                >
                    <i class="fas fa-filter mr-2"></i> Apply Search
                </button>
            </div>
        </form>
    </div>

    <!-- Students Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-romantic-gradient px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-users mr-2"></i> Student Records
                </h2>
                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm whitespace-nowrap">
                    <?= $totalStudents ?> student<?= $totalStudents != 1 ? 's' : '' ?>
                </span>
            </div>
        </div>
        
        <div class="p-4 md:p-6">
            <?php if (count($students) > 0): ?>
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i> Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-venus-mars mr-1"></i> Gender
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-calendar-alt mr-1"></i> Registered
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-book mr-1"></i> Books
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-cog mr-1"></i> Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($students as $student): 
                                $borrowedCount = $pdo->query("
                                    SELECT COUNT(*) FROM borrow_book 
                                    WHERE student_id = {$student['id']} 
                                    AND return_date IS NULL
                                ")->fetchColumn();
                            ?>
                            <tr class="hover:bg-romantic-pale transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        <?= htmlspecialchars($student['firstname']) ?> <?= htmlspecialchars($student['lastname']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?= htmlspecialchars($student['email']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?= htmlspecialchars($student['gender']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?= date('M d, Y', strtotime($student['registerdate'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($borrowedCount > 0): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-book-open mr-1"></i> <?= $borrowedCount ?> active
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-check-circle mr-1"></i> None
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex justify-start space-x-3">
                                        <a href="edit.php?id=<?= $student['id'] ?>" 
                                           class="w-9 h-9 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition duration-200"
                                           title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="index.php?delete_id=<?= $student['id'] ?>" 
                                           class="w-9 h-9 flex items-center justify-center bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition duration-200"
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this student?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                        <a href="../transactions/borrow.php?student_id=<?= $student['id'] ?>" 
                                           class="w-9 h-9 flex items-center justify-center bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition duration-200"
                                           title="View Borrowed Books">
                                            <i class="fas fa-book-open"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4 mobile-card">
                    <?php foreach ($students as $student): 
                        $borrowedCount = $pdo->query("
                            SELECT COUNT(*) FROM borrow_book 
                            WHERE student_id = {$student['id']} 
                            AND return_date IS NULL
                        ")->fetchColumn();
                    ?>
                    <div class="mobile-table-row">
                        <div class="mobile-table-cell">
                            <span class="mobile-label">Name:</span>
                            <div class="font-medium text-gray-900 text-right">
                                <?= htmlspecialchars($student['firstname']) ?> <?= htmlspecialchars($student['lastname']) ?>
                            </div>
                        </div>
                        <div class="mobile-table-cell">
                            <span class="mobile-label">Email:</span>
                            <span class="text-gray-600"><?= htmlspecialchars($student['email']) ?></span>
                        </div>
                        <div class="mobile-table-cell">
                            <span class="mobile-label">Gender:</span>
                            <span class="text-gray-600"><?= htmlspecialchars($student['gender']) ?></span>
                        </div>
                        <div class="mobile-table-cell">
                            <span class="mobile-label">Registered:</span>
                            <span class="text-gray-600"><?= date('M d, Y', strtotime($student['registerdate'])) ?></span>
                        </div>
                        <div class="mobile-table-cell">
                            <span class="mobile-label">Books:</span>
                            <span class="<?= $borrowedCount > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' ?> px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                <?= $borrowedCount > 0 ? '<i class="fas fa-book-open mr-1"></i> ' . $borrowedCount . ' active' : '<i class="fas fa-check-circle mr-1"></i> None' ?>
                            </span>
                        </div>
                        <div class="mobile-actions">
                            <a href="edit.php?id=<?= $student['id'] ?>" 
                               class="w-9 h-9 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition duration-200"
                               title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="index.php?delete_id=<?= $student['id'] ?>" 
                               class="w-9 h-9 flex items-center justify-center bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition duration-200"
                               title="Delete"
                               onclick="return confirm('Are you sure you want to delete this student?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <a href="../transactions/borrow.php?student_id=<?= $student['id'] ?>" 
                               class="w-9 h-9 flex items-center justify-center bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition duration-200"
                               title="View Borrowed Books">
                                <i class="fas fa-book-open"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pages > 1): ?>
                    <div class="mt-6 flex flex-col md:flex-row items-center justify-between border-t border-gray-200 pt-6 gap-4">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?= $start + 1 ?></span> to <span class="font-medium"><?= min($start + $perPage, $totalStudents) ?></span> of <span class="font-medium"><?= $totalStudents ?></span> students
                            </p>
                        </div>
                        <nav class="flex flex-wrap justify-center gap-1">
                            <?php if ($page > 1): ?>
                                <a 
                                    href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                    class="px-3 py-1 border border-gray-300 rounded-l-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center"
                                >
                                    <i class="fas fa-chevron-left mr-1"></i> <span class="hidden sm:inline">Previous</span>
                                </a>
                            <?php endif; ?>
                            
                            <?php 
                            // Show limited page numbers with ellipsis
                            $maxVisiblePages = 5;
                            $startPage = max(1, $page - floor($maxVisiblePages/2));
                            $endPage = min($pages, $startPage + $maxVisiblePages - 1);
                            
                            if ($startPage > 1): ?>
                                <span class="px-3 py-1 border border-gray-300 text-gray-700">...</span>
                            <?php endif;
                            
                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <a 
                                    href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                    class="px-3 py-1 border border-gray-300 <?= ($page == $i) ? 'bg-romantic-deepblue text-white border-romantic-deepblue' : 'text-gray-700 hover:bg-gray-50' ?> transition-colors"
                                >
                                    <?= $i ?>
                                </a>
                            <?php endfor;
                            
                            if ($endPage < $pages): ?>
                                <span class="px-3 py-1 border border-gray-300 text-gray-700">...</span>
                            <?php endif; ?>
                            
                            <?php if ($page < $pages): ?>
                                <a 
                                    href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                    class="px-3 py-1 border border-gray-300 rounded-r-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center"
                                >
                                    <span class="hidden sm:inline">Next</span> <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 flex items-center justify-center bg-romantic-pale rounded-full text-romantic-deepblue mb-4">
                        <i class="fas fa-user-graduate text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No students found</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">
                        <?= !empty($search) ? 'Your search didn\'t match any students.' : 'There are no student records yet.' ?>
                    </p>
                    <?php if (empty($search)): ?>
                        <div class="mt-2">
                            <a href="add.php" class="inline-flex items-center px-5 py-2.5 bg-romantic-pink text-white rounded-lg hover:bg-romantic-deepblue transition-colors shadow-md">
                                <i class="fas fa-user-plus mr-2"></i> Add First Student
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
  </main>
</body>
</html>
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

    // Initialize sidebar on load
    document.addEventListener('DOMContentLoaded', () => {
      if (window.innerWidth >= 768) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
      }
    });
  </script>