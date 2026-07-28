<?php
require_once 'core/Database.php';

$database = new Database();
$db = $database->connect();

// Fetch all transactions across all barangays (history)
// We might want to filter out 'pending' to only show history, or show everything.
// The user asked for "transaction history across all barangay", so we can show all non-pending, or all of them.
// Let's show all non-pending for a "history" view.
$history_tx_query = "SELECT t.*, p.title as project_title, u.barangay_name 
             FROM transactions t 
             JOIN projects p ON t.project_id = p.id 
             JOIN users u ON p.user_id = u.id 
             WHERE t.status != 'pending' 
             ORDER BY t.created_at DESC";
$history_tx_stmt = $db->query($history_tx_query);
$history_txs = $history_tx_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Transaction History';
ob_start();
?>

<!-- Header Actions -->
<div class="flex justify-between items-end mb-8 relative z-10">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Transaction History</h2>
        <p class="text-slate-500 mt-2 font-medium">View all historical transactions across all barangays.</p>
    </div>
</div>

<!-- Transaction History Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-700 uppercase tracking-wider text-xs font-bold">
                <tr>
                    <th class="px-6 py-5">Barangay / Project</th>
                    <th class="px-6 py-5">Transaction Details</th>
                    <th class="px-6 py-5">Date</th>
                    <th class="px-6 py-5">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(count($history_txs) > 0): ?>
                    <?php foreach($history_txs as $tx): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($tx['barangay_name']) ?></div>
                                <div class="text-xs text-slate-500 truncate max-w-[200px]"><?= htmlspecialchars($tx['project_title']) ?></div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-800">₱<?= number_format($tx['amount'], 2) ?></div>
                                <div class="text-sm text-slate-500 capitalize whitespace-nowrap"><?= htmlspecialchars($tx['type']) ?> (<?= htmlspecialchars($tx['reference_no']) ?>)</div>
                                <?php if (!empty($tx['document_path'])): ?>
                                    <?php 
                                        $paths = json_decode($tx['document_path'], true);
                                        if (json_last_error() !== JSON_ERROR_NONE || !is_array($paths)) {
                                            $paths = [$tx['document_path']];
                                        }
                                    ?>
                                    <div class="flex flex-col gap-2 mt-2">
                                        <?php foreach ($paths as $idx => $path): ?>
                                            <a href="<?= htmlspecialchars($path) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md text-xs font-bold transition-all border border-blue-100 hover:border-blue-600 w-max" title="<?= htmlspecialchars(basename($path)) ?>">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                <span class="max-w-[150px] truncate"><?= htmlspecialchars(basename($path)) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-slate-900 font-medium"><?= date('M d, Y', strtotime($tx['created_at'])) ?></div>
                                <div class="text-xs text-slate-500"><?= date('h:i A', strtotime($tx['created_at'])) ?></div>
                            </td>
                            <td class="px-6 py-5">
                                <?php if($tx['status'] === 'reviewed'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        Reviewed
                                    </span>
                                <?php elseif($tx['status'] === 'recorded'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        Recorded
                                    </span>
                                <?php elseif($tx['status'] === 'returned'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        Returned
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <?= htmlspecialchars(ucfirst($tx['status'])) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                No transaction history found across any barangay.
                            </div>
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
