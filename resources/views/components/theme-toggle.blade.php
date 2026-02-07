<button x-data="{
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }
    }" @click="toggleTheme()"
    class="relative p-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-dark-state-hover group focus:outline-none focus:ring-2 focus:ring-primary-500"
    :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
    <!-- Sun Icon (Visible in Dark Mode) -->
    <svg x-show="darkMode" x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="rotate-90 scale-0 opacity-0" x-transition:enter-end="rotate-0 scale-100 opacity-100"
        class="w-5 h-5 text-secondary-500" fill="currentColor" viewBox="0 0 20 20">
        <path
            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
            fill-rule="evenodd" clip-rule="evenodd"></path>
    </svg>

    <!-- Moon Icon (Visible in Light Mode) -->
    <svg x-show="!darkMode" x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="-rotate-90 scale-0 opacity-0" x-transition:enter-end="rotate-0 scale-100 opacity-100"
        class="w-5 h-5 text-primary-900" fill="currentColor" viewBox="0 0 20 20">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
    </svg>
</button>