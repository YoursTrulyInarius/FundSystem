document.addEventListener('DOMContentLoaded', () => {
    const projectForm = document.getElementById('projectForm');
    const submitProjectBtn = document.getElementById('submitProjectBtn');

    if (projectForm) {
        projectForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(projectForm);
            submitProjectBtn.textContent = 'Saving...';
            submitProjectBtn.disabled = true;

            try {
                const response = await fetch('api/projects/create', {
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
                        text: 'Project registered successfully.',
                        icon: 'success',
                        confirmButtonText: 'Great!',
                        confirmButtonColor: '#0f172a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    // SweetAlert Error from Server
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to register project.',
                        icon: 'error',
                        confirmButtonColor: '#0f172a'
                    });
                }
            } catch (error) {
                // SweetAlert Network/Parsing Error
                Swal.fire({
                    title: 'Something went wrong',
                    text: error.message || 'An error occurred. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#0f172a'
                });
            } finally {
                submitProjectBtn.textContent = 'Save Project';
                submitProjectBtn.disabled = false;
            }
        });
    }
});
