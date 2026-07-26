document.addEventListener('DOMContentLoaded', () => {
    const marForm = document.getElementById('marForm');
    const submitMarBtn = document.getElementById('submitMarBtn');
    const marAlert = document.getElementById('marAlert');

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
