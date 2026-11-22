let activeReportId = null;
let activeFiles = {};

document.addEventListener('DOMContentLoaded', () => {
    const marForm = document.getElementById('marForm');
    const submitMarBtn = document.getElementById('submitMarBtn');
    const marAlert = document.getElementById('marAlert');
    const activityRows = document.getElementById('activityRows');
    const resolutionRows = document.getElementById('resolutionRows');

    document.querySelectorAll('[data-view-files]').forEach((button) => {
        button.addEventListener('click', () => {
            window.viewFiles(
                Number(button.dataset.reportId),
                JSON.parse(button.dataset.files),
                button.dataset.canManage === 'true'
            );
        });
    });

    if (!marForm) return;

    const otherImagesInput = marForm.querySelector('[data-other-images]');
    const otherImageStatus = marForm.querySelector('[data-other-image-status]');
    let activityIndex = 0;
    let resolutionIndex = 0;
    const validMarText = /^[\p{L}\p{M}\p{N}\s.,;:'"()/&+#%-]*$/u;
    const invalidMarTextMessage = 'Use letters, numbers, spaces, and basic punctuation only. Exclamation marks and unsupported symbols are not allowed.';

    function updateOtherImageStatus(existingCount, selectedCount) {
        const existing = Number(existingCount) || 0;
        const selected = Number(selectedCount) || 0;
        marForm.dataset.existingOtherImages = String(existing);
        if (otherImageStatus) {
            otherImageStatus.textContent = existing || selected
                ? `${existing} already attached; ${selected} selected (${existing + selected}/5 maximum).`
                : 'Optional — upload 1–5 images, or leave blank.';
        }
    }

    function validateMarTextField(field) {
        field.setCustomValidity(validMarText.test(field.value) ? '' : invalidMarTextMessage);
    }

    function validateActivityRows() {
        activityRows.querySelectorAll('tr').forEach((row) => {
            const description = row.querySelector('textarea');
            const center = row.querySelector('[name$="[center]"]');
            const amount = row.querySelector('[name$="[amount]"]');
            const rowHasData = [...row.querySelectorAll('input, textarea')].some((field) => field.value.trim() !== '');
            if (amount) {
                const complete = description.value.trim() !== '' && center.value.trim() !== '' && amount.value.trim() !== '';
                amount.setCustomValidity(rowHasData && !complete
                    ? 'Complete the activity description, participation, and non-negative amount.'
                    : '');
            }

            const started = row.querySelector('[name$="[started]"]');
            const completed = row.querySelector('[name$="[completed]"]');
            if (completed) {
                completed.setCustomValidity(started.value && completed.value && completed.value < started.value
                    ? 'The completion date cannot be before the start date.'
                    : '');
            }
        });
    }

    function validateSessionDates() {
        marForm.querySelectorAll('input[name^="sessions["][name$="[number]"]').forEach((number) => {
            const date = number.closest('tr').querySelector('input[type="date"]');
            date.setCustomValidity(Number(number.value) > 0 && !date.value
                ? 'Enter the date for a session with a non-zero number.'
                : '');
        });
    }

    marForm.addEventListener('input', (event) => {
        if (event.target.matches('[data-mar-text]')) validateMarTextField(event.target);
        if (event.target.name && event.target.name.indexOf('activities[') === 0) validateActivityRows();
        if (event.target.name && event.target.name.indexOf('sessions[') === 0) validateSessionDates();
    });

    function addRow(container, templateId, index) {
        const template = document.getElementById(templateId);
        const row = template.content.firstElementChild.cloneNode(true);
        row.innerHTML = row.innerHTML.split('__INDEX__').join(index);
        container.appendChild(row);
        return row;
    }

    function clearDynamicRows() {
        activityRows.replaceChildren();
        resolutionRows.replaceChildren();
        activityIndex = 0;
        resolutionIndex = 0;
        for (let i = 0; i < 3; i++) addRow(activityRows, 'activityRowTemplate', activityIndex++);
        for (let i = 0; i < 2; i++) addRow(resolutionRows, 'resolutionRowTemplate', resolutionIndex++);
    }

    function setField(name, value) {
        const input = marForm.querySelector(`[name="${name}"]`);
        if (input) input.value = value === null || value === undefined ? '' : value;
    }

    function clearMarAlert() {
        marAlert.textContent = '';
        marAlert.classList.add('hidden');
    }

    function setEditMode(report) {
        marForm.reset();
        clearDynamicRows();
        clearMarAlert();
        marForm.dataset.reportId = report.id;
        document.querySelector('#marModal #modal-title').textContent = 'Correct and Resubmit MAR';
        submitMarBtn.textContent = 'Resubmit MAR';
        setField('month', report.month);
        setField('year', report.year);

        const form = report.form || {};
        setField('other_details', form.other_details);
        updateOtherImageStatus(Array.isArray(form.other_images) ? form.other_images.length : 0, 0);
        Object.entries(form.sessions || {}).forEach(([type, values]) => {
            Object.entries(values || {}).forEach(([field, value]) => setField(`sessions[${type}][${field}]`, value));
        });
        (form.activities || []).forEach((values, index) => {
            const row = activityRows.children[index] || addRow(activityRows, 'activityRowTemplate', activityIndex++);
            Object.entries(values || {}).forEach(([field, value]) => {
                const input = row.querySelector(`[name="activities[${index}][${field}]"]`);
                if (input) input.value = value ?? '';
            });
        });
        (form.resolutions || []).forEach((description, index) => {
            const row = resolutionRows.children[index] || addRow(resolutionRows, 'resolutionRowTemplate', resolutionIndex++);
            const input = row.querySelector(`[name="resolutions[${index}][description]"]`);
            if (input) input.value = description;
        });
        const signatories = form.signatories || {};
        ['secretary', 'treasurer', 'chairperson'].forEach((field) => setField(`signatories[${field}]`, signatories[field]));
        (signatories.kagawads || []).forEach((name, index) => setField(`signatories[kagawads][${index}]`, name));

        const files = report.files || {};
        marForm.querySelectorAll('[data-existing-document]').forEach((label) => {
            label.textContent = '';
            label.classList.remove('hidden');
            label.hidden = true;
        });
        Object.keys(files).forEach((field) => {
            const input = marForm.querySelector(`[name="${field}"]`);
            const status = marForm.querySelector(`[data-existing-document="${field}"]`);
            if (input) input.required = !files[field];
            if (status && files[field]) {
                status.textContent = `Current file: ${String(files[field]).split(/[\\/]/).pop()} (leave blank to keep)`;
                status.hidden = false;
            } else if (status) {
                status.textContent = 'Required document missing — upload a replacement.';
                status.hidden = false;
            }
        });
        document.getElementById('marModal').classList.remove('hidden');
    }

    window.openNewMarForm = function() {
        marForm.reset();
        clearDynamicRows();
        clearMarAlert();
        delete marForm.dataset.reportId;
        updateOtherImageStatus(0, 0);
        document.querySelector('#marModal #modal-title').textContent = 'Submit MAR';
        submitMarBtn.textContent = 'Submit MAR';
        marForm.querySelectorAll('[data-required-document]').forEach((input) => { input.required = true; });
        marForm.querySelectorAll('[data-existing-document]').forEach((label) => {
            label.textContent = '';
            label.hidden = true;
        });
        document.getElementById('marModal').classList.remove('hidden');
    };

    window.closeMarForm = function() {
        document.getElementById('marModal').classList.add('hidden');
        marForm.reset();
        clearDynamicRows();
        clearMarAlert();
        delete marForm.dataset.reportId;
        updateOtherImageStatus(0, 0);
    };

    document.querySelectorAll('[data-edit-mar]').forEach((button) => {
        button.addEventListener('click', () => {
            try {
                setEditMode(JSON.parse(button.dataset.editMar));
            } catch (error) {
                Swal.fire('Unable to edit MAR', 'The saved MAR form data could not be loaded.', 'error');
            }
        });
    });

    document.querySelector('[data-add-activity]').addEventListener('click', () => {
        addRow(activityRows, 'activityRowTemplate', activityIndex++);
    });
    document.querySelector('[data-add-resolution]').addEventListener('click', () => {
        addRow(resolutionRows, 'resolutionRowTemplate', resolutionIndex++);
    });
    document.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-row]');
        if (removeButton) removeButton.closest('tr').remove();
    });
    clearDynamicRows();

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

    if (otherImagesInput) {
        otherImagesInput.addEventListener('change', () => {
            const existing = Number(marForm.dataset.existingOtherImages) || 0;
            updateOtherImageStatus(existing, otherImagesInput.files.length);
        });
    }

    marForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        marForm.querySelectorAll('[data-mar-text]').forEach(validateMarTextField);
        validateActivityRows();
        validateSessionDates();
        const existingImages = Number(marForm.dataset.existingOtherImages) || 0;
        const selectedImages = otherImagesInput ? otherImagesInput.files.length : 0;
        if (existingImages + selectedImages > 5) {
            marAlert.textContent = 'OTHER can include a maximum of five images.';
            marAlert.classList.remove('hidden');
            otherImagesInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (!marForm.checkValidity()) {
            const firstInvalidField = marForm.querySelector(':invalid');
            marAlert.textContent = firstInvalidField && firstInvalidField.validationMessage
                ? firstInvalidField.validationMessage
                : 'Correct the invalid values and complete all required fields before submitting.';
            marAlert.classList.remove('hidden');
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus({ preventScroll: true });
            }
            marForm.reportValidity();
            return;
        }

        const formData = new FormData(marForm);
        submitMarBtn.textContent = 'Uploading...';
        submitMarBtn.disabled = true;
        clearMarAlert();

        try {
            const isResubmission = Boolean(marForm.dataset.reportId);
            if (isResubmission) formData.append('report_id', marForm.dataset.reportId);
            const response = await fetch(isResubmission ? 'api/reports/resubmit' : 'api/reports/submit', {
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
                window.location.reload();
            } else {
                marAlert.textContent = data.message || 'Failed to submit MAR.';
                marAlert.classList.remove('hidden');
            }
        } catch (error) {
            marAlert.textContent = error.message || 'An error occurred. Please try again later.';
            marAlert.classList.remove('hidden');
        } finally {
            submitMarBtn.textContent = marForm.dataset.reportId ? 'Resubmit MAR' : 'Submit MAR';
            submitMarBtn.disabled = false;
        }
    });
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
        session_minutes: ['link_session_minutes', 'SK Minutes with Attendance and photo documentation'],
        attendance_records: ['link_attendance_records', 'SK Resolutions'],
        post_activity_reports: ['link_post_activity', 'Post Activity Report with Pictures'],
        financial_reports: ['link_financial_reports', 'Copy of Monthly Utilization Report (with DV)']
    };

    activeReportId = Number(reportId);
    activeFiles = files || {};
    let documentNumber = 0;
    for (const [field, [linkId, defaultTitle]] of Object.entries(documentFields)) {
        documentNumber++;
        const title = activeFiles.attachment_labels && activeFiles.attachment_labels[field]
            ? activeFiles.attachment_labels[field]
            : defaultTitle;
        const label = document.querySelector(`[data-document-label="${field}"]`);
        if (label) label.textContent = `${documentNumber}. ${title}`;
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

    const otherImageDocuments = document.getElementById('otherImageDocuments');
    if (otherImageDocuments) {
        otherImageDocuments.replaceChildren();
        const otherImages = Array.isArray(activeFiles.other_images)
            ? activeFiles.other_images.filter((path) => typeof path === 'string' &&
                /^uploads\/mars\/other_image_[a-f0-9]{32}\.(?:jpe?g|png|gif|webp)$/i.test(path))
            : [];
        if (otherImages.length) {
            const title = document.createElement('h4');
            title.className = 'text-sm font-semibold text-slate-700';
            title.textContent = 'OTHER images';
            const gallery = document.createElement('div');
            gallery.className = 'flex flex-wrap gap-3';
            otherImages.forEach((path, index) => {
                const link = document.createElement('a');
                link.href = path;
                link.target = '_blank';
                link.rel = 'noopener';
                link.className = 'flex flex-col items-center gap-1 text-xs text-slate-600';
                const image = document.createElement('img');
                image.src = path;
                image.alt = `OTHER image ${index + 1}`;
                image.className = 'h-20 w-20 rounded-lg border border-slate-200 object-cover';
                const label = document.createElement('span');
                label.textContent = `Image ${index + 1}`;
                link.append(image, label);
                gallery.appendChild(link);
            });
            otherImageDocuments.append(title, gallery);
            otherImageDocuments.classList.remove('hidden');
        } else {
            otherImageDocuments.classList.add('hidden');
        }
    }

    document.getElementById('viewFilesModal').classList.remove('hidden');
};
