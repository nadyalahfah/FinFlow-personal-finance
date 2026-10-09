<?php
require_once 'db.php';
check_auth();

header('Content-Type: application/json');
$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'add_transaction') {
    $title = trim($_POST['title']);
    $amount = (float)$_POST['amount'];
    $type = $_POST['type'];
    $wallet_id = (int)$_POST['wallet_id'];
    $target_wallet_id = isset($_POST['target_wallet_id']) ? (int)$_POST['target_wallet_id'] : null;
    $category = trim($_POST['category']);
    $date = $_POST['date'];

    if ($amount <= 0 || empty($date)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
        exit;
    }

    if ($type === 'expense') {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, title, amount, type, category, date) VALUES (?, ?, ?, ?, 'expense', ?, ?)");
        $stmt->execute([$user_id, $wallet_id, $title, $amount, $category, $date]);
        
        $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ? AND user_id = ?")->execute([$amount, $wallet_id, $user_id]);
    } 
    elseif ($type === 'income') {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, title, amount, type, category, date) VALUES (?, ?, ?, ?, 'income', ?, ?)");
        $stmt->execute([$user_id, $wallet_id, $title, $amount, $category, $date]);

        $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ? AND user_id = ?")->execute([$amount, $wallet_id, $user_id]);
    } 
    elseif ($type === 'transfer' && $target_wallet_id && $wallet_id !== $target_wallet_id) {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, target_wallet_id, title, amount, type, category, date) VALUES (?, ?, ?, ?, ?, 'transfer', 'Transfer', ?)");
        $stmt->execute([$user_id, $wallet_id, $target_wallet_id, $title, $amount, $date]);

        $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE id = ? AND user_id = ?")->execute([$amount, $wallet_id, $user_id]);
        $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ? AND user_id = ?")->execute([$amount, $target_wallet_id, $user_id]);
    }

    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'add_wallet') {
    $name = trim($_POST['name']);
    $balance = (float)$_POST['balance'];
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO wallets (user_id, name, balance) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $name, $balance]);
    }
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'get_dashboard') {
    $month = $_GET['month'] ?? date('Y-m');

    $wallets = $pdo->prepare("SELECT * FROM wallets WHERE user_id = ?");
    $wallets->execute([$user_id]);
    $wallet_list = $wallets->fetchAll();

    $stmt_summary = $pdo->prepare("
        SELECT type, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ? 
        GROUP BY type
    ");
    $stmt_summary->execute([$user_id, $month]);
    $summary_rows = $stmt_summary->fetchAll();

    $income = 0; $expense = 0;
    foreach ($summary_rows as $r) {
        if ($r['type'] === 'income') $income = (float)$r['total'];
        if ($r['type'] === 'expense') $expense = (float)$r['total'];
    }

    $stmt_budget = $pdo->prepare("
        SELECT c.name, c.monthly_budget, SUM(t.amount) as total_spent
        FROM categories c
        JOIN transactions t ON c.name = t.category AND t.user_id = c.user_id
        WHERE c.user_id = ? AND t.type = 'expense' AND DATE_FORMAT(t.date, '%Y-%m') = ?
        GROUP BY c.id
    ");
    $stmt_budget->execute([$user_id, $month]);
    $budget_status = $stmt_budget->fetchAll();

    $overbudget_warnings = [];
    foreach ($budget_status as $b) {
        if ($b['monthly_budget'] > 0 && $b['total_spent'] > $b['monthly_budget']) {
            $overbudget_warnings[] = "Peringatan: Kategori " . $b['name'] . " melebih batas anggaran! (Terpakai: Rp " . number_format($b['total_spent']) . " / Batas: Rp " . number_format($b['monthly_budget']) . ")";
        }
    }

    $trend_labels = []; $trend_income = []; $trend_expense = [];
    for ($i = 5; $i >= 0; $i--) {
        $m = date('Y-m', strtotime("-$i months"));
        $trend_labels[] = date('M Y', strtotime($m . '-01'));

        $stmt_t = $pdo->prepare("
            SELECT type, SUM(amount) as total 
            FROM transactions 
            WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ? 
            GROUP BY type
        ");
        $stmt_t->execute([$user_id, $m]);
        $t_rows = $stmt_t->fetchAll();
        
        $inc = 0; $exp = 0;
        foreach ($t_rows as $tr) {
            if ($tr['type'] === 'income') $inc = (float)$tr['total'];
            if ($tr['type'] === 'expense') $exp = (float)$tr['total'];
        }
        $trend_income[] = $inc;
        $trend_expense[] = $exp;
    }

    $stmt_tx = $pdo->prepare("
        SELECT t.*, w.name as wallet_name 
        FROM transactions t 
        JOIN wallets w ON t.wallet_id = w.id 
        WHERE t.user_id = ? AND DATE_FORMAT(t.date, '%Y-%m') = ? 
        ORDER BY t.date DESC, t.id DESC
    ");
    $stmt_tx->execute([$user_id, $month]);
    $transactions = $stmt_tx->fetchAll();

    $categories = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
    $categories->execute([$user_id]);

    echo json_encode([
        'wallets' => $wallet_list,
        'income' => $income,
        'expense' => $expense,
        'overbudget' => $overbudget_warnings,
        'trend' => [
            'labels' => $trend_labels,
            'income' => $trend_income,
            'expense' => $trend_expense
        ],
        'transactions' => $transactions,
        'categories' => $categories->fetchAll()
    ]);
    exit;
}
?>