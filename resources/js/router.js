
export default function router() {
    const startLoading = () => window.dispatchEvent(new CustomEvent('loading-start'));
    const stopLoading = () => window.dispatchEvent(new CustomEvent('loading-end'));

    // Cache parser to avoid re-creating it
    const parser = new DOMParser();

    async function navigate(url, push = true) {
        if (url === window.location.href) return;

        startLoading();

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Mark as AJAX
                    'X-Inertia': 'true', // Optional: mimic Inertia if we want similar server handling later
                }
            });

            if (!response.ok) throw new Error('Navigation failed');

            const html = await response.text();
            const doc = parser.parseFromString(html, 'text/html');

            // 0. Update History (Critical: Do this BEFORE DOM swap so components read correct URL)
            if (push) {
                window.history.pushState({}, newTitle, url);
            }

            // 1. Synchronize the Content Wrapper (Scroll behavior & Layout context)
            const newWrapper = doc.getElementById('content-wrapper');
            const currentWrapper = document.getElementById('content-wrapper');
            if (newWrapper && currentWrapper) {
                currentWrapper.className = newWrapper.className;
            }

            // 2. Synchronize the Header Slot
            const newHeader = doc.getElementById('header-container');
            const currentHeader = document.getElementById('header-container');
            if (newHeader && currentHeader) {
                currentHeader.innerHTML = newHeader.innerHTML;
                currentHeader.className = newHeader.className;
            } else if (currentHeader) {
                // If the new page has no header slot, clear the current one
                currentHeader.innerHTML = '';
            }

            // 3. Synchronize the Main Content
            const newMain = doc.querySelector('main');
            const currentMain = document.querySelector('main');
            if (newMain && currentMain) {
                currentMain.innerHTML = newMain.innerHTML;
                currentMain.className = newMain.className;
            }

            // 3.5. Synchronize the Navigation Bar (Active States)
            const newNav = doc.getElementById('main-navigation');
            const currentNav = document.getElementById('main-navigation');
            if (newNav && currentNav) {
                // We only want to swap the inner HTML to preserve x-data state if possible,
                // BUT x-data is on the nav tag itself. Swapping innerHTML is safer for Alpine parent state?
                // Actually, the active classes are likely deep inside.
                // Let's swap the whole thing, but we might lose the 'open' state if it was open.
                // That's acceptable (resetting menu on nav).
                // However, replacing the element destroys the Alpine instance.
                // We need to be careful. Alpine 3 handles DOM morphing better if we use Alpine.morph (not installed).
                // Simpler approach: innerHTML replacement.
                currentNav.innerHTML = newNav.innerHTML;
                currentNav.className = newNav.className; // In case classes change
            }

            // 4. Update the Title
            const newTitle = doc.querySelector('title')?.innerText || document.title;
            document.title = newTitle;


            // 6. Reset Scroll
            if (currentWrapper && currentWrapper.classList.contains('overflow-y-auto')) {
                currentWrapper.scrollTo(0, 0);
            } else {
                window.scrollTo(0, 0);
            }

            // 7. Dispatch event for other listeners
            window.dispatchEvent(new CustomEvent('page-navigated'));

        } catch (error) {
            console.error('Navigation error:', error);
            // Fallback to full reload on error
            window.location.href = url;
        } finally {
            stopLoading();
        }
    }

    // Intercept Clicks
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');

        // Validation: Must be a link, local, not hash implementation, not new tab
        if (!link ||
            !link.href.startsWith(window.location.origin) ||
            link.hash ||
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.hasAttribute('data-no-spa') || // Escape hatch
            e.metaKey || e.ctrlKey || e.shiftKey || e.altKey
        ) {
            return;
        }

        e.preventDefault();
        navigate(link.href);
    });

    // Handle Back/Forward support
    window.addEventListener('popstate', () => {
        navigate(window.location.href, false);
    });
}
