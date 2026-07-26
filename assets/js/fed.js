document.addEventListener('DOMContentLoaded', () => {
    // Function to handle SK Fed record actions
    window.recordItem = function(type, id) {
        let endpoint = type === 'tx' ? 'api/fed/record_tx' : 'api/fed/record_mar';

        Swal.fire({
            title: 'Record & Certify?',
            text: "This will officially record the document and generate the final Certification of Recording.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1', // indigo-500
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, record it'
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
                        Swal.fire('Error', data.message || 'Failed to record document.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                });
            }
        });
    }
});
