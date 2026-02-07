<div class="space-y-8">
    <!-- Summary Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-lg font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tighter">Comparative Analytical Matrix</h2>
            <p class="text-[9px] font-bold text-gray-400 dark:text-dark-text-muted uppercase tracking-widest mt-1">
                @switch($data['sub_type'])
                    @case('by_year') Global Distribution by Graduation Year @break
                    @case('by_course') Programmatic Distribution Model @break
                    @case('employment_by_year') Employment Status Multi-Year Trends @break
                    @case('employment_by_course') Employment Status by Program @break
                    @case('location_by_year') Work Location Multi-Year Trends @break
                    @case('location_by_course') Work Location by Program @break
                    @default Analytical Intelligence Node
                @endswitch
            </p>
        </div>
        <div class="flex gap-2">
            <span class="px-4 py-1.5 bg-brand-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-brand-100 italic">Visual Analytics</span>
        </div>
    </div>

    @if($data['distribution']->isEmpty())
        <div class="bg-white dark:bg-dark-bg-elevated p-16 rounded-[3rem] border border-gray-100 dark:border-dark-border shadow-sm text-center">
            <div class="w-20 h-20 bg-gray-50 dark:bg-dark-bg-subtle text-gray-400 dark:text-dark-text-muted rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="text-lg font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-tighter">No Analytical Data</p>
            <p class="text-[10px] font-bold text-gray-400 dark:text-dark-text-muted uppercase tracking-widest mt-2">No records match the current filter selection for this matrix.</p>
        </div>
    @else

    <!-- Chart Container -->
    <div class="bg-white dark:bg-dark-bg-elevated rounded-[3rem] border border-gray-100 dark:border-dark-border shadow-sm p-10">
        <div class="h-[500px] relative">
            <canvas id="distributionMainChart" 
                data-type="{{ $data['chart_type'] }}"
                data-labels='{!! json_encode($data['distribution']->pluck('batch_year')->map(fn($y) => (string)$y)->merge($data['distribution']->pluck('label'))->filter()->values()) !!}'
                data-values='{!! json_encode($data['distribution']->pluck('count')->values()) !!}'
                data-raw='{!! json_encode($data['distribution']) !!}'
                data-subtype="{{ $data['sub_type'] }}"
            ></canvas>
        </div>
    </div>

    <!-- Legend/Summary Table -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gray-50/50 dark:bg-dark-bg-subtle/50 rounded-[2.5rem] p-8 border border-gray-100 dark:border-dark-border">
            <h4 class="text-[10px] font-black text-gray-900 dark:text-dark-text-primary uppercase tracking-widest mb-6">Distribution Summary</h4>
            <div class="space-y-4">
                @foreach($data['distribution']->take(5) as $item)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-dark-text-secondary uppercase">{{ $item->batch_year ?? $item->label ?? 'Unknown' }}</span>
                        <div class="flex items-center gap-4">
                            <div class="w-32 h-2 bg-gray-200 dark:bg-dark-bg-subtle rounded-full overflow-hidden">
                                <div class="h-full bg-purple-600 rounded-full" style="width: {{ $data['distribution']->max('count') > 0 ? ($item->count / $data['distribution']->max('count') * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-[11px] font-black text-gray-900 dark:text-dark-text-primary">{{ $item->count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-purple-600 rounded-[2.5rem] p-8 text-white flex flex-col justify-center">
            <div class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Total Sample Size</div>
            <div class="text-5xl font-black tracking-tighter">{{ $data['distribution']->sum('count') }}</div>
            <p class="text-[10px] font-bold uppercase tracking-widest mt-6 opacity-80 leading-relaxed">
                Aggregated data based on current filter context. Charts recalculate in real-time.
            </p>
        </div>
    </div>
    @endif
</div>
