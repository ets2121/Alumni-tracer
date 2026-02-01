<div x-data="{
    messages: [],
    remove(id) {
        this.messages = this.messages.filter(m => m.id !== id)
    },
    add(message, type = 'success') {
        const id = Date.now()
        this.messages.push({ id, message, type })
        setTimeout(() => this.remove(id), 5000)
    }
}" @toast.window="add($event.detail.message, $event.detail.type)"
    class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none">
    <template x-for="msg in messages" :key="msg.id">
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="pointer-events-auto min-w-[300px] flex items-center p-4 rounded-2xl shadow-2xl border backdrop-blur-md"
            :class="{
                'bg-emerald-50/90 border-emerald-100 text-emerald-800': msg.type === 'success',
                'bg-red-50/90 border-red-100 text-red-800': msg.type === 'error',
                'bg-amber-50/90 border-amber-100 text-amber-800': msg.type === 'warning',
                'bg-blue-50/90 border-blue-100 text-blue-800': msg.type === 'info'
             }">

            <div class="mr-3">
                <template x-if="msg.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </template>
                <template x-if="msg.type === 'error'">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </template>
                <template x-if="msg.type === 'warning' || msg.type === 'info'">
                    <svg class="w-5 h-5 text-current opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </template>
            </div>

            <p class="text-xs font-black uppercase tracking-widest" x-text="msg.message"></p>

            <button @click="remove(msg.id)"
                class="ml-auto pl-4 text-current opacity-50 hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </template>
</div>

{{-- Flash Message Handler --}}
@if(session('success'))
    <script>window.addEventListener('DOMContentLoaded', () => window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes(session('success')) }}', type: 'success' } })))</script>
@endif
@if(session('error'))
    <script>window.addEventListener('DOMContentLoaded', () => window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes(session('error')) }}', type: 'error' } })))</script>
@endif