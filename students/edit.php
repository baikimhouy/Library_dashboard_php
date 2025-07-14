<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM student_information WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    
    $stmt = $pdo->prepare("UPDATE student_information SET firstname = ?, lastname = ?, email = ?, gender = ? WHERE id = ?");
    $stmt->execute([$firstname, $lastname, $email, $gender, $id]);
    
    header("Location: index.php?updated=1");
    exit();
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-romantic-deepblue">Edit Student</h1>
        <a href="index.php" class="flex items-center text-romantic-deepblue hover:text-romantic-lightblue">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Students
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="bg-gradient-to-r from-romantic-lightblue to-romantic-deepblue px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Student Information</h2>
        </div>
        
        <form method="post" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div class="space-y-2">
                    <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input 
                        type="text" 
                        id="firstname" 
                        name="firstname" 
                        value="<?= htmlspecialchars($student['firstname']) ?>" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                </div>
                
                <!-- Last Name -->
                <div class="space-y-2">
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input 
                        type="text" 
                        id="lastname" 
                        name="lastname" 
                        value="<?= htmlspecialchars($student['lastname']) ?>" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                </div>
                
                <!-- Email -->
                <div class="space-y-2 md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($student['email']) ?>" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-romantic-lightblue focus:border-transparent"
                    >
                </div>
                
                <!-- Gender -->
                <div class="space-y-2 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                    <div class="mt-2 flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input 
                                type="radio" 
                                name="gender" 
                                value="Male" 
                                <?= $student['gender'] === 'Male' ? 'checked' : '' ?> 
                                required
                                class="h-4 w-4 text-romantic-deepblue focus:ring-romantic-lightblue border-gray-300"
                            >
                            <span class="ml-2 text-gray-700">Male</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input 
                                type="radio" 
                                name="gender" 
                                value="Female" 
                                <?= $student['gender'] === 'Female' ? 'checked' : '' ?>
                                class="h-4 w-4 text-romantic-deepblue focus:ring-romantic-lightblue border-gray-300"
                            >
                            <span class="ml-2 text-gray-700">Female</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input 
                                type="radio" 
                                name="gender" 
                                value="Other" 
                                <?= $student['gender'] === 'Other' ? 'checked' : '' ?>
                                class="h-4 w-4 text-romantic-deepblue focus:ring-romantic-lightblue border-gray-300"
                            >
                            <span class="ml-2 text-gray-700">Other</span>
                        </label>
                    </div>
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
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>