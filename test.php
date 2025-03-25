<?php
require 'db.php';

if (!isset($_GET['user_id'])) {
    die("Xatolik: Foydalanuvchi ro'yxatdan o'tmagan.");
}
$user_id = (int)$_GET['user_id'];

// JSON fayldan savollarni yuklab olamiz!
$questions = json_decode(file_get_contents('questions.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = json_encode($_POST['answers']);
    $final_result = "Анализ завершен"; // Bu joyda hisoblash logikasi bo'ladi.

    $stmt = $pdo->prepare("INSERT INTO results (user_id, answers, final_result) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $answers, $final_result]);

    header("Location: result.php?user_id=$user_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест</title>
</head>
<body>
    <form action="test.php?user_id=<?= $user_id ?>" method="POST">
        <?php foreach ($questions as $index => $question): ?>
            <p><?= htmlspecialchars($question['question']) ?></p>
            <?php foreach ($question['answers'] as $answer): ?>
                <label>
                    <input type="radio" name="answers[<?= $index ?>]" value="<?= htmlspecialchars($answer) ?>" required>
                    <?= htmlspecialchars($answer) ?>
                </label><br>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <button type="submit">Завершить тест</button>
    </form>
</body>
</html>
