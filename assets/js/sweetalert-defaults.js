if (window.Swal) {
    window.Swal = window.Swal.mixin({
        showCloseButton: true,
        confirmButtonText: 'Okay'
    });

    const sweetAlertStyles = document.createElement('style');
    sweetAlertStyles.textContent = `
        .swal2-actions {
            width: calc(100% - 3em) !important;
            justify-content: flex-end !important;
            margin: 1.25em 1.5em 0 !important;
        }
    `;
    document.head.appendChild(sweetAlertStyles);
}
