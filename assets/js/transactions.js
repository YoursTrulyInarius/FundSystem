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

    // Drag and Drop Logic
    const fileUpload = document.getElementById('file-upload');
    const dropZone = document.getElementById('dropzone');
    const fileNameDisplay = document.getElementById('file-name-display');

    if (fileUpload && dropZone) {
        // Prevent default drag behaviors on the whole window to stop accidental redirects
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            window.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        }

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            
            if (files.length) {
                fileUpload.files = files; // Assign files to the input
                updateFileName(files);
            }
        }

        // Handle file selected via click
        fileUpload.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                updateFileName(this.files);
            }
        });

        function updateFileName(files) {
            if (fileNameDisplay && files.length > 0) {
                let fileNames = [];
                for (let i = 0; i < files.length; i++) {
                    fileNames.push(files[i].name);
                }
                fileNameDisplay.innerHTML = fileNames.join('<br>');
                fileNameDisplay.classList.add('text-blue-600', 'font-semibold');
            }
        }
    }
});
