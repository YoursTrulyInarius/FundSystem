document.addEventListener('DOMContentLoaded', () => {
    window.approveItem = function(type, id, defaults = {}) {
        if (type === 'tx') {
            showRecordingCertificateForm(id, defaults);
            return;
        }

        Swal.fire({
            title: 'Approve?',
            text: 'This LYDO-reviewed MAR will be approved. No approval remarks are required.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Approve'
        }).then((result) => {
            if (result.isConfirmed) sendDecision(id, 'approve', '');
        });
    };

    window.rejectMarItem = function(id) {
        Swal.fire({
            title: 'Reject MAR?',
            text: 'Explain why this MAR is being rejected. The SK Council will receive the reason and can resubmit it.',
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Reason for rejection',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Reject MAR',
            preConfirm: (reason) => {
                if (!reason || !reason.trim()) {
                    Swal.showValidationMessage('A rejection reason is required.');
                    return false;
                }
                return reason.trim();
            }
        }).then((result) => {
            if (result.isConfirmed) sendDecision(id, 'reject', result.value);
        });
    };

    function showRecordingCertificateForm(id, defaults) {
        const escapeHtml = (value) => String(value == null ? '' : value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[character]);
        const today = escapeHtml(defaults.today);

        Swal.fire({
            width: 600,
            showCloseButton: true,
            html: `
                <style>
                    .swal2-popup { box-sizing: border-box; display: flex !important; flex-direction: column; height: 88vh; max-height: 88vh; padding: 12px !important; position: relative; }
                    .swal2-close { align-self: auto !important; margin: 0 !important; position: absolute !important; right: 8px; top: 8px; z-index: 2; }
                    .swal2-html-container { flex: 1 1 auto; margin: 0 !important; max-height: none !important; min-height: 0; overflow: hidden; padding: 0 !important; width: 100%; }
                    .fed-annex3-scroll { height: 100%; overflow-y: auto; padding: 4px 8px; }
                    .fed-annex3-paper { background: #fff; box-sizing: border-box; color: #111; font: 12px/1.45 Tahoma, Arial, sans-serif; margin: 0 auto; max-width: 100%; min-height: 735px; padding: 25px 28px 20px; text-align: left; width: 520px; }
                    .fed-annex3-header { align-items: center; display: flex; height: 108px; justify-content: center; position: relative; text-align: center; }
                    .fed-annex3-header img { height: 82px; object-fit: contain; position: absolute; width: 82px; }
                    .fed-annex3-municipal-seal { left: 0; }
                    .fed-annex3-header img.fed-annex3-lydo-seal { height: 106px; left: auto; right: 0; width: 106px; }
                    .fed-annex3-header-copy { font-family: Bahnschrift, Arial, sans-serif; line-height: 1.15; margin: 0 116px 0 92px; }
                    .fed-annex3-header-copy p { margin: 0; }
                    .fed-annex3-municipality, .fed-annex3-address { font-size: 10px; }
                    .fed-annex3-office { font-size: 16px; font-weight: 700; margin-top: 5px !important; white-space: nowrap; }
                    .fed-annex3-label { border: 1px solid #111; display: table; font: bold 10px Arial, sans-serif; margin: 2px auto 4px; padding: 2px 12px; }
                    .fed-annex3-heading { font-size: 11px; font-weight: 700; line-height: 1.25; margin: 0 0 4px; text-align: center; }
                    .fed-annex3-title { font-size: 14px; font-weight: 700; margin: 0 0 18px; text-align: center; }
                    .fed-annex3-form p { margin: 0 0 9px; }
                    .fed-annex3-intro, .fed-annex3-disclaimer { text-align: justify; }
                    .fed-annex3-barangay { background: #fef3c7; border-bottom: 2px solid #d97706; border-radius: 2px; color: #78350f; font-weight: 700; padding: 1px 5px; }
                    .fed-annex3-field-row { align-items: baseline; display: grid; grid-template-columns: 180px minmax(0, 1fr); margin-bottom: 5px !important; }
                    .fed-annex3-field-label { font-weight: 700; }
                    .fed-annex3-inline-field { background: transparent; border: 0; border-bottom: 1px solid #111; border-radius: 0; box-sizing: border-box; color: #111; font: inherit; max-width: 100%; min-width: 0; padding: 0 3px 1px; width: 100%; }
                    .fed-annex3-inline-field:focus { background: #f8fafc; outline: 1px solid #94a3b8; }
                    .fed-annex3-issue-date { width: 185px; }
                    .fed-annex3-issued { margin-top: 18px !important; }
                    .fed-annex3-signed { margin-top: 14px !important; }
                    .fed-annex3-signature-space { height: 42px; }
                    .fed-annex3-signature-title { font-weight: 700; }
                    .fed-annex3-footer { align-items: flex-end; border-top: 1px solid #aaa; display: flex; font: 9px/1.3 Bahnschrift, Arial, sans-serif; justify-content: space-between; margin-top: 18px; padding-top: 7px; }
                    .fed-annex3-footer-copy { flex: 1; text-align: center; }
                    .fed-annex3-footer-copy p { margin: 1px 0; }
                    .fed-annex3-motto { font-size: 10px; font-style: italic; }
                    .fed-annex3-footer-contact { align-items: center; display: flex; flex-wrap: wrap; gap: 3px; justify-content: center; }
                    .fed-annex3-footer-contact img { height: 10px; object-fit: contain; width: 10px; }
                    .fed-annex3-footer-brands { align-items: flex-end; display: flex; gap: 4px; margin-left: 6px; }
                    .fed-annex3-dpo { height: 60px; object-fit: contain; width: 44px; }
                    .fed-annex3-qr { height: 45px; object-fit: contain; width: 45px; }
                    .fed-annex3-bagong { height: 42px; object-fit: contain; width: 46px; }
                    @media (max-width: 700px) {
                        .swal2-popup { height: 90vh; max-height: 90vh; padding: 8px !important; width: calc(100vw - 20px) !important; }
                        .fed-annex3-scroll { padding: 4px; }
                        .fed-annex3-paper { min-height: 735px; padding: 20px 16px; width: 100%; }
                        .fed-annex3-header { height: 82px; }
                        .fed-annex3-header img { height: 54px; width: 54px; }
                        .fed-annex3-header img.fed-annex3-lydo-seal { height: 70px; width: 70px; }
                        .fed-annex3-header-copy { margin: 0 74px; text-align: center; }
                        .fed-annex3-office { font-size: 13px; }
                        .fed-annex3-field-row { grid-template-columns: 1fr; gap: 2px; }
                        .fed-annex3-footer-brands { display: none; }
                    }
                </style>
                <div class="fed-annex3-scroll">
                    <article class="fed-annex3-paper">
                        <header class="fed-annex3-header">
                            <img class="fed-annex3-municipal-seal" src="/FundSystem/assets/img/annex2-municipal-seal.png" alt="Municipality seal">
                            <img class="fed-annex3-lydo-seal" src="/FundSystem/assets/img/annex2-lydo-seal.png" alt="LYDO seal">
                            <div class="fed-annex3-header-copy">
                                <p class="fed-annex3-municipality">MUNICIPALITY OF RAMON MAGSAYSAY</p>
                                <p class="fed-annex3-address">Municipal Hall, Quezon St., Poblacion, Ramon Magsaysay</p>
                                <p class="fed-annex3-address">Zamboanga del Sur, 7024 Philippines</p>
                                <p class="fed-annex3-office">LOCAL YOUTH DEVELOPMENT OFFICE</p>
                            </div>
                        </header>
                        <div class="fed-annex3-label">Annex 3</div>
                        <h2 class="fed-annex3-heading">LIARGAONON YOUTH OFFICE FISCAL OVERSIGHT AND TRANSPARENCY (LYOFOST)<br>SK FUND PROGRAM</h2>
                        <h2 class="fed-annex3-title">CERTIFICATION OF RECORDING</h2>
                        <form id="fed-recording-certificate-form" class="fed-annex3-form">
                            <p class="fed-annex3-intro">
                                This is to certify that the financial transaction record of the Sangguniang Kabataan of Barangay
                                <span class="fed-annex3-barangay">${escapeHtml(String(defaults.barangay || '').replace(/^Barangay\s+/i, ''))}</span>
                                has been duly entered and recorded in the Official SK Financial Transaction Registry maintained under the LYOFOST SK Fund Program.
                            </p>
                            <p>The following record has been officially registered:</p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Registry Control No.:</span><input name="registry_control_no" class="fed-annex3-inline-field" type="text" maxlength="100" required aria-label="Registry Control Number"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Entry No.:</span><input name="entry_no" class="fed-annex3-inline-field" type="text" maxlength="100" required aria-label="Entry Number"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Reference Document No.:</span><input name="reference_document_no" class="fed-annex3-inline-field" type="text" maxlength="100" required value="${escapeHtml(defaults.referenceNo)}" aria-label="Reference Document Number"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Project/Program/Activity:</span><input name="project_activity" class="fed-annex3-inline-field" type="text" maxlength="255" required value="${escapeHtml(defaults.projectActivity)}" aria-label="Project, Program, or Activity"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Amount Involved (Php):</span><input name="amount" class="fed-annex3-inline-field" type="number" min="0.01" max="9999999999999.99" step="0.01" required value="${escapeHtml(defaults.amount)}" aria-label="Amount Involved"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Date of Transaction:</span><input name="date_of_transaction" class="fed-annex3-inline-field" type="date" required aria-label="Date of Transaction"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Date Recorded:</span><input name="date_recorded" class="fed-annex3-inline-field" type="date" required value="${today}" aria-label="Date Recorded"></p>
                            <p class="fed-annex3-field-row"><span class="fed-annex3-field-label">Compliance Status:</span><input name="compliance_status" class="fed-annex3-inline-field" type="text" maxlength="100" required aria-label="Compliance Status"></p>
                            <p class="fed-annex3-disclaimer">This certification confirms only the recording of the transaction in the official registry and does not, by itself, constitute approval, audit clearance, or financial certification of the expenditure.</p>
                            <p class="fed-annex3-issued">Issued this <input name="issue_date" type="date" required value="${today}" class="fed-annex3-inline-field fed-annex3-issue-date" aria-label="Issue Date"> at Ramon Magsaysay, Zamboanga del Sur.</p>
                            <p class="fed-annex3-signed">Signed:</p>
                            <div class="fed-annex3-signature-space"></div>
                            <p class="fed-annex3-signature-title">MUNICIPAL SK FEDERATION</p>
                        </form>
                        <footer class="fed-annex3-footer">
                            <div class="fed-annex3-footer-copy">
                                <p class="fed-annex3-motto">Hapsay, Han-ay, Husay, Hamugaway nga Ramon Magsaysay</p>
                                <p class="fed-annex3-motto">For the Filipino Youth, For the Liargaonon Youth</p>
                                <p class="fed-annex3-footer-contact">
                                    <img src="/FundSystem/assets/img/annex2-web-icon.png" alt=""> www.ramonmagsaysay.gov.ph
                                    <img src="/FundSystem/assets/img/annex2-email-icon.png" alt=""> skliargao@gmail.com
                                    <img src="/FundSystem/assets/img/annex2-facebook-icon.png" alt=""> SK LYDO Ramon Magsaysay
                                </p>
                            </div>
                            <div class="fed-annex3-footer-brands">
                                <img class="fed-annex3-dpo" src="/FundSystem/assets/img/annex2-dpo-seal.jpeg" alt="Data privacy seal">
                                <img class="fed-annex3-qr" src="/FundSystem/assets/img/annex2-registry-qr.png" alt="Registry QR code">
                                <img class="fed-annex3-bagong" src="/FundSystem/assets/img/annex2-bagong-pilipinas.png" alt="Bagong Pilipinas">
                            </div>
                        </footer>
                    </article>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Issue Recording Certificate',
            cancelButtonText: 'Cancel',
            focusConfirm: false,
            preConfirm: () => {
                const form = document.getElementById('fed-recording-certificate-form');
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
                    formData.get('date_of_transaction') > formData.get('date_recorded') ||
                    formData.get('date_recorded') > formData.get('issue_date')
                ) {
                    Swal.showValidationMessage('The transaction date must not be after the recording date, and the recording date must not be after the issue date.');
                    return false;
                }
                return Object.fromEntries(formData.entries());
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitRecordingCertificate(id, result.value);
            }
        });
    }

    function submitRecordingCertificate(id, certificateData) {
        const formData = new FormData();
        formData.append('transaction_id', id);
        Object.entries(certificateData).forEach(([key, value]) => formData.append(key, value));
        fetch('api/fed/record_tx', {
            method: 'POST',
            body: formData
        })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                Swal.fire('Error', data.message || 'Failed to record the transaction.', 'error');
                return;
            }
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#0f172a',
                showCancelButton: true,
                confirmButtonText: 'View certificate',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed && data.certificate_id) {
                    window.open(`certification?id=${encodeURIComponent(data.certificate_id)}`, '_blank', 'noopener');
                }
                window.location.reload();
            });
        })
        .catch(() => Swal.fire('Error', 'An unexpected error occurred.', 'error'));
    }

    function sendDecision(id, action, reason) {
        const formData = new FormData();
        formData.append('report_id', id);
        formData.append('action', action);
        formData.append('reason', reason);

        fetch('api/fed/record_mar', {
            method: 'POST',
            body: formData
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#0f172a'
                }).then(() => window.location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to process the decision.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'An unexpected error occurred.', 'error'));
    }
});
