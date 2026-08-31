<script>
window.loadAjaxPage = function(url, containerSelector = null) {
    let target = null;
    if (containerSelector) {
        target = document.querySelector(containerSelector);
    }
    
    if (!target) {
        // Priority list of dynamic container IDs across LensMatch
        const possibleSelectors = [
            '#photographers-katalog-ajax-container',
            '#elearning-katalog-ajax-container',
            '#forum-ajax-container',
            '#contests-ajax-container',
            '#dashboard-table-container',
            'main'
        ];
        for (const sel of possibleSelectors) {
            const el = document.querySelector(sel);
            if (el) {
                target = el;
                break;
            }
        }
    }

    if (!target) {
        window.location.href = url;
        return;
    }

    // Apply smooth opacity fade
    target.style.transition = 'opacity 0.2s ease-in-out';
    target.style.opacity = '0.35';
    target.style.pointerEvents = 'none';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newTarget = doc.querySelector(target.id ? `#${target.id}` : 'main');
        if (newTarget) {
            target.innerHTML = newTarget.innerHTML;
        } else {
            const bodyMain = doc.querySelector('main');
            if (bodyMain) target.innerHTML = bodyMain.innerHTML;
        }

        // Update dashboard stats container if present
        const currentStats = document.querySelector('#dashboard-stats-container');
        const newStats = doc.querySelector('#dashboard-stats-container');
        if (currentStats && newStats) {
            currentStats.innerHTML = newStats.innerHTML;
        }

        window.history.pushState(null, '', url);
    })
    .catch(err => {
        console.error('AJAX pagination error:', err);
        window.location.href = url;
    })
    .finally(() => {
        target.style.opacity = '1';
        target.style.pointerEvents = 'auto';
    });
};

// Global click interceptor for pagination links
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[data-ajax-page]');
    if (link && link.href) {
        e.preventDefault();
        window.loadAjaxPage(link.href);
    }
});

// Popstate listener for back / forward navigation
window.addEventListener('popstate', function() {
    window.loadAjaxPage(window.location.href);
});
</script>
