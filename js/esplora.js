document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('search-form');
    
    // Se il form non esiste (es. nella Dashboard), esci silenziosamente senza errori
    if (!searchForm) {
        return; 
    }

    const searchInput = document.getElementById('search-input');
    const filterCategoria = document.getElementById('filter-categoria');
    const btnReset = document.getElementById('btn-reset');
    
    // Assicurati che il contenitore dei risultati corrisponda al nuovo template
    const resultsContainer = document.getElementById('results-grid') || document.getElementById('esplora-content');
    
    const loadingIndicator = document.getElementById('search-loading');

    let debounceTimer;
    const DEBOUNCE_DELAY = 400;

    // Ora getAttribute non darà più errore perché searchForm è validato
    const baseUrl = searchForm.getAttribute('data-url');

    function fetchResults(pushState = true) {
        loadingIndicator.classList.remove('hidden');
        resultsContainer.classList.add('is-loading');

        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);

        params.append('ajax', '1');

        const url = `${baseUrl}?${params.toString()}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                resultsContainer.innerHTML = data.html_risultati;

                const currentSearch = params.get('q');
                const currentCat = params.get('categoria');


                const hasFilters = currentSearch || currentCat;

                if (!hasFilters) {
                    if (pushState) {
                        window.location.href = baseUrl;
                        return;
                    }
                }

                if (pushState) {
                    const newUrl = `${baseUrl}?${new URLSearchParams(formData).toString()}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                }

            })
            .catch(error => {
                console.error('Error:', error);
                resultsContainer.innerHTML = '<p class="error-msg">Si è verificato un errore durante la ricerca.</p>';
            })
            .finally(() => {
                loadingIndicator.classList.add('hidden');
                resultsContainer.classList.remove('is-loading');
            });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchResults();
        }, DEBOUNCE_DELAY);
    });

    filterCategoria.addEventListener('change', () => fetchResults());


    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchResults();
    });

    window.addEventListener('popstate', function (event) {
        location.reload();
    });
});
