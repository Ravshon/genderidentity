<?php
require 'db.php';

if (!isset($_GET['user_id'])) {
    die("Xatolik: Foydalanuvchi ro'yxatdan o'tmagan.");
}
$user_id = (int)$_GET['user_id'];

// Savollarni JSON dan yuklab olamiz
$questions = json_decode(file_get_contents('questions.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'];
    $answers_json = json_encode($answers);

    // Savollar bo'limlari
    $categories = [
        "biological" => range(1, 10),
        "psychological" => range(11, 20),
        "social" => range(21, 30)
    ];

    // Ballarni yuklab olamiz
    $scores = [
        "biological" => 0,
        "psychological" => 0,
        "social" => 0
    ];

    // Javoblar uchun ballar beramiz
    $points = ['A' => 3, 'B' => 2, 'C' => 1];

    // Javoblarni tekshiramiz
    foreach ($answers as $question_id => $answer) {
        if (isset($points[$answer])) {
            if (in_array($question_id, $categories['biological'])) {
                $scores["biological"] += $points[$answer];
            } elseif (in_array($question_id, $categories['psychological'])) {
                $scores["psychological"] += $points[$answer];
            } elseif (in_array($question_id, $categories['social'])) {
                $scores["social"] += $points[$answer];
            }
        }
    }

    // Barcha ballar
    $total_score = $scores["biological"] + $scores["psychological"] + $scores["social"];

    // Test javobini bazaga saqlaymiz
    $stmt = $pdo->prepare("INSERT INTO results (user_id, answers, final_result, total_score, bio_score, psy_score, soc_score) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $answers_json, "Analiz tugadi", $total_score, $scores["biological"], $scores["psychological"], $scores["social"]]);

    header("Location: result.php?user_id=$user_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Psixologik test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 700px;
            margin-top: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .question {
            font-weight: bold;
            margin-top: 10px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Psixologik test</h2>
    <form action="test.php?user_id=<?= $user_id ?>" method="POST">
        <?php foreach ($questions as $index => $question): ?>
            <div class="mb-3">
                <p class="question"><?= htmlspecialchars($index + 1) . ". " . htmlspecialchars($question['question']) ?></p>
                <div class="form-check">
                    <?php foreach ($question['answers'] as $key => $answer): ?>
                        <input class="form-check-input" type="radio" name="answers[<?= $index + 1 ?>]" value="<?= $key ?>" required>
                        <label class="form-check-label"><?= htmlspecialchars($answer) ?></label><br>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-custom w-100">Testni yakunlash</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
