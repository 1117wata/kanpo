<?php
require_once 'admin_auth.php';
require_once '../DB/db_connect.php';

$pdo = getDB();

// ---- 日付範囲の決定（ここを自由に変えられる） ---- //
$startDate = '2025-10-26';
$endDate = date('Y-m-d'); 

// DBから日別件数取得
$sql = "
    SELECT DATE(created_at) AS date, COUNT(*) AS count
    FROM user
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$startDate, $endDate]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 取得した日 ⇒ 件数 の配列にする
$dailyData = [];
foreach ($result as $row) {
    $dailyData[$row['date']] = (int)$row['count'];
}

// ---- 日付範囲の全日を埋める処理 ---- //
$start = new DateTime($startDate);
$end   = new DateTime($endDate);
$end->modify('+1 day');

$dateLabels = []; // X軸（表示する日付）
$dataCounts = []; // Y軸（件数）

for ($date = $start; $date < $end; $date->modify('+1 day')) {
    $d = $date->format('Y-m-d');
    $dateLabels[] = $d;
    $dataCounts[] = isset($dailyData[$d]) ? $dailyData[$d] : 0;
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
        </a> <h1 class="page-title">管理者ダッシュボード</h1> 
    </header>

<h2>日別会員登録数</h2>
<canvas id="dailyChart"></canvas>

<script>
const ctx = document.getElementById('dailyChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dateLabels); ?>,
        datasets: [{
            label: '登録数',
            data: <?php echo json_encode($dataCounts); ?>,
        }]
    },
    options: {
    responsive: true,
    plugins: { legend: { display: true } },
    scales: {
        x: { title: { display: true, text: '期間' } },
        y: { 
            title: { display: true, text: '会員数' }, 
            beginAtZero: true,
            max: 120
        }
    }
}
});
</script>

</body>
</html>
