<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM booklist WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookname = $_POST['bookname'];
    $bookcode = $_POST['bookcode'];
    $booknote = $_POST['booknote'];
    
    $stmt = $pdo->prepare("UPDATE booklist SET bookname = ?, bookcode = ?, booknote = ? WHERE id = ?");
    $stmt->execute([$bookname, $bookcode, $booknote, $id]);
    
    header("Location: index.php?updated=1");
    exit();
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
       
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Book Details</h2>
        </div>
        
        <form method="post" class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Book Name -->
                <div class="space-y-2">
                    <label for="bookname" class="block text-sm font-medium text-gray-700">Book Name</label>
                    <input 
                        type="text" 
                        id="bookname" 
                        name="bookname" 
                        value="<?= htmlspecialchars($book['bookname']) ?>" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                </div>
                
                <!-- Book Code -->
                <div class="space-y-2">
                    <label for="bookcode" class="block text-sm font-medium text-gray-700">Book Code</label>
                    <input 
                        type="text" 
                        id="bookcode" 
                        name="bookcode" 
                        value="<?= htmlspecialchars($book['bookcode']) ?>" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                </div>
                
                <!-- Book Notes -->
                <div class="space-y-2">
                    <label for="booknote" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea 
                        id="booknote" 
                        name="booknote" 
                        rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    ><?= htmlspecialchars($book['booknote']) ?></textarea>
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
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                    </svg>
                    Update Book
                </button>
            </div>
        </form>
    </div>
</div>
