document.addEventListener('DOMContentLoaded', () => {
    const txForm = document.getElementById('txForm');
    const submitTxBtn = document.getElementById('submitTxBtn');

    if (txForm) {
        txForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(txForm);
            submitTxBtn.textContent = 'Uploading...';
            submitTxBtn.disabled = true;

            try {
                const response = await fetch('api/transactions/submit', {
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
                    // SweetAlert Success
                    Swal.fire({
                        title: 'Success!',
                        text: 'Transaction document uploaded successfully.',
                        icon: 'success',
                        confirmButtonText: 'Great!',
                        confirmButtonColor: '#0f172a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    // SweetAlert Error
                    Swal.fire({
                        title: 'Upload Failed',
                        text: data.message || 'Failed to submit transaction.',
                        icon: 'error',
                        confirmButtonColor: '#0f172a'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Something went wrong',
                    text: error.message || 'An error occurred. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#0f172a'
                });
            } finally {
                submitTxBtn.textContent = 'Save Transaction';
                submitTxBtn.disabled = false;
            }
        });
    }
});
