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

// Fetch Barangay Profile
$admin_bname = str_replace('Barangay ', '', $_SESSION['barangay_name'] ?? '');
$bpStmt = $db->prepare("SELECT * FROM barangay_profiles WHERE barangay_name = :bname LIMIT 1");
$bpStmt->execute([':bname' => $admin_bname]);
$barangay_profile = $bpStmt->fetch(PDO::FETCH_ASSOC);
$community_benefits = json_decode($barangay_profile['community_benefits'] ?? '', true);
if (!is_array($community_benefits)) {
    $community_benefits = [];
}

$page_title = 'SK Administrator Dashboard';
ob_start();
?>

<!-- Barangay Cover & Public Profile Showcase Card -->
<div class="card overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 relative z-10">
    <div class="relative min-h-[17rem] w-full overflow-hidden bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900">
        <?php if (!empty($barangay_profile['image_path'])): ?>
            <img id="dashboardCoverPreview" src="<?= htmlspecialchars($barangay_profile['image_path']) ?>" alt="<?= htmlspecialchars($admin_bname) ?>" class="absolute inset-0 h-full w-full object-cover opacity-85">
        <?php else: ?>
            <div id="dashboardCoverPreviewFallback" class="absolute inset-0 flex items-start justify-center bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white">
                <div class="text-center p-6">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 z-10 flex flex-col gap-4 p-4 sm:p-6">
            <div class="min-w-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-md border border-white/30 mb-1.5">
                    🏛️ Barangay Showcase Profile
                </span>
                <h2 class="break-words text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Barangay <?= htmlspecialchars($admin_bname ?: 'Poblacion') ?></h2>
                <p id="dashboardTaglineDisplay" class="mt-0.5 max-w-2xl break-words text-sm leading-relaxed text-slate-200"><?= htmlspecialchars($barangay_profile['tagline'] ?? 'Dedicated to youth development & transparent community service.') ?></p>
            </div>
            <div class="flex min-w-0 flex-wrap items-center gap-2">
                <button type="button" onclick="openProfileModal()" class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-3 py-2.5 text-xs font-bold text-slate-900 shadow-lg transition hover:bg-slate-100 sm:flex-none sm:px-4">
                    <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Upload Photo & Info
                </button>
                <a href="public/barangay?name=<?= urlencode($admin_bname) ?>" target="_blank" class="inline-flex min-h-10 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl border border-white/20 bg-slate-900/80 px-3 py-2.5 text-xs font-bold text-white backdrop-blur-md transition hover:bg-slate-900 sm:flex-none sm:px-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Public Page
                </a>
            </div>
        </div>
    </div>
</div>

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

