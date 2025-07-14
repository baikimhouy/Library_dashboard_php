<div class="mb-3">
<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookname = $_POST['bookname'];
    $bookcode = $_POST['bookcode'];
    $booknote = $_POST['booknote'];
    $created_at = $_POST['created_at'];

    $stmt = $pdo->prepare("INSERT INTO booklist (bookname, bookcode,booknote,created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$bookname, $bookcode, $booknote, $created_at]);

    header("Location: index.php?added=1");
    
    exit();
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-romantic-deepblue">Add a New Book</h1>
        <a href="books.php" class="flex items-center text-romantic-deepblue hover:text-romantic-lightblue">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Book Collection
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue px-6 py-4">
            <h2 class="text-xl font-semibold text-white">New Book Details</h2>
        </div>
        
        <form method="post" class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Book Name -->
                <div class="space-y-2">
                    <label for="bookname" class="block text-sm font-medium text-gray-700">Book Title</label>
                    <input 
                        type="text" 
                        id="bookname" 
                        name="bookname" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                        placeholder="Enter the book title"
                    >
                </div>
                
                <!-- Book Code -->
                <div class="space-y-2">
                    <label for="bookcode" class="block text-sm font-medium text-gray-700">Book Code</label>
                    <input 
                        type="text" 
                        id="bookcode" 
                        name="bookcode" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                        placeholder="Enter unique book code"
                    >
                </div>
                
                <!-- Author -->
                <div class="space-y-2">
                    <label for="author" class="block text-sm font-medium text-gray-700">Book Note</label>
                    <input 
                        type="text" 
                        id="booknote" 
                        name="booknote" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                        placeholder="Enter note"
                    >
                </div>
                
                <!-- Published Year -->
                <div class="space-y-2">
                    <label for="published_year" class="block text-sm font-medium text-gray-700">Created Date</label>
                    <input 
                        type="number" 
                        id="created_at" 
                        name="created_at" 
                        min="1000" 
                        max="<?= date('Y') + 1 ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                        placeholder="Year of publication"
                    >
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="books.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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

<?php require_once '../includes/footer.php'; ?>