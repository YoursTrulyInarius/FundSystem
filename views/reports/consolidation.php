<?php
require_once 'core/Database.php';

$database = new Database();
$db = $database->connect();

$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$selected_year = $selected_year > 2000 ? $selected_year : date('Y');
$selected_quarter = isset($_GET['quarter']) ? intval($_GET['quarter']) : ceil(date('n') / 3);
$selected_quarter = in_array($selected_quarter, [1, 2, 3, 4]) ? $selected_quarter : ceil(date('n') / 3);

$quarter_months = [
    1 => [1, 2, 3],
    2 => [4, 5, 6],
    3 => [7, 8, 9],
    4 => [10, 11, 12]
];
$months = $quarter_months[$selected_quarter];
$month_list = implode(', ', $months);

$reportQuery = "SELECT r.*, u.barangay_name, u.full_name as submitter 
    FROM reports r 
    JOIN users u ON r.user_id = u.id 
    WHERE MONTH(r.submitted_at) IN ($month_list) 
      AND YEAR(r.submitted_at) = :year 
    ORDER BY r.submitted_at DESC";
$stmt = $db->prepare($reportQuery);
$stmt->execute([':year' => $selected_year]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$summaryQuery = "SELECT 
    COUNT(*) AS total_submissions,
    SUM(CASE WHEN r.status = 'reviewed' THEN 1 ELSE 0 END) AS reviewed_count,
    SUM(CASE WHEN r.status = 'returned' THEN 1 ELSE 0 END) AS returned_count,
    SUM(CASE WHEN r.status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
    COUNT(DISTINCT u.barangay_name) AS barangay_count
    FROM reports r 
    JOIN users u ON r.user_id = u.id 
    WHERE MONTH(r.submitted_at) IN ($month_list) 
      AND YEAR(r.submitted_at) = :year";
$summaryStmt = $db->prepare($summaryQuery);
$summaryStmt->execute([':year' => $selected_year]);
$summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

$quarter_label = 'Q' . $selected_quarter . ' ' . $selected_year;

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $filename = sprintf('mar_consolidation_Q%d_%d.csv', $selected_quarter, $selected_year);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Barangay', 'Submitter', 'Reporting Period', 'Status', 'Remarks', 'Submitted At']);
    foreach ($reports as $report) {
        fputcsv($output, [
            $report['barangay_name'] ?? '',
            $report['submitter'] ?? '',
            date('F', mktime(0, 0, 0, $report['month'], 10)) . ' ' . $report['year'],
            $report['status'],
            $report['remarks'] ?? '',
            date('Y-m-d H:i:s', strtotime($report['submitted_at']))
        ]);
    }
    fclose($output);
    exit;
}

$page_title = 'MAR Consolidation & Compliance Monitoring';
ob_start();
?>

<!-- Header -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900">MAR Consolidation & Compliance Monitoring</h2>
        <p class="text-sm text-slate-500">Review, track compliance deadlines, and consolidate Monthly Accomplishment Reports across constituent barangays.</p>
    </div>
    <div class="flex flex-wrap gap-3 items-center">
        <label class="text-sm text-slate-500">Quarter</label>
        <form action="" method="get" class="flex flex-wrap gap-2 items-center">
            <select name="quarter" class="px-3 py-2 border border-slate-200 rounded-xl bg-white text-sm text-slate-700 focus:outline-none focus:border-blue-500">
                <option value="1" <?= $selected_quarter === 1 ? 'selected' : '' ?>>Q1</option>
                <option value="2" <?= $selected_quarter === 2 ? 'selected' : '' ?>>Q2</option>
                <option value="3" <?= $selected_quarter === 3 ? 'selected' : '' ?>>Q3</option>
                <option value="4" <?= $selected_quarter === 4 ? 'selected' : '' ?>>Q4</option>
            </select>
            <select name="year" class="px-3 py-2 border border-slate-200 rounded-xl bg-white text-sm text-slate-700 focus:outline-none focus:border-blue-500">
                <?php for ($year = date('Y'); $year >= date('Y') - 3; $year--): ?>
                    <option value="<?= $year ?>" <?= $selected_year === $year ? 'selected' : '' ?>><?= $year ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn-primary text-sm">Filter</button>
        </form>
        <a href="?quarter=<?= $selected_quarter ?>&year=<?= $selected_year ?>&export=csv" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="card p-5 border border-slate-200 rounded-2xl bg-slate-50">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Selected Period</p>
        <p class="text-lg font-bold text-slate-900"><?= htmlspecialchars($quarter_label) ?></p>
    </div>
    <div class="card p-5 border border-slate-200 rounded-2xl bg-white">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Total Submissions</p>
        <p class="text-3xl font-black text-slate-900"><?= number_format($summary['total_submissions'] ?: 0) ?></p>
    </div>
    <div class="card p-5 border border-slate-200 rounded-2xl bg-white">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Reviewed</p>
        <p class="text-3xl font-black text-emerald-700"><?= number_format($summary['reviewed_count'] ?: 0) ?></p>
    </div>
    <div class="card p-5 border border-slate-200 rounded-2xl bg-white">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Returned</p>
        <p class="text-3xl font-black text-red-700"><?= number_format($summary['returned_count'] ?: 0) ?></p>
    </div>
    <div class="card p-5 border border-slate-200 rounded-2xl bg-white">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Barangays Covered</p>
        <p class="text-3xl font-black text-slate-900"><?= number_format($summary['barangay_count'] ?: 0) ?></p>
    </div>
</div>

<!-- Compliance Overview Table -->
<div class="card overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
        <h3 class="text-base font-bold text-slate-900">Barangay MAR Submissions Dataset</h3>
        <span class="text-xs text-slate-500 font-medium"><?= count($reports) ?> Total Submissions</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 border-collapse">
            <thead class="bg-slate-50/50 text-slate-500 text-xs uppercase font-bold tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Barangay</th>
                    <th class="px-6 py-4">Submitted By</th>
                    <th class="px-6 py-4">Reporting Period</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Submitted Date</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!empty($reports)): ?>
                    <?php foreach ($reports as $report): ?>
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900"><?= htmlspecialchars($report['barangay_name'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($report['submitter']) ?></td>
                            <td class="px-6 py-4 font-medium text-slate-900">
                                <?= date("F", mktime(0, 0, 0, $report['month'], 10)) ?> <?= htmlspecialchars($report['year']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($report['status'] === 'reviewed'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Reviewed & Approved</span>
                                <?php elseif ($report['status'] === 'returned'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">Returned for Correction</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Pending Review</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400 font-medium"><?= date('M d, Y', strtotime($report['submitted_at'])) ?></td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="viewFiles('<?= htmlspecialchars($report['session_minutes_path'] ?? '') ?>', '<?= htmlspecialchars($report['attendance_records_path'] ?? '') ?>', '<?= htmlspecialchars($report['post_activity_reports_path'] ?? '') ?>', '<?= htmlspecialchars($report['financial_reports_path'] ?? '') ?>')" class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-900 hover:text-white rounded-lg text-xs font-bold transition-all">
                                    View Documents
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">No barangay MAR submissions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View Files Modal -->
<div id="viewFilesModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('viewFilesModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <div class="bg-white px-6 pt-6 pb-6">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight mb-4">Submitted MAR Documents</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-xl bg-slate-50">
                        <span class="text-sm font-semibold text-slate-700">1. Session Minutes</span>
                        <a id="link_session_minutes" href="#" target="_blank" class="text-indigo-600 hover:underline text-xs font-bold">View Document</a>
                    </div>
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-xl bg-slate-50">
                        <span class="text-sm font-semibold text-slate-700">2. Attendance Records</span>
                        <a id="link_attendance_records" href="#" target="_blank" class="text-indigo-600 hover:underline text-xs font-bold">View Document</a>
                    </div>
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-xl bg-slate-50">
                        <span class="text-sm font-semibold text-slate-700">3. Post-Activity Reports</span>
                        <a id="link_post_activity" href="#" target="_blank" class="text-indigo-600 hover:underline text-xs font-bold">View Document</a>
                    </div>
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-xl bg-slate-50">
                        <span class="text-sm font-semibold text-slate-700">4. Financial Reports</span>
                        <a id="link_financial_reports" href="#" target="_blank" class="text-indigo-600 hover:underline text-xs font-bold">View Document</a>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse border-t border-slate-100">
                <button type="button" onclick="document.getElementById('viewFilesModal').classList.add('hidden')" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/reports.js"></script>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
