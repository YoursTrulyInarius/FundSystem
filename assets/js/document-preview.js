document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-preview-file]');
        if (!trigger) return;

        event.preventDefault();
        const path = trigger.dataset.previewFile;
        if (path) {
            window.showDocumentPreview(path, trigger.dataset.previewTitle);
        }
    });

    document.addEventListener('keydown', (event) => {
        const modal = document.getElementById('documentPreviewModal');
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            window.closeDocumentPreview();
        }
    });
});

window.showDocumentPreview = function(path, title) {
    const modal = document.getElementById('documentPreviewModal');
    const frame = document.getElementById('documentPreviewFrame');
    const image = document.getElementById('documentPreviewImage');
    const fallback = document.getElementById('documentPreviewFallback');
    const downloadLink = document.getElementById('documentPreviewDownload');
    const documentUrl = new URL(path, window.location.href);
    const extension = documentUrl.pathname.split('.').pop().toLowerCase();

    document.getElementById('documentPreviewTitle').textContent = title || 'Document Preview';
    frame.classList.add('hidden');
    image.classList.add('hidden');
    fallback.classList.add('hidden');
    fallback.classList.remove('flex');
    frame.removeAttribute('src');
    image.removeAttribute('src');

    if (extension === 'pdf') {
        frame.src = documentUrl.href;
        frame.classList.remove('hidden');
    } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
        image.src = documentUrl.href;
        image.classList.remove('hidden');
    } else {
        downloadLink.href = documentUrl.href;
        downloadLink.download = documentUrl.pathname.split('/').pop();
        fallback.classList.remove('hidden');
        fallback.classList.add('flex');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.closeDocumentPreview = function() {
    const modal = document.getElementById('documentPreviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('documentPreviewFrame').removeAttribute('src');
    document.getElementById('documentPreviewImage').removeAttribute('src');
};
