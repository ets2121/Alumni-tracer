<div class="space-y-8">
    <div class="border-b dark:border-dark-border pb-6 text-center sm:text-left">
        <h2 class="text-xl font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest">Geographic Alumni Mapping</h2>
        <p class="text-xs text-gray-500 dark:text-dark-text-secondary font-bold mt-1">Geographic distribution grouped by registered addresses.</p>
    </div>

    @foreach($data as $address => $profiles)
        <div class="mb-8 p-6 border border-gray-100 dark:border-dark-border rounded-3xl bg-gray-50/30 dark:bg-dark-bg-subtle/30">
            <h3 class="text-sm font-black text-gray-900 dark:text-dark-text-primary mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd" />
                </svg>
                {{ $address }}
                <span
                    class="ml-auto text-[10px] bg-white dark:bg-dark-bg px-3 py-1 rounded-full shadow-sm dark:shadow-none text-gray-400 dark:text-dark-text-muted uppercase tracking-widest">{{ $profiles->count() }}
                    Alumni</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($profiles as $profile)
                    <div
                        class="bg-white dark:bg-dark-bg-elevated p-4 rounded-2xl shadow-sm dark:shadow-none border border-gray-100 dark:border-dark-border flex items-center gap-3 hover:border-amber-200 dark:hover:border-amber-900/50 transition-colors">
                        <div
                            class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 font-black text-xs">
                            {{ substr($profile->first_name, 0, 1) }}{{ substr($profile->last_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-[11px] font-black text-gray-900 dark:text-dark-text-primary">{{ $profile->first_name }}
                                {{ $profile->last_name }}</div>
                            <div class="text-[9px] font-bold text-gray-400 dark:text-dark-text-muted uppercase tracking-tighter">
                                {{ $profile->course->code }} • Batch {{ $profile->batch_year }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>