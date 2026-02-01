<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('alumni.news.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-brand-600 mb-8 transition-colors group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to News
            </a>

            <article class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                @if($newsEvent->image_path)
                    <div class="aspect-video w-full overflow-hidden">
                        <img src="{{ asset('storage/' . $newsEvent->image_path) }}" class="w-full h-full object-cover"
                            alt="{{ $newsEvent->title }}">
                    </div>
                @endif

                <div class="p-8 md:p-12">
                    <div class="flex items-center gap-4 mb-6">
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $newsEvent->type === 'news' ? 'bg-blue-100 text-blue-700' : 'bg-brand-100 text-brand-700' }}">
                            {{ $newsEvent->type }}
                        </span>
                        <span
                            class="text-sm text-gray-400 font-medium">{{ $newsEvent->created_at->toFormattedDateString() }}</span>
                    </div>

                    <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-8">
                        {{ $newsEvent->title }}</h1>

                    @if($newsEvent->type === 'event' && ($newsEvent->event_date || $newsEvent->location))
                        <div class="bg-gray-50 rounded-2xl p-6 mb-8 flex flex-col md:flex-row gap-6 border border-gray-100">
                            @if($newsEvent->event_date)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-brand-600 border border-gray-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</div>
                                        <div class="font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($newsEvent->event_date)->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($newsEvent->location)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-brand-600 border border-gray-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Location
                                        </div>
                                        <div class="font-bold text-gray-800">{{ $newsEvent->location }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed font-medium">
                        {!! nl2br(e($newsEvent->content)) !!}
                    </div>
                </div>
            </article>

            <div class="mt-12 flex justify-center">
                <a href="{{ route('alumni.news.index') }}"
                    class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all shadow-lg hover:shadow-xl">
                    Back to All News
                </a>
            </div>
        </div>
    </div>
</x-app-layout>