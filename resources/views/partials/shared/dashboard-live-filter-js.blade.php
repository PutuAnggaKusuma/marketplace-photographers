<script>
window.dashboardLiveTable = function() {
    return {
        isLoading: false,
        searchTimer: null,

        applyFilter(paramName, value) {
            const url = new URL(window.location.href);
            if (value && value !== 'all') {
                url.searchParams.set(paramName, value);
            } else {
                url.searchParams.delete(paramName);
            }
            url.searchParams.delete('page'); // Reset pagination to page 1 on filter
            this.fetchData(url.toString());
        },

        handleSearch(e) {
            clearTimeout(this.searchTimer);
            const query = e.target.value.trim();
            this.searchTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                if (query) {
                    url.searchParams.set('search', query);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                this.fetchData(url.toString());
            }, 300);
        },

        fetchData(url) {
            this.isLoading = true;
            window.history.pushState(null, '', url);

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

                // 1. Update Table Container
                const newTable = doc.querySelector('#dashboard-table-container');
                const currentTable = document.querySelector('#dashboard-table-container');
                if (newTable && currentTable) {
                    currentTable.innerHTML = newTable.innerHTML;
                }

                // 2. Update Stats Summary Container (if exists)
                const newStats = doc.querySelector('#dashboard-stats-container');
                const currentStats = document.querySelector('#dashboard-stats-container');
                if (newStats && currentStats) {
                    currentStats.innerHTML = newStats.innerHTML;
                }
            })
            .catch(err => {
                console.error('Error fetching live data:', err);
                window.location.href = url;
            })
            .finally(() => {
                this.isLoading = false;
            });
        }
    };
};

// Global interceptor for pagination links inside live table container
document.addEventListener('click', function(e) {
    const link = e.target.closest('#dashboard-table-container a');
    if (link && link.href && !link.hasAttribute('download') && !link.getAttribute('href').startsWith('#') && !link.getAttribute('href').startsWith('javascript:')) {
        if (link.href.includes('page=') || link.href.includes('sort=') || link.href.includes('status=')) {
            e.preventDefault();
            const liveCompEl = document.querySelector('[x-data*="dashboardLiveTable"]');
            if (liveCompEl && window.Alpine) {
                const stack = Alpine.$data(liveCompEl);
                if (stack && typeof stack.fetchData === 'function') {
                    stack.fetchData(link.href);
                    return;
                }
            }
            window.location.href = link.href;
        }
    }
});

// Handle Browser History Back / Forward navigation
window.addEventListener('popstate', function() {
    const liveCompEl = document.querySelector('[x-data*="dashboardLiveTable"]');
    if (liveCompEl && window.Alpine) {
        const stack = Alpine.$data(liveCompEl);
        if (stack && typeof stack.fetchData === 'function') {
            stack.fetchData(window.location.href);
        }
    }
});
</script>
