<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if (!empty($search)) {
    $where = "WHERE bookname LIKE :search OR bookcode LIKE :search OR booknote LIKE :search";
}

// Get total books for pagination
$total = $pdo->prepare("SELECT COUNT(*) FROM booklist $where");
if (!empty($search)) {
    $total->execute(['search' => "%$search%"]);
} else {
    $total->execute();
}
$totalBooks = $total->fetchColumn();
$pages = ceil($totalBooks / $perPage);

// Get books with pagination
$stmt = $pdo->prepare("
    SELECT * FROM booklist 
    $where
    ORDER BY bookname
    LIMIT $start, $perPage
");

if (!empty($search)) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}

$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-romantic-deepblue">
                        <i class="fas fa-book-open mr-2"></i> Book Management
                    </h1>
                    <p class="text-romantic-lightblue mt-1">Manage your library collection</p>
                </div>
                
                <!-- Search and Add Book -->
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <form method="get" class="flex-1">
                        <div class="relative">
                            <input type="text" name="search" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-romantic-lightblue focus:outline-none focus:ring-2 focus:ring-romantic-pink focus:border-transparent" 
                                   placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
                            <i class="fas fa-search absolute left-3 top-3 text-romantic-deepblue"></i>
                            <?php if (!empty($search)): ?>
                                <a href="index.php" class="absolute right-3 top-3 text-romantic-deepblue hover:text-romantic-pink">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                    
                    <a href="add.php" class="bg-romantic-pink hover:bg-romantic-deepblue text-white font-medium py-2.5 px-5 rounded-lg transition duration-300 flex items-center justify-center shadow-md hover:shadow-lg">
                        <i class="fas fa-plus-circle mr-2"></i> Add Book
                    </a>
                </div>
            </div>

            <!-- Status Messages -->
            <?php if (isset($_GET['deleted'])): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-medium">Success!</p>
                        <p>Book deleted successfully.</p>
                    </div>
                </div>
            <?php elseif (isset($_GET['added'])): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-medium">Success!</p>
                        <p>Book added successfully.</p>
                    </div>
                </div>
            <?php elseif (isset($_GET['updated'])): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-medium">Success!</p>
                        <p>Book updated successfully.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Books Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-romantic-gradient px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <i class="fas fa-books mr-2"></i> Library Collection
                        </h2>
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                            <?= $totalBooks ?> book<?= $totalBooks != 1 ? 's' : '' ?>
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if (count($books) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-book mr-1"></i> Book Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-barcode mr-1"></i> Book Code
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-info-circle mr-1"></i> Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-calendar-plus mr-1"></i> Added On
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-cog mr-1"></i> Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($books as $book): 
                                        $isBorrowed = $pdo->query("SELECT COUNT(*) FROM borrow_book WHERE book_id = {$book['id']} AND return_date IS NULL")->fetchColumn();
                                    ?>
                                    <tr class="hover:bg-romantic-pale transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($book['bookname']) ?></div>
                                            <?php if (!empty($book['booknote'])): ?>
                                                <div class="text-sm text-gray-500 mt-1"><?= substr(htmlspecialchars($book['booknote']), 0, 50) ?>...</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">
                                            <?= htmlspecialchars($book['bookcode']) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($isBorrowed): ?>
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-book-reader mr-1"></i> Borrowed
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Available
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">
                                            <?= date('M d, Y', strtotime($book['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center space-x-3">
                                                <a href="edit.php?id=<?= $book['id'] ?>" 
                                                   class="w-9 h-9 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition duration-200"
                                                   title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="index.php?delete=<?= $book['id'] ?>" 
                                                   class="w-9 h-9 flex items-center justify-center bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition duration-200"
                                                   title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this book?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                <a href="../transactions/borrow.php?book_id=<?= $book['id'] ?>" 
                                                   class="w-9 h-9 flex items-center justify-center <?= $isBorrowed ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-green-100 text-green-600 hover:bg-green-200' ?> rounded-full transition duration-200"
                                                   title="Borrow"
                                                   <?= $isBorrowed ? 'aria-disabled="true"' : '' ?>>
                                                    <i class="fas fa-book"></i>
                                                </a>
                                            </div>
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
                                        Showing <span class="font-medium"><?= $start + 1 ?></span> to <span class="font-medium"><?= min($start + $perPage, $totalBooks) ?></span> of <span class="font-medium"><?= $totalBooks ?></span> books
                                    </p>
                                </div>
                                <nav class="flex space-x-1">
                                    <?php if ($page > 1): ?>
                                        <a 
                                            href="?page=<?= $page-1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
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
                                            href="?page=<?= $i ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
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
                                            href="?page=<?= $page+1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
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
                                <i class="fas fa-book text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">
                                <?php if (!empty($search)): ?>
                                    No books found
                                <?php else: ?>
                                    Your library is empty
                                <?php endif; ?>
                            </h3>
                            <p class="text-gray-500 max-w-md mx-auto mb-4">
                                <?php if (!empty($search)): ?>
                                    Your search for "<?= htmlspecialchars($search) ?>" didn't match any books.
                                <?php else: ?>
                                    Start building your collection by adding the first book.
                                <?php endif; ?>
                            </p>
                            <a href="add.php" class="inline-flex items-center px-5 py-2.5 bg-romantic-pink text-white rounded-lg hover:bg-romantic-deepblue transition-colors shadow-md">
                                <i class="fas fa-plus mr-2"></i> Add First Book
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>