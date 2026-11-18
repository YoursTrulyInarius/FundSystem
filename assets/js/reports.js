let activeReportId = null;
let activeFiles = {};

document.addEventListener('DOMContentLoaded', () => {
    const marForm = document.getElementById('marForm');
    const submitMarBtn = document.getElementById('submitMarBtn');
    const marAlert = document.getElementById('marAlert');

    document.querySelectorAll('[data-view-files]').forEach((button) => {
        button.addEventListener('click', () => {
            window.viewFiles(
                Number(button.dataset.reportId),
                JSON.parse(button.dataset.files),
                button.dataset.canManage === 'true'
            );
        });
    });

    document.querySelectorAll('[data-trigger-replace]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = button.dataset.triggerReplace;
            document.querySelector(`[data-replace-document="${field}"]`).click();
        });
    });

    document.querySelectorAll('[data-replace-document]').forEach((input) => {
        input.addEventListener('change', async () => {
            if (!input.files.length || !activeReportId) return;

            const formData = new FormData();
            formData.append('report_id', activeReportId);
            formData.append('field', input.dataset.replaceDocument);
            formData.append('document', input.files[0]);
            await sendDocumentRequest('update_document', formData);
        });
    });

    document.querySelectorAll('[data-delete-document]').forEach((button) => {
        button.addEventListener('click', async () => {
            const field = button.dataset.deleteDocument;
            if (!activeReportId || !activeFiles[field]) return;

            const result = await Swal.fire({
                title: 'Delete this document?',
                text: 'This will remove the document from the MAR submission.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            });
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('report_id', activeReportId);
            formData.append('field', field);
            await sendDocumentRequest('delete_document', formData);
        });
    });

    if (submitMarBtn) {
        submitMarBtn.addEventListener('click', async () => {
            if (!marForm.checkValidity()) {
                marForm.reportValidity();
                return;
            }

            const formData = new FormData(marForm);
            submitMarBtn.textContent = 'Uploading...';
            submitMarBtn.disabled = true;
            marAlert.classList.add('hidden');

            try {
                const response = await fetch('api/reports/submit', {
                    method: 'POST',
                    body: formData
                });
                
                let data;
                const text = await response.text();
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error("Server returned non-JSON response:", text);
                    throw new Error("Server returned an invalid response.");
                }

                if (data.success) {
                    window.location.reload(); // Reload to show the new submission
                } else {
                    marAlert.textContent = data.message || 'Failed to submit MAR.';
                    marAlert.classList.remove('hidden');
                }
            } catch (error) {
                marAlert.textContent = error.message || 'An error occurred. Please try again later.';
                marAlert.classList.remove('hidden');
            } finally {
                submitMarBtn.textContent = 'Submit MAR';
                submitMarBtn.disabled = false;
            }
        });
    }
});

async function sendDocumentRequest(action, formData) {
    try {
        const response = await fetch(`api/reports/${action}`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'The document operation failed.');
        }

        await Swal.fire({
            title: 'Success',
            text: data.message,
            icon: 'success'
        });
        window.location.reload();
    } catch (error) {
        await Swal.fire({
            title: 'Unable to update document',
            text: error.message || 'Please try again.',
            icon: 'error'
        });
    }
}

window.viewFiles = function(reportId, files, canManage) {
    const documentFields = {
        session_minutes: ['link_session_minutes', 'Session Minutes'],
        attendance_records: ['link_attendance_records', 'Attendance Records'],
        post_activity_reports: ['link_post_activity', 'Post-Activity Reports'],
        financial_reports: ['link_financial_reports', 'Financial Reports']
    };

    activeReportId = Number(reportId);
    activeFiles = files || {};
    for (const [field, [linkId, title]] of Object.entries(documentFields)) {
        const link = document.getElementById(linkId);
        const path = activeFiles[field];
        link.dataset.documentTitle = title;
        if (path) {
            link.href = path;
            link.dataset.previewFile = path;
            link.dataset.previewTitle = title;
            link.textContent = 'View Document';
            link.classList.remove('pointer-events-none', 'text-slate-400');
        } else {
            link.removeAttribute('href');
            delete link.dataset.previewFile;
            delete link.dataset.previewTitle;
            link.textContent = 'No document uploaded';
            link.classList.add('pointer-events-none', 'text-slate-400');
        }

        const controls = document.getElementById(`document_controls_${field}`);
        if (controls) {
            controls.classList.toggle('hidden', !canManage);
            controls.classList.toggle('flex', canManage);
            const deleteButton = controls.querySelector('[data-delete-document]');
            if (deleteButton) deleteButton.classList.toggle('hidden', !path);
        }
    }

    document.getElementById('viewFilesModal').classList.remove('hidden');
};
