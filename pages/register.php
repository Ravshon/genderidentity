<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../config/db.php';

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    if (!empty($first_name) && !empty($last_name)) {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name) VALUES (?, ?)");
        $stmt->execute([$first_name, $last_name]);
        $user_id = $pdo->lastInsertId();
        header("Location: test.php?user_id=$user_id");
        exit();
    } 
    
    else {
        echo "Iltimos, ismingizni va familiyangizni kiriting!";
    }
}
?>