<?php
require_once 'core/Database.php';

$database = new Database();
$db = $database->connect();

// Fetch Reviewed Reports (MARs) waiting to be recorded
$mars_query = "SELECT r.*, u.barangay_name 
               FROM reports r 
               JOIN users u ON r.user_id = u.id 
               WHERE r.status = 'reviewed' 
               ORDER BY r.submitted_at ASC";
$mars_stmt = $db->query($mars_query);
$pending_mars = $mars_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Reviewed Transactions waiting to be recorded
$tx_query = "SELECT t.*, p.title as project_title, u.barangay_name 
             FROM transactions t 
             JOIN projects p ON t.project_id = p.id 
             JOIN users u ON p.user_id = u.id 
             WHERE t.status = 'reviewed' 
             ORDER BY t.created_at ASC";
$tx_stmt = $db->query($tx_query);
$pending_txs = $tx_stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$recorded_tx_count = $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'recorded'")->fetchColumn();
$recorded_mars_count = $db->query("SELECT COUNT(*) FROM reports WHERE status = 'recorded'")->fetchColumn();
$total_pending = count($pending_mars) + count($pending_txs);

$page_title = 'SK Federation Dashboard';
ob_start();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 relative z-10">
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-sm font-bold text-slate-500 mb-1 uppercase tracking-wider">Pending Recording</h3>
        <p class="text-4xl font-black text-slate-900"><?= $total_pending ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-sm font-bold text-slate-500 mb-1 uppercase tracking-wider">Recorded MARs</h3>
        <p class="text-4xl font-black text-slate-900"><?= $recorded_mars_count ?></p>
    </div>
    <div class="card p-6 bg-white border border-slate-200 shadow-sm rounded-2xl">
        <h3 class="text-sm font-bold text-slate-500 mb-1 uppercase tracking-wider">Recorded Transactions</h3>
        <p class="text-4xl font-black text-slate-900"><?= $recorded_tx_count ?></p>
    </div>
</div>

<div class="grid grid-cols-1 gap-8">
    <!-- Pending Transactions Table -->
    <div class="card overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-900">Transactions Pending Recording (<?= count($pending_txs) ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">Barangay / Project</th>
                        <th class="px-6 py-4">Transaction Details</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(count($pending_txs) > 0): ?>
                        <?php foreach($pending_txs as $tx): ?>
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900"><?= htmlspecialchars($tx['barangay_name']) ?></div>
                                    <div class="text-xs text-slate-500 truncate max-w-[150px]"><?= htmlspecialchars($tx['project_title']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">₱<?= number_format($tx['amount'], 2) ?></div>
                                    <div class="text-sm text-slate-500 capitalize whitespace-nowrap"><?= htmlspecialchars($tx['type']) ?> (<?= htmlspecialchars($tx['reference_no']) ?>)</div>
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
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button onclick="recordItem('tx', <?= $tx['id'] ?>)" class="inline-flex items-center gap-1.5 justify-center px-4 py-2 bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm hover:shadow-md rounded-lg text-sm font-bold transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Record & Certify
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                No transactions pending recording.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pending MARs Table -->
    <div class="card overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-900">MARs Pending Recording (<?= count($pending_mars) ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">Barangay</th>
                        <th class="px-6 py-4">Coverage Period</th>
                        <th class="px-6 py-4">Attachments</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(count($pending_mars) > 0): ?>
                        <?php foreach($pending_mars as $mar): ?>
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900"><?= htmlspecialchars($mar['barangay_name']) ?></td>
                                <td class="px-6 py-4 font-medium"><?= date("F Y", mktime(0, 0, 0, $mar['month'], 1, $mar['year'])) ?></td>
                                <td class="px-6 py-4 text-sm space-y-2 whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($mar['session_minutes_path']) ?>" target="_blank" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Session Minutes
                                    </a>
                                    <a href="<?= htmlspecialchars($mar['attendance_records_path']) ?>" target="_blank" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        Attendance
                                    </a>
                                    <a href="<?= htmlspecialchars($mar['post_activity_reports_path']) ?>" target="_blank" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        Post-Activity
                                    </a>
                                    <a href="<?= htmlspecialchars($mar['financial_reports_path']) ?>" target="_blank" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Financial Report
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button onclick="recordItem('mar', <?= $mar['id'] ?>)" class="inline-flex items-center gap-1.5 justify-center px-4 py-2 bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm hover:shadow-md rounded-lg text-sm font-bold transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Record & Certify
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                No Monthly Accomplishment Reports pending recording.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="assets/js/fed.js"></script>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
