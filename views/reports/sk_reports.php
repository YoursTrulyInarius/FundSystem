<?php
require_once 'core/Database.php';
require_once 'models/Report.php';

$database = new Database();
$db = $database->connect();
$reportModel = new Report($db);
$stmt = $reportModel->read($_SESSION['user_id']);

$page_title = 'MAR Submissions';
ob_start();
?>

<!-- Header Actions -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Monthly Accomplishment Reports (MAR)</h2>
        <p class="text-sm text-slate-500">Submit your 4 mandated documents for LYDO compliance review.</p>
    </div>
    <button onclick="document.getElementById('marModal').classList.remove('hidden')" class="btn-primary flex items-center">
        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
        </svg>
        Submit MAR
    </button>
</div>

<!-- Past Submissions Table -->
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700">
                <tr>
                    <th class="px-6 py-4 font-medium">Reporting Period</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium">Submitted At</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if($stmt->rowCount() > 0): ?>
                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900"><?= date("F", mktime(0, 0, 0, $row['month'], 10)) ?> <?= htmlspecialchars($row['year']) ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize 
                                    <?= $row['status'] == 'reviewed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?= date('M d, Y', strtotime($row['submitted_at'])) ?></td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="viewFiles('<?= htmlspecialchars($row['session_minutes_path']) ?>', '<?= htmlspecialchars($row['attendance_records_path']) ?>', '<?= htmlspecialchars($row['post_activity_reports_path']) ?>', '<?= htmlspecialchars($row['financial_reports_path']) ?>')" class="text-accent hover:text-blue-700 font-medium focus:outline-none">View Files</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            No MAR submissions found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Submit MAR Modal -->
<div id="marModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="document.getElementById('marModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4" id="modal-title">Submit MAR</h3>
                
                <div id="marAlert" class="hidden mb-4 p-3 rounded-lg text-sm bg-red-50 text-red-600 border border-red-200"></div>

                <form id="marForm" class="space-y-4" enctype="multipart/form-data">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Month</label>
                            <select name="month" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                                <?php for($m=1; $m<=12; ++$m): ?>
                                    <option value="<?= $m ?>" <?= date('n') == $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Year</label>
                            <input type="number" name="year" value="<?= date('Y') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none" required>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <h4 class="text-sm font-semibold text-slate-800 mb-3">Required Documents</h4>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">1. Session Minutes</label>
                                <input type="file" name="session_minutes" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 border border-slate-200 rounded-md p-1" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">2. Attendance Records</label>
                                <input type="file" name="attendance_records" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 border border-slate-200 rounded-md p-1" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">3. Post-Activity Reports</label>
                                <input type="file" name="post_activity_reports" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 border border-slate-200 rounded-md p-1" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">4. Monthly Financial Reports</label>
                                <input type="file" name="financial_reports" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 border border-slate-200 rounded-md p-1" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                <button type="button" id="submitMarBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-900 text-base font-medium text-white hover:bg-slate-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Submit MAR
                </button>
                <button type="button" onclick="document.getElementById('marModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Files Modal -->
<div id="viewFilesModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="document.getElementById('viewFilesModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4">Submitted Documents</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span class="text-sm font-medium text-slate-700">1. Session Minutes</span>
                        <a id="link_session_minutes" href="#" target="_blank" class="text-accent hover:text-blue-700 text-sm font-medium">View Document</a>
                    </div>
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span class="text-sm font-medium text-slate-700">2. Attendance Records</span>
                        <a id="link_attendance_records" href="#" target="_blank" class="text-accent hover:text-blue-700 text-sm font-medium">View Document</a>
                    </div>
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span class="text-sm font-medium text-slate-700">3. Post-Activity Reports</span>
                        <a id="link_post_activity" href="#" target="_blank" class="text-accent hover:text-blue-700 text-sm font-medium">View Document</a>
                    </div>
                    <div class="flex justify-between items-center p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span class="text-sm font-medium text-slate-700">4. Financial Reports</span>
                        <a id="link_financial_reports" href="#" target="_blank" class="text-accent hover:text-blue-700 text-sm font-medium">View Document</a>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                <button type="button" onclick="document.getElementById('viewFilesModal').classList.add('hidden')" class="w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:w-auto sm:text-sm">
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
