<?php
require_once 'includes/config.php';
require_once 'core/Database.php';

// Access control check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['verification', 'lydo', 'sk_fed'])) {
    header("Location: " . $base_path . "login");
    exit;
}

$database = new Database();
$db = $database->connect();

// Fetch summary metrics for verification
$stmtProjects = $db->query("SELECT COUNT(*) as total_projects, SUM(budget) as total_budget FROM projects");
$projectStats = $stmtProjects->fetch();

$stmtTransactions = $db->query("SELECT 
    COUNT(*) as total_tx,
    SUM(CASE WHEN status = 'recorded' THEN amount ELSE 0 END) as recorded_amount,
    SUM(CASE WHEN status = 'reviewed' THEN amount ELSE 0 END) as reviewed_amount,
    SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount
    FROM transactions");
$txStats = $stmtTransactions->fetch();

// Fetch recorded transactions for verification
$stmtTxList = $db->query("SELECT t.*, p.title as project_title, u.barangay_name, u.full_name as submitter 
    FROM transactions t 
    JOIN projects p ON t.project_id = p.id 
    JOIN users u ON p.user_id = u.id 
    ORDER BY t.created_at DESC");
$transactions = $stmtTxList->fetchAll();

$page_title = 'Verification & Oversight Dashboard';
ob_start();
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 relative z-10">
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Total Registered Projects</h3>
        <p class="text-3xl font-black text-slate-900 mt-1"><?php echo number_format($projectStats['total_projects']); ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Total Allocated Budget</h3>
        <p class="text-3xl font-black text-slate-900 mt-1">₱<?php echo number_format($projectStats['total_budget'], 2); ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-xs font-bold text-emerald-600 mb-1 uppercase tracking-wider">Recorded Expenditures</h3>
        <p class="text-3xl font-black text-emerald-700 mt-1">₱<?php echo number_format($txStats['recorded_amount'], 2); ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-xs font-bold text-amber-600 mb-1 uppercase tracking-wider">Pending / Pipeline</h3>
        <p class="text-3xl font-black text-amber-700 mt-1">₱<?php echo number_format($txStats['pending_amount'] + $txStats['reviewed_amount'], 2); ?></p>
    </div>
</div>

<!-- Financial Transactions Ledger -->
<div class="card overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Barangay Financial Transactions Ledger</h3>
            <p class="text-xs text-slate-500 mt-0.5">Verification & oversight view of recorded SK financial transactions</p>
        </div>
        <span class="text-xs bg-indigo-100 text-indigo-800 font-bold px-3 py-1 rounded-full border border-indigo-200">Official Audit Trail</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 border-collapse">
            <thead class="bg-slate-50/50 text-slate-500 text-xs uppercase font-bold tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Barangay</th>
                    <th class="px-6 py-4">Project</th>
                    <th class="px-6 py-4">Ref No.</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">No transaction records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900"><?php echo htmlspecialchars($tx['barangay_name'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 text-slate-700"><?php echo htmlspecialchars($tx['project_title']); ?></td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500"><?php echo htmlspecialchars($tx['reference_no']); ?></td>
                            <td class="px-6 py-4 uppercase text-xs font-bold text-slate-600"><?php echo htmlspecialchars($tx['type']); ?></td>
                            <td class="px-6 py-4 font-bold text-slate-900">₱<?php echo number_format($tx['amount'], 2); ?></td>
                            <td class="px-6 py-4">
                                <?php if ($tx['status'] === 'recorded'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Recorded</span>
                                <?php elseif ($tx['status'] === 'reviewed'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">LYDO Approved</span>
                                <?php elseif ($tx['status'] === 'returned'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">Returned</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Pending Review</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-slate-400 font-medium"><?php echo date('M d, Y', strtotime($tx['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
