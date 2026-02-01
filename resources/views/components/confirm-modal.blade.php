@props(['event' => 'open-confirmation-modal'])

<div x-data="{ 
        show: false, 
        title: '', 
        message: '', 
        confirmText: 'Confirm', 
        cancelText: 'Cancel',
        method: '', 
        action: '',
        danger: false
    }" @class(['fixed inset-0 z-[100] overflow-y-auto']) role="dialog" aria-modal="true" style="display: none;"
    x-show="show" x-on:{{ $event }}.window="
        show = true; 
        title = $event.detail.title; 
        message = $event.detail.message; 
        action = $event.detail.action; 
        method = $event.detail.method || 'POST';
        confirmText = $event.detail.confirmText || 'Confirm';
        danger = $event.detail.danger || false;
    ">

    <!-- Backdrop -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="show = false"
            x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100"
            x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                        :class="danger ? 'bg-red-100' : 'bg-brand-100'">
                        <svg class="h-6 w-6" :class="danger ? 'text-red-600' : 'text-brand-600'" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="danger" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            <path x-show="!danger" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <!-- Dynamic Form Submission -->
                <form :action="action" :method="method === 'GET' ? 'GET' : 'POST'"
                    class="inline-flex w-full sm:w-auto sm:ml-3">
                    <template x-if="method !== 'GET'">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </template>
                    <template x-if="['PUT', 'PATCH', 'DELETE'].includes(method)">
                        <input type="hidden" name="_method" :value="method">
                    </template>

                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto sm:text-sm transition-all"
                        :class="danger ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-brand-600 hover:bg-brand-700 focus:ring-brand-500'"
                        x-text="confirmText">
                    </button>
                </form>

                <button type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    @click="show = false">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>