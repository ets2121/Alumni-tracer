@forelse($posts as $post)
    @include('alumni.partials._feed_card', ['post' => $post])
@empty
    <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                </path>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">No posts found</h3>
        <p class="text-sm text-gray-500 max-w-xs mx-auto">We couldn't find any content for this category. Check back later
            for updates!</p>
    </div>
@endforelse