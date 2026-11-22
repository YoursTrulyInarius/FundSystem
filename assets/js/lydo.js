document.addEventListener('DOMContentLoaded', () => {
    window.reviewItem = function(type, id, action, defaults = {}) {
        const isMar = type === 'mar';
        let endpoint = type === 'tx' ? 'api/lydo/approve_tx' : 'api/lydo/approve_mar';
        if (action === 'return' || action === 'reject') {
            endpoint = type === 'tx' ? 'api/lydo/return_tx' : 'api/lydo/return_mar';
        }

        if (type === 'tx' && action === 'approve') {
            showReviewCertificateForm(endpoint, id, defaults);
            return;
        }

        if (action === 'approve') {
            Swal.fire({
                title: 'Approve MAR?',
                text: 'This will approve the MAR and forward it to the SK Federation for final approval.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Approve MAR'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitReview(endpoint, type, id, action, '');
                }
            });
        } else {
            Swal.fire({
                title: isMar ? 'Reject MAR and Request Corrections' : 'Return for Correction',
                text: isMar
                    ? 'The MAR will be sent back to the SK Council. Explain what must be corrected.'
                    : 'Please provide a note explaining what needs to be fixed:',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: isMar
                    ? 'e.g., Add the missing signatures to the attendance sheet...'
                    : 'e.g., Missing signatures on the attendance sheet...',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#64748b',
                confirmButtonText: isMar ? 'Reject and Return MAR' : 'Return Document',
                preConfirm: (remarks) => {
                    if (!remarks || !remarks.trim()) {
                        Swal.showValidationMessage(isMar
                            ? 'You need to provide a reason for rejecting the MAR.'
                            : 'You need to provide a note explaining what needs to be fixed.');
                        return false;
                    }
                    return remarks.trim();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitReview(endpoint, type, id, action, result.value);
                }
            });
        }
    };

    function showReviewCertificateForm(endpoint, id, defaults) {
        const escapeHtml = (value) => String(value == null ? '' : value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[character]);
        const today = escapeHtml(defaults.today);
        const options = [
            ['complete', 'Complete and in order'],
            ['minor_clarification', 'Complete but subject to minor clarification'],
            ['incomplete', 'Incomplete and requiring submission of additional documents']
        ];
        const statusOptions = options.map(([value, label], index) => `
            <label class="lydo-review-option">
                <input type="radio" name="review_status" value="${value}" ${index === 0 ? 'checked' : ''}>
                <span class="lydo-review-checkbox" aria-hidden="true"></span>
                <span>${label}</span>
            </label>
        `).join('');

        Swal.fire({
            width: 600,
            showCloseButton: true,
            html: `
                <style>
                    .swal2-popup { box-sizing: border-box; display: flex !important; flex-direction: column; height: 88vh; max-height: 88vh; padding: 12px !important; position: relative; }
                    .swal2-close { align-self: auto !important; margin: 0 !important; position: absolute !important; right: 8px; top: 8px; z-index: 2; }
                    .swal2-html-container { flex: 1 1 auto; margin: 0 !important; max-height: none !important; min-height: 0; overflow: hidden; padding: 0 !important; width: 100%; }
                    .lydo-annex2-scroll { height: 100%; overflow-y: auto; padding: 4px 8px; }
                    .lydo-annex2-paper { background: #fff; box-sizing: border-box; color: #111; font: 12px/1.45 Tahoma, Arial, sans-serif; margin: 0 auto; max-width: 100%; min-height: 735px; padding: 25px 28px 20px; text-align: left; width: 520px; }
                    .lydo-annex2-header { align-items: center; display: flex; height: 108px; justify-content: center; position: relative; text-align: center; }
                    .lydo-annex2-header img { height: 82px; object-fit: contain; position: absolute; width: 82px; }
                    .lydo-annex2-municipal-seal { left: 0; }
                    .lydo-annex2-header img.lydo-annex2-lydo-seal { height: 106px; left: auto; right: 0; width: 106px; }
                    .lydo-annex2-header-copy { font-family: Bahnschrift, Arial, sans-serif; line-height: 1.15; }
                    .lydo-annex2-header-copy { margin: 0 116px 0 92px; }
                    .lydo-annex2-header-copy p { margin: 0; }
                    .lydo-annex2-municipality, .lydo-annex2-address { font-size: 10px; }
                    .lydo-annex2-office { font-size: 16px; font-weight: 700; margin-top: 5px !important; white-space: nowrap; }
                    .lydo-annex2-label { border: 1px solid #111; display: table; font: bold 10px Arial, sans-serif; margin: 2px auto 4px; padding: 2px 12px; }
                    .lydo-annex2-title { font-size: 14px; font-weight: 700; margin: 0 0 18px; text-align: center; }
                    .lydo-annex2-form p { margin: 0 0 9px; }
                    .lydo-annex2-intro, .lydo-annex2-statement { text-align: justify; }
                    .lydo-annex2-inline-field { background: transparent; border: 0; border-bottom: 1px solid #111; border-radius: 0; box-sizing: border-box; color: #111; font: inherit; max-width: 100%; padding: 0 3px 1px; }
                    .lydo-annex2-inline-field:focus { background: #f8fafc; outline: 1px solid #94a3b8; }
                    .lydo-annex2-barangay { background: #fef3c7; border-bottom: 2px solid #d97706; border-radius: 2px; color: #78350f; font-weight: 700; padding: 1px 5px; }
                    .lydo-annex2-reference { min-width: 0; width: 350px; }
                    .lydo-annex2-project { min-width: 0; width: 390px; }
                    .lydo-annex2-amount { min-width: 0; width: 160px; }
                    .lydo-annex2-date { min-width: 0; width: 185px; }
                    .lydo-annex2-options { list-style: none; margin: 0 0 10px; padding: 0 0 0 28px; }
                    .lydo-review-option { align-items: flex-start; color: #111; cursor: pointer; display: flex; font: inherit; gap: 7px; margin: 4px 0; position: relative; }
                    .lydo-review-option input { height: 15px; left: 0; margin: 1px 0 0; opacity: 0; position: absolute; width: 15px; }
                    .lydo-review-checkbox { border: 1px solid #111; box-sizing: border-box; flex: 0 0 13px; height: 13px; margin-top: 2px; position: relative; }
                    .lydo-review-option input:checked + .lydo-review-checkbox::after { border-bottom: 1.5px solid #111; border-right: 1.5px solid #111; content: ""; height: 7px; left: 3px; position: absolute; top: 0; transform: rotate(45deg); width: 4px; }
                    .lydo-review-option input:focus-visible + .lydo-review-checkbox { outline: 2px solid #64748b; outline-offset: 2px; }
                    .lydo-annex2-records { margin-bottom: 4px !important; }
                    .lydo-annex2-field-row { align-items: baseline; display: grid; grid-template-columns: 180px minmax(0, 1fr); margin-bottom: 5px !important; }
                    .lydo-annex2-field-row .lydo-annex2-inline-field { min-width: 0; width: 100%; }
                    .lydo-annex2-field-label { font-weight: 700; }
                    .lydo-annex2-statement { margin-top: 18px !important; }
                    .lydo-annex2-issued { margin-top: 20px !important; }
                    .lydo-annex2-signed { margin-top: 14px !important; }
                    .lydo-annex2-signature-space { height: 46px; }
                    .lydo-annex2-signature-title { font-weight: 700; }
                    .lydo-annex2-correction { margin: 12px 0 !important; }
                    .lydo-annex2-correction[hidden] { display: none; }
                    .lydo-annex2-correction textarea { border: 1px solid #94a3b8; border-radius: 4px; box-sizing: border-box; display: block; font: inherit; margin-top: 4px; padding: 7px; width: 100%; }
                    .lydo-annex2-footer { align-items: flex-end; border-top: 1px solid #aaa; display: flex; font: 9px/1.3 Bahnschrift, Arial, sans-serif; justify-content: space-between; margin-top: 18px; padding-top: 7px; }
                    .lydo-annex2-footer-copy { flex: 1; text-align: center; }
                    .lydo-annex2-footer-copy p { margin: 1px 0; }
                    .lydo-annex2-motto { font-size: 10px; font-style: italic; }
                    .lydo-annex2-footer-contact { align-items: center; display: flex; flex-wrap: wrap; gap: 3px; justify-content: center; }
                    .lydo-annex2-footer-contact img { height: 10px; object-fit: contain; width: 10px; }
                    .lydo-annex2-footer-brands { align-items: flex-end; display: flex; gap: 4px; margin-left: 6px; }
                    .lydo-annex2-dpo { height: 60px; object-fit: contain; width: 44px; }
                    .lydo-annex2-qr { height: 45px; object-fit: contain; width: 45px; }
                    .lydo-annex2-bagong { height: 42px; object-fit: contain; width: 46px; }
                    @media (max-width: 700px) {
                        .swal2-popup { height: 90vh; max-height: 90vh; padding: 8px !important; width: calc(100vw - 20px) !important; }
                        .lydo-annex2-scroll { padding: 4px; }
                        .lydo-annex2-paper { min-height: 735px; padding: 20px 16px; width: 100%; }
                        .lydo-annex2-header { height: 82px; }
                        .lydo-annex2-header img { height: 54px; width: 54px; }
                        .lydo-annex2-header img.lydo-annex2-lydo-seal { height: 70px; width: 70px; }
                        .lydo-annex2-municipal-seal { left: 0; }
                        .lydo-annex2-header-copy { margin: 0 74px; text-align: center; }
                        .lydo-annex2-office { font-size: 13px; }
                        .lydo-annex2-inline-field { max-width: 100%; }
                        .lydo-annex2-field-row { grid-template-columns: 1fr; gap: 2px; }
                        .lydo-annex2-reference, .lydo-annex2-project, .lydo-annex2-amount, .lydo-annex2-date { width: 100%; }
                        .lydo-annex2-footer-brands { display: none; }
                    }
                </style>
                <div class="lydo-annex2-scroll">
                    <article class="lydo-annex2-paper">
                        <header class="lydo-annex2-header">
                            <img class="lydo-annex2-municipal-seal" src="/FundSystem/assets/img/annex2-municipal-seal.png" alt="Municipality seal">
                            <img class="lydo-annex2-lydo-seal" src="/FundSystem/assets/img/annex2-lydo-seal.png" alt="LYDO seal">
                            <div class="lydo-annex2-header-copy">
                                <p class="lydo-annex2-municipality">MUNICIPALITY OF RAMON MAGSAYSAY</p>
                                <p class="lydo-annex2-address">Municipal Hall, Quezon St., Poblacion, Ramon Magsaysay</p>
                                <p class="lydo-annex2-address">Zamboanga del Sur, 7024 Philippines</p>
                                <p class="lydo-annex2-office">LOCAL YOUTH DEVELOPMENT OFFICE</p>
                            </div>
                        </header>
                        <div class="lydo-annex2-label">Annex 2</div>
                        <h2 class="lydo-annex2-title">CERTIFICATION OF COMPLETENESS AND REVIEW</h2>
                        <form id="lydo-review-certificate-form" class="lydo-annex2-form">
                            <p class="lydo-annex2-intro">
                                This is to certify that the financial transaction documents and supporting records submitted by the Sangguniang Kabataan of Barangay
                                <span class="lydo-annex2-barangay" aria-label="Barangay">${escapeHtml(defaults.barangay.replace(/^Barangay\s+/i, ''))}</span>
                                have been reviewed by the Office of the Local Youth Development Officer pursuant to the requirements of the LYOFOST SK Fund Program.
                            </p>
                            <p>Upon review, it is hereby certified that the submitted documents are:</p>
                            <ul class="lydo-annex2-options" aria-label="Review outcome">${statusOptions}</ul>
                            <p class="lydo-annex2-records">The records reviewed pertain to the following:</p>
                            <p class="lydo-annex2-field-row"><span class="lydo-annex2-field-label">Reference Document No.:</span>
                                <input name="reference_document_no" class="lydo-annex2-inline-field lydo-annex2-reference" type="text" maxlength="100" required value="${escapeHtml(defaults.referenceNo)}" aria-label="Reference Document Number">
                            </p>
                            <p class="lydo-annex2-field-row"><span class="lydo-annex2-field-label">Project/Program/Activity:</span>
                                <input name="project_activity" class="lydo-annex2-inline-field lydo-annex2-project" type="text" maxlength="255" required value="${escapeHtml(defaults.projectActivity)}" aria-label="Project, Program, or Activity">
                            </p>
                            <p class="lydo-annex2-field-row"><span class="lydo-annex2-field-label">Amount Involved (Php):</span>
                                <input name="amount" class="lydo-annex2-inline-field lydo-annex2-amount" type="number" min="0.01" max="9999999999999.99" step="0.01" required value="${escapeHtml(defaults.amount)}" aria-label="Amount Involved">
                            </p>
                            <p class="lydo-annex2-field-row"><span class="lydo-annex2-field-label">Date of Transaction:</span>
                                <input name="date_of_transaction" class="lydo-annex2-inline-field lydo-annex2-date" type="date" required aria-label="Date of Transaction">
                            </p>
                            <p class="lydo-annex2-field-row"><span class="lydo-annex2-field-label">Date Reviewed:</span>
                                <input name="date_reviewed" class="lydo-annex2-inline-field lydo-annex2-date" type="date" required value="${today}" aria-label="Date Reviewed">
                            </p>
                            <p class="lydo-annex2-statement">This certification is issued to attest that the submitted records have undergone document review and compliance verification for purposes of registration and monitoring under the LYOFOST SK Fund Program.</p>
                            <p class="lydo-annex2-issued">Issued this
                                <input name="issue_date" type="date" required value="${today}" class="lydo-annex2-inline-field lydo-annex2-date" aria-label="Issue Date">
                                at Ramon Magsaysay, Zamboanga del Sur.
                            </p>
                            <label id="lydo-correction-note-label" class="lydo-annex2-correction" hidden>
                                Correction note (required for incomplete documents)
                                <textarea name="remarks" maxlength="2000" rows="2" placeholder="Specify which additional documents or corrections are required."></textarea>
                            </label>
                            <p class="lydo-annex2-signed">Signed:</p>
                            <div class="lydo-annex2-signature-space"></div>
                            <p class="lydo-annex2-signature-title">LOCAL YOUTH DEVELOPMENT OFFICER</p>
                        </form>
                        <footer class="lydo-annex2-footer">
                            <div class="lydo-annex2-footer-copy">
                                <p class="lydo-annex2-motto">Hapsay, Han-ay, Husay, Hamugaway nga Ramon Magsaysay</p>
                                <p class="lydo-annex2-motto">For the Filipino Youth, For the Liargaonon Youth</p>
                                <p class="lydo-annex2-footer-contact">
                                    <img src="/FundSystem/assets/img/annex2-web-icon.png" alt=""> www.ramonmagsaysay.gov.ph
                                    <img src="/FundSystem/assets/img/annex2-email-icon.png" alt=""> skliargao@gmail.com
                                    <img src="/FundSystem/assets/img/annex2-facebook-icon.png" alt=""> SK LYDO Ramon Magsaysay
                                </p>
                            </div>
                            <div class="lydo-annex2-footer-brands">
                                <img class="lydo-annex2-dpo" src="/FundSystem/assets/img/annex2-dpo-seal.jpeg" alt="Data privacy seal">
                                <img class="lydo-annex2-qr" src="/FundSystem/assets/img/annex2-registry-qr.png" alt="Registry QR code">
                                <img class="lydo-annex2-bagong" src="/FundSystem/assets/img/annex2-bagong-pilipinas.png" alt="Bagong Pilipinas">
                            </div>
                        </footer>
                    </article>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Issue Review Certificate',
            cancelButtonText: 'Cancel',
            focusConfirm: false,
            didOpen: () => {
                const form = document.getElementById('lydo-review-certificate-form');
                const correctionLabel = document.getElementById('lydo-correction-note-label');
                const correctionNote = form.elements.namedItem('remarks');
                form.querySelectorAll('input[name="review_status"]').forEach((input) => {
                    input.addEventListener('change', () => {
                        const needsNote = form.elements.namedItem('review_status').value === 'incomplete';
                        correctionLabel.hidden = !needsNote;
                        correctionNote.required = needsNote;
                    });
                });
            },
            preConfirm: () => {
                const form = document.getElementById('lydo-review-certificate-form');
                if (!form.reportValidity()) {
                    return false;
                }
                const formData = new FormData(form);
                const amount = String(formData.get('amount') || '');
                if (!/^\d{1,13}(?:\.\d{1,2})?$/.test(amount) || Number(amount) <= 0) {
                    Swal.showValidationMessage('Enter a positive amount with up to two decimal places.');
                    return false;
                }
                if (
                    formData.get('date_of_transaction') > formData.get('date_reviewed') ||
                    formData.get('date_reviewed') > formData.get('issue_date')
                ) {
                    Swal.showValidationMessage('The transaction date must not be after the review date, and the review date must not be after the issue date.');
                    return false;
                }
                return Object.fromEntries(formData.entries());
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitReview(endpoint, 'tx', id, 'approve', result.value.remarks || '', result.value);
            }
        });
    }

    function submitReview(endpoint, type, id, action, remarks, certificateData = null) {
        const formData = new FormData();
        if (type === 'tx') {
            formData.append('transaction_id', id);
        } else {
            formData.append('report_id', id);
        }
        formData.append('action', type === 'mar' && action === 'return' ? 'reject' : action);
        formData.append('remarks', remarks);
        if (certificateData) {
            Object.entries(certificateData).forEach(([key, value]) => formData.append(key, value));
        }

        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successDialog = {
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#0f172a'
                };
                if (data.certificate_id) {
                    successDialog.showCancelButton = true;
                    successDialog.confirmButtonText = 'View certificate';
                    successDialog.cancelButtonText = 'Close';
                }
                Swal.fire(successDialog).then((result) => {
                    if (data.certificate_id && result.isConfirmed) {
                        window.open(`certification?id=${encodeURIComponent(data.certificate_id)}&format=pdf`, '_blank', 'noopener');
                    }
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to process review.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }
});
