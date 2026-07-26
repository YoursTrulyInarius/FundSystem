<?php
$page_title = 'Public Transparency Portal';
ob_start();
?>

<div class="mb-8">
    <div class="card p-6 bg-slate-900 text-white border-0">
        <h2 class="text-2xl font-bold mb-2">Transparency & Accountability</h2>
        <p class="text-slate-300">View the utilization status of SK Funds and submit your feedback directly to the council.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-lg font-medium text-slate-800">Ongoing Projects</h3>
        </div>
        <div class="p-6 text-center text-slate-500 py-12">
            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p>No projects to display.</p>
        </div>
    </div>
    
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-lg font-medium text-slate-800">Submit Feedback</h3>
        </div>
        <div class="p-6">
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Your Name (Optional)</label>
                    <input type="text" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Select Project</label>
                    <select class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                        <option>General Feedback</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Your Feedback</label>
                    <textarea rows="4" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-accent focus:border-accent outline-none" required></textarea>
                </div>
                <button type="button" class="btn-primary w-full">Submit Feedback</button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'views/layout.php';
?>
