<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <h3 class="text-lg font-bold">Welcome, {{ Auth::user()->name }}!</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- News Feed -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4">Latest News & Events</h3>
                        @if($newsEvents->isEmpty())
                            <p class="text-gray-500">No news yet.</p>
                        @else
                            <div class="space-y-6">
                                @foreach($newsEvents as $post)
                                    <div class="border-b pb-4 last:border-b-0">
                                        @if($post->image_path)
                                            <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                                                class="w-full h-48 object-cover rounded mb-2">
                                        @endif
                                        <h4 class="font-bold text-lg">{{ $post->title }}</h4>
                                        <p class="text-sm text-gray-500 mb-2">
                                            <span
                                                class="inline-block bg-{{ $post->type === 'news' ? 'blue' : 'purple' }}-100 text-{{ $post->type === 'news' ? 'blue' : 'purple' }}-800 text-xs px-2 rounded">{{ ucfirst($post->type) }}</span>
                                            {{ $post->created_at->format('M d, Y') }}
                                        </p>
                                        <p class="text-gray-700">{{ Str::limit($post->content, 100) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions / Profile Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4">My Status</h3>
                        @if(Auth::user()->status !== 'active')
                            <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-r"
                                role="alert">
                                <p class="font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pending Approval
                                </p>
                                <p class="text-sm mt-2">Your account is currently under review. To speed up the verification
                                    process, please ensure your profile is complete and you have uploaded a valid proof of
                                    graduation.</p>
                                <div class="mt-4">
                                    <a href="{{ route('alumni.profile.edit') }}"
                                        class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 active:bg-amber-900 focus:outline-none focus:border-amber-900 focus:ring ring-amber-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Complete Profile & Upload Proof
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('profile.edit') }}"
                                class="text-blue-600 hover:text-blue-900 underline">Update Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>