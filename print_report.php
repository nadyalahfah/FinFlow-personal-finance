<?php
require_once 'db.php';
check_auth();

$user_id =$_SESSION['user_id'];
$month =$_GET['month'] ?? date('Y-m');

$stmt =$pdo->prepare("
    SELECT t.*, w.name as wallet_name 
    FROM transactions t 
    JOIN wallets w ON t.wallet_id = w.id 
    WHERE t.user_id = ? AND DATE_FORMAT(t.date, '%Y-%m') = ? 
    ORDER BY t.date ASC
");
$stmt->execute([$user_id,$month]);
$transactions =$stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan FinFlow - <?php echo $month; ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #000; }
        h1, h2 { text-align: center; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">Cetak / Simpan ke PDF</button>
    </div>

    <h1>Laporan Keuangan FinFlow</h1>
    <h2>Periode: <?php echo date('F Y', strtotime($month . '-01')); ?></h2>
    <p>Pengguna: <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Judul</th>
                <th>Dompet</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as$t): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($t['date'])); ?></td>
                    <td><?php echo htmlspecialchars($t['title']); ?></td>
                    <td><?php echo htmlspecialchars($t['wallet_name']); ?></td>
                    <td><?php echo htmlspecialchars($t['category']); ?></td>
                    <td><?php echo strtoupper($t['type']); ?></td>
                    <td>Rp <?php echo number_format($t['amount'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>