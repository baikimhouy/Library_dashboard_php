<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirectWithMessage($url, $message) {
    $_SESSION['message'] = $message;
    header("Location: $url");
    exit();
}

function displayMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}
?>