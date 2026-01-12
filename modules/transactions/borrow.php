<?php
require_once '../../database/migrations/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $book_id = $_POST['book_id'];

    $stmt = $pdo->prepare("SELECT bookname, bookcode FROM booklist WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    // Set dates
    $borrow_date = date('Y-m-d');
    $return_date = null;

    // Record the borrowing
    $stmt = $pdo->prepare("INSERT INTO borrow_book (bookname, bookcode, student_id, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$book['bookname'], $book['bookcode'], $student_id, $book_id, $borrow_date, $return_date]);

    header("Location: index.php?borrowed=1");
    exit();
}

require_once '../../includes/header.php'; // Now safe to include


// Fetch all students and available books
$students = $pdo->query("SELECT * FROM student_information ORDER BY lastname, firstname")->fetchAll();
$books = $pdo->query("SELECT * FROM booklist WHERE id NOT IN (SELECT book_id FROM borrow_book WHERE return_date IS NULL) ORDER BY bookname")->fetchAll();
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Book Borrowing</h2>
        </div>
        
        <form method="post" class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Student Selection -->
                <div class="space-y-2">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                    <select 
                        id="student_id" 
                        name="student_id" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                        <option value="">Select a student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student['id'] ?>">
                                <?= $student['lastname'] ?>, <?= $student['firstname'] ?> (<?= $student['email'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Book Selection -->
                <div class="space-y-2">
                    <label for="book_id" class="block text-sm font-medium text-gray-700">Book</label>
                    <select 
                        id="book_id" 
                        name="book_id" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                        <option value="">Select a book</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= $book['id'] ?>">
                                <?= $book['bookname'] ?> (<?= $book['bookcode'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="index.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-romantic-deepblue text-white rounded-lg hover:bg-romantic-lightblue transition-colors flex items-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                    Borrow Book
                </button>
            </div>
        </form>
    </div>
</div>

