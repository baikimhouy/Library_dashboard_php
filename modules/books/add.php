<?php
// Start output buffering before anything else
ob_start();

require_once '../../database/migrations/database.php';

// Initialize variables
$errors = [];
$bookname = $bookcode = $booknote = '';
$created_at = date('Y'); // Default to current year

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $bookname = trim($_POST['bookname']);
    $bookcode = trim($_POST['bookcode']);
    $booknote = trim($_POST['booknote']);
    $created_at = (int)$_POST['created_at'];

    // Validate inputs
    if (empty($bookname)) {
        $errors['bookname'] = 'Book title is required';
    } elseif (strlen($bookname) > 255) {
        $errors['bookname'] = 'Book title must be less than 255 characters';
    }

    if (empty($bookcode)) {
        $errors['bookcode'] = 'Book code is required';
    } elseif (strlen($bookcode) > 100) {
        $errors['bookcode'] = 'Book code must be less than 100 characters';
    } else {
        // Check if book code already exists
        $stmt = $pdo->prepare("SELECT id FROM booklist WHERE bookcode = ? AND deleted = 0");
        $stmt->execute([$bookcode]);
        if ($stmt->fetch()) {
            $errors['bookcode'] = 'This book code already exists';
        }
    }

    if ($created_at < 1000 || $created_at > date('Y') + 1) {
        $errors['created_at'] = 'Invalid year';
    }

    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO booklist (bookname, bookcode, booknote, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$bookname, $bookcode, $booknote, $created_at]);

            // Redirect (now safe because of output buffering)
            header("Location: index.php?added=1");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = "Error adding book: " . $e->getMessage();
        }
    }
}

// Load the HTML layout after logic
require_once '../../includes/header.php';
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
    
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
            <h3 class="font-bold mb-2">Please fix the following errors:</h3>
            <ul class="list-disc pl-5">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue px-6 py-4">
            <h2 class="text-xl font-semibold text-white">New Book Details</h2>
        </div>

        <form method="post" class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Book Title -->
                <div class="space-y-2">
                    <label for="bookname" class="block text-sm font-medium text-gray-700">Book Title *</label>
                    <input 
                        type="text" 
                        id="bookname" 
                        name="bookname" 
                        value="<?= htmlspecialchars($bookname) ?>" 
                        required
                        class="mt-1 block w-full border <?= isset($errors['bookname']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg px-4 py-2"
                        placeholder="Enter the book title"
                    >
                </div>

                <!-- Book Code -->
                <div class="space-y-2">
                    <label for="bookcode" class="block text-sm font-medium text-gray-700">Book Code *</label>
                    <input 
                        type="text" 
                        id="bookcode" 
                        name="bookcode" 
                        value="<?= htmlspecialchars($bookcode) ?>" 
                        required
                        class="mt-1 block w-full border <?= isset($errors['bookcode']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg px-4 py-2"
                        placeholder="Enter unique book code"
                    >
                </div>

                <!-- Book Note -->
                <div class="space-y-2">
                    <label for="booknote" class="block text-sm font-medium text-gray-700">Book Note</label>
                    <textarea
                        id="booknote" 
                        name="booknote" 
                        rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2"
                        placeholder="Enter any notes about the book"
                    ><?= htmlspecialchars($booknote) ?></textarea>
                </div>

                <!-- Created Year -->
                <div class="space-y-2">
                    <label for="created_at" class="block text-sm font-medium text-gray-700">Publication Year *</label>
                    <input 
                        type="number" 
                        id="created_at" 
                        name="created_at" 
                        value="<?= htmlspecialchars($created_at) ?>" 
                        min="1000" 
                        max="<?= date('Y') + 1 ?>"
                        class="mt-1 block w-full border <?= isset($errors['created_at']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg px-4 py-2"
                        placeholder="Year of publication"
                    >
                </div>
            </div>

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
                    Add Book
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Flush output at the end
ob_end_flush();
?>
