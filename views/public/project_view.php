<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project['title']) ?> | Public Project Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; margin: 0; }
        .badge { display: inline-flex; align-items: center; gap: .4rem; padding: .4rem .75rem; border-radius: 999px; font-size: .8rem; font-weight: 700; }
        .badge-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-status { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .detail-label { color: #475569; text-transform: uppercase; letter-spacing: .08em; font-size: .7rem; font-weight: 700; }
        .stat-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; }
        .section-title { font-size: 1.15rem; font-weight: 800; }
        .table-header { background: #f8fafc; }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <a href="/FundSystem/" class="text-sm font-semibold text-blue-600 hover:text-blue-800">← Back to Public Portal</a>
                    <h1 class="text-3xl sm:text-4xl font-bold mt-4"><?= htmlspecialchars($project['title']) ?></h1>
                    <p class="text-slate-600 mt-2 max-w-2xl"><?= htmlspecialchars($project['description'] ?? 'No detailed description available.') ?></p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="badge badge-primary">Budget ₱<?= number_format($project['budget'], 2) ?></span>
                    <span class="badge badge-status capitalize"><?= htmlspecialchars($project['status']) ?></span>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.8fr_1fr]">
                <div class="space-y-6">
                    <div class="stat-box">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="text-slate-500 uppercase tracking-[0.18em] text-xs">Project owner</p>
                                <p class="text-lg font-semibold text-slate-900 mt-1"><?= htmlspecialchars($project['project_owner'] ?? 'SK Barangay Representative') ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-slate-500 uppercase tracking-[0.18em] text-xs">Barangay</p>
                                <p class="text-lg font-semibold text-slate-900 mt-1"><?= htmlspecialchars($project['barangay_name'] ?? 'Unassigned') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="detail-label">Abyip code</p>
                                <p class="mt-1 text-slate-900 font-semibold"><?= htmlspecialchars($project['abyip_code'] ?? 'N/A') ?></p>
                            </div>
                            <div>
                                <p class="detail-label">Budget category</p>
                                <p class="mt-1 text-slate-900 font-semibold"><?= htmlspecialchars($project['budget_category'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div>
                                <p class="detail-label">Progress milestones</p>
                                <p class="mt-1 text-slate-900 font-semibold"><?= count($milestones) ?> of 4 completed</p>
                            </div>
                            <span class="badge badge-secondary">Updated <?= date('M d, Y', strtotime($project['created_at'])) ?></span>
                        </div>
                        <div class="space-y-3">
                            <?php
                                $stage_order = ['planning', 'authorization', 'implementation', 'monitoring'];
                                $labels = [
                                    'planning' => 'Planning',
                                    'authorization' => 'Authorization',
                                    'implementation' => 'Implementation',
                                    'monitoring' => 'Monitoring',
                                ];
                            ?>
                            <?php foreach ($stage_order as $stage): ?>
                                <?php $matched = null; foreach ($milestones as $m) { if ($m['stage'] === $stage) { $matched = $m; break; } } ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded-full <?= $matched ? 'bg-emerald-500' : 'bg-slate-300' ?>"></span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-900"><?= $labels[$stage] ?></p>
                                        <p class="text-xs text-slate-500 mt-0.5"><?= $matched ? 'Completed ' . date('M d, Y', strtotime($matched['date_achieved'] ?? $matched['created_at'])) : 'Not completed yet' ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="stat-box">
                        <h2 class="section-title">Project Transactions</h2>
                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600 border-collapse">
                                <thead class="table-header text-slate-500 uppercase text-xs tracking-[0.18em]">
                                    <tr>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Ref</th>
                                        <th class="px-4 py-3">Amount</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transactions)): ?>
                                        <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No public transactions available yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($transactions as $tx): ?>
                                            <tr class="border-t border-slate-200">
                                                <td class="px-4 py-3 capitalize"><?= htmlspecialchars($tx['type']) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($tx['reference_no']) ?></td>
                                                <td class="px-4 py-3 font-semibold">₱<?= number_format($tx['amount'], 2) ?></td>
                                                <td class="px-4 py-3 capitalize">
                                                    <span class="badge badge-secondary"><?= htmlspecialchars($tx['status']) ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="stat-box">
                        <h2 class="section-title">Key project details</h2>
                        <dl class="mt-4 space-y-4 text-sm text-slate-600">
                            <div>
                                <dt class="detail-label">Project title</dt>
                                <dd class="mt-1 text-slate-900 font-semibold"><?= htmlspecialchars($project['title']) ?></dd>
                            </div>
                            <div>
                                <dt class="detail-label">Barangay</dt>
                                <dd class="mt-1 text-slate-900 font-semibold"><?= htmlspecialchars($project['barangay_name'] ?? 'N/A') ?></dd>
                            </div>
                            <div>
                                <dt class="detail-label">SK Officer</dt>
                                <dd class="mt-1 text-slate-900 font-semibold"><?= htmlspecialchars($project['project_owner'] ?? 'N/A') ?></dd>
                            </div>
                            <div>
                                <dt class="detail-label">Budget</dt>
                                <dd class="mt-1 text-slate-900 font-semibold">₱<?= number_format($project['budget'], 2) ?></dd>
                            </div>
                            <div>
                                <dt class="detail-label">Status</dt>
                                <dd class="mt-1 text-slate-900 font-semibold capitalize"><?= htmlspecialchars($project['status']) ?></dd>
                            </div>
                            <div>
                                <dt class="detail-label">Created</dt>
                                <dd class="mt-1 text-slate-900 font-semibold"><?= date('M d, Y', strtotime($project['created_at'])) ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="stat-box">
                        <h2 class="section-title">About this project</h2>
                        <p class="mt-4 text-slate-600 leading-7"><?= nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
