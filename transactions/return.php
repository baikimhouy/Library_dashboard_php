<?php
require_once '../../includes/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("UPDATE borrow_book SET return_date = CURDATE() WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: transactions.php?returned=1");
exit();
