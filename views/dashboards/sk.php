<?php
require_once 'core/Database.php';

$database = new Database();
$db = $database->connect();
$user_id = $_SESSION['user_id'];

// Get Total Project Budget
$budget_query = "SELECT SUM(budget) as total FROM projects WHERE user_id = :user_id";
$stmt = $db->prepare($budget_query);
$stmt->execute([':user_id' => $user_id]);
$total_budget = $stmt->fetchColumn() ?: 0;

// Get Pending Reports Count
$pending_reports_query = "SELECT COUNT(*) FROM reports WHERE user_id = :user_id AND status IN ('pending', 'returned')";
$stmt = $db->prepare($pending_reports_query);
$stmt->execute([':user_id' => $user_id]);
$pending_reports = $stmt->fetchColumn() ?: 0;

// Get Recorded Transactions Count
$recorded_tx_query = "SELECT COUNT(t.id) FROM transactions t JOIN projects p ON t.project_id = p.id WHERE p.user_id = :user_id AND t.status = 'recorded'";
$stmt = $db->prepare($recorded_tx_query);
$stmt->execute([':user_id' => $user_id]);
$recorded_tx = $stmt->fetchColumn() ?: 0;

// Get Returned MARs for correction
$returned_reports_query = "SELECT r.id, r.month, r.year, r.remarks, r.status FROM reports r WHERE r.user_id = :user_id AND r.status = 'returned' ORDER BY r.submitted_at DESC";
$stmt = $db->prepare($returned_reports_query);
$stmt->execute([':user_id' => $user_id]);
$returned_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Returned Transactions for correction
$returned_txs_query = "SELECT t.*, p.title as project_title FROM transactions t JOIN projects p ON t.project_id = p.id WHERE p.user_id = :user_id AND t.status = 'returned' ORDER BY t.created_at DESC";
$stmt = $db->prepare($returned_txs_query);
$stmt->execute([':user_id' => $user_id]);
$returned_txs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$returned_count = count($returned_reports) + count($returned_txs);

// Get Recent Projects
$recent_proj_query = "SELECT * FROM projects WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($recent_proj_query);
$stmt->execute([':user_id' => $user_id]);
$recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'SK Administrator Dashboard';
ob_start();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 relative z-10">
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-sm font-bold text-slate-500 mb-1 uppercase tracking-wider">Total Project Budget</h3>
        <p class="text-4xl font-black text-slate-900">₱<?= number_format($total_budget, 2) ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-sm font-bold text-slate-500 mb-1 uppercase tracking-wider">Pending/Returned MARs</h3>
        <p class="text-4xl font-black text-slate-900"><?= $pending_reports ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-sm font-bold text-slate-500 mb-1 uppercase tracking-wider">Recorded Transactions</h3>
        <p class="text-4xl font-black text-slate-900"><?= $recorded_tx ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 relative z-10">
    <div class="card p-6 bg-amber-50 border border-amber-200 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-amber-800 mb-1 uppercase tracking-wider">Returned Corrections</h3>
        <p class="text-xs text-amber-700 mb-4">LYDO returned these items for correction and resubmission.</p>
        <p class="text-4xl font-black text-amber-900"><?= $returned_count ?></p>
    </div>

    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Returned Items for Correction</h3>
                <p class="text-sm text-slate-500 mt-1">Review LYDO remarks and resubmit the required documents.</p>
            </div>
            <span class="text-xs uppercase font-semibold text-slate-500 tracking-[0.2em]">Last updated</span>
        </div>
        <?php if ($returned_count === 0): ?>
            <div class="px-6 py-10 text-center text-slate-500">No returned items at this time. Keep your reports and transactions on track.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-xs font-bold">
                        <tr>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Remarks</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($returned_reports as $item): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-900">MAR</td>
                                <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars(date('F', mktime(0,0,0,$item['month'],1))) ?> <?= htmlspecialchars($item['year']) ?></td>
                                <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($item['remarks'] ?: 'No remarks provided.') ?></td>
                                <td class="px-4 py-3 text-right"><a href="reports" class="text-blue-600 hover:text-blue-800 font-semibold">View MAR</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($returned_txs as $item): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-900">Transaction</td>
                                <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($item['project_title']) ?> &#8226; ₱<?= number_format($item['amount'], 2) ?></td>
                                <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($item['remarks'] ?: 'No remarks provided.') ?></td>
                                <td class="px-4 py-3 text-right"><a href="project-view?id=<?= $item['project_id'] ?>" class="text-blue-600 hover:text-blue-800 font-semibold">View Transaction</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
        <h3 class="text-lg font-bold text-slate-900">Recent Projects</h3>
        <a href="projects" class="inline-flex items-center gap-1.5 justify-center px-4 py-2 bg-slate-900 text-white hover:bg-slate-800 shadow-sm rounded-lg text-sm font-bold transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add Project
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Project Title</th>
                    <th class="px-6 py-4">Budget</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(count($recent_projects) > 0): ?>
                    <?php foreach($recent_projects as $proj): ?>
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 group-hover:text-blue-700 transition-colors"><?= htmlspecialchars($proj['title']) ?></div>
                                <div class="text-sm text-slate-500 mt-0.5 truncate max-w-xs"><?= htmlspecialchars($proj['description'] ?? 'No description') ?></div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">₱<?= number_format($proj['budget'], 2) ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide capitalize shadow-sm
                                    <?= $proj['status'] == 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($proj['status'] == 'ongoing' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200') ?>">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 <?= $proj['status'] == 'completed' ? 'bg-emerald-500' : ($proj['status'] == 'ongoing' ? 'bg-amber-500' : 'bg-blue-500') ?>"></span>
                                    <?= htmlspecialchars($proj['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="project-view?id=<?= $proj['id'] ?>" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-900 hover:text-white rounded-lg text-sm font-bold transition-all">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-sm font-bold text-slate-900">No Projects Found</h3>
                            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Get started by registering your first SK project.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
