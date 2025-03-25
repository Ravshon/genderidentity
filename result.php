<?php
require 'db.php';

if (!isset($_GET['user_id'])) {
    die("Ошибка: пользователь не найден.");
}
$user_id = (int)$_GET['user_id'];

// Test javoblarini olamiz
$stmt = $pdo->prepare("SELECT bio_score, psy_score, soc_score FROM results WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Результаты теста не найдены.");
}

// Ballarni olamiz
$bio_score = $result['bio_score'];
$psy_score = $result['psy_score'];
$soc_score = $result['soc_score'];

// Foizlarda hisoblaymiz
$bio_percent = round(($bio_score / 30) * 100, 2);
$psy_percent = round(($psy_score / 30) * 100, 2);
$soc_percent = round(($soc_score / 30) * 100, 2);

// Tavsiyalar berish uchun funksiya
function getRecommendations($bio, $psy, $soc) {
    $rec = "";

    // Biologik omil
    if ($bio >= 80) {
        $rec .= "<strong>✅ Биологик омил (Юқори):</strong><br>
        - Спорт ва жисмоний машқларни давом эттириш.<br>
        - Спортдаги қизиқишларга мос профессионал тўгаракларга қатнашиш.<br>
        - Соғлом овқатланиш ва уйқуга риоя қилиш.<br><br>";
    } elseif ($bio >= 50) {
        $rec .= "<strong>🔹 Биологик омил (Ўртача):</strong><br>
        - Жисмоний фаолликни ошириш учун кундалик ҳаракатни кўпайтириш.<br>
        - Руҳий ва жисмоний уйғунликни сақлашга эътибор қаратиш.<br><br>";
    } else {
        $rec .= "<strong>⚠️ Биологик омил (Паст):</strong><br>
        - Жисмоний ҳаракатни кўпайтириш ва фаол турмуш тарзини шакллантириш.<br>
        - Спортга қизиқтириш учун гуруҳли машғулотларда қатнашиш.<br><br>";
    }

    // Psixologik faktor
    if ($psy >= 80) {
        $rec .= "<strong>✅ Психологик омил (Юқори):</strong><br>
        - Муаммоларни мустақил ҳал қилиш ва ҳис-туйғуларни бошқариш.<br>
        - Лидерлик ва ташаббускорликни ривожлантириш.<br><br>";
    } elseif ($psy >= 50) {
        $rec .= "<strong>🔹 Психологик омил (Ўртача):</strong><br>
        - Стресс ва тушкунлик ҳолатларини бошқариш бўйича маслаҳатлар бериш.<br>
        - Кўпроқ ижтимоий мулоқот қилиш ва ўзига ишончни ошириш.<br><br>";
    } else {
        $rec .= "<strong>⚠️ Психологик омил (Паст):</strong><br>
        - Психологик қўллаб-қувватлаш ва мотивация бериш.<br>
        - Ҳис-туйғуларни бошқариш ва стрессни енгиш бўйича машқлар ўтказиш.<br><br>";
    }

    // Ijtimoiy omil
    if ($soc >= 80) {
        $rec .= "<strong>✅ Ижтимоий омил (Юқори):</strong><br>
        - Жамоатчилик ишларида фаол иштирок этиш тавсия этилади.<br>
        - Ташаббускорликни кучайтириш ва етакчилик қобилиятларини ривожлантириш.<br><br>";
    } elseif ($soc >= 50) {
        $rec .= "<strong>🔹 Ижтимоий омил (Ўртача):</strong><br>
        - Жамоада ишлаш ва ижтимоий муносабатларни кучайтириш.<br>
        - Интернет ва ижтимоий тармоқларни самарали фойдаланиш бўйича маслаҳатлар.<br><br>";
    } else {
        $rec .= "<strong>⚠️ Ижтимоий омил (Паст):</strong><br>
        - Жамоат ишларига жалб қилиш ва ижтимоий муносабатни кучайтириш.<br>
        - Оилавий муҳит ва ота-она билан муносабатни яхшилаш.<br><br>";
    }

    return $rec;
}

// Tavsiyalarni generatsiya qilamiz
$recommendations = getRecommendations($bio_percent, $psy_percent, $soc_percent);

// Grafik uchun ma'lumotlar
$labels = json_encode(["Биологик", "Психологик", "Ижтимоий"]);
$data = json_encode([$bio_percent, $psy_percent, $soc_percent]);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test javoblari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            max-width: 400px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h2 class="mb-3">Test javoblari</h2>
    <h4>Биологик: <?= $bio_percent ?>% | Психологик: <?= $psy_percent ?>% | Ижтимоий: <?= $soc_percent ?>%</h4>

    <div class="chart-container">
        <canvas id="resultChart"></canvas>
    </div>

    <div class="mt-4 text-start">
        <h5><strong>Shaxsiy tavsiyalar:</strong></h5>
        <p><?= $recommendations ?></p>
    </div>

    <a href="index.html" class="btn btn-primary mt-3">Asosiy Sahifaga qaytish</a>
</div>

<script>
    const ctx = document.getElementById('resultChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= $labels ?>,
            datasets: [{
                data: <?= $data ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
