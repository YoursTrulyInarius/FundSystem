<div id="documentPreviewModal" class="hidden fixed inset-0 z-[60] items-center justify-center bg-slate-950/70 p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="documentPreviewTitle">
    <button type="button" class="absolute inset-0 h-full w-full cursor-default" aria-label="Close document preview" onclick="closeDocumentPreview()"></button>
    <section class="relative z-10 flex h-[88vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <header class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3 sm:px-6">
            <h2 id="documentPreviewTitle" class="truncate text-base font-bold text-slate-900">Document Preview</h2>
            <button type="button" onclick="closeDocumentPreview()" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100" aria-label="Close document preview">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </header>
        <div class="min-h-0 flex-1 bg-slate-100">
            <iframe id="documentPreviewFrame" class="hidden h-full w-full border-0" title="Document preview"></iframe>
            <img id="documentPreviewImage" class="hidden h-full w-full object-contain p-3 sm:p-6" alt="Document preview">
            <div id="documentPreviewFallback" class="hidden h-full flex-col items-center justify-center gap-3 p-6 text-center">
                <p class="font-semibold text-slate-800">Preview is not available for this file type.</p>
                <p class="text-sm text-slate-600">Download the document to open it on your device.</p>
                <a id="documentPreviewDownload" href="#" download class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Download Document</a>
            </div>
        </div>
    </section>
</div>
