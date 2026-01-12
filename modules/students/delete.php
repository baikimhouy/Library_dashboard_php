<?php
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    
    try {
        $borrowedCount = $pdo->query("SELECT COUNT(*) FROM borrow_book WHERE student_id = $deleteId AND return_date IS NULL")->fetchColumn();
        
        if ($borrowedCount > 0) {
            header("Location: index.php?error=Student has active book borrowings and cannot be deleted");
            exit();
        }
        
        $stmt = $pdo->prepare("DELETE FROM student_information WHERE id = ?");
        $stmt->execute([$deleteId]);
        
        header("Location: index.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        header(header: "Location: index.php?error=Error deleting student");
        exit();
    }
}
?>