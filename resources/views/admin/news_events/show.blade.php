<x-layouts.admin>
    <x-slot name="header">
        {{ $newsEvent->title }}
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start gap-6 border-b border-gray-100 pb-8">
                <div class="flex-1 space-y-4">
                    <div class="flex items-center gap-3">
                        @if($newsEvent->type === 'news')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-100 uppercase">News</span>
                        @elseif($newsEvent->type === 'event')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-50 text-purple-600 border border-purple-100 uppercase">Event</span>
                        @elseif($newsEvent->type === 'announcement')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-600 border border-amber-100 uppercase">Announcement</span>
                        @endif
                        
                        @if($newsEvent->is_pinned)
                            <span class="flex items-center text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                Pinned
                            </span>
                        @endif
                        
                        <span class="text-sm text-gray-500">Posted {{ $newsEvent->created_at->format('M d, Y') }}</span>
                    </div>

                    <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-tight">{{ $newsEvent->title }}</h1>
                    
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                        @if($newsEvent->author)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="font-medium">{{ $newsEvent->author }}</span>
                            </div>
                        @endif
                        
                        @if($newsEvent->category)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                @foreach($newsEvent->category as $tag)
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-600 font-medium">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('admin.news_events.index') }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Back to List</a>
                    <button class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-bold hover:bg-brand-700 transition-colors shadow-lg shadow-brand-100">Edit Post</button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    @if($newsEvent->image_path)
                        <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100 aspect-video relative">
                            <img src="{{ asset('storage/' . $newsEvent->image_path) }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($newsEvent->content)) !!}
                    </div>

                    <!-- Event Gallery -->
                    @if($newsEvent->type === 'event' && $newsEvent->photos->count() > 0)
                        <div class="pt-8 border-t border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Event Gallery
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($newsEvent->photos as $photo)
                                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 hover:shadow-lg transition-all hover:scale-[1.02] cursor-pointer group relative">
                                        <img src="{{ asset('storage/' . $photo->image_path) }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar Details -->
                <div class="space-y-6">
                    @if($newsEvent->type === 'event')
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-6">
                            <h3 class="font-bold text-gray-900 border-b border-gray-200 pb-2">Event Details</h3>
                            
                            @if($newsEvent->event_date)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex flex-col items-center justify-center border border-gray-200 shadow-sm text-xs font-bold text-gray-900 overflow-hidden">
                                        <span class="text-[0.6rem] text-red-500 uppercase tracking-wide bg-red-50 w-full text-center py-0.5">{{ $newsEvent->event_date->format('M') }}</span>
                                        <span>{{ $newsEvent->event_date->format('d') }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $newsEvent->event_date->format('l, F j, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $newsEvent->event_date->format('g:i A') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($newsEvent->location)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gray-200 shadow-sm text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">Venue</div>
                                        <div class="text-xs text-gray-500">{{ $newsEvent->location }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($newsEvent->registration_link)
                                <a href="{{ $newsEvent->registration_link }}" target="_blank" class="block w-full text-center bg-brand-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-100 text-sm">
                                    Register Now
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($newsEvent->type === 'announcement' && $newsEvent->expires_at)
                        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-amber-900">Expiration Date</h3>
                                <p class="text-xs text-amber-700 mt-1">This announcement will be automatically archived on <span class="font-bold">{{ $newsEvent->expires_at->format('M d, Y') }}</span>.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
