@props([
    'event' => 'toast',
])

<div
    x-data="{
        toasts: [],
        addToast(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 3000);
        }
    }"
    x-init="
        window.addEventListener('{{ $event }}', (e) => {
            addToast(e.detail.type, e.detail.message);
        })
    "
    class="fixed top-5 right-5 z-50 space-y-3"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform ease-in duration-300"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white min-w-[250px]"
            :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
                'bg-yellow-500 text-black': toast.type === 'warning',
            }"
        >
            <span class="text-sm font-medium" x-text="toast.message"></span>
        </div>
    </template>
</div>
