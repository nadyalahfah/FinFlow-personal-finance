<?php
require_once 'db.php';
check_auth();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinFlow - Personal Finance Ecosystem</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>FinFlow</h1>
                <small>Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
            </div>
            <div>
                <button id="themeToggle" class="btn-theme">Mode Gelap</button>
                <a href="logout.php" style="color: var(--text-main); margin-left: 12px; text-decoration: none; font-weight: 600;">Keluar</a>
            </div>
        </header>

        <div id="overbudgetAlert"></div>

        <div class="form-section" style="padding: 16px 20px;">
            <div style="display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="filterMonth" style="margin-bottom: 0; white-space: nowrap;">Filter Bulan:</label>
                    <input type="month" id="filterMonth" value="<?php echo date('Y-m'); ?>" style="width: auto;" onchange="loadDashboard()">
                </div>
                <div>
                    <a id="pdfBtn" href="#" target="_blank" class="btn-submit" style="text-decoration: none; display: inline-block;">Cetak PDF</a>
                </div>
            </div>
        </div>

        <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 16px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Dompet Saya</h3>
        <div class="grid-summary" id="walletList"></div>

        <div class="grid-summary">
            <div class="card">
                <h3>Pemasukan Bulan Ini</h3>
                <p id="totalIncome" style="font-weight: bold; font-size: 20px; margin: 0;">Rp 0</p>
            </div>
            <div class="card">
                <h3>Pengeluaran Bulan Ini</h3>
                <p id="totalExpense" style="font-weight: bold; font-size: 20px; margin: 0;">Rp 0</p>
            </div>
        </div>

        <div class="form-section">
            <h2>Grafik Tren Keuangan (6 Bulan Terakhir)</h2>
            <canvas id="trendChart" height="100"></canvas>
        </div>

        <div class="form-section">
            <h2>Catat Transaksi / Transfer</h2>
            <form id="txForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="txType">Jenis Transaksi</label>
                        <select id="txType" name="type" onchange="toggleTransferUI()">
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                            <option value="transfer">Transfer Antar Dompet</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="txWallet">Dompet Asal</label>
                        <select id="txWallet" name="wallet_id" required></select>
                    </div>
                    <div class="form-group" id="targetWalletGroup" style="display: none;">
                        <label for="txTargetWallet">Dompet Tujuan</label>
                        <select id="txTargetWallet" name="target_wallet_id"></select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="titleInput">Judul Transaksi</label>
                        <input type="text" id="titleInput" name="title" required placeholder="Contoh: Makan Siang / Transfer ke BCA">
                    </div>
                    <div class="form-group">
                        <label for="amountInput">Jumlah (Rp)</label>
                        <input type="number" id="amountInput" name="amount" required placeholder="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" id="categoryGroup">
                        <label for="txCategory">Kategori</label>
                        <select id="txCategory" name="category"></select>
                    </div>
                    <div class="form-group">
                        <label for="dateInput">Tanggal</label>
                        <input type="date" id="dateInput" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Simpan Transaksi</button>
            </form>
        </div>

        <div class="table-section">
            <h2>Riwayat Transaksi</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Judul</th>
                        <th>Dompet</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody id="txTableBody"></tbody>
            </table>
        </div>
    </div>

    <script>
    let trendChart = null;
    let currentTrendData = null;

    const themeBtn = document.getElementById('themeToggle');
    themeBtn.addEventListener('click', () => {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        if (isDark) {
            document.body.removeAttribute('data-theme');
            themeBtn.innerText = 'Mode Gelap';
            localStorage.setItem('theme', 'light');
        } else {
            document.body.setAttribute('data-theme', 'dark');
            themeBtn.innerText = 'Mode Terang';
            localStorage.setItem('theme', 'dark');
        }
        if (currentTrendData) {
            renderChart(currentTrendData);
        }
    });

    if (localStorage.getItem('theme') === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        themeBtn.innerText = 'Mode Terang';
    }

    function toggleTransferUI() {
        const type = document.getElementById('txType').value;
        document.getElementById('targetWalletGroup').style.display = (type === 'transfer') ? 'block' : 'none';
        document.getElementById('categoryGroup').style.display = (type === 'transfer') ? 'none' : 'block';
    }

    function loadDashboard() {
        const month = document.getElementById('filterMonth').value;
        document.getElementById('pdfBtn').href = 'print_report.php?month=' + month;

        fetch('api.php?action=get_dashboard&month=' + month)
            .then(res => res.json())
            .then(data => {
                const alertDiv = document.getElementById('overbudgetAlert');
                alertDiv.innerHTML = '';
                if (data.overbudget && data.overbudget.length > 0) {
                    data.overbudget.forEach(msg => {
                        alertDiv.innerHTML += `<div class="alert-warning">${msg}</div>`;
                    });
                }

                const walletDiv = document.getElementById('walletList');
                const walletSelect = document.getElementById('txWallet');
                const targetSelect = document.getElementById('txTargetWallet');
                
                walletDiv.innerHTML = '';
                walletSelect.innerHTML = '';
                targetSelect.innerHTML = '';

                if (data.wallets) {
                    data.wallets.forEach(w => {
                        walletDiv.innerHTML += `
                            <div class="card">
                                <h3>${w.name}</h3>
                                <p style="font-weight: bold; margin: 0; font-size: 18px;">Rp ${Number(w.balance).toLocaleString('id-ID')}</p>
                            </div>
                        `;
                        walletSelect.innerHTML += `<option value="${w.id}">${w.name}</option>`;
                        targetSelect.innerHTML += `<option value="${w.id}">${w.name}</option>`;
                    });
                }

                document.getElementById('totalIncome').innerText = 'Rp ' + (data.income || 0).toLocaleString('id-ID');
                document.getElementById('totalIncome').style.color = 'var(--income-color)';
                
                document.getElementById('totalExpense').innerText = 'Rp ' + (data.expense || 0).toLocaleString('id-ID');
                document.getElementById('totalExpense').style.color = 'var(--expense-color)';

                const catSelect = document.getElementById('txCategory');
                catSelect.innerHTML = '';
                if (data.categories) {
                    data.categories.forEach(c => {
                        catSelect.innerHTML += `<option value="${c.name}">${c.name}</option>`;
                    });
                }

                const tbody = document.getElementById('txTableBody');
                tbody.innerHTML = '';
                if (!data.transactions || data.transactions.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Belum ada data transaksi pada bulan ini.</td></tr>';
                } else {
                    data.transactions.forEach(t => {
                        const badgeColor = t.type === 'income' ? 'var(--income-color)' : (t.type === 'expense' ? 'var(--expense-color)' : 'var(--primary-accent)');
                        tbody.innerHTML += `
                            <tr>
                                <td>${t.date}</td>
                                <td><strong>${t.title}</strong></td>
                                <td>${t.wallet_name}</td>
                                <td>${t.category}</td>
                                <td><span style="color: ${badgeColor}; font-weight: bold;">${t.type.toUpperCase()}</span></td>
                                <td>Rp ${Number(t.amount).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });
                }

                currentTrendData = data.trend;
                renderChart(data.trend);
            })
            .catch(err => console.error('Gagal memuat data dashboard:', err));
    }

    function renderChart(trendData) {
        if (!trendData) return;

        const ctx = document.getElementById('trendChart').getContext('2d');
        if (trendChart) trendChart.destroy();

        const isDark = document.body.getAttribute('data-theme') === 'dark';
        
        const incomeColor = isDark ? '#34d399' : '#059669';
        const expenseColor = isDark ? '#f87171' : '#dc2626';
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels || [],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: trendData.income || [],
                        borderColor: incomeColor,
                        backgroundColor: incomeColor,
                        borderWidth: 3,
                        tension: 0.3,
                        pointRadius: 4,
                        fill: false
                    },
                    {
                        label: 'Pengeluaran',
                        data: trendData.expense || [],
                        borderColor: expenseColor,
                        backgroundColor: expenseColor,
                        borderWidth: 3,
                        tension: 0.3,
                        pointRadius: 4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: textColor, font: { family: 'Inter', size: 13, weight: '600' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    y: {
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'rb';
                            }
                        },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }

    document.getElementById('txForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('api.php?action=add_transaction', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                this.reset();
                toggleTransferUI();
                loadDashboard();
            }
        })
        .catch(err => console.error('Gagal menyimpan transaksi:', err));
    });

    loadDashboard();
    </script>
</body>
</html>