import './bootstrap';
import Swal from 'sweetalert2';

// Make it globally available for ad-hoc JS usage if needed
window.Swal = Swal;

// Configure SweetAlert2 to match DOSTorage Bootstrap 5 theme perfectly
const swalBootstrapTheme = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-danger px-4 py-2',
        cancelButton: 'btn btn-outline-secondary px-4 py-2 ms-3',
        popup: 'rounded-4 shadow-sm border-0' // Modern rounded corners
    },
    buttonsStyling: false // Let Bootstrap 5 handle the button styling completely
});

window.swalBootstrap = swalBootstrapTheme;

// Global listener for Livewire dispatch events
window.addEventListener('swal:confirm', (event) => {
    // In Livewire v3, event.detail can sometimes be an array if dispatched with positional arguments
    const data = event.detail[0] || event.detail;
    
    swalBootstrapTheme.fire({
        title: data.title || 'Are you sure?',
        text: data.text || "You won't be able to revert this!",
        icon: data.icon || 'warning',
        showCancelButton: true,
        confirmButtonText: data.confirmText || 'Yes, proceed',
        cancelButtonText: data.cancelText || 'Cancel'
    }).then((result) => {
        if (result.isConfirmed && data.action) {
            // Dispatch an event back to the Livewire component that requested it
            Livewire.dispatch(data.action, { id: data.id });
        }
    });
});
