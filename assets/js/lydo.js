document.addEventListener('DOMContentLoaded', () => {
    // Function to handle LYDO review actions
    window.reviewItem = function(type, id, action) {
        const isMar = type === 'mar';
        let endpoint = type === 'tx' ? 'api/lydo/approve_tx' : 'api/lydo/approve_mar';
        if (action === 'return') {
            endpoint = type === 'tx' ? 'api/lydo/return_tx' : 'api/lydo/return_mar';
        }

        if (action === 'approve') {
            Swal.fire({
                title: isMar ? 'Approve MAR?' : 'Approve & Certify?',
                text: isMar
                    ? 'This will approve the MAR and forward it to the SK Federation for final approval.'
                    : 'This will digitally generate a Certification of Review Completeness and forward it to the SK Federation.',
                icon: 'question',
                input: isMar ? 'textarea' : undefined,
                inputPlaceholder: isMar ? 'Optional remarks for the SK Admin...' : undefined,
                inputAttributes: isMar ? { 'aria-label': 'Optional approval remarks' } : undefined,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: isMar ? 'Approve MAR' : 'Yes, issue certification'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitReview(endpoint, type, id, action, result.value || '');
                }
            });
        } else {
            Swal.fire({
                title: isMar ? 'Reject MAR and Request Corrections' : 'Return for Correction',
                text: isMar
                    ? 'The MAR will be sent back to the SK Admin. Explain what must be corrected.'
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
                        Swal.showValidationMessage('You need to provide a reason for returning the document.');
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
    }

    function submitReview(endpoint, type, id, action, remarks) {
        const formData = new FormData();
        if (type === 'tx') {
            formData.append('transaction_id', id);
        } else {
            formData.append('report_id', id);
        }
        formData.append('action', action);
        formData.append('remarks', remarks);

        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#0f172a'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to process review.', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }
});
