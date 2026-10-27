document.addEventListener('DOMContentLoaded', () => {
    window.approveItem = function(type, id) {
        const isTransaction = type === 'tx';
        const endpoint = isTransaction ? 'api/fed/record_tx' : 'api/fed/record_mar';
        const actionLabel = isTransaction ? 'Approve & certify' : 'Approve';

        Swal.fire({
            title: actionLabel + '?',
            text: isTransaction
                ? 'This LYDO-reviewed transaction will be approved and certified. Submitted details and documents cannot be edited here.'
                : 'This LYDO-reviewed MAR will be approved. Submitted details and documents cannot be edited here.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1', // indigo-500
            cancelButtonColor: '#64748b',
            confirmButtonText: actionLabel
        }).then((result) => {
            if (result.isConfirmed) {
                
                const formData = new FormData();
                if (type === 'tx') {
                    formData.append('transaction_id', id);
                } else {
                    formData.append('report_id', id);
                }

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
                        Swal.fire('Error', data.message || 'Failed to approve submission.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                });
            }
        });
    }
});
