<?php
require_once 'core/Database.php';
require_once 'models/Project.php';
require_once 'models/Transaction.php';

if (!isset($_GET['id'])) {
    header('Location: projects');
    exit;
}

$project_id = $_GET['id'];
$database = new Database();
$db = $database->connect();

// Fetch Project details
$query = "SELECT * FROM projects WHERE id = :id AND user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $project_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "Project not found or access denied.";
    exit;
}

// Fetch Transactions
$txModel = new Transaction($db);
$transactions = $txModel->readByProject($project_id);

// Fetch Milestones
$stages = ['planning', 'authorization', 'implementation', 'monitoring'];
$milestone_query = $db->prepare("SELECT * FROM project_milestones WHERE project_id = :pid");
$milestone_query->execute([':pid' => $project_id]);
$milestones_raw = $milestone_query->fetchAll(PDO::FETCH_ASSOC);

// Index by stage for easy lookup
$milestones = [];
foreach ($milestones_raw as $m) {
    $milestones[$m['stage']] = $m;
}

$page_title = 'Project Details: ' . htmlspecialchars($project['title']);
ob_start();
?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <a href="projects" class="sans text-sm text-blue-600 hover:text-blue-800 mb-2 inline-flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Projects
        </a>
        <h2 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($project['title']) ?></h2>
        <div class="text-sm text-slate-500 mt-1 flex items-center gap-2">
            <span>Budget: <span class="font-bold text-slate-700">₱<?= number_format($project['budget'], 2) ?></span> &nbsp;|&nbsp; Status:</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize border
                <?= $project['status'] === 'completed' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 
                   ($project['status'] === 'ongoing' ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                   'bg-slate-100 text-slate-700 border-slate-200') ?>">
                <?= htmlspecialchars($project['status']) ?>
            </span>
        </div>
    </div>
    <button onclick="document.getElementById('txModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 justify-center px-4 py-2.5 bg-slate-900 text-white hover:bg-slate-700 shadow-sm rounded-lg text-sm font-bold transition-all">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Transaction
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Transactions -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Financial Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Ref No.</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Document</th>
                        <th class="px-6 py-4">Certificate</th>
                    </tr>
                </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if($transactions->rowCount() > 0): ?>
                                <?php while($row = $transactions->fetch(PDO::FETCH_ASSOC)): ?>
                                    <?php
                                        // Fetch certificate for this transaction if it exists
                                        $cert_query = $db->prepare("SELECT id FROM certifications WHERE transaction_id = :tx_id ORDER BY created_at DESC LIMIT 1");
                                        $cert_query->execute([':tx_id' => $row['id']]);
                                        $cert_row = $cert_query->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 capitalize text-slate-900 font-semibold"><?= htmlspecialchars($row['type']) ?></td>
                                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($row['reference_no']) ?></td>
                                        <td class="px-6 py-4 font-bold text-slate-800">₱<?= number_format($row['amount'], 2) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold capitalize shadow-sm
                                                <?= $row['status'] == 'recorded' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($row['status'] == 'reviewed' ? 'bg-blue-100 text-blue-800 border border-blue-200' : ($row['status'] == 'returned' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-amber-100 text-amber-800 border border-amber-200')) ?>">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 <?= $row['status'] == 'recorded' ? 'bg-emerald-500' : ($row['status'] == 'reviewed' ? 'bg-blue-500' : ($row['status'] == 'returned' ? 'bg-red-500' : 'bg-amber-500')) ?>"></span>
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if($row['document_path']): ?>
                                                <?php 
                                                    $paths = json_decode($row['document_path'], true);
                                                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($paths)) {
                                                        $paths = [$row['document_path']]; // Fallback for old single paths
                                                    }
                                                ?>
                                                <div class="flex flex-col gap-2">
                                                    <?php foreach ($paths as $idx => $path): ?>
                                                        <a href="<?= htmlspecialchars($path) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md text-xs font-bold transition-all border border-blue-100 w-max" title="<?= htmlspecialchars(basename($path)) ?>">
                                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            <span class="max-w-[150px] truncate"><?= htmlspecialchars(basename($path)) ?></span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-sm">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if($cert_row): ?>
                                                <a href="certification?id=<?= $cert_row['id'] ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-md text-xs font-bold transition-all border border-emerald-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                                    View Certificate
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-sm">Pending</span>
                                            <?php endif; ?>
                                            <?php if ($row['status'] === 'returned'): ?>
                                                <p class="text-xs text-red-700 mt-2">Returned reason: <?= htmlspecialchars($row['remarks'] ?: 'No reason provided.') ?></p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-slate-500 font-medium">No financial transactions recorded yet.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar: Milestones -->
    <div class="space-y-6">
        <div class="card bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80">
                <h3 class="text-lg font-bold text-slate-900">Project Milestones</h3>
                <p class="text-xs text-slate-500 mt-0.5">Track the 4-stage SK project workflow</p>
            </div>
            <div class="p-6">
                <div class="relative">
                    <!-- Vertical line -->
                    <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-slate-200 z-0"></div>

                    <div class="space-y-6 relative z-10">
                        <?php
                        $stage_labels = [
                            'planning'       => ['label' => 'Planning',        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            'authorization'  => ['label' => 'Authorization',   'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            'implementation' => ['label' => 'Implementation',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                            'monitoring'     => ['label' => 'Monitoring',      'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ];
                        $is_last = count($stages) - 1;
                        foreach ($stages as $i => $stage):
                            $achieved = isset($milestones[$stage]);
                            $info = $stage_labels[$stage];
                        ?>
                        <div class="flex gap-4">
                            <!-- Circle indicator -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center z-10 shadow-sm
                                <?= $achieved ? 'bg-emerald-500 border-2 border-emerald-500' : 'bg-white border-2 border-slate-300' ?>">
                                <?php if ($achieved): ?>
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $info['icon'] ?>"/></svg>
                                <?php endif; ?>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 pb-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold <?= $achieved ? 'text-emerald-700' : 'text-slate-500' ?>"><?= $info['label'] ?></p>
                                    <?php if (!$achieved): ?>
                                        <button onclick="markMilestone('<?= $stage ?>', <?= $project_id ?>)" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">Mark done</button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($achieved): ?>
                                    <p class="text-xs text-slate-500 mt-0.5"><?= date('M d, Y', strtotime($milestones[$stage]['date_achieved'] ?: $milestones[$stage]['created_at'])) ?></p>
                                    <?php if (!empty($milestones[$stage]['description'])): ?>
                                        <p class="text-xs text-slate-600 mt-1 italic">"<?= htmlspecialchars($milestones[$stage]['description']) ?>"</p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-xs text-slate-400 mt-0.5">Not yet completed</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="txModal" class="hidden fixed inset-0 z-50 transition-all duration-300" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Premium glassmorphism backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300" aria-hidden="true" onclick="document.getElementById('txModal').classList.add('hidden')"></div>

        <!-- Modal panel with premium shadow and rounded corners -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full max-h-[calc(100vh-3rem)] overflow-y-auto border border-slate-100 ring-1 ring-slate-900/5">
            
            <!-- Header with subtle gradient -->
            <div class="bg-gradient-to-b from-slate-50 to-white px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800" id="modal-title">Add Financial Transaction</h3>
                </div>
                <p class="text-sm text-slate-500 mt-2 ml-13">Record a new disbursement or liquidation for this project.</p>
            </div>
            
            <div id="txAlert" class="hidden mx-6 mt-4 p-4 rounded-xl text-sm bg-red-50 text-red-600 border border-red-100 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="flex-1 message-content"></span>
            </div>

            <div class="px-6 py-5">
                <form id="txForm" class="space-y-5" enctype="multipart/form-data">
                    <input type="hidden" name="project_id" value="<?= $project_id ?>">
                    
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Transaction Type</label>
                        <div class="relative">
                            <select name="type" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-700 font-medium appearance-none transition-colors duration-200" required>
                                <option value="disbursement">Disbursement Voucher</option>
                                <option value="liquidation">Liquidation Report</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-5">
                        <div class="flex-1 space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Amount (₱)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-medium">₱</div>
                                <input type="number" step="0.01" name="amount" class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-900 font-bold transition-colors duration-200" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Reference No.</label>
                            <input type="text" name="reference_no" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-900 font-medium transition-colors duration-200" placeholder="e.g. DV-2023-001" required>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Supporting Document</label>
                        <div id="dropzone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors duration-200 group relative">
                            <!-- Native file input covering the entire dropzone area -->
                            <input id="file-upload" name="document[]" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" multiple required>
                            
                            <div class="space-y-1 text-center relative z-0">
                                <svg class="mx-auto h-10 w-10 text-slate-400 group-hover:text-blue-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600 justify-center flex-col items-center gap-1">
                                    <div class="flex">
                                        <span class="relative bg-transparent rounded-md font-medium text-blue-600 group-hover:text-blue-500">
                                            Upload files
                                        </span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <span id="file-name-display" class="text-xs text-slate-500 font-medium"></span>
                                </div>
                                <p class="text-xs text-slate-500 mt-2">PDF, PNG, JPG, DOC up to 10MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Remarks <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <textarea name="remarks" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-700 resize-none transition-colors duration-200" placeholder="Add any additional details here..."></textarea>
                    </div>
                </form>
            </div>
            
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('txModal').classList.add('hidden')" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-all duration-200 shadow-sm">
                    Cancel
                </button>
                <button type="submit" form="txForm" id="submitTxBtn" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl border border-transparent bg-slate-900 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Transaction
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/transactions.js"></script>

<script>
function markMilestone(stage, projectId) {
    Swal.fire({
        title: 'Mark ' + stage.charAt(0).toUpperCase() + stage.slice(1) + ' as Done?',
        html: '<textarea id="milestoneNote" class="swal2-textarea" placeholder="Optional: add a short note (e.g. Approved by MPDC on July 25)" style="margin-top:8px;font-size:14px;"></textarea>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: '✓ Mark as Completed',
        preConfirm: () => {
            return document.getElementById('milestoneNote').value;
        }
    }).then(result => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('project_id', projectId);
            formData.append('stage', stage);
            formData.append('description', result.value || '');

            fetch('api/milestone/mark', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Done!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#0f172a'
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }
    });
}
function updateProjectStatus(projectId, newStatus) {
    const formData = new FormData();
    formData.append('project_id', projectId);
    formData.append('status', newStatus);

    fetch('api/project/update_status', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Project status updated to ' + newStatus,
                showConfirmButton: false,
                timer: 2000
            }).then(() => window.location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
