document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-box input');
    const searchBox = document.querySelector('.search-box');
    
    // Create suggestions container
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'search-suggestions';
    searchBox.appendChild(suggestionsContainer);
    
    function showSuggestions(inputValue) {
        const matchingLocations = malaysianLocations.filter(location => 
            location.toLowerCase().includes(inputValue.toLowerCase())
        );
        
        suggestionsContainer.innerHTML = '';
        if (inputValue && matchingLocations.length > 0) {
            matchingLocations.forEach(location => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = location;
                div.addEventListener('click', () => {
                    searchInput.value = location;
                    suggestionsContainer.innerHTML = '';
                    window.location.href = `dashboard.php?location=${encodeURIComponent(location)}`;
                });
                suggestionsContainer.appendChild(div);
            });
            suggestionsContainer.style.display = 'block';
        } else {
            suggestionsContainer.style.display = 'none';
        }
    }
    
    // Input event listener
    searchInput.addEventListener('input', (e) => {
        showSuggestions(e.target.value);
    });
    
    // Form submission
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && searchInput.value.trim()) {
            e.preventDefault();
            window.location.href = `dashboard.php?location=${encodeURIComponent(searchInput.value.trim())}`;
        }
    });
    
    // Click outside to close suggestions
    document.addEventListener('click', (e) => {
        if (!searchBox.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
});