<!-- Upload Cover Photo & Info Modal -->
<div id="editProfileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeProfileModal()"></div>
    <div class="flex h-full min-h-0 w-full items-center justify-center overflow-hidden p-4 sm:p-6">
        <div class="relative flex w-full max-w-lg min-h-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-2xl" style="max-height: calc(100vh - 2rem); max-height: calc(100dvh - 2rem);">
            <form id="uploadBarangayProfileForm" class="flex min-h-0 flex-1 flex-col" enctype="multipart/form-data">
                <div class="flex min-h-0 flex-1 flex-col bg-white px-6 pt-6 pb-4">
                    <div class="mb-4 flex shrink-0 items-center justify-between border-b border-slate-100 pb-3">
                        <h3 id="modal-title" class="text-lg font-bold text-slate-900">Upload Barangay Photo & Info</h3>
                        <button type="button" onclick="closeProfileModal()" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div id="profileModalContent" class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain pr-2" style="touch-action: pan-y; scrollbar-gutter: stable;">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Barangay Cover Photo</label>
                            <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-blue-500 transition cursor-pointer" onclick="document.getElementById('photoInput').click()">
                                <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewSelectedPhoto(this)">
                                <div id="uploadPrompt">
                                    <svg class="mx-auto h-8 w-8 text-slate-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs font-semibold text-slate-700">Click to select an image from your device</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">JPG, PNG, or WebP up to 5MB</p>
                                </div>
                                <div id="previewContainer" class="hidden mt-2">
                                    <img id="photoPreviewImg" src="#" alt="Preview" class="h-32 w-full object-cover rounded-lg mx-auto">
                                    <p class="text-[11px] text-emerald-600 font-semibold mt-1">Photo ready to upload</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="taglineInput" class="block text-xs font-bold uppercase text-slate-500 mb-1">Tagline or Mission</label>
                            <input type="text" id="taglineInput" name="tagline" value="<?= htmlspecialchars($barangay_profile['tagline'] ?? '') ?>" placeholder="e.g. Empowering Youth for a Brighter Future" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label for="descInput" class="block text-xs font-bold uppercase text-slate-500 mb-1">About the Barangay (Optional)</label>
                            <textarea id="descInput" name="description" rows="3" placeholder="Share a short introduction or background about youth initiatives in your barangay..." class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500"><?= htmlspecialchars($barangay_profile['description'] ?? '') ?></textarea>
                        </div>

                        <div class="border-t border-slate-100 pt-4">
                            <h4 class="text-xs font-bold uppercase text-slate-500 mb-1">Community Benefits</h4>
                            <p class="text-xs text-slate-500 mb-3">Only benefits with a description will appear on your public barangay page. Leave the others blank to hide them.</p>
                            <div class="space-y-3">
                                <?php foreach ([
                                    'education' => 'Education & Youth Capability',
                                    'sports' => 'Sports & Physical Wellness',
                                    'environment' => 'Environmental Protection',
                                    'livelihood' => 'Livelihood & Empowerment'
                                ] as $benefitKey => $benefitTitle): ?>
                                    <div>
                                        <label for="benefit_<?= $benefitKey ?>" class="block text-xs font-semibold text-slate-700 mb-1"><?= htmlspecialchars($benefitTitle) ?></label>
                                        <textarea id="benefit_<?= $benefitKey ?>" name="community_benefits[<?= $benefitKey ?>]" rows="2" maxlength="500" placeholder="Describe this benefit (leave blank to hide it)" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500"><?= htmlspecialchars($community_benefits[$benefitKey] ?? '') ?></textarea>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeProfileModal()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit" id="saveProfileBtn" class="px-5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition flex items-center gap-1.5">
                        <span id="saveProfileText">Save & Publish</span>
                        <svg id="saveProfileSpinner" class="hidden animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const profileModal = document.getElementById('editProfileModal');
const dashboardScrollArea = profileModal.parentElement;
const previousScrollOverflow = dashboardScrollArea.style.overflow;
document.body.appendChild(profileModal);

function openProfileModal() {
    profileModal.classList.remove('hidden');
    profileModal.setAttribute('aria-hidden', 'false');
    dashboardScrollArea.style.overflow = 'hidden';
}

function closeProfileModal() {
    profileModal.classList.add('hidden');
    profileModal.setAttribute('aria-hidden', 'true');
    dashboardScrollArea.style.overflow = previousScrollOverflow;
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !profileModal.classList.contains('hidden')) {
        closeProfileModal();
    }
});

function previewSelectedPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreviewImg').src = e.target.result;
            document.getElementById('previewContainer').classList.remove('hidden');
            document.getElementById('uploadPrompt').classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('uploadBarangayProfileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('saveProfileBtn');
    const txt = document.getElementById('saveProfileText');
    const spin = document.getElementById('saveProfileSpinner');

    btn.disabled = true;
    txt.textContent = 'Saving...';
    spin.classList.remove('hidden');

    const formData = new FormData(this);

    try {
        const res = await fetch('api/barangay/update_profile', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: data.message,
                confirmButtonColor: '#0f172a'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data.message || 'Unable to update profile.',
                confirmButtonColor: '#0f172a'
            });
        }
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Could not connect to the server. Please try again.',
            confirmButtonColor: '#0f172a'
        });
    } finally {
        btn.disabled = false;
        txt.textContent = 'Save & Publish';
        spin.classList.add('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
