<?php
require_once 'core/Database.php';
require_once 'models/Report.php';
require_once 'views/reports/mar_helpers.php';

$database = new Database();
$db = $database->connect();
$reportModel = new Report($db);
$stmt = $reportModel->read($_SESSION['user_id']);

$page_title = 'MAR Submissions';
ob_start();
?>

<!-- Header Actions -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Monthly Accomplishment Reports (MAR)</h2>
        <p class="text-sm text-slate-500">Complete the Annex 4 report and attach the four required supporting documents.</p>
        <p class="mt-2 max-w-3xl rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
            The Monthly Accomplishment Report and supporting documents shall be submitted to the LYDO not later than the <strong>10th day of the succeeding month</strong>, unless otherwise prescribed by law or municipal policy.
        </p>
    </div>
    <button type="button" onclick="openNewMarForm()" class="btn-primary flex items-center">
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
                    <th class="px-6 py-4 font-medium">Review Feedback</th>
                    <th class="px-6 py-4 font-medium">Submitted At</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if($stmt->rowCount() > 0): ?>
                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php
                        $detailsPayload = mar_details_payload($row);
                        $attachmentLabels = mar_attachment_labels($row);
                        $formData = mar_form_data($row);
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900"><?= date("F", mktime(0, 0, 0, $row['month'], 10)) ?> <?= htmlspecialchars($row['year']) ?></td>
                            <td class="px-6 py-4">
                                <?php if ($row['status'] === 'reviewed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">LYDO Approved — Awaiting SK Federation</span>
                                <?php elseif ($row['status'] === 'returned'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Returned for Correction</span>
                                <?php elseif ($row['status'] === 'recorded'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Approved by SK Federation</span>
                                <?php elseif ($row['status'] === 'pending'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">Pending LYDO Review</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">Status unavailable</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?= $row['status'] === 'returned' ? htmlspecialchars($row['remarks'] ?: 'No rejection reason provided.') : '—' ?></td>
                            <td class="px-6 py-4 text-slate-500"><?= date('M d, Y', strtotime($row['submitted_at'])) ?></td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" data-mar-details="<?= htmlspecialchars(json_encode($detailsPayload), ENT_QUOTES, 'UTF-8') ?>" class="mr-3 text-accent hover:text-blue-700 font-medium">View Form</button>
                                <?php if ($row['status'] === 'returned'): ?>
                                    <button type="button" data-edit-mar="<?= htmlspecialchars(json_encode([
                                        'id' => (int)$row['id'],
                                        'month' => (int)$row['month'],
                                        'year' => (int)$row['year'],
                                        'form' => $formData,
                                        'files' => [
                                            'session_minutes' => $row['session_minutes_path'],
                                            'attendance_records' => $row['attendance_records_path'],
                                            'post_activity_reports' => $row['post_activity_reports_path'],
                                            'financial_reports' => $row['financial_reports_path']
                                        ]
                                    ]), ENT_QUOTES, 'UTF-8') ?>" class="mr-3 text-red-700 hover:text-red-900 font-semibold">Edit &amp; Resubmit</button>
                                <?php endif; ?>
                                <button type="button" data-view-files data-report-id="<?= (int)$row['id'] ?>" data-can-manage="<?= $row['status'] === 'returned' ? 'true' : 'false' ?>"
                                    data-files="<?= htmlspecialchars(json_encode([
                                        'session_minutes' => $row['session_minutes_path'],
                                        'attendance_records' => $row['attendance_records_path'],
                                        'post_activity_reports' => $row['post_activity_reports_path'],
                                        'financial_reports' => $row['financial_reports_path'],
                                        'other_images' => $formData['other_images'],
                                        'attachment_labels' => $attachmentLabels
                                    ]), ENT_QUOTES, 'UTF-8') ?>"
                                    class="text-accent hover:text-blue-700 font-medium focus:outline-none">View Files</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
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
        
        <div class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="closeMarForm()"></div>

        <!-- Modal panel -->
        <div class="mar-modal-shell inline-block align-bottom text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle border border-slate-200">
            <div class="mar-modal-header">
                <div class="mar-modal-heading">
                    <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Submit MAR</h3>
                    <button type="button" class="mar-modal-close" aria-label="Close MAR form" title="Close" onclick="closeMarForm()">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="marAlert" class="hidden p-3 rounded-lg text-sm bg-red-50 text-red-600 border border-red-200" role="alert" aria-live="assertive"></div>
            </div>
            <div class="mar-modal-scroll">
                <form id="marForm" class="space-y-4" enctype="multipart/form-data" novalidate>
                    <?php require 'views/reports/mar_form_fields.php'; ?>
                </form>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                <button type="submit" form="marForm" id="submitMarBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-900 text-base font-medium text-white hover:bg-slate-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Submit MAR
                </button>
                <button type="button" onclick="closeMarForm()" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span data-document-label="session_minutes" class="text-sm font-medium text-slate-700">1. SK Minutes with Attendance and photo documentation</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <a id="link_session_minutes" href="#" class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">View Document</a>
                            <div id="document_controls_session_minutes" class="hidden flex-wrap items-center gap-2">
                                <input type="file" data-replace-document="session_minutes" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                                <button type="button" data-trigger-replace="session_minutes" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Replace</button>
                                <button type="button" data-delete-document="session_minutes" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span data-document-label="attendance_records" class="text-sm font-medium text-slate-700">2. SK Resolutions</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <a id="link_attendance_records" href="#" class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">View Document</a>
                            <div id="document_controls_attendance_records" class="hidden flex-wrap items-center gap-2">
                                <input type="file" data-replace-document="attendance_records" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                                <button type="button" data-trigger-replace="attendance_records" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Replace</button>
                                <button type="button" data-delete-document="attendance_records" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span data-document-label="post_activity_reports" class="text-sm font-medium text-slate-700">3. Post Activity Report with Pictures</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <a id="link_post_activity" href="#" class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">View Document</a>
                            <div id="document_controls_post_activity_reports" class="hidden flex-wrap items-center gap-2">
                                <input type="file" data-replace-document="post_activity_reports" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                                <button type="button" data-trigger-replace="post_activity_reports" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Replace</button>
                                <button type="button" data-delete-document="post_activity_reports" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-3 border border-slate-100 rounded-lg bg-slate-50">
                        <span data-document-label="financial_reports" class="text-sm font-medium text-slate-700">4. Copy of Monthly Utilization Report (with DV)</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <a id="link_financial_reports" href="#" class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">View Document</a>
                            <div id="document_controls_financial_reports" class="hidden flex-wrap items-center gap-2">
                                <input type="file" data-replace-document="financial_reports" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                                <button type="button" data-trigger-replace="financial_reports" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Replace</button>
                                <button type="button" data-delete-document="financial_reports" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div id="otherImageDocuments" class="hidden"></div>
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

<?php require 'views/reports/document_preview_modal.php'; ?>
<?php require 'views/reports/mar_details_modal.php'; ?>

<script src="assets/js/document-preview.js"></script>
<script src="assets/js/mar-details.js"></script>
<script src="assets/js/reports.js"></script>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
