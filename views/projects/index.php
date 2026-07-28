<?php
require_once 'core/Database.php';
require_once 'models/Project.php';

$database = new Database();
$db = $database->connect();
$projectModel = new Project($db);
$stmt = $projectModel->read($_SESSION['user_id']);

$page_title = 'Projects & ABYIP';
ob_start();
?>

<!-- Header Actions -->
<div class="flex justify-between items-end mb-8 relative z-10">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Project Registry</h2>
        <p class="text-slate-500 mt-2 font-medium">Manage and profile your SK-funded projects and activities.</p>
    </div>
    <button
        onclick="document.getElementById('projectModal').classList.remove('hidden')"
        class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Register Project
    </button>
</div>

<!-- Projects Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead
                class="bg-slate-50/80 border-b border-slate-200 text-slate-700 uppercase tracking-wider text-xs font-bold">
                <tr>
                    <th class="px-6 py-5">Project Title</th>
                    <th class="px-6 py-5">Budget</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5">Registered Date</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if ($stmt->rowCount() > 0): ?>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 group-hover:text-blue-700 transition-colors">
                                    <?= htmlspecialchars($row['title']) ?></div>
                                <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">
                                    <?= htmlspecialchars($row['description'] ?? 'No description') ?></div>
                            </td>
                            <td class="px-6 py-5 font-medium text-slate-700">₱<?= number_format($row['budget'], 2) ?></td>
                            <td class="px-6 py-5">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide capitalize shadow-sm
                                    <?= $row['status'] == 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($row['status'] == 'ongoing' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200') ?>">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full mr-1.5 <?= $row['status'] == 'completed' ? 'bg-emerald-500' : ($row['status'] == 'ongoing' ? 'bg-amber-500' : 'bg-blue-500') ?>"></span>
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-slate-500 font-medium">
                                <?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td class="px-6 py-5 text-right">
                                <a href="project-view?id=<?= $row['id'] ?>"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-900 hover:text-white rounded-lg text-xs font-bold transition-all">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">No Projects Found</h3>
                            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Get started by registering your first SK
                                project or activity from your approved ABYIP.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="projectModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
            onclick="document.getElementById('projectModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <div class="bg-white px-6 pt-6 pb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight" id="modal-title">Register New Project
                    </h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600 transition"
                        onclick="document.getElementById('projectModal').classList.add('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="projectForm" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Project Title</label>
                        <input type="text" name="title"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition"
                            required placeholder="e.g. Inter-Barangay Basketball League">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Approved Budget (₱)</label>
                        <input type="number" step="0.01" name="budget"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition"
                            required placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Description / Alignment with
                            ABYIP</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none transition resize-none"
                            placeholder="Briefly describe the project..."></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                <button type="submit" form="projectForm" id="submitProjectBtn"
                    class="flex-1 sm:flex-none inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-slate-900 text-sm font-bold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all">
                    Save Project
                </button>
                <button type="button" onclick="document.getElementById('projectModal').classList.add('hidden')"
                    class="flex-1 sm:flex-none inline-flex justify-center rounded-xl border border-slate-200 shadow-sm px-6 py-2.5 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus:outline-none transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/projects.js"></script>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>