<div id="marDetailsModal" class="hidden fixed inset-0 z-[55] items-center justify-center bg-slate-950/70 p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="marDetailsTitle">
    <button type="button" class="absolute inset-0 h-full w-full cursor-default" aria-label="Close MAR details" onclick="closeMarDetails()"></button>
    <section class="mar-modal-shell relative z-10 flex max-h-[92vh] flex-col overflow-hidden bg-white shadow-2xl">
        <header class="flex shrink-0 items-center justify-end gap-2 border-b border-slate-200 bg-white px-3 py-2">
            <span id="lydoRemarksStatus" class="mr-auto text-xs text-slate-600" role="status" aria-live="polite"></span>
            <button id="saveLydoRemarks" type="button" class="hidden rounded bg-slate-900 px-3 py-1 text-xs font-semibold text-white hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-50">Save LYDO Remarks</button>
            <button type="button" onclick="closeMarDetails()" class="rounded border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">Close</button>
        </header>
        <div id="marDetailsContent" class="mar-modal-scroll overflow-y-auto"></div>
    </section>
</div>
