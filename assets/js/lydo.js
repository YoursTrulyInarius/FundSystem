document.addEventListener('DOMContentLoaded', () => {
    // Function to handle LYDO review actions
    window.reviewItem = function(type, id, action) {
        let endpoint = type === 'tx' ? 'api/lydo/approve_tx' : 'api/lydo/approve_mar';
        if (action === 'return') {
            endpoint = type === 'tx' ? 'api/lydo/return_tx' : 'api/lydo/return_mar';
        }

        if (action === 'approve') {
            Swal.fire({
                title: 'Approve & Certify?',
                text: "This will digitally generate a Certification of Review Completeness and forward it to the SK Federation.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, issue certification'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitReview(endpoint, type, id, action, '');
                }
            });
        } else {
            // For Return, we want to ask for a reason using SweetAlert input
            Swal.fire({
                title: 'Return for Correction',
                text: "Please provide a note explaining what needs to be fixed:",
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'e.g., Missing signatures on the attendance sheet...',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Return Document',
                preConfirm: (remarks) => {
                    if (!remarks) {
                        Swal.showValidationMessage('You need to provide a reason for returning the document.');
                    }
                    return remarks;
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
