<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';
$pdo = getDB();

// ---- 日付範囲 ---- //
$startDate = '2025-10-26';
$endDate   = date('Y-m-d');

// ---- 日別データ取得 ---- //
$sql_daily = "
    SELECT DATE(created_at) AS date, COUNT(*) AS count
    FROM user
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
";
$stmt = $pdo->prepare($sql_daily);
$stmt->execute([$startDate, $endDate]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dailyData = [];
foreach ($result as $row) {
    $dailyData[$row['date']] = (int)$row['count'];
}

// 日付範囲を埋める
$start = new DateTime($startDate);
$end   = new DateTime($endDate);
$end->modify('+1 day');

$dateLabels = [];
$dataCounts = [];
for ($date = $start; $date < $end; $date->modify('+1 day')) {
    $d = $date->format('Y-m-d');
    $dateLabels[] = $d;
    $dataCounts[] = $dailyData[$d] ?? 0;
}

// ---- 月別データ取得 ---- //
$sql_month = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count
    FROM user
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
";
$stmt_month = $pdo->prepare($sql_month);
$stmt_month->execute([$startDate, $endDate]);
$result_month = $stmt_month->fetchAll(PDO::FETCH_ASSOC);

$monthLabels = [];
$monthCounts = [];
foreach ($result_month as $row) {
    $monthLabels[] = $row['month'];
    $monthCounts[] = (int)$row['count'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header class="header-bar">
    <a href="admin_home.php" class="logo-link">
        <img src="../images/Akanpo.png" alt="サイトロゴ">
    </a> 
    <h1 class="page-title">管理者ダッシュボード</h1> 
</header>

<h2 class="title">会員登録数</h2>

<div class="chart-buttons">
    <button id="dailyBtn" class="chart-btn">日別</button>
    <button id="monthlyBtn" class="chart-btn">月別</button>
</div>

<canvas id="chartCanvas"></canvas>

<script>
const dailyLabels = <?php echo json_encode($dateLabels); ?>;
const dailyCounts = <?php echo json_encode($dataCounts); ?>;
const monthlyLabels = <?php echo json_encode($monthLabels); ?>;
const monthlyCounts = <?php echo json_encode($monthCounts); ?>;

const ctx = document.getElementById('chartCanvas');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: '日別登録数',
            data: dailyCounts,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: {
            x: { title: { display: true, text: '期間' } },
            y: { title: { display: true, text: '会員数' }, beginAtZero: true }
        }
    }
});

// ボタンで切り替え
document.getElementById('dailyBtn').addEventListener('click', () => {
    chart.data.labels = dailyLabels;
    chart.data.datasets[0].data = dailyCounts;
    chart.data.datasets[0].label = '日別登録数';
    chart.update();

    document.getElementById('dailyBtn').classList.add('active');
    document.getElementById('monthlyBtn').classList.remove('active');
});

document.getElementById('monthlyBtn').addEventListener('click', () => {
    chart.data.labels = monthlyLabels;
    chart.data.datasets[0].data = monthlyCounts;
    chart.data.datasets[0].label = '月別登録数';
    chart.update();

    document.getElementById('monthlyBtn').classList.add('active');
    document.getElementById('dailyBtn').classList.remove('active');
});
</script>

</body>
</html>
