<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Filter parameters
$status = $_GET['status'] ?? 'all';
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

// Build WHERE clause for filters
$where = [];
$params = [];

if ($status === 'borrowed') {
    $where[] = "br.return_date IS NULL";
} elseif ($status === 'returned') {
    $where[] = "br.return_date IS NOT NULL";
}

if ($student_id > 0) {
    $where[] = "br.student_id = :student_id";
    $params['student_id'] = $student_id;
}

if ($book_id > 0) {
    $where[] = "br.book_id = :book_id";
    $params['book_id'] = $book_id;
}

$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Pagination setup
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Count total records
$total = $pdo->prepare("
    SELECT COUNT(*) 
    FROM borrow_book br
    JOIN student_information s ON br.student_id = s.id
    JOIN booklist b ON br.book_id = b.id
    $whereClause
");
$total->execute($params);
$totalTransactions = $total->fetchColumn();
$pages = ceil($totalTransactions / $perPage);

// Fetch transactions
$stmt = $pdo->prepare("
    SELECT 
        br.*, 
        s.firstname, 
        s.lastname, 
        b.bookname AS original_bookname,
        b.bookcode AS original_bookcode,
        DATEDIFF(IFNULL(br.return_date, NOW()), br.borrow_date) AS days_borrowed
    FROM borrow_book br
    JOIN student_information s ON br.student_id = s.id
    JOIN booklist b ON br.book_id = b.id
    $whereClause
    ORDER BY br.borrow_date DESC
    LIMIT $start, $perPage
");
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Fetch dropdown data
$students = $pdo->query("SELECT id, firstname, lastname FROM student_information ORDER BY lastname, firstname")->fetchAll();
$books = $pdo->query("SELECT id, bookname FROM booklist ORDER BY bookname")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        romantic: {
                            pink: '#BREADD',
                            deepblue: '#5688C9',
                            lightblue: '#8CCDE9',
                            pale: '#EBFBFA',
                            gradient: 'linear-gradient(to right, #8CCDE9, #5688C9)'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .bg-romantic-gradient {
            background: linear-gradient(to right, #8CCDE9, #5688C9);
        }
    </style>
</head>
<body class="bg-romantic-pale min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-romantic-deepblue">
                        <i class="fas fa-exchange-alt mr-2"></i> Transaction History
                    </h1>
                    <p class="text-romantic-lightblue mt-1">Track all book borrowings and returns</p>
                </div>
                
                <a href="borrow.php" class="flex items-center px-5 py-2.5 bg-romantic-pink text-white rounded-lg hover:bg-romantic-deepblue transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-book-medical mr-2"></i> New Borrowing
                </a>
            </div>

            <!-- Status Messages -->
            <?php if (isset($_GET['borrowed'])): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-medium">Success!</p>
                        <p>Book borrowed successfully.</p>
                    </div>
                </div>
            <?php elseif (isset($_GET['returned'])): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-medium">Success!</p>
                        <p>Book returned successfully.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filters Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="bg-romantic-gradient px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-filter mr-2"></i> Filter Transactions
                    </h2>
                </div>
                
                <form method="get" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-tag mr-2 text-romantic-lightblue"></i> Status
                            </label>
                            <div class="relative">
                                <select 
                                    id="status" 
                                    name="status" 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-romantic-pink focus:border-transparent appearance-none bg-white"
                                >
                                    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Transactions</option>
                                    <option value="borrowed" <?= $status === 'borrowed' ? 'selected' : '' ?>>Currently Borrowed</option>
                                    <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Returned</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Student Filter -->
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-user-graduate mr-2 text-romantic-lightblue"></i> Student
                            </label>
                            <div class="relative">
                                <select 
                                    id="student_id" 
                                    name="student_id" 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-romantic-pink focus:border-transparent appearance-none bg-white"
                                >
                                    <option value="0">All Students</option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= $student['id'] ?>" <?= $student_id == $student['id'] ? 'selected' : '' ?>>
                                            <?= $student['lastname'] ?>, <?= $student['firstname'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user-graduate text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Book Filter -->
                        <div>
                            <label for="book_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-book mr-2 text-romantic-lightblue"></i> Book
                            </label>
                            <div class="relative">
                                <select 
                                    id="book_id" 
                                    name="book_id" 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-romantic-pink focus:border-transparent appearance-none bg-white"
                                >
                                    <option value="0">All Books</option>
                                    <?php foreach ($books as $book): ?>
                                        <option value="<?= $book['id'] ?>" <?= $book_id == $book['id'] ? 'selected' : '' ?>>
                                            <?= $book['bookname'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-book text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter Actions -->
                    <div class="mt-6 flex justify-end gap-3">
                        <a href="index.php" class="px-5 py-2.5 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors flex items-center">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 bg-romantic-deepblue text-white rounded-lg hover:bg-romantic-pink transition-colors flex items-center shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-romantic-gradient px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <i class="fas fa-list-alt mr-2"></i> Transaction Records
                        </h2>
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                            <?= $totalTransactions ?> record<?= $totalTransactions != 1 ? 's' : '' ?>
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if (count($transactions) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-user mr-1"></i> Student
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-book mr-1"></i> Book
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-barcode mr-1"></i> Code
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-calendar-plus mr-1"></i> Borrow Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-calendar-check mr-1"></i> Return Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-clock mr-1"></i> Duration
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-info-circle mr-1"></i> Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-cog mr-1"></i> Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($transactions as $transaction): 
                                        $isReturned = !empty($transaction['return_date']);
                                        $duration = $transaction['days_borrowed'];
                                        $isOverdue = !$isReturned && $duration > 14;
                                    ?>
                                        <tr class="hover:bg-romantic-pale transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($transaction['firstname'] . ' ' . $transaction['lastname']) ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <?= htmlspecialchars($transaction['original_bookname']) ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($transaction['original_bookcode']) ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    <?= date('M d, Y', strtotime($transaction['borrow_date'])) ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    <?= $isReturned ? date('M d, Y', strtotime($transaction['return_date'])) : '<span class="text-gray-400">-</span>' ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <span class="text-sm <?= $isOverdue ? 'text-red-600 font-medium' : 'text-gray-500' ?>">
                                                        <?= $duration ?> day<?= $duration != 1 ? 's' : '' ?>
                                                    </span>
                                                    <?php if ($isOverdue): ?>
                                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Overdue
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($isReturned): ?>
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i> Returned
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-book-reader mr-1"></i> Borrowed
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <?php if (!$isReturned): ?>
                                                    <a href="return.php?id=<?= $transaction['id'] ?>" class="text-green-600 hover:text-green-800 flex items-center">
                                                        <i class="fas fa-undo mr-1"></i> Return
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-gray-400">No actions</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pages > 1): ?>
                            <div class="mt-6 flex flex-col md:flex-row items-center justify-between border-t border-gray-200 pt-6">
                                <div class="mb-4 md:mb-0">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium"><?= $start + 1 ?></span> to <span class="font-medium"><?= min($start + $perPage, $totalTransactions) ?></span> of <span class="font-medium"><?= $totalTransactions ?></span> results
                                    </p>
                                </div>
                                <nav class="flex space-x-1">
                                    <?php if ($page > 1): ?>
                                        <a 
                                            href="?page=<?= $page-1 ?>&status=<?= $status ?>&student_id=<?= $student_id ?>&book_id=<?= $book_id ?>" 
                                            class="px-3 py-1 border border-gray-300 rounded-l-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center"
                                        >
                                            <i class="fas fa-chevron-left mr-1"></i> Previous
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
                                            href="?page=<?= $i ?>&status=<?= $status ?>&student_id=<?= $student_id ?>&book_id=<?= $book_id ?>" 
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
                                            href="?page=<?= $page+1 ?>&status=<?= $status ?>&student_id=<?= $student_id ?>&book_id=<?= $book_id ?>" 
                                            class="px-3 py-1 border border-gray-300 rounded-r-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center"
                                        >
                                            Next <i class="fas fa-chevron-right ml-1"></i>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 flex items-center justify-center bg-romantic-pale rounded-full text-romantic-deepblue mb-4">
                                <i class="fas fa-book-open text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions found</h3>
                            <p class="text-gray-500 max-w-md mx-auto">
                                <?= !empty($search) ? 'Your filters didn\'t match any transactions.' : 'There are no transaction records yet.' ?>
                            </p>
                            <?php if (empty($search)): ?>
                                <div class="mt-6">
                                    <a href="borrow.php" class="inline-flex items-center px-5 py-2.5 bg-romantic-pink text-white rounded-lg hover:bg-romantic-deepblue transition-colors shadow-md">
                                        <i class="fas fa-book-medical mr-2"></i> Create First Transaction
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>