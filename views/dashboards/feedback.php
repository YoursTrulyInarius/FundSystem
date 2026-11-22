<?php
// views/dashboards/feedback.php
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">Community Feedback & Concerns</h3>
            <p class="text-sm text-slate-500 mt-1">Review feedback submitted by community members about your SK-funded projects</p>
        </div>
        <?php if ($unreadCount > 0): ?>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-medium">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                <?= $unreadCount ?> new feedback
            </div>
        <?php endif; ?>
    </div>

    <form method="get" action="feedback" class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-end">
        <div class="w-full sm:max-w-xs">
            <label for="feedbackPeriod" class="mb-1 block text-sm font-medium text-slate-700">Filter by date</label>
            <select id="feedbackPeriod" name="period" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-500 focus:outline-none">
                <?php foreach ($periodOptions as $periodValue => $periodLabel): ?>
                    <option value="<?= htmlspecialchars($periodValue) ?>" <?= $feedbackPeriod === $periodValue ? 'selected' : '' ?>>
                        <?= htmlspecialchars($periodLabel) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            Apply filter
        </button>
        <?php if ($feedbackPeriod !== 'all'): ?>
            <a href="feedback" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Clear
            </a>
        <?php endif; ?>
    </form>

    <!-- Feedback List -->
    <div class="card overflow-hidden">
        <?php if (empty($feedbacks)): ?>
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900">
                    <?= $feedbackPeriod === 'all' ? 'No feedback yet' : 'No feedback in this date range' ?>
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    <?= $feedbackPeriod === 'all'
                        ? 'Community feedback will appear here once submitted.'
                        : 'Try a wider date range or clear the filter to see all feedback.' ?>
                </p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-200">
                <?php foreach ($feedbacks as $fb): ?>
                    <?php
                    $feedbackMessage = (string) ($fb['message'] ?? '');
                    $previewLimit = 180;
                    $hasLongMessage = mb_strlen($feedbackMessage, 'UTF-8') > $previewLimit;
                    $feedbackPreview = $hasLongMessage
                        ? rtrim(mb_substr($feedbackMessage, 0, $previewLimit, 'UTF-8')) . '…'
                        : $feedbackMessage;
                    $fullMessageId = 'feedback-message-full-' . (int) $fb['id'];
                    ?>
                    <div class="p-6 hover:bg-slate-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0 flex-1">
                                <!-- Header with project and date -->
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($fb['project_title'] ?? ($fb['feedback_barangay'] ? 'Barangay ' . $fb['feedback_barangay'] . ' Feedback' : 'General Community Feedback')) ?>
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        <?= date('M d, Y \a\t H:i', strtotime($fb['created_at'])) ?>
                                    </span>
                                </div>

                                <!-- Submitter info -->
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-medium text-slate-700">
                                        <?= htmlspecialchars($fb['user_name'] ?? 'Anonymous') ?>
                                    </span>
                                    <?php if (!empty($fb['contact_info'])): ?>
                                        <span class="text-sm text-slate-500">
                                            • <?= htmlspecialchars($fb['contact_info']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Message -->
                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-3">
                                    <p class="feedback-message-text text-sm text-slate-700 leading-relaxed">
                                        <span class="feedback-message-preview"><?= nl2br(htmlspecialchars($feedbackPreview, ENT_QUOTES, 'UTF-8')) ?></span>
                                        <?php if ($hasLongMessage): ?>
                                            <span id="<?= $fullMessageId ?>" class="feedback-message-full" hidden><?= nl2br(htmlspecialchars($feedbackMessage, ENT_QUOTES, 'UTF-8')) ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($hasLongMessage): ?>
                                        <button type="button" class="feedback-message-toggle mt-2 text-sm font-semibold" aria-expanded="false" aria-controls="<?= $fullMessageId ?>">
                                            View more
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Project context -->
                                <div class="grid grid-cols-3 gap-4 text-xs">
                                    <div>
                                        <span class="text-slate-500 font-medium">Project Owner</span>
                                        <p class="text-slate-700 mt-0.5"><?= htmlspecialchars($fb['project_owner'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 font-medium">Barangay</span>
                                        <p class="text-slate-700 mt-0.5"><?= htmlspecialchars($fb['barangay_name'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 font-medium">Budget</span>
                                        <p class="text-slate-700 mt-0.5">₱<?= number_format($fb['budget'] ?? 0, 2) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete button -->
                            <button type="button" class="delete-feedback-btn ml-4 p-2 text-slate-400 hover:text-red-600 transition" data-feedback-id="<?= (int)$fb['id'] ?>" title="Delete feedback">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($totalFeedback > 0): ?>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Showing <?= number_format($firstFeedbackNumber) ?>–<?= number_format($lastFeedbackNumber) ?>
                of <?= number_format($totalFeedback) ?> feedback entries
            </p>
            <?php if ($totalPages > 1): ?>
                <nav class="flex items-center gap-2" aria-label="Feedback pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?period=<?= urlencode($feedbackPeriod) ?>&amp;page=<?= $currentPage - 1 ?>"
                           class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Previous
                        </a>
                    <?php else: ?>
                        <span class="cursor-not-allowed rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-400" aria-disabled="true">
                            Previous
                        </span>
                    <?php endif; ?>
                    <span class="px-2 text-sm font-medium text-slate-600">Page <?= $currentPage ?> of <?= $totalPages ?></span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?period=<?= urlencode($feedbackPeriod) ?>&amp;page=<?= $currentPage + 1 ?>"
                           class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Next
                        </a>
                    <?php else: ?>
                        <span class="cursor-not-allowed rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-400" aria-disabled="true">
                            Next
                        </span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.feedback-message-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const message = button.closest('.bg-slate-50');
            const preview = message.querySelector('.feedback-message-preview');
            const full = message.querySelector('.feedback-message-full');
            const expanded = button.getAttribute('aria-expanded') !== 'true';

            preview.hidden = expanded;
            full.hidden = !expanded;
            button.setAttribute('aria-expanded', String(expanded));
            button.textContent = expanded ? 'View less' : 'View more';
        });
    });

    document.querySelectorAll('.delete-feedback-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const feedbackId = button.getAttribute('data-feedback-id');
            
            Swal.fire({
                title: 'Delete Feedback?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('feedback_id', feedbackId);

                    try {
                        const response = await fetch('api/feedback/delete', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'Feedback has been removed.',
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Failed to delete feedback.'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting feedback.'
                        });
                    }
                }
            });
        });
    });
</script>

