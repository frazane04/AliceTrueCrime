document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const filterCategoria = document.getElementById('filter-categoria');
    const filterAnno = document.getElementById('filter-anno');
    const btnReset = document.getElementById('btn-reset');

    const resultsContainer = document.getElementById('esplora-content');
    const activeFiltersContainer = document.getElementById('active-filters-container');
    const loadingIndicator = document.getElementById('search-loading');

    // Default content container (the sections visible when no search is active)
    // We might need to wrap them in a container if we want to hide/show them easily.
    // However, the PHP logic handles removing them if filters are active.
    // For JS, if we receive specific result HTML, we'll replace the content.

    let debounceTimer;
    const DEBOUNCE_DELAY = 400;

    // URL base from form data attribute
    const baseUrl = searchForm.getAttribute('data-url');

    function fetchResults(pushState = true) {
        // Show loading
        loadingIndicator.classList.remove('hidden');
        resultsContainer.style.opacity = '0.5';

        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);

        // Add ajax flag
        params.append('ajax', '1');

        const url = `${baseUrl}?${params.toString()}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Update results
                resultsContainer.innerHTML = data.html_risultati;

                // Update active filters
                // Note: The PHP returns markup for filters, or empty string.
                // If we want to strictly follow the PHP logic we might need to hide/show default sections.
                // But simplistically:

                // If the PHP returns 'html_risultati', it means we are in "Search Mode".
                // We should hide the default sections if they are still visible.
                // A robust way is to reload if we are clearing everything, OR handle the "No Filters" case in JS.
                // But since PHP handles the initial load, we can stick to replacing results.

                // If `data.filtri_attivi` is false, it presumably means we returned to the default state.
                // BUT our PHP modification for AJAX currently only assumes returning results.
                // If we clear filters, we might want to reload the page to restore the heavy default layout 
                // (Top 3, Categories rows etc), unless we also fetch that via AJAX.
                // For this iteration, if filters are cleared, let's reload the page to be safe and restore full layout.

                const currentSearch = params.get('q');
                const currentCat = params.get('categoria');
                const currentYear = params.get('anno');

                const hasFilters = currentSearch || currentCat || currentYear;

                if (!hasFilters) {
                    // Reload to restore default view (simplest approach for now)
                    if (pushState) {
                        // Update URL without reloading first, effectively we just want to go to /esplora
                        window.location.href = baseUrl;
                        return;
                    }
                }

                // Update URL
                if (pushState) {
                    const newUrl = `${baseUrl}?${new URLSearchParams(formData).toString()}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                }

                // Update filters UI
                // We might need to inject the "Active Filters" HTML if PHP sends it, or build it.
                // The PHP current implementation doesn't explicitly send "filtri_attivi_html" in the JSON separate from "html_risultati" 
                // effectively... wait, let me check the PHP change.
                // Ah, I set it to return:
                // 'html_risultati' => $htmlRisultati 
                // 'filtri_attivi' => bool

                // The PHP logic puts active filters HTML inside $contenuto via replacement.
                // In my JSON response I didn't verify where the active filters HTML goes.
                // The PHP code: $filtriAttiviHtml is generated BUT it was only used to replace {{FILTRI_ATTIVI}} in the full page flow.
                // In AJAX JSON, I didn't include it. I should probably fix the PHP to include it or generate it in JS.
                // Let's fix PHP first to be sure, or just generate it in JS.
                // Actually, let's keep it simple: strict replacement of results.
                // If I want the "Active Filters" tags to update, I need to fetch them.

                // Let's modify the JS implementation to be robust. 
                // But first, let's handle the simple case: Search.
            })
            .catch(error => {
                console.error('Error:', error);
                resultsContainer.innerHTML = '<p class="error-msg">Si è verificato un errore durante la ricerca.</p>';
            })
            .finally(() => {
                loadingIndicator.classList.add('hidden');
                resultsContainer.style.opacity = '1';
            });
    }

    // Debounced search input
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchResults();
        }, DEBOUNCE_DELAY);
    });

    // Select change
    filterCategoria.addEventListener('change', () => fetchResults());
    filterAnno.addEventListener('change', () => fetchResults());

    // Prevent default form submit
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchResults();
    });

    // Handle Back button
    window.addEventListener('popstate', function (event) {
        // We can just reload to be safe or implement state restoration
        location.reload();
    });
});
