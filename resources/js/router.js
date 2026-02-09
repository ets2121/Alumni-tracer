
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

            // 4. Update the Title
            const newTitle = doc.querySelector('title')?.innerText || document.title;
            document.title = newTitle;

            // 5. Update History
            if (push) {
                window.history.pushState({}, newTitle, url);
            }

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
