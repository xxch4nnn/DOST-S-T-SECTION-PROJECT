<div x-data="notificationToasts()"
     x-on:notify.window="addToast($event.detail)"
     class="notification-toasts-container"
     aria-live="polite"
     aria-atomic="true">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div :class="getToastClass(toast.type)"
             class="notification-toast"
             role="alert"
             x-transition:enter="animate-toast-in"
             x-transition:leave="animate-toast-out"
             style="animation-duration: 250ms;">
            
            <div class="toast-content">
                <i :class="getToastIcon(toast.type)" class="toast-icon ph-bold"></i>
                <div class="toast-message" x-text="toast.message"></div>
            </div>

            <button type="button" 
                    @click="removeToast(toast.id)" 
                    class="toast-close-btn" 
                    aria-label="Close notification alert">
                <i class="ph ph-x"></i>
            </button>
        </div>
    </template>
</div>

<script>
    function notificationToasts() {
        return {
            toasts: [],
            
            init() {
                // Also listen for Livewire-dispatched events
                if (window.Livewire) {
                    Livewire.on('notify', (data) => {
                        const payload = Array.isArray(data) ? data[0] : data;
                        this.addToast(payload);
                    });
                }
            },

            addToast(detail) {
                const payload = typeof detail === 'string' ? { message: detail } : (detail || {});
                const id = Date.now() + Math.random().toString(36).substring(2, 7);
                const type = payload.type || 'purple';
                const message = payload.message || 'A simple content alert—check it out!';
                const duration = payload.duration !== undefined ? payload.duration : 6000;

                const newToast = { id, type, message };
                this.toasts.push(newToast);

                if (duration > 0) {
                    setTimeout(() => {
                        this.removeToast(id);
                    }, duration);
                }
            },

            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },

            getToastClass(type) {
                const map = {
                    'purple': 'toast-purple',
                    'info': 'toast-purple',
                    'gray': 'toast-gray',
                    'default': 'toast-gray',
                    'green': 'toast-green',
                    'success': 'toast-green',
                    'red': 'toast-red',
                    'danger': 'toast-red',
                    'error': 'toast-red',
                    'cyan': 'toast-cyan',
                    'sky': 'toast-cyan',
                    'yellow': 'toast-yellow',
                    'warning': 'toast-yellow',
                };
                return map[type] || 'toast-purple';
            },

            getToastIcon(type) {
                // Triangle warning icons matching Image 2
                return 'ph-warning';
            }
        };
    }
</script>
