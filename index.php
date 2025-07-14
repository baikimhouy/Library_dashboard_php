<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Dashboard</title>
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
                            pale: '#EBFBFA'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #EBFBFA 0%, #8CCDE9 50%, #5688C9 100%);
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
<body class="bg-romantic-pale">
    <div class="container mx-auto px-4 py-8">

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
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Add New Student
                    </a>
                    <a href="books/add.php" class="block w-full px-4 py-3 bg-romantic-pale text-romantic-deepblue rounded-lg hover:bg-romantic-lightblue transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Book
                    </a>
                    <a href="transactions/borrow.php" class="block w-full px-4 py-3 bg-romantic-pale text-romantic-deepblue rounded-lg hover:bg-romantic-lightblue transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
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
                                            <div class="font-medium text-gray-900"><?= $item['firstname'] ?> <?= $item['lastname'] ?></div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600"><?= $item['bookname'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600"><?= $item['bookcode'] ?></td>
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
                            <svg class="mx-auto h-12 w-12 text-romantic-lightblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
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
                                    <svg class="h-6 w-6 text-romantic-deepblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900"><?= $book['bookname'] ?></p>
                                    <p class="text-sm text-romantic-lightblue">Added recently</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-romantic-pale text-romantic-deepblue">
                                <?= $book['bookcode'] ?>
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
                                        <svg class="h-6 w-6 text-romantic-deepblue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= $student['firstname'] ?> <?= $student['lastname'] ?></p>
                                    <p class="text-sm text-romantic-lightblue truncate"><?= $student['email'] ?></p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-romantic-pale text-romantic-deepblue">New</span>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